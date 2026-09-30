<?php

namespace App\Controllers;

use App\Models\MedicalCertificateModel;
use App\Models\PatientModel;
use App\Models\DoctorModel;

class McController extends BaseController
{
    protected MedicalCertificateModel $mcModel;
    protected PatientModel            $patientModel;
    protected DoctorModel             $doctorModel;

    public function __construct()
    {
        $this->mcModel      = new MedicalCertificateModel();
        $this->patientModel = new PatientModel();
        $this->doctorModel  = new DoctorModel();
    }

    /**
     * List all Medical Certificates with Search (RN + Patient Name) and Pagination (30/page)
     */
    public function index(): string
    {
        $rn   = trim((string) ($this->request->getGet('rn') ?? ''));
        $name = trim((string) ($this->request->getGet('name') ?? ''));
        $perPage = 30;

        $builder = $this->mcModel
            ->select('medical_certificates.*, patients.full_name, patients.ic_number, patients.rn, doctors.name as doctor_name, doctors.department')
            ->join('patients', 'patients.id = medical_certificates.patient_id')
            ->join('doctors', 'doctors.id = medical_certificates.doctor_id', 'left')
            ->orderBy('medical_certificates.id', 'DESC');

        if ($rn !== '') {
            $builder->like('patients.rn', $rn);
        }

        if ($name !== '') {
            $builder->like('patients.full_name', $name);
        }

        $records = $builder->paginate($perPage);
        $pager   = $this->mcModel->pager;

        $pagerDetails = $pager ? $pager->getDetails() : [];
        $totalRecords = (int) ($pagerDetails['total'] ?? count($records));
        $currentPage  = $pager ? (int) $pager->getCurrentPage() : 1;
        $from         = $totalRecords > 0 ? (($currentPage - 1) * $perPage) + 1 : 0;
        $to           = min($currentPage * $perPage, $totalRecords);

        $data = [
            'title'        => 'Medical Certificate Records',
            'records'      => $records,
            'pager'        => $pager,
            'rn'           => $rn,
            'name'         => $name,
            'totalRecords' => $totalRecords,
            'from'         => $from,
            'to'           => $to,
            'currentPage'  => $currentPage,
            'perPage'      => $perPage,
        ];

        return view('layouts/main', array_merge($data, ['content_view' => 'mc/index']));
    }

    /**
     * Form to create a new MC for a patient
     */
    public function create($patientId)
    {
        // $patient = $this->patientModel->find($patientId);
        $patientAPI = $this->patientModel->getPatientFromApi($patientId);
// print_r($patientAPI);
// die();
        if (!$patientAPI['success']) {
            $msg = 'Failed to retrieve patient information from API (Server Down / ' . ($patientAPI['message'] ?? 'Error') . ').';
            log_message('error', 'MC Create - Patient API failed: ' . $msg);
            return view('errors/ipesakit_only');
        }
        $patient = $patientAPI['data'];
        $data = [
            'title'           => 'Issue Medical Certificate',
            'patient'         => $patient,
            'doctors'         => $this->doctorModel->getActiveDoctors(),
            'currentDoctorId' => (int) (session()->get('doctor_id') ?? 0),
            'errors'          => session()->getFlashdata('errors'),
        ];

        return view('layouts/main', array_merge($data, ['content_view' => 'mc/create']));
    }

    /**
     * Process save MC
     */
    public function store()
    {
        // $patientId = (int) $this->request->getPost('patient_id');
        $doctorId  = (int) ($this->request->getPost('doctor_id') ?: (session()->get('doctor_id') ?? 0));

        // Klausa a) Dimasukkan ke wad
        $hasWard       = $this->request->getPost('has_ward') ? 1 : 0;
        $wardNumDays   = $hasWard ? (int)$this->request->getPost('ward_num_days') : null;
        $wardStartDate = $hasWard ? ($this->request->getPost('ward_start_date') ?: null) : null;
        $wardEndDate   = ($hasWard && $wardStartDate && $wardNumDays)
            ? date('Y-m-d', strtotime($wardStartDate . ' +' . ($wardNumDays - 1) . ' days'))
            : null;

        // Klausa b) Tidak sihat untuk menjalankan tugas atau persekolahan
        $hasUnfit  = $this->request->getPost('has_unfit') ? 1 : 0;
        $numDays   = $hasUnfit ? (int)$this->request->getPost('num_days') : null;
        $startDate = $hasUnfit ? ($this->request->getPost('start_date') ?: null) : null;
        $endDate   = ($hasUnfit && $startDate && $numDays)
            ? date('Y-m-d', strtotime($startDate . ' +' . ($numDays - 1) . ' days'))
            : null;

        // Klausa c) Rawatan susulan
        $hasFollowup  = $this->request->getPost('has_followup') ? 1 : 0;
        $followupDate = $hasFollowup ? ($this->request->getPost('followup_date') ?: null) : null;

        // Klausa d) Tugas-tugas ringan
        $hasLightDuty       = $this->request->getPost('has_light_duty') ? 1 : 0;
        $lightDutyNumDays   = $hasLightDuty ? (int)$this->request->getPost('light_duty_num_days') : null;
        $lightDutyStartDate = $hasLightDuty ? ($this->request->getPost('light_duty_start_date') ?: null) : null;
        $lightDutyEndDate   = ($hasLightDuty && $lightDutyStartDate && $lightDutyNumDays)
            ? date('Y-m-d', strtotime($lightDutyStartDate . ' +' . ($lightDutyNumDays - 1) . ' days'))
            : null;

        $refNo = $this->mcModel->generateReferenceNo();

        // Map API field names → DB column names for the patients table
        $datapatient = [
            'ic_number'     => $this->request->getPost('patient_mykad_mykid'),
            'rn'            => $this->request->getPost('patient_rn') ?: null,
            'full_name'     => $this->request->getPost('patient_name'),
            'date_of_birth' => $this->request->getPost('patient_dob'),
            'gender'        => $this->request->getPost('patient_gender') ?: null,
        ];

        // Check if patient with same IC already exists; reuse if found
        $existingPatient = $this->patientModel->findByIC($datapatient['ic_number']);
        if ($existingPatient) {
            $patientId = $existingPatient['id'];
        } else {
            $patientId = $this->patientModel->insert($datapatient);
        }

        $data = [
            'reference_no'          => $refNo,
            'patient_id'            => $patientId,
            'doctor_id'             => $doctorId,
            'date_issued'           => $this->request->getPost('date_issued') ?: date('Y-m-d'),
            'start_date'            => $startDate,
            'num_days'              => $numDays,
            'end_date'              => $endDate,
            'has_ward'              => $hasWard,
            'ward_num_days'         => $wardNumDays,
            'ward_start_date'       => $wardStartDate,
            'ward_end_date'         => $wardEndDate,
            'has_unfit'             => $hasUnfit,
            'has_followup'          => $hasFollowup,
            'followup_date'         => $followupDate,
            'has_light_duty'        => $hasLightDuty,
            'light_duty_num_days'   => $lightDutyNumDays,
            'light_duty_start_date' => $lightDutyStartDate,
            'light_duty_end_date'   => $lightDutyEndDate,
            'diagnosis'             => $this->request->getPost('diagnosis'),
            'notes'                 => $this->request->getPost('notes'),
            'language'              => $this->request->getPost('language') ?: 'EN',
        ];

        $id = $this->mcModel->insert($data);
        if (! $id) {
            session()->setFlashdata('errors', $this->mcModel->errors());
            return redirect()->to('/mc/create/' . $patientId);
        }

        return redirect()->to('/mc/view/' . $id);
    }

    /**
     * Display MC for printing
     */
    public function view(int $id)
    {
        $mc = $this->mcModel->getWithDetails($id);
        if (! $mc) {
            return redirect()->to('/mc')->with('error', 'Medical Certificate record not found.');
        }

        $rawLang = $this->request->getGet('lang') ?? ($_GET['lang'] ?? 'BI');
        $lang = (strtoupper($rawLang) === 'BM') ? 'BM' : 'BI';

        $data = [
            'title' => 'Medical Certificate - ' . $mc['reference_no'],
            'mc'    => $mc,
            'lang'  => $lang,
        ];

        return view('mc/view', $data);
    }

    /**
     * Edit form for an existing MC record
     */
    public function edit(int $id)
    {
        $mc = $this->mcModel->getWithDetails($id);
        if (! $mc) {
            return redirect()->to('/mc')->with('error', 'Medical Certificate record not found.');
        }

        $data = [
            'title'           => 'Edit Medical Certificate',
            'mc'              => $mc,
            'doctors'         => $this->doctorModel->getActiveDoctors(),
            'currentDoctorId' => (int) (session()->get('doctor_id') ?? 0),
            'errors'          => session()->getFlashdata('errors'),
        ];

        return view('layouts/main', array_merge($data, ['content_view' => 'mc/edit']));
    }

    /**
     * Save changes to an existing MC record
     */
    public function update(int $id)
    {
        $mc = $this->mcModel->find($id);
        if (! $mc) {
            return redirect()->to('/mc')->with('error', 'Medical Certificate record not found.');
        }

        $doctorId      = (int) ($this->request->getPost('doctor_id') ?: (session()->get('doctor_id') ?? 0));
        $hasWard       = $this->request->getPost('has_ward')       ? 1 : 0;
        $hasUnfit      = $this->request->getPost('has_unfit')      ? 1 : 0;
        $hasFollowup   = $this->request->getPost('has_followup')   ? 1 : 0;
        $hasLightDuty  = $this->request->getPost('has_light_duty') ? 1 : 0;

        // Ward
        $wardStart   = $hasWard ? ($this->request->getPost('ward_start_date')  ?: null) : null;
        $wardDays    = $hasWard ? (int)$this->request->getPost('ward_num_days') : null;
        $wardEnd     = ($hasWard && $wardStart && $wardDays)
            ? date('Y-m-d', strtotime($wardStart . ' +' . ($wardDays - 1) . ' days')) : null;

        // Unfit
        $start   = $hasUnfit ? ($this->request->getPost('start_date') ?: null) : null;
        $numDays = $hasUnfit ? (int)$this->request->getPost('num_days')         : null;
        $endDate = ($hasUnfit && $start && $numDays)
            ? date('Y-m-d', strtotime($start . ' +' . ($numDays - 1) . ' days')) : null;

        // Follow-up
        $followupDate = $hasFollowup ? ($this->request->getPost('followup_date') ?: null) : null;

        // Light Duty
        $ldStart = $hasLightDuty ? ($this->request->getPost('light_duty_start_date') ?: null) : null;
        $ldDays  = $hasLightDuty ? (int)$this->request->getPost('light_duty_num_days')         : null;
        $ldEnd   = ($hasLightDuty && $ldStart && $ldDays)
            ? date('Y-m-d', strtotime($ldStart . ' +' . ($ldDays - 1) . ' days')) : null;

        $updateData = [
            'doctor_id'             => $doctorId,
            'date_issued'           => $this->request->getPost('date_issued') ?: date('Y-m-d'),
            'has_ward'              => $hasWard,
            'ward_start_date'       => $wardStart,
            'ward_num_days'         => $wardDays,
            'ward_end_date'         => $wardEnd,
            'has_unfit'             => $hasUnfit,
            'start_date'            => $start,
            'num_days'              => $numDays,
            'end_date'              => $endDate,
            'has_followup'          => $hasFollowup,
            'followup_date'         => $followupDate,
            'has_light_duty'        => $hasLightDuty,
            'light_duty_start_date' => $ldStart,
            'light_duty_num_days'   => $ldDays,
            'light_duty_end_date'   => $ldEnd,
            'diagnosis'             => $this->request->getPost('diagnosis'),
            'notes'                 => $this->request->getPost('notes'),
        ];

        if (! $this->mcModel->update($id, $updateData)) {
            session()->setFlashdata('errors', $this->mcModel->errors());
            return redirect()->to('/mc/edit/' . $id)->withInput();
        }

        return redirect()->to('/mc/view/' . $id)->with('success', 'Medical Certificate updated successfully.');
    }
}
