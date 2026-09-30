<?php

namespace App\Controllers;

use App\Models\DoctorModel;
use App\Models\MedicalCertificateModel;
use App\Models\TimeSlipModel;
use App\Models\PatientModel;

class AdminController extends BaseController
{
    protected DoctorModel             $doctorModel;
    protected MedicalCertificateModel $mcModel;
    protected TimeSlipModel           $tsModel;
    protected PatientModel            $patientModel;

    public function __construct()
    {
        $this->doctorModel  = new DoctorModel();
        $this->mcModel      = new MedicalCertificateModel();
        $this->tsModel      = new TimeSlipModel();
        $this->patientModel = new PatientModel();
    }

    /**
     * Dashboard / Index redirect for IT Admin
     */
    public function index()
    {
        return redirect()->to('/admin/doctors');
    }

    /**
     * Medical Practitioners (Doctors) Management
     */
    public function doctors(): string
    {
        $doctors = $this->doctorModel->orderBy('id', 'DESC')->findAll();

        $data = [
            'title'   => 'Medical Practitioners Management',
            'doctors' => $doctors,
        ];

        return view('layouts/main', array_merge($data, ['content_view' => 'admin/doctors']));
    }

    /**
     * Save / Update Doctor Record
     */
    public function saveDoctor()
    {
        $id = $this->request->getPost('id');

        $rules = [
            'name'          => 'required|min_length[3]|max_length[150]',
            'mcr_number'    => 'permit_empty|max_length[50]',
            'qualification' => 'permit_empty|max_length[200]',
            'department'    => 'permit_empty|max_length[150]',
        ];

        if (! $this->validate($rules)) {
            return redirect()->back()->withInput()->with('error', 'Please ensure all required information is filled in correctly.');
        }

        $payload = [
            'name'          => trim($this->request->getPost('name')),
            'qualification' => trim($this->request->getPost('qualification')),
            'mcr_number'    => trim($this->request->getPost('mcr_number')),
            'department'    => trim($this->request->getPost('department')),
            'is_active'     => $this->request->getPost('is_active') ? 1 : 0,
        ];

        if ($id) {
            $this->doctorModel->update($id, $payload);
            $msg = 'Doctor information updated successfully.';
        } else {
            $this->doctorModel->insert($payload);
            $msg = 'New doctor registered successfully.';
        }

        return redirect()->to('/admin/doctors')->with('success', $msg);
    }

    /**
     * Toggle Doctor Active / Inactive Status
     */
    public function toggleDoctor(int $id)
    {
        $doctor = $this->doctorModel->find($id);
        if ($doctor) {
            $newStatus = $doctor['is_active'] ? 0 : 1;
            $this->doctorModel->update($id, ['is_active' => $newStatus]);
            $statusText = $newStatus ? 'activated' : 'deactivated';
            return redirect()->to('/admin/doctors')->with('success', "Doctor {$doctor['name']} has been {$statusText}.");
        }

        return redirect()->to('/admin/doctors')->with('error', 'Doctor record not found.');
    }

    /**
     * iPesakit API Connectivity Diagnostics
     */
    public function apiStatus(): string
    {
        $apiUrl = env('PATIENT_API_URL');
        $token  = env('PATIENT_API_TOKEN');

        $pingStatus = null;
        $httpCode   = null;
        $responseBody = null;
        $errorMsg   = null;

        if ($this->request->getGet('test_connection')) {
            try {
                $client = \Config\Services::curlrequest();
                $testRn = $this->request->getGet('test_rn') ?: '1000002';
                $start = microtime(true);

                $response = $client->get($apiUrl, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                        'Accept'        => 'application/json',
                    ],
                    'query' => [
                        'rn' => $testRn,
                    ],
                    'timeout'     => 5,
                    'http_errors' => false,
                    'verify'      => false,
                ]);

                $timeTaken = round((microtime(true) - $start) * 1000, 2);
                $httpCode  = $response->getStatusCode();
                $responseBody = $response->getBody();
                $pingStatus = [
                    'code'       => $httpCode,
                    'time_ms'    => $timeTaken,
                    'is_success' => ($httpCode === 200),
                ];
            } catch (\Throwable $e) {
                $errorMsg = $e->getMessage();
                $pingStatus = [
                    'code'       => 0,
                    'time_ms'    => 0,
                    'is_success' => false,
                ];
            }
        }

        $data = [
            'title'        => 'iPesakit API Connectivity Diagnostics',
            'apiUrl'       => $apiUrl,
            'tokenMasked'  => $token ? (substr($token, 0, 4) . '••••••••' . substr($token, -3)) : 'Not Configured',
            'pingStatus'   => $pingStatus,
            'responseBody' => $responseBody,
            'errorMsg'     => $errorMsg,
        ];

        return view('layouts/main', array_merge($data, ['content_view' => 'admin/api_status']));
    }

    /**
     * Document Issuance Audit Log (MC & Time Slip)
     */
    public function auditLog(): string
    {
        $type   = $this->request->getGet('type') ?: 'all';
        $search = $this->request->getGet('q') ?: '';

        $mcList = [];
        $tsList = [];

        if ($type === 'all' || $type === 'mc') {
            $mcQuery = $this->mcModel
                ->select('medical_certificates.id, medical_certificates.reference_no, medical_certificates.date_issued, medical_certificates.num_days,
                          patients.full_name as patient_name, patients.ic_number, patients.rn,
                          doctors.name as doctor_name, \'MC\' as doc_type')
                ->join('patients', 'patients.id = medical_certificates.patient_id')
                ->join('doctors', 'doctors.id = medical_certificates.doctor_id');

            if ($search) {
                $mcQuery->groupStart()
                    ->like('medical_certificates.reference_no', $search)
                    ->orLike('patients.full_name', $search)
                    ->orLike('patients.ic_number', $search)
                    ->orLike('patients.rn', $search)
                    ->orLike('doctors.name', $search)
                ->groupEnd();
            }

            $mcList = $mcQuery->orderBy('medical_certificates.id', 'DESC')->limit(100)->findAll();
        }

        if ($type === 'all' || $type === 'ts') {
            $tsQuery = $this->tsModel
                ->select('time_slips.id, time_slips.reference_no, time_slips.date_issued, time_slips.time_in, time_slips.time_out,
                          patients.full_name as patient_name, patients.ic_number, patients.rn,
                          doctors.name as doctor_name, \'Time Slip\' as doc_type')
                ->join('patients', 'patients.id = time_slips.patient_id')
                ->join('doctors', 'doctors.id = time_slips.doctor_id');

            if ($search) {
                $tsQuery->groupStart()
                    ->like('time_slips.reference_no', $search)
                    ->orLike('patients.full_name', $search)
                    ->orLike('patients.ic_number', $search)
                    ->orLike('patients.rn', $search)
                    ->orLike('doctors.name', $search)
                ->groupEnd();
            }

            $tsList = $tsQuery->orderBy('time_slips.id', 'DESC')->limit(100)->findAll();
        }

        $allLogs = array_merge($mcList, $tsList);
        usort($allLogs, fn($a, $b) => strcmp($b['date_issued'] ?? '', $a['date_issued'] ?? ''));

        $data = [
            'title'   => 'Document Issuance Audit Log',
            'logs'    => $allLogs,
            'type'    => $type,
            'search'  => $search,
        ];

        return view('layouts/main', array_merge($data, ['content_view' => 'admin/audit_log']));
    }
}
