<?php

namespace App\Models;

use CodeIgniter\Model;

class PatientModel extends Model
{
    protected $table         = 'patients';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['ic_number', 'rn', 'full_name', 'date_of_birth', 'gender', 'phone', 'address'];
    protected $useTimestamps = true;
    protected $createdField  = 'created_at';
    protected $updatedField  = 'updated_at';

    public function findByIC(string $ic): array|null
    {
        return $this->where('ic_number', $ic)->first();
    }

    public function findByRn(string $rn): array|null
    {
        return $this->where('rn', trim($rn))->first();
    }

    public function getPatientFromApi($patientId, string $queryParam = 'idpesakit')
    {
        $apiUrl = env('PATIENT_API_URL');
        $token  = env('PATIENT_API_TOKEN');

        try {
            $client = \Config\Services::curlrequest();

            $response = $client->get($apiUrl, [
                'headers' => [
                    'Authorization' => 'Bearer ' . $token,
                    'Accept'        => 'application/json',
                ],
                'query' => [
                    $queryParam => $patientId,
                ],
                'timeout'     => 15,
                'http_errors' => false,
                'verify'      => false,
            ]);

            $statusCode = $response->getStatusCode();
            $body       = $response->getBody();

            // Log untuk debugging
            log_message('debug', 'Patient API Status: ' . $statusCode);
            log_message('debug', 'Patient API Response: ' . $body);

            if ($statusCode !== 200) {
                return [
                    'success' => false,
                    'message' => 'API returned HTTP status ' . $statusCode,
                    'data'    => null,
                ];
            }

            $result = json_decode($body, true);

            if (!is_array($result)) {
                return [
                    'success' => false,
                    'message' => 'Invalid JSON response from API.',
                    'data'    => null,
                ];
            }

            if (($result['status'] ?? '') !== 'success') {
                return [
                    'success' => false,
                    'message' => $result['message'] ?? 'Patient not found.',
                    'data'    => null,
                ];
            }

            return [
                'success' => true,
                'message' => $result['message'] ?? 'Record found',
                'data'    => $result['data'] ?? null,
            ];

        } catch (\Throwable $e) {

            log_message(
                'error',
                'Patient API Error: ' . $e->getMessage()
            );

            return [
                'success' => false,
                'message' => $e->getMessage(),
                'data'    => null,
            ];
        }
    }

    /**
     * Kira pesakit berdasarkan filter bulan/tahun (berdasarkan created_at).
     * Jika bulan=0 dan tahun=0, kembalikan jumlah keseluruhan.
     */
    public function countFiltered(int $bulan = 0, int $tahun = 0): int
    {
        $builder = $this->db->table('patients');
        if ($tahun > 0) {
            $builder->where('YEAR(created_at)', $tahun);
        }
        if ($bulan > 0) {
            $builder->where('MONTH(created_at)', $bulan);
        }
        return $builder->countAllResults();
    }

    /**
     * Dapatkan senarai tahun yang ada rekod pesakit.
     */
    public function getAvailableYears(): array
    {
        $rows = $this->db->query(
            'SELECT DISTINCT YEAR(created_at) AS yr FROM patients ORDER BY yr DESC'
        )->getResultArray();
        return array_column($rows, 'yr');
    }

    /**
     * Bilangan pesakit baharu per bulan bagi tahun tertentu.
     * Mengembalikan array 12 elemen [Jan..Dis] (0-indexed).
     */
    public function getMonthlyCount(int $tahun): array
    {
        $rows = $this->db->query(
            'SELECT MONTH(created_at) AS m, COUNT(*) AS total
             FROM patients WHERE YEAR(created_at) = ?
             GROUP BY MONTH(created_at)',
            [$tahun]
        )->getResultArray();

        $result = array_fill(1, 12, 0);
        foreach ($rows as $row) {
            $result[(int) $row['m']] = (int) $row['total'];
        }
        return array_values($result); // index 0 = Jan, 11 = Dis
    }

    /**
     * Bilangan pesakit baharu per tahun (semua tahun).
     */
    public function getYearlyCount(): array
    {
        return $this->db->query(
            'SELECT YEAR(created_at) AS yr, COUNT(*) AS total
             FROM patients GROUP BY YEAR(created_at) ORDER BY yr'
        )->getResultArray();
    }

    public function getDummyPatients(): array
    {
        return $this->whereIn('ic_number', [
            '850312-10-5671',
            '901225-14-5432',
            '780609-07-5891',
        ])->findAll();
    }

    /**
     * Dapatkan kiraan pesakit aktif/terlibat berdasarkan julat tarikh, jabatan & jenis dokumen.
     */
    public function getFilteredCount(?string $dateFrom = null, ?string $dateTo = null, ?string $department = null, ?string $docType = null): int
    {
        // Jika All Time tanpa jabatan dan tanpa jenis dokumen, pulangkan jumlah pesakit berdaftar
        if (empty($dateFrom) && empty($dateTo) && empty($department) && (empty($docType) || $docType === 'all')) {
            return $this->countAllResults();
        }

        $queries = [];
        $params  = [];

        if (empty($docType) || $docType === 'all' || $docType === 'mc') {
            $mcSql = "SELECT mc.patient_id FROM medical_certificates mc";
            if (!empty($department)) {
                $mcSql .= " JOIN doctors d ON d.id = mc.doctor_id WHERE d.department = ?";
                $params[] = $department;
            } else {
                $mcSql .= " WHERE 1=1";
            }
            if (!empty($dateFrom)) {
                $mcSql .= " AND mc.date_issued >= ?";
                $params[] = $dateFrom;
            }
            if (!empty($dateTo)) {
                $mcSql .= " AND mc.date_issued <= ?";
                $params[] = $dateTo;
            }
            $queries[] = $mcSql;
        }

        if (empty($docType) || $docType === 'all' || $docType === 'timeslip') {
            $tsSql = "SELECT ts.patient_id FROM time_slips ts";
            if (!empty($department)) {
                $tsSql .= " JOIN doctors d ON d.id = ts.doctor_id WHERE d.department = ?";
                $params[] = $department;
            } else {
                $tsSql .= " WHERE 1=1";
            }
            if (!empty($dateFrom)) {
                $tsSql .= " AND ts.date_issued >= ?";
                $params[] = $dateFrom;
            }
            if (!empty($dateTo)) {
                $tsSql .= " AND ts.date_issued <= ?";
                $params[] = $dateTo;
            }
            $queries[] = $tsSql;
        }

        if (empty($queries)) {
            return 0;
        }

        $unionSql = implode(" UNION ", $queries);
        $finalSql = "SELECT COUNT(DISTINCT patient_id) AS total FROM (" . $unionSql . ") AS active_patients";
        $row      = $this->db->query($finalSql, $params)->getRowArray();
        return (int) ($row['total'] ?? 0);
    }

    /**
     * Kiraan pesakit bagi tempoh terdahulu untuk indikator perbandingan.
     */
    public function getPreviousPeriodCount(string $periodType, ?string $dateFrom = null, ?string $dateTo = null, ?string $department = null, ?string $docType = null): ?int
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

        return $this->getFilteredCount($prevFrom, $prevTo, $department, $docType);
    }

    /**
     * Kiraan pesakit unik yang menerima dokumen hari ini.
     */
    public function getTodayCount(): int
    {
        $today = date('Y-m-d');
        $sql   = "
            SELECT COUNT(DISTINCT patient_id) AS total FROM (
                SELECT patient_id FROM medical_certificates WHERE date_issued = ?
                UNION
                SELECT patient_id FROM time_slips WHERE date_issued = ?
            ) AS t_patients
        ";
        $row = $this->db->query($sql, [$today, $today])->getRowArray();
        return (int) ($row['total'] ?? 0);
    }

    /**
     * Carian pantas pesakit menggunakan RN atau Nama Pesakit.
     * Mengutamakan padanan RN, kemudian Nama Pesakit dan IC.
     */
    public function searchByRnOrName(string $query, int $limit = 8): array
    {
        $q = trim($query);
        if ($q === '') {
            return [];
        }

        $builder = $this->db->table('patients')
            ->select('patients.id, patients.rn, patients.full_name, patients.ic_number, patients.gender, patients.date_of_birth, patients.phone')
            ->groupStart()
                ->like('patients.rn', $q)
                ->orLike('patients.full_name', $q)
                ->orLike('patients.ic_number', $q)
            ->groupEnd()
            ->orderBy("CASE WHEN patients.rn = " . $this->db->escape($q) . " THEN 0 WHEN patients.rn LIKE " . $this->db->escape($q . '%') . " THEN 1 ELSE 2 END")
            ->orderBy('patients.full_name', 'ASC')
            ->limit($limit);

        $patients = $builder->get()->getResultArray();

        // Tambah ringkasan dokumen untuk setiap pesakit
        foreach ($patients as &$p) {
            $pid = (int) $p['id'];
            $mcCount = $this->db->table('medical_certificates')->where('patient_id', $pid)->countAllResults();
            $tsCount = $this->db->table('time_slips')->where('patient_id', $pid)->countAllResults();

            $p['mc_count']    = $mcCount;
            $p['ts_count']    = $tsCount;
            $p['total_docs']  = $mcCount + $tsCount;
        }
        unset($p);

        return $patients;
    }
}
