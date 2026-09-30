<?php

namespace App\Models;

use CodeIgniter\Model;

class MedicalCertificateModel extends Model
{
    protected $table         = 'medical_certificates';
    protected $primaryKey    = 'id';
    protected $allowedFields = [
        'reference_no', 'patient_id', 'doctor_id', 'date_issued',
        'start_date', 'num_days', 'end_date',
        'has_ward', 'ward_num_days', 'ward_start_date', 'ward_end_date',
        'has_unfit',
        'has_followup', 'followup_date',
        'has_light_duty', 'light_duty_num_days', 'light_duty_start_date', 'light_duty_end_date',
        'diagnosis', 'notes', 'language'
    ];
    protected $useTimestamps = false;

    public function generateReferenceNo(): string
    {
        $prefix = 'MC-' . date('ym');
        $last   = $this->like('reference_no', $prefix, 'after')
                       ->orderBy('id', 'DESC')
                       ->first();
        $seq = $last ? ((int) substr($last['reference_no'], -3) + 1) : 1;
        return $prefix . '-' . str_pad($seq, 3, '0', STR_PAD_LEFT);
    }

    public function getWithDetails(int $id): array|null
    {
        return $this->select('medical_certificates.*, patients.full_name, patients.ic_number, patients.rn,
                              patients.date_of_birth, patients.gender, patients.address, patients.phone,
                              doctors.name as doctor_name, doctors.qualification, doctors.mcr_number, doctors.department')
                    ->join('patients', 'patients.id = medical_certificates.patient_id')
                    ->join('doctors', 'doctors.id = medical_certificates.doctor_id')
                    ->find($id);
    }

    /**
     * Dapatkan semua MC milik pesakit tertentu (untuk paparan sejarah).
     */
    public function getByPatientId(int $patientId): array
    {
        return $this->select(
                        'medical_certificates.id, medical_certificates.reference_no,
                         medical_certificates.date_issued, medical_certificates.num_days,
                         medical_certificates.has_ward, medical_certificates.has_unfit,
                         medical_certificates.has_followup, medical_certificates.has_light_duty,
                         medical_certificates.diagnosis,
                         doctors.name as doctor_name, doctors.department'
                    )
                    ->join('doctors', 'doctors.id = medical_certificates.doctor_id')
                    ->where('medical_certificates.patient_id', $patientId)
                    ->orderBy('medical_certificates.date_issued', 'DESC')
                    ->orderBy('medical_certificates.id', 'DESC')
                    ->findAll();
    }

    /**
     * Dapatkan senarai tahun yang ada rekod MC dalam database.
     */
    public function getAvailableYears(): array
    {
        $rows = $this->db->query(
            'SELECT DISTINCT YEAR(date_issued) AS yr FROM medical_certificates ORDER BY yr DESC'
        )->getResultArray();
        return array_column($rows, 'yr');
    }

    /**
     * Kira jumlah rekod MC berdasarkan filter bulan/tahun.
     * $bulan = 0 bermaksud semua bulan; $tahun = 0 bermaksud semua tahun.
     */
    public function countFiltered(int $bulan = 0, int $tahun = 0): int
    {
        $builder = $this->db->table('medical_certificates');
        if ($tahun > 0) {
            $builder->where('YEAR(date_issued)', $tahun);
        }
        if ($bulan > 0) {
            $builder->where('MONTH(date_issued)', $bulan);
        }
        return $builder->countAllResults();
    }

    /**
     * Dapatkan rekod MC terkini berdasarkan filter bulan/tahun.
     */
    public function getRecentFiltered(int $bulan = 0, int $tahun = 0, int $limit = 5): array
    {
        $this->select('medical_certificates.*, patients.full_name, patients.ic_number, patients.rn')
             ->join('patients', 'patients.id = medical_certificates.patient_id')
             ->orderBy('medical_certificates.id', 'DESC')
             ->limit($limit);
        if ($tahun > 0) {
            $this->where('YEAR(medical_certificates.date_issued)', $tahun);
        }
        if ($bulan > 0) {
            $this->where('MONTH(medical_certificates.date_issued)', $bulan);
        }
        return $this->findAll();
    }

    /**
     * Bilangan MC per bulan bagi tahun tertentu.
     * Mengembalikan array 12 elemen [Jan..Dis] (0-indexed).
     */
    public function getMonthlyCount(int $tahun): array
    {
        $rows = $this->db->query(
            'SELECT MONTH(date_issued) AS m, COUNT(*) AS total
             FROM medical_certificates WHERE YEAR(date_issued) = ?
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
     * Bilangan MC per tahun (semua tahun).
     */
    public function getYearlyCount(): array
    {
        return $this->db->query(
            'SELECT YEAR(date_issued) AS yr, COUNT(*) AS total
             FROM medical_certificates GROUP BY YEAR(date_issued) ORDER BY yr'
        )->getResultArray();
    }

    /**
     * Dapatkan kiraan MC berdasarkan julat tarikh & jabatan.
     */
    public function getFilteredCount(?string $dateFrom = null, ?string $dateTo = null, ?string $department = null): int
    {
        $builder = $this->db->table('medical_certificates');
        if (!empty($department)) {
            $builder->join('doctors', 'doctors.id = medical_certificates.doctor_id');
            $builder->where('doctors.department', $department);
        }
        if (!empty($dateFrom)) {
            $builder->where('medical_certificates.date_issued >=', $dateFrom);
        }
        if (!empty($dateTo)) {
            $builder->where('medical_certificates.date_issued <=', $dateTo);
        }
        return $builder->countAllResults();
    }

    /**
     * Kiraan MC untuk hari ini secara dinamik.
     */
    public function getTodayCount(): int
    {
        $today = date('Y-m-d');
        return $this->db->table('medical_certificates')
            ->where('date_issued', $today)
            ->countAllResults();
    }

    /**
     * Kiraan MC bagi tempoh terdahulu untuk indikator perbandingan.
     */
    public function getPreviousPeriodCount(string $periodType, ?string $dateFrom = null, ?string $dateTo = null, ?string $department = null): ?int
    {
        if ($periodType === 'all') {
            return null; // Tiada tempoh terdahulu untuk "All Time"
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
        $builder = $this->db->table('medical_certificates')
            ->select('MONTH(medical_certificates.date_issued) AS m, COUNT(medical_certificates.id) AS total');

        if (!empty($department)) {
            $builder->join('doctors', 'doctors.id = medical_certificates.doctor_id');
            $builder->where('doctors.department', $department);
        }

        if ($year && $year > 0) {
            $builder->where('YEAR(medical_certificates.date_issued)', $year);
        }

        $builder->groupBy('MONTH(medical_certificates.date_issued)');
        $rows = $builder->get()->getResultArray();

        $result = array_fill(1, 12, 0);
        foreach ($rows as $row) {
            $result[(int) $row['m']] = (int) $row['total'];
        }
        return array_values($result);
    }

    /**
     * Dapatkan taburan MC mengikut jabatan.
     */
    public function getDepartmentDistribution(?string $dateFrom = null, ?string $dateTo = null): array
    {
        $builder = $this->db->table('medical_certificates')
            ->select("COALESCE(NULLIF(doctors.department, ''), 'Unassigned') AS dept, COUNT(medical_certificates.id) AS total")
            ->join('doctors', 'doctors.id = medical_certificates.doctor_id', 'left');

        if (!empty($dateFrom)) {
            $builder->where('medical_certificates.date_issued >=', $dateFrom);
        }
        if (!empty($dateTo)) {
            $builder->where('medical_certificates.date_issued <=', $dateTo);
        }

        $builder->groupBy('dept')->orderBy('total', 'DESC');
        return $builder->get()->getResultArray();
    }

    /**
     * Taburan tempoh MC (Duration Overview: 1 Day, 2 Days, 3 Days, 4-7 Days, >7 Days).
     */
    public function getDurationDistribution(?string $dateFrom = null, ?string $dateTo = null, ?string $department = null): array
    {
        $builder = $this->db->table('medical_certificates')
            ->select("
                SUM(CASE WHEN medical_certificates.num_days = 1 THEN 1 ELSE 0 END) AS day_1,
                SUM(CASE WHEN medical_certificates.num_days = 2 THEN 1 ELSE 0 END) AS day_2,
                SUM(CASE WHEN medical_certificates.num_days = 3 THEN 1 ELSE 0 END) AS day_3,
                SUM(CASE WHEN medical_certificates.num_days >= 4 AND medical_certificates.num_days <= 7 THEN 1 ELSE 0 END) AS day_4_7,
                SUM(CASE WHEN medical_certificates.num_days > 7 THEN 1 ELSE 0 END) AS day_more_7,
                COUNT(medical_certificates.id) AS total
            ");

        if (!empty($department)) {
            $builder->join('doctors', 'doctors.id = medical_certificates.doctor_id');
            $builder->where('doctors.department', $department);
        }
        if (!empty($dateFrom)) {
            $builder->where('medical_certificates.date_issued >=', $dateFrom);
        }
        if (!empty($dateTo)) {
            $builder->where('medical_certificates.date_issued <=', $dateTo);
        }

        $row = $builder->get()->getRowArray();
        return [
            '1 Day'            => (int) ($row['day_1'] ?? 0),
            '2 Days'           => (int) ($row['day_2'] ?? 0),
            '3 Days'           => (int) ($row['day_3'] ?? 0),
            '4–7 Days'         => (int) ($row['day_4_7'] ?? 0),
            'More than 7 Days' => (int) ($row['day_more_7'] ?? 0),
            'total'            => (int) ($row['total'] ?? 0),
        ];
    }

    /**
     * Dapatkan rekod MC terkini untuk panel Recent Activity.
     */
    public function getRecentRecords(int $limit = 6, ?string $dateFrom = null, ?string $dateTo = null, ?string $department = null): array
    {
        $builder = $this->db->table('medical_certificates')
            ->select('medical_certificates.id, medical_certificates.reference_no, medical_certificates.date_issued,
                      medical_certificates.created_at, medical_certificates.num_days,
                      medical_certificates.diagnosis, "mc" AS doc_type,
                      patients.full_name AS patient_name, patients.rn, patients.ic_number,
                      doctors.name AS doctor_name, doctors.department')
            ->join('patients', 'patients.id = medical_certificates.patient_id', 'left')
            ->join('doctors', 'doctors.id = medical_certificates.doctor_id', 'left');

        if (!empty($department)) {
            $builder->where('doctors.department', $department);
        }
        if (!empty($dateFrom)) {
            $builder->where('medical_certificates.date_issued >=', $dateFrom);
        }
        if (!empty($dateTo)) {
            $builder->where('medical_certificates.date_issued <=', $dateTo);
        }

        $builder->orderBy('medical_certificates.date_issued', 'DESC')
                ->orderBy('medical_certificates.id', 'DESC')
                ->limit($limit);

        return $builder->get()->getResultArray();
    }
}
