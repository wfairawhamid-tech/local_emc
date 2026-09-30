<?php

namespace App\Models;

use CodeIgniter\Model;

class TimeSlipModel extends Model
{
    protected $table         = 'time_slips';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'reference_no', 'patient_id', 'doctor_id', 'date_issued',
        'time_in', 'time_out', 'purpose', 'notes', 'language',
        'visit_type', 'companion_name', 'companion_ic', 'companion_relation'
    ];
    protected $useTimestamps = false;

    public function generateReferenceNo(): string
    {
        $prefix = 'TS-' . date('ym');
        $last   = $this->like('reference_no', $prefix, 'after')
                       ->orderBy('id', 'DESC')
                       ->first();
        $seq = $last ? ((int) substr($last['reference_no'], -3) + 1) : 1;
        return $prefix . '-' . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    public function getWithDetails(int $id): array|null
    {
        return $this->select('time_slips.*, patients.full_name, patients.ic_number, patients.rn,
                              patients.date_of_birth, patients.gender, patients.address, patients.phone,
                              doctors.name as doctor_name, doctors.qualification, doctors.mcr_number, doctors.department')
                    ->join('patients', 'patients.id = time_slips.patient_id')
                    ->join('doctors', 'doctors.id = time_slips.doctor_id')
                    ->find($id);
    }

    /**
     * Dapatkan semua Time Slip milik pesakit tertentu (untuk paparan sejarah).
     */
    public function getByPatientId(int $patientId): array
    {
        return $this->select(
                        'time_slips.id, time_slips.reference_no,
                         time_slips.date_issued, time_slips.time_in, time_slips.time_out,
                         time_slips.purpose, time_slips.visit_type,
                         doctors.name as doctor_name, doctors.department'
                    )
                    ->join('doctors', 'doctors.id = time_slips.doctor_id')
                    ->where('time_slips.patient_id', $patientId)
                    ->orderBy('time_slips.date_issued', 'DESC')
                    ->orderBy('time_slips.id', 'DESC')
                    ->findAll();
    }

    /**
     * Dapatkan senarai tahun yang ada rekod Slip Masa dalam database.
     */
    public function getAvailableYears(): array
    {
        $rows = $this->db->query(
            'SELECT DISTINCT YEAR(date_issued) AS yr FROM time_slips ORDER BY yr DESC'
        )->getResultArray();
        return array_column($rows, 'yr');
    }

    /**
     * Kira jumlah rekod Slip Masa berdasarkan filter bulan/tahun.
     * $bulan = 0 bermaksud semua bulan; $tahun = 0 bermaksud semua tahun.
     */
    public function countFiltered(int $bulan = 0, int $tahun = 0): int
    {
        $builder = $this->db->table('time_slips');
        if ($tahun > 0) {
            $builder->where('YEAR(date_issued)', $tahun);
        }
        if ($bulan > 0) {
            $builder->where('MONTH(date_issued)', $bulan);
        }
        return $builder->countAllResults();
    }

    /**
     * Dapatkan rekod Slip Masa terkini berdasarkan filter bulan/tahun.
     */
    public function getRecentFiltered(int $bulan = 0, int $tahun = 0, int $limit = 5): array
    {
        $this->select('time_slips.*, patients.full_name, patients.ic_number, patients.rn')
             ->join('patients', 'patients.id = time_slips.patient_id')
             ->orderBy('time_slips.id', 'DESC')
             ->limit($limit);
        if ($tahun > 0) {
            $this->where('YEAR(time_slips.date_issued)', $tahun);
        }
        if ($bulan > 0) {
            $this->where('MONTH(time_slips.date_issued)', $bulan);
        }
        return $this->findAll();
    }

    /**
     * Bilangan TS per bulan bagi tahun tertentu.
     * Mengembalikan array 12 elemen [Jan..Dis] (0-indexed).
     */
    public function getMonthlyCount(int $tahun): array
    {
        $rows = $this->db->query(
            'SELECT MONTH(date_issued) AS m, COUNT(*) AS total
             FROM time_slips WHERE YEAR(date_issued) = ?
             GROUP BY MONTH(date_issued)',
            [$tahun]
        )->getResultArray();

        $result = array_fill(1, 12, 0);
        foreach ($rows as $row) {
            $result[(int) $row['m']] = (int) $row['total'];
        }
        return array_values($result);
    }

    /**
     * Bilangan TS per tahun (semua tahun).
     */
    public function getYearlyCount(): array
    {
        return $this->db->query(
            'SELECT YEAR(date_issued) AS yr, COUNT(*) AS total
             FROM time_slips GROUP BY YEAR(date_issued) ORDER BY yr'
        )->getResultArray();
    }

    /**
     * Dapatkan kiraan Time Slip berdasarkan julat tarikh & jabatan.
     */
    public function getFilteredCount(?string $dateFrom = null, ?string $dateTo = null, ?string $department = null): int
    {
        $builder = $this->db->table('time_slips');
        if (!empty($department)) {
            $builder->join('doctors', 'doctors.id = time_slips.doctor_id');
            $builder->where('doctors.department', $department);
        }
        if (!empty($dateFrom)) {
            $builder->where('time_slips.date_issued >=', $dateFrom);
        }
        if (!empty($dateTo)) {
            $builder->where('time_slips.date_issued <=', $dateTo);
        }
        return $builder->countAllResults();
    }

    /**
     * Kiraan Time Slip untuk hari ini secara dinamik.
     */
    public function getTodayCount(): int
    {
        $today = date('Y-m-d');
        return $this->db->table('time_slips')
            ->where('date_issued', $today)
            ->countAllResults();
    }

    /**
     * Kiraan Time Slip bagi tempoh terdahulu untuk indikator perbandingan.
     */
    public function getPreviousPeriodCount(string $periodType, ?string $dateFrom = null, ?string $dateTo = null, ?string $department = null): ?int
    {
        if ($periodType === 'all') {
            return null;
        }

        $prevFrom = null;
        $prevTo   = null;

        if ($periodType === 'month') {
            $refDate  = !empty($dateFrom) ? $dateFrom : date('Y-m-d');
            $time     = strtotime($refDate);
            $prevTime = strtotime('-1 month', strtotime(date('Y-m-01', $time)));
            $prevFrom = date('Y-m-01', $prevTime);
            $prevTo   = date('Y-m-t', $prevTime);
        } elseif ($periodType === 'year') {
            $refYear  = !empty($dateFrom) ? (int) date('Y', strtotime($dateFrom)) : (int) date('Y');
            $prevYear = $refYear - 1;
            $prevFrom = $prevYear . '-01-01';
            $prevTo   = $prevYear . '-12-31';
        } elseif ($periodType === 'custom' && !empty($dateFrom) && !empty($dateTo)) {
            $dFrom = new \DateTime($dateFrom);
            $dTo   = new \DateTime($dateTo);
            $diff  = $dFrom->diff($dTo)->days + 1;
            $prevToEnd = clone $dFrom;
            $prevToEnd->modify('-1 day');
            $prevToStart = clone $prevToEnd;
            $prevToStart->modify('-' . ($diff - 1) . ' days');
            $prevFrom = $prevToStart->format('Y-m-d');
            $prevTo   = $prevToEnd->format('Y-m-d');
        } else {
            return null;
        }

        return $this->getFilteredCount($prevFrom, $prevTo, $department);
    }

    /**
     * Dapatkan trend bulanan bagi tahun tertentu atau agregat.
     */
    public function getMonthlyTrend(?int $year = null, ?string $department = null): array
    {
        $builder = $this->db->table('time_slips')
            ->select('MONTH(time_slips.date_issued) AS m, COUNT(time_slips.id) AS total');

        if (!empty($department)) {
            $builder->join('doctors', 'doctors.id = time_slips.doctor_id');
            $builder->where('doctors.department', $department);
        }

        if ($year && $year > 0) {
            $builder->where('YEAR(time_slips.date_issued)', $year);
        }

        $builder->groupBy('MONTH(time_slips.date_issued)');
        $rows = $builder->get()->getResultArray();

        $result = array_fill(1, 12, 0);
        foreach ($rows as $row) {
            $result[(int) $row['m']] = (int) $row['total'];
        }
        return array_values($result);
    }

    /**
     * Dapatkan taburan Time Slip mengikut jabatan.
     */
    public function getDepartmentDistribution(?string $dateFrom = null, ?string $dateTo = null): array
    {
        $builder = $this->db->table('time_slips')
            ->select("COALESCE(NULLIF(doctors.department, ''), 'Unassigned') AS dept, COUNT(time_slips.id) AS total")
            ->join('doctors', 'doctors.id = time_slips.doctor_id', 'left');

        if (!empty($dateFrom)) {
            $builder->where('time_slips.date_issued >=', $dateFrom);
        }
        if (!empty($dateTo)) {
            $builder->where('time_slips.date_issued <=', $dateTo);
        }

        $builder->groupBy('dept')->orderBy('total', 'DESC');
        return $builder->get()->getResultArray();
    }

    /**
     * Dapatkan rekod Time Slip terkini untuk panel Recent Activity.
     */
    public function getRecentRecords(int $limit = 6, ?string $dateFrom = null, ?string $dateTo = null, ?string $department = null): array
    {
        $builder = $this->db->table('time_slips')
            ->select('time_slips.id, time_slips.reference_no, time_slips.date_issued,
                      time_slips.created_at, time_slips.time_in, time_slips.time_out,
                      time_slips.purpose, time_slips.visit_type, "timeslip" AS doc_type,
                      patients.full_name AS patient_name, patients.rn, patients.ic_number,
                      doctors.name AS doctor_name, doctors.department')
            ->join('patients', 'patients.id = time_slips.patient_id', 'left')
            ->join('doctors', 'doctors.id = time_slips.doctor_id', 'left');

        if (!empty($department)) {
            $builder->where('doctors.department', $department);
        }
        if (!empty($dateFrom)) {
            $builder->where('time_slips.date_issued >=', $dateFrom);
        }
        if (!empty($dateTo)) {
            $builder->where('time_slips.date_issued <=', $dateTo);
        }

        $builder->orderBy('time_slips.date_issued', 'DESC')
                ->orderBy('time_slips.id', 'DESC')
                ->limit($limit);

        return $builder->get()->getResultArray();
    }
}
