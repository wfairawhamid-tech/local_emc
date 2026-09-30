<?php
namespace App\Controllers;

use App\Models\PatientModel;
use App\Models\MedicalCertificateModel;
use App\Models\TimeSlipModel;
use App\Models\DoctorModel;

class Home extends BaseController
{
    protected PatientModel            $patientModel;
    protected MedicalCertificateModel $mcModel;
    protected TimeSlipModel           $tsModel;
    protected DoctorModel            $doctorModel;

    public function __construct()
    {
        $this->patientModel = new PatientModel();
        $this->mcModel      = new MedicalCertificateModel();
        $this->tsModel      = new TimeSlipModel();
        $this->doctorModel  = new DoctorModel();
    }

    public function index(): string
    {
        $filterState = $this->resolveFilters();
        $data        = $this->buildDashboardData($filterState);

        return view('layouts/main', array_merge($data, ['content_view' => 'home/index']));
    }

    /**
     * AJAX endpoint for comprehensive dynamic dashboard updates
     * GET /dashboard/chartData
     */
    public function chartData()
    {
        $filterState = $this->resolveFilters();
        $data        = $this->buildDashboardData($filterState);
        $response    = $this->response ?? \Config\Services::response();

        return $response
            ->setContentType('application/json')
            ->setJSON([
                'success'            => true,
                'periodLabel'        => $data['periodLabel'],
                'kpis'               => [
                    'totalPatients' => $data['totalPatients'],
                    'totalMC'       => $data['totalMC'],
                    'totalTS'       => $data['totalTS'],
                    'totalDepts'    => $data['totalDepts'],
                ],
                'comparisons'        => [
                    'patients' => $data['compPatients'],
                    'mc'       => $data['compMC'],
                    'ts'       => $data['compTS'],
                ],
                'trend'              => $data['trend'],
                'departments'        => $data['deptDistribution'],
                'duration'           => $data['mcDuration'],
                'docTypeRatio'       => [
                    'mc'    => $data['totalMC'],
                    'ts'    => $data['totalTS'],
                    'total' => $data['totalMC'] + $data['totalTS'],
                ],
                'recent'             => $data['recentActivity'],
                'today'              => $data['todaySummary'],
            ]);
    }

    /**
     * AJAX endpoint for Quick Patient Search by RN or Name
     * GET /dashboard/searchPatient?q=...
     */
    public function searchPatient()
    {
        $request  = $this->request ?? \Config\Services::request();
        $response = $this->response ?? \Config\Services::response();
        $query    = trim((string) ($request->getGet('q') ?? $_GET['q'] ?? ''));
        if ($query === '') {
            return $response->setJSON(['success' => true, 'data' => []]);
        }

        $results = $this->patientModel->searchByRnOrName($query, 8);

        return $response->setJSON([
            'success' => true,
            'query'   => $query,
            'data'    => $results,
        ]);
    }

    /**
     * Parse and resolve period, date range, department, and document type filters
     */
    private function resolveFilters(): array
    {
        $request    = $this->request ?? \Config\Services::request();
        $period     = trim((string) ($request->getGet('period') ?? $_GET['period'] ?? ''));
        $dateFrom   = trim((string) ($request->getGet('date_from') ?? $_GET['date_from'] ?? ''));
        $dateTo     = trim((string) ($request->getGet('date_to') ?? $_GET['date_to'] ?? ''));
        $department = trim((string) ($request->getGet('department') ?? $_GET['department'] ?? ''));
        $docType    = trim((string) ($request->getGet('doc_type') ?? $_GET['doc_type'] ?? 'all'));
        $tahun      = (int) ($request->getGet('tahun') ?? $_GET['tahun'] ?? 0);
        $bulan      = (int) ($request->getGet('bulan') ?? $_GET['bulan'] ?? 0);

        // Backward compatibility for legacy tahun / bulan query parameters
        if ($period === '' && ($tahun > 0 || $bulan > 0)) {
            if ($bulan > 0 && $tahun > 0) {
                $period   = 'month';
                $dateFrom = sprintf('%04d-%02d-01', $tahun, $bulan);
                $dateTo   = date('Y-m-t', strtotime($dateFrom));
            } elseif ($tahun > 0) {
                $period   = 'year';
                $dateFrom = sprintf('%04d-01-01', $tahun);
                $dateTo   = sprintf('%04d-12-31', $tahun);
            }
        } elseif ($period === '' && ($request->getGet('tahun') === '0' || ($_GET['tahun'] ?? '') === '0') && ($request->getGet('bulan') === '0' || ($_GET['bulan'] ?? '') === '0')) {
            $period = 'all';
        }

        // Default to "This Month" if no period is selected
        if ($period === '') {
            $period = 'month';
        }

        $chartYear = (int) date('Y');

        switch ($period) {
            case 'all':
                $dateFrom    = null;
                $dateTo      = null;
                $periodLabel = 'All Time';
                break;

            case 'year':
                if (empty($dateFrom)) {
                    $dateFrom = $chartYear . '-01-01';
                    $dateTo   = $chartYear . '-12-31';
                }
                $periodLabel = 'Year ' . date('Y', strtotime($dateFrom));
                $chartYear   = (int) date('Y', strtotime($dateFrom));
                break;

            case 'custom':
                if (empty($dateFrom)) {
                    $dateFrom = date('Y-m-01');
                }
                if (empty($dateTo)) {
                    $dateTo = date('Y-m-d');
                }
                $periodLabel = date('d M Y', strtotime($dateFrom)) . ' – ' . date('d M Y', strtotime($dateTo));
                $chartYear   = (int) date('Y', strtotime($dateTo));
                break;

            case 'month':
            default:
                $period = 'month';
                if (empty($dateFrom)) {
                    $dateFrom = date('Y-m-01');
                    $dateTo   = date('Y-m-t');
                }
                $periodLabel = date('F Y', strtotime($dateFrom));
                $chartYear   = (int) date('Y', strtotime($dateFrom));
                break;
        }

        return [
            'period'      => $period,
            'dateFrom'    => $dateFrom,
            'dateTo'      => $dateTo,
            'department'  => $department,
            'docType'     => in_array($docType, ['all', 'mc', 'timeslip']) ? $docType : 'all',
            'periodLabel' => $periodLabel,
            'chartYear'   => $chartYear,
            'tahun'       => $tahun,
            'bulan'       => $bulan,
        ];
    }

    /**
     * Compute all dashboard datasets, KPIs, charts, and summaries based on filters
     */
    private function buildDashboardData(array $f): array
    {
        $period     = $f['period'];
        $dateFrom   = $f['dateFrom'];
        $dateTo     = $f['dateTo'];
        $department = $f['department'];
        $docType    = $f['docType'];
        $chartYear  = $f['chartYear'];

        // ── 1. Top 4 KPI Metrics ───────────────────────────────────────
        $totalPatients = $this->patientModel->getFilteredCount($dateFrom, $dateTo, $department, $docType);

        $totalMC = ($docType === 'timeslip')
            ? 0
            : $this->mcModel->getFilteredCount($dateFrom, $dateTo, $department);

        $totalTS = ($docType === 'mc')
            ? 0
            : $this->tsModel->getFilteredCount($dateFrom, $dateTo, $department);

        // Departments Count
        $allDepartments = $this->doctorModel->getAllDepartments();
        if (!empty($department)) {
            $totalDepts = 1;
        } else {
            // Distinct departments with document issuance in period (or total active depts)
            $deptDist = $this->computeMergedDepartments($dateFrom, $dateTo, $docType);
            $totalDepts = count($deptDist);
            if ($totalDepts === 0 && count($allDepartments) > 0) {
                $totalDepts = count($allDepartments);
            }
        }

        // ── 2. KPI Comparison Indicators ──────────────────────────────
        $prevPatients = $this->patientModel->getPreviousPeriodCount($period, $dateFrom, $dateTo, $department, $docType);
        $prevMC       = ($docType === 'timeslip') ? null : $this->mcModel->getPreviousPeriodCount($period, $dateFrom, $dateTo, $department);
        $prevTS       = ($docType === 'mc') ? null : $this->tsModel->getPreviousPeriodCount($period, $dateFrom, $dateTo, $department);

        $compPatients = $this->formatComparison($totalPatients, $prevPatients, $period);
        $compMC       = $this->formatComparison($totalMC, $prevMC, $period);
        $compTS       = $this->formatComparison($totalTS, $prevTS, $period);

        // ── 3. Monthly Document Trend ─────────────────────────────────
        $mcMonthly = ($docType === 'timeslip')
            ? array_fill(0, 12, 0)
            : $this->mcModel->getMonthlyTrend($chartYear, $department);

        $tsMonthly = ($docType === 'mc')
            ? array_fill(0, 12, 0)
            : $this->tsModel->getMonthlyTrend($chartYear, $department);

        $trend = [
            'labels' => ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
            'mc'     => $mcMonthly,
            'ts'     => $tsMonthly,
            'year'   => $chartYear,
        ];

        // ── 4. Department Distribution (Documents by Department) ──────
        $deptDistribution = $this->computeMergedDepartments($dateFrom, $dateTo, $docType, $department);

        // ── 5. Medical Certificate Duration Overview ──────────────────
        $mcDuration = ($docType === 'timeslip')
            ? ['1 Day' => 0, '2 Days' => 0, '3 Days' => 0, '4–7 Days' => 0, 'More than 7 Days' => 0, 'total' => 0]
            : $this->mcModel->getDurationDistribution($dateFrom, $dateTo, $department);

        // ── 6. Recent Activity ────────────────────────────────────────
        $recentMC = ($docType === 'timeslip')
            ? []
            : $this->mcModel->getRecentRecords(8, $dateFrom, $dateTo, $department);

        $recentTS = ($docType === 'mc')
            ? []
            : $this->tsModel->getRecentRecords(8, $dateFrom, $dateTo, $department);

        $recentMerged = array_merge($recentMC, $recentTS);

        // Sort descending by date_issued and created_at
        usort($recentMerged, function ($a, $b) {
            $tA = strtotime($a['created_at'] ?? $a['date_issued'] ?? '1970-01-01');
            $tB = strtotime($b['created_at'] ?? $b['date_issued'] ?? '1970-01-01');
            return $tB <=> $tA;
        });
        $recentActivity = array_slice($recentMerged, 0, 7);

        // ── 7. Today's Summary ────────────────────────────────────────
        $todaySummary = [
            'patients' => $this->patientModel->getTodayCount(),
            'mc'       => $this->mcModel->getTodayCount(),
            'ts'       => $this->tsModel->getTodayCount(),
            'date'     => date('d M Y'),
        ];

        // Combine available years for selector
        $mcYears        = $this->mcModel->getAvailableYears();
        $tsYears        = $this->tsModel->getAvailableYears();
        $patientYears   = $this->patientModel->getAvailableYears();
        $availableYears = array_values(array_unique(array_merge($mcYears, $tsYears, $patientYears, [(int) date('Y')])));
        rsort($availableYears);

        return [
            'title'            => 'Dashboard',
            'period'           => $period,
            'dateFrom'         => $dateFrom,
            'dateTo'           => $dateTo,
            'selectedDept'     => $department,
            'selectedDocType'  => $docType,
            'periodLabel'      => $f['periodLabel'],
            'chartYear'        => $chartYear,
            'availableYears'   => $availableYears,
            'allDepartments'   => $allDepartments,

            // KPI Cards
            'totalPatients'    => $totalPatients,
            'totalMC'          => $totalMC,
            'totalTS'          => $totalTS,
            'totalDepts'       => $totalDepts,
            'compPatients'     => $compPatients,
            'compMC'           => $compMC,
            'compTS'           => $compTS,

            // Charts & Sections
            'trend'            => $trend,
            'deptDistribution' => $deptDistribution,
            'mcDuration'       => $mcDuration,
            'recentActivity'   => $recentActivity,
            'todaySummary'     => $todaySummary,

            // Legacy backward compatibility
            'selectedBulan'    => $f['bulan'],
            'selectedTahun'    => $f['tahun'],
            'chartTahun'       => $chartYear,
        ];
    }

    /**
     * Compute aggregated department distribution combining MC and Time Slip documents
     */
    private function computeMergedDepartments(?string $dateFrom, ?string $dateTo, string $docType = 'all', ?string $filterDept = null): array
    {
        $deptMap = [];

        if ($docType !== 'timeslip') {
            $mcDepts = $this->mcModel->getDepartmentDistribution($dateFrom, $dateTo);
            foreach ($mcDepts as $row) {
                $d = trim((string) $row['dept']);
                if ($d === '') $d = 'General Practice';
                $deptMap[$d] = ($deptMap[$d] ?? 0) + (int) $row['total'];
            }
        }

        if ($docType !== 'mc') {
            $tsDepts = $this->tsModel->getDepartmentDistribution($dateFrom, $dateTo);
            foreach ($tsDepts as $row) {
                $d = trim((string) $row['dept']);
                if ($d === '') $d = 'General Practice';
                $deptMap[$d] = ($deptMap[$d] ?? 0) + (int) $row['total'];
            }
        }

        if (!empty($filterDept)) {
            $filtered = [];
            if (isset($deptMap[$filterDept])) {
                $filtered[$filterDept] = $deptMap[$filterDept];
            } else {
                $filtered[$filterDept] = 0;
            }
            $deptMap = $filtered;
        }

        arsort($deptMap);

        $result = [];
        foreach ($deptMap as $name => $count) {
            $result[] = [
                'name'  => $name,
                'count' => $count,
            ];
        }

        return $result;
    }

    /**
     * Format comparison indicator data without misleading percentages
     */
    private function formatComparison(?int $current, ?int $prev, string $periodType): ?array
    {
        if ($prev === null || $periodType === 'all') {
            return null;
        }

        $periodName = match($periodType) {
            'month'  => 'Previous Month',
            'year'   => 'Previous Year',
            'custom' => 'Previous Period',
            default  => 'Previous Period',
        };

        if ($prev == 0) {
            if ($current > 0) {
                return [
                    'direction' => 'up',
                    'class'     => 'comp-up',
                    'symbol'    => '↑',
                    'text'      => "+{$current} vs {$periodName}",
                ];
            }
            return [
                'direction' => 'neutral',
                'class'     => 'comp-neutral',
                'symbol'    => '—',
                'text'      => "0% vs {$periodName}",
            ];
        }

        $diff = $current - $prev;
        $pct  = round(($diff / $prev) * 100, 1);

        if ($pct > 0) {
            return [
                'direction' => 'up',
                'class'     => 'comp-up',
                'symbol'    => '↑',
                'text'      => "+{$pct}% vs {$periodName}",
            ];
        } elseif ($pct < 0) {
            return [
                'direction' => 'down',
                'class'     => 'comp-down',
                'symbol'    => '↓',
                'text'      => "{$pct}% vs {$periodName}",
            ];
        }

        return [
            'direction' => 'neutral',
            'class'     => 'comp-neutral',
            'symbol'    => '—',
            'text'      => "0.0% vs {$periodName}",
        ];
    }
}
