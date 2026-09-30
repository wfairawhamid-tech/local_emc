<?php

namespace App\Controllers;

use App\Models\DoctorModel;
use App\Models\PatientModel;

class IntegrationController extends BaseController
{
    protected DoctorModel  $doctorModel;
    protected PatientModel $patientModel;

    public function __construct()
    {
        $this->doctorModel  = new DoctorModel();
        $this->patientModel = new PatientModel();
    }

    /**
     * Endpoint integrasi iPesakit:
     * Dipanggil semasa doktor menekan butang dalam iPesakit
     *
     * Contoh pautan:
     *   /launch?doctor_id=1&rn=1000002
     *   /launch?doctor_id=1&idpesakit=12
     *   /launch?doctor_id=1&rn=1000002&doc=mc
     *   /launch?doctor_id=1&rn=1000002&doc=timeslip
     */
    public function launch()
    {
        $session   = session();
        $doctorId  = (int) ($this->request->getGet('doctor_id') ?? 0);
        $rn        = trim($this->request->getGet('rn') ?? '');
        $idpatient = trim($this->request->getGet('idpesakit') ?? '');
        $docType   = trim($this->request->getGet('doc') ?? ''); // 'mc', 'timeslip', atau kosong

        // ── 1. Pengesahan & Pengaktifan Sesi Doktor ──────────────────────────────
        if ($doctorId > 0) {
            $doctor = $this->doctorModel->find($doctorId);
            if ($doctor && $doctor['is_active']) {
                $session->set([
                    'logged_in'     => true,
                    'role'          => 'doctor',
                    'user_id'       => (int) $doctor['id'],
                    'doctor_id'     => (int) $doctor['id'],
                    'user_name'     => $doctor['name'],
                    'qualification' => $doctor['qualification'] ?? '',
                    'mcr_number'    => $doctor['mcr_number'] ?? '',
                    'department'    => $doctor['department'] ?? '',
                    'is_admin'      => false,
                    'login_source'  => 'ipesakit',
                ]);
            }
        } elseif (! $session->get('logged_in')) {
            // Jika tiada doktor ditentukan dan belum login, ambil doktor aktif lalai
            $defaultDoc = $this->doctorModel->where('is_active', 1)->first();
            if ($defaultDoc) {
                $session->set([
                    'logged_in'     => true,
                    'role'          => 'doctor',
                    'user_id'       => (int) $defaultDoc['id'],
                    'doctor_id'     => (int) $defaultDoc['id'],
                    'user_name'     => $defaultDoc['name'],
                    'qualification' => $defaultDoc['qualification'] ?? '',
                    'mcr_number'    => $defaultDoc['mcr_number'] ?? '',
                    'department'    => $defaultDoc['department'] ?? '',
                    'is_admin'      => false,
                    'login_source'  => 'ipesakit_fallback',
                ]);
            }
        }

        // ── 2. Dapatkan Maklumat Pesakit dari API iPesakit ────────────────────────
        $patient = null;

        if (!empty($rn)) {
            // Cari pesakit menggunakan RN (Registration Number) dari iPesakit
            $apiResult = $this->patientModel->getPatientFromApi($rn, 'rn');
            if ($apiResult['success'] && !empty($apiResult['data'])) {
                $raw     = $apiResult['data'];
                $patient = $this->normalisePatient($raw);
            }
        } elseif (!empty($idpatient)) {
            // Cari pesakit menggunakan ID pesakit dari iPesakit
            $apiResult = $this->patientModel->getPatientFromApi($idpatient, 'idpesakit');
            if ($apiResult['success'] && !empty($apiResult['data'])) {
                $raw     = $apiResult['data'];
                $patient = $this->normalisePatient($raw);
            }
        }

        // Jika tiada data pesakit — paparkan halaman info (sistem hanya via iPesakit)
        if (! $patient) {
            log_message('warning', 'IntegrationController::launch() — tiada data pesakit. RN=[' . $rn . '] idpesakit=[' . $idpatient . ']');
            return view('errors/ipesakit_only');
        }

        // Simpan data pesakit dalam session untuk kegunaan semula (mengelak API call berulang)
        $session->set('current_patient', $patient);

        // ── 3. Halakan Doktor ke Skrin Sesuai ────────────────────────────────────
        if ($docType === 'mc') {
            // Terus ke borang MC
            return redirect()->to('/mc/create/' . $patient['idpesakit']);
        }

        if ($docType === 'timeslip') {
            // Terus ke borang Time Slip
            return redirect()->to('/timeslip/create/' . $patient['idpesakit']);
        }

        // Lalai: tunjuk halaman Pilih Jenis Dokumen (Select Document Type)
        $data = [
            'title'   => 'Select Document Type',
            'patient' => $patient,
        ];
        return view('layouts/main', array_merge($data, ['content_view' => 'patients/choose_doc']));
    }

    /**
     * Normalisasi data pesakit dari API iPesakit ke format seragam.
     * Mengelak ralat akibat perbezaan nama field dari pelbagai endpoint API.
     */
    private function normalisePatient(array $raw): array
    {
        return [
            'idpesakit'   => $raw['idpesakit']   ?? $raw['id']          ?? null,
            'id'          => $raw['idpesakit']   ?? $raw['id']          ?? null,
            'rn'          => $raw['RN']           ?? $raw['rn']          ?? null,
            'name'        => $raw['namapesakit']  ?? $raw['full_name']   ?? $raw['name'] ?? '',
            'mykad_mykid' => $raw['mykad_mykid']  ?? $raw['ic_number']   ?? '',
            'dob'         => $raw['dob']           ?? $raw['date_of_birth'] ?? null,
            'gender'      => $raw['gender']        ?? null,
        ];
    }
}
