<?php

namespace App\Controllers;

use App\Models\MedicalCertificateModel;
use App\Models\TimeSlipModel;
use App\Models\PatientModel;

/**
 * PatientHistoryController
 * Provides a search-by-RN interface that retrieves all Medical
 * Certificate and Time Slip records for a given patient.
 * RN is the primary/master patient identifier.
 */
class PatientHistoryController extends BaseController
{
    protected PatientModel             $patientModel;
    protected MedicalCertificateModel  $mcModel;
    protected TimeSlipModel            $tsModel;

    public function __construct()
    {
        $this->patientModel = new PatientModel();
        $this->mcModel      = new MedicalCertificateModel();
        $this->tsModel      = new TimeSlipModel();
    }

    /**
     * Search form (GET /patient-history)
     * Supports search by RN, Patient Name, or unified query `q`.
     */
    public function index(): string
    {
        $rn    = trim((string) ($this->request->getGet('rn') ?? ''));
        $name  = trim((string) ($this->request->getGet('name') ?? ''));
        $query = trim((string) ($this->request->getGet('q') ?? ''));

        // Resolve search term
        $searchTerm = $rn !== '' ? $rn : ($name !== '' ? $name : $query);

        $patient          = null;
        $multiplePatients = [];
        $mcList           = [];
        $tsList           = [];
        $error            = null;

        if ($searchTerm !== '') {
            // 1. Try finding direct match by RN first
            $patient = $this->patientModel->findByRn($searchTerm);

            // 2. If not found by exact RN, search by RN, Name, or IC
            if (! $patient) {
                $matches = $this->patientModel->searchByRnOrName($searchTerm, 10);
                if (count($matches) === 1) {
                    $patient = $matches[0];
                } elseif (count($matches) > 1) {
                    $multiplePatients = $matches;
                } else {
                    $error = 'No patient found with RN or Name matching <strong>' . esc($searchTerm) . '</strong>. '
                           . 'Only patients with registered records or issued documents are searchable.';
                }
            }

            // 3. If a patient is uniquely identified, fetch their document history
            if ($patient) {
                $patientId = (int) $patient['id'];
                $rn        = (string) ($patient['rn'] ?? $rn);
                $mcList    = $this->mcModel->getByPatientId($patientId);
                $tsList    = $this->tsModel->getByPatientId($patientId);
            }
        }

        $data = [
            'title'            => 'Patient History',
            'rn'               => $rn,
            'searchTerm'       => $searchTerm,
            'patient'          => $patient,
            'multiplePatients' => $multiplePatients,
            'mcList'           => $mcList,
            'tsList'           => $tsList,
            'error'            => $error,
        ];

        return view('layouts/main', array_merge($data, ['content_view' => 'patient_history/index']));
    }
}
