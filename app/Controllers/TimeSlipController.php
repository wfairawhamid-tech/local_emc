<?php

namespace App\Controllers;

use App\Models\TimeSlipModel;
use App\Models\PatientModel;
use App\Models\DoctorModel;

class TimeSlipController extends BaseController
{
    protected TimeSlipModel $tsModel;
    protected PatientModel  $patientModel;
    protected DoctorModel   $doctorModel;

    public function __construct()
    {
        $this->tsModel      = new TimeSlipModel();
        $this->patientModel = new PatientModel();
        $this->doctorModel  = new DoctorModel();
    }

    /**
     * List all Time Slips
     */
    public function index(): string
    {
        $records = $this->tsModel
            ->select('time_slips.*, patients.full_name, patients.ic_number, patients.rn')
            ->join('patients', 'patients.id = time_slips.patient_id')
            ->orderBy('time_slips.id', 'DESC')
            ->findAll();

        $data = [
            'title'   => 'Time Slip Records',
            'records' => $records,
        ];

        return view('layouts/main', array_merge($data, ['content_view' => 'timeslip/index']));
    }

    /**
     * Form to create a new Time Slip for a patient
     */
    public function create($patientId)
    {
        $patientAPI = $this->patientModel->getPatientFromApi($patientId);
        if (!$patientAPI['success']) {
            $msg = 'Failed to retrieve patient information from API (Server Down / ' . ($patientAPI['message'] ?? 'Error') . ').';
            log_message('error', 'TimeSlip Create - Patient API failed: ' . $msg);
            return view('errors/ipesakit_only');
        }

        $patient = $patientAPI['data'];

        $data = [
            'title'           => 'Issue Time Slip',
            'patient'         => $patient,
            'doctors'         => $this->doctorModel->getActiveDoctors(),
            'currentDoctorId' => (int) (session()->get('doctor_id') ?? 0),
            'errors'          => session()->getFlashdata('errors'),
        ];

        return view('layouts/main', array_merge($data, ['content_view' => 'timeslip/create']));
    }

    /**
     * Process save Time Slip
     */
    public function store()
    {
        $doctorId     = (int) ($this->request->getPost('doctor_id') ?: (session()->get('doctor_id') ?? 0));
        $visitType    = $this->request->getPost('visit_type') ?: 'diri_sendiri';
        $hasCompanion = $visitType !== 'diri_sendiri';

        $refNo = $this->tsModel->generateReferenceNo();

        // Map API / Form field names → DB column names for the patients table
        $icNumber = $this->request->getPost('patient_mykad_mykid');
        $rawDob   = $this->request->getPost('patient_dob');
        $dob      = $rawDob ? date('Y-m-d', strtotime($rawDob)) : null;
        $gender   = $this->request->getPost('patient_gender');

        if (!$gender && !empty($icNumber)) {
            $parsedIC = \App\Libraries\ICParser::parse($icNumber);
            $gender   = $parsedIC ? $parsedIC['gender'] : 'Lelaki';
            if (!$dob && $parsedIC) {
                $dob = $parsedIC['dob'];
            }
        }

        $datapatient = [
            'ic_number'     => $icNumber,
            'rn'            => $this->request->getPost('patient_rn') ?: null,
            'full_name'     => $this->request->getPost('patient_name'),
            'date_of_birth' => $dob ?: date('Y-m-d'),
            'gender'        => $gender ?: 'Lelaki',
        ];

        // Check if patient with same IC already exists in local DB; reuse or insert
        if (!empty($datapatient['ic_number'])) {
            $existingPatient = $this->patientModel->findByIC($datapatient['ic_number']);
            if ($existingPatient) {
                $patientDbId = $existingPatient['id'];
            } else {
                $patientDbId = $this->patientModel->insert($datapatient);
            }
        } else {
            $patientDbId = (int) $this->request->getPost('patient_id');
        }

        $data = [
            'reference_no'       => $refNo,
            'patient_id'         => $patientDbId,
            'doctor_id'          => $doctorId,
            'date_issued'        => $this->request->getPost('date_issued') ?: date('Y-m-d'),
            'time_in'            => $this->request->getPost('time_in'),
            'time_out'           => $this->request->getPost('time_out'),
            'purpose'            => $this->request->getPost('purpose'),
            'notes'              => $this->request->getPost('notes'),
            'language'           => $this->request->getPost('language') ?: 'EN',
            'visit_type'         => $visitType,
            'companion_name'     => $hasCompanion ? $this->request->getPost('companion_name') : null,
            'companion_ic'       => $hasCompanion ? ($this->request->getPost('companion_ic') ?: null) : null,
            'companion_relation' => $hasCompanion ? ($this->request->getPost('companion_relation') ?: null) : null,
        ];

        $id = $this->tsModel->insert($data);
        if (! $id) {
            session()->setFlashdata('errors', $this->tsModel->errors());
            $origPatientId = $this->request->getPost('patient_id');
            return redirect()->to('/timeslip/create/' . $origPatientId);
        }

        return redirect()->to('/timeslip/view/' . $id);
    }

    /**
     * Display Time Slip for printing
     */
    public function view(int $id)
    {
        $ts = $this->tsModel->getWithDetails($id);
        if (! $ts) {
            return redirect()->to('/timeslip')->with('error', 'Time Slip record not found.');
        }

        $rawLang = $this->request->getGet('lang') ?? ($_GET['lang'] ?? 'BI');
        $lang = (strtoupper($rawLang) === 'BM') ? 'BM' : 'BI';

        $data = [
            'title' => 'Time Slip - ' . $ts['reference_no'],
            'ts'    => $ts,
            'lang'  => $lang,
        ];

        return view('timeslip/view', $data);
    }

    /**
     * Edit form for an existing Time Slip record
     */
    public function edit(int $id)
    {
        $ts = $this->tsModel->getWithDetails($id);
        if (! $ts) {
            return redirect()->to('/timeslip')->with('error', 'Time Slip record not found.');
        }

        $data = [
            'title'           => 'Edit Time Slip',
            'ts'              => $ts,
            'doctors'         => $this->doctorModel->getActiveDoctors(),
            'currentDoctorId' => (int) (session()->get('doctor_id') ?? 0),
            'errors'          => session()->getFlashdata('errors'),
        ];

        return view('layouts/main', array_merge($data, ['content_view' => 'timeslip/edit']));
    }

    /**
     * Save changes to an existing Time Slip record
     */
    public function update(int $id)
    {
        $ts = $this->tsModel->find($id);
        if (! $ts) {
            return redirect()->to('/timeslip')->with('error', 'Time Slip record not found.');
        }

        $doctorId     = (int) ($this->request->getPost('doctor_id') ?: (session()->get('doctor_id') ?? 0));
        $visitType    = $this->request->getPost('visit_type') ?: 'diri_sendiri';
        $hasCompanion = $visitType !== 'diri_sendiri';

        $updateData = [
            'doctor_id'          => $doctorId,
            'date_issued'        => $this->request->getPost('date_issued') ?: date('Y-m-d'),
            'time_in'            => $this->request->getPost('time_in'),
            'time_out'           => $this->request->getPost('time_out'),
            'purpose'            => $this->request->getPost('purpose'),
            'notes'              => $this->request->getPost('notes'),
            'visit_type'         => $visitType,
            'companion_name'     => $hasCompanion ? $this->request->getPost('companion_name')     : null,
            'companion_ic'       => $hasCompanion ? ($this->request->getPost('companion_ic') ?: null) : null,
            'companion_relation' => $hasCompanion ? ($this->request->getPost('companion_relation') ?: null) : null,
        ];

        if (! $this->tsModel->update($id, $updateData)) {
            session()->setFlashdata('errors', $this->tsModel->errors());
            return redirect()->to('/timeslip/edit/' . $id)->withInput();
        }

        return redirect()->to('/timeslip/view/' . $id)->with('success', 'Time Slip updated successfully.');
    }
}
