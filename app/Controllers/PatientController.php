<?php

namespace App\Controllers;

use App\Models\PatientModel;

class PatientController extends BaseController
{
    protected PatientModel $patientModel;

    public function __construct()
    {
        $this->patientModel = new PatientModel();
    }

    /**
     * Akses terus ditolak — sistem mesti dibuka melalui iPesakit
     */
    public function accessDenied(): string
    {
        return view('errors/ipesakit_only');
    }

    /**
     * Pilih pesakit → paparan choose_doc (Select Document Type)
     * Digunakan sebagai fallback redirect dari IntegrationController
     * apabila doc=mc atau doc=timeslip tidak dinyatakan.
     */
    public function select($idpatient)
    {
        // Guna data session jika sudah dimuatkan oleh IntegrationController (elak API call berganda)
        $sessionPatient = session()->get('current_patient');
        if ($sessionPatient && (string)($sessionPatient['idpesakit'] ?? '') === (string)$idpatient) {
            $data = [
                'title'   => 'Select Document Type',
                'patient' => $sessionPatient,
            ];
            return view('layouts/main', array_merge($data, ['content_view' => 'patients/choose_doc']));
        }

        // Tiada dalam session — panggil API
        $apiResult = $this->patientModel->getPatientFromApi($idpatient, 'idpesakit');

        if (!$apiResult['success'] || empty($apiResult['data'])) {
            log_message('error', 'PatientController::select() API gagal untuk idpesakit=' . $idpatient . ' — ' . ($apiResult['message'] ?? 'Unknown error'));
            return view('errors/ipesakit_only');
        }

        $raw = $apiResult['data'];

        $patient = [
            'idpesakit'   => $raw['idpesakit']   ?? $raw['id']           ?? null,
            'id'          => $raw['idpesakit']   ?? $raw['id']           ?? null,
            'rn'          => $raw['RN']           ?? $raw['rn']           ?? null,
            'name'        => $raw['namapesakit']  ?? $raw['full_name']    ?? $raw['name'] ?? '',
            'mykad_mykid' => $raw['mykad_mykid']  ?? $raw['ic_number']    ?? '',
            'dob'         => $raw['dob']           ?? $raw['date_of_birth'] ?? null,
            'gender'      => $raw['gender']        ?? null,
        ];

        // Simpan dalam session untuk kegunaan semula
        session()->set('current_patient', $patient);

        $data = [
            'title'   => 'Select Document Type',
            'patient' => $patient,
        ];
        return view('layouts/main', array_merge($data, ['content_view' => 'patients/choose_doc']));
    }

    public function testApi($rn)
    {
        $client = \Config\Services::curlrequest();

        $apiUrl = env('PATIENT_API_URL');
        $token  = env('PATIENT_API_TOKEN');
        try {
            $response = $client->get($apiUrl, [
                    'headers' => [
                        'Authorization' => 'Bearer ' . $token,
                        'Accept'        => 'application/json',
                    ],
                    'query' => [
                        'rn' => $rn,
                    ],
                    'http_errors' => false,
                ]);


            return $this->response
                ->setContentType('application/json')
                ->setBody($response->getBody());

        } catch (\Throwable $e) {
            return $this->response
                ->setStatusCode(500)
                ->setJSON([
                    'error' => $e->getMessage()
                ]);
        }
    }
}
