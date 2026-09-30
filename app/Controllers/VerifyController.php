<?php

namespace App\Controllers;

use App\Models\MedicalCertificateModel;
use App\Models\TimeSlipModel;

class VerifyController extends BaseController
{
    protected MedicalCertificateModel $mcModel;
    protected TimeSlipModel           $tsModel;

    public function __construct()
    {
        $this->mcModel = new MedicalCertificateModel();
        $this->tsModel = new TimeSlipModel();
    }

    /**
     * Portal Pengesahan Awam Sijil Sakit (MC)
     * URL: /verify/mc/MC-2609-001
     */
    public function mc(string $ref)
    {
        $ref = urldecode($ref);

        // Cari rekod berdasarkan reference_no atau ID
        $mc = $this->mcModel->where('reference_no', $ref)->first();
        if (! $mc && is_numeric($ref)) {
            $mc = $this->mcModel->find((int) $ref);
        }

        $details = null;
        if ($mc) {
            $details = $this->mcModel->getWithDetails((int) $mc['id']);
        }

        $data = [
            'title'   => 'Pengesahan Sijil Cuti Sakit — Hospital Sultan Zainal Abidin',
            'isValid' => ($details !== null),
            'mc'      => $details,
            'ref'     => $ref,
        ];

        return view('verify/mc', $data);
    }

    /**
     * Portal Pengesahan Awam Slip Masa (Time Slip)
     * URL: /verify/timeslip/TS-2609-001
     */
    public function timeslip(string $ref)
    {
        $ref = urldecode($ref);

        $ts = $this->tsModel->where('reference_no', $ref)->first();
        if (! $ts && is_numeric($ref)) {
            $ts = $this->tsModel->find((int) $ref);
        }

        $details = null;
        if ($ts) {
            $details = $this->tsModel->getWithDetails((int) $ts['id']);
        }

        $data = [
            'title'   => 'Pengesahan Slip Masa Rawatan — Hospital Sultan Zainal Abidin',
            'isValid' => ($details !== null),
            'ts'      => $details,
            'ref'     => $ref,
        ];

        return view('verify/timeslip', $data);
    }
}
