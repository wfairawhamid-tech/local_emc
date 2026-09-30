<?php

namespace App\Controllers;

use App\Models\DoctorModel;

class AuthController extends BaseController
{
    protected DoctorModel $doctorModel;

    public function __construct()
    {
        $this->doctorModel = new DoctorModel();
    }

    /**
     * Login Page (with 1-Click Quick Switcher)
     */
    public function index()
    {
        if (session()->get('logged_in')) {
            return redirect()->to('/');
        }

        $doctors = $this->doctorModel->getActiveDoctors();

        $data = [
            'title'   => 'eMC System Login',
            'doctors' => $doctors,
            'errors'  => session()->getFlashdata('errors'),
        ];

        return view('auth/login', $data);
    }

    /**
     * Process 1-Click Quick Login
     * role: 'admin' or 'doctor'
     */
    public function dummyLogin(string $role, int $doctorId = 0)
    {
        $session = session();

        if ($role === 'admin') {
            $session->set([
                'logged_in'   => true,
                'role'        => 'admin',
                'user_id'     => 0,
                'user_name'   => 'IT Administrator',
                'department'  => 'Information Technology Department (IT)',
                'is_admin'    => true,
            ]);

            return redirect()->to('/')->with('success', 'Successfully logged in as IT Administrator.');
        }

        if ($role === 'doctor') {
            $doctor = null;
            if ($doctorId > 0) {
                $doctor = $this->doctorModel->find($doctorId);
            } else {
                $doctor = $this->doctorModel->where('is_active', 1)->first();
            }

            if (! $doctor) {
                return redirect()->to('/login')->with('error', 'Doctor record not found.');
            }

            $session->set([
                'logged_in'   => true,
                'role'        => 'doctor',
                'user_id'     => (int) $doctor['id'],
                'doctor_id'   => (int) $doctor['id'],
                'user_name'   => $doctor['name'],
                'qualification'=> $doctor['qualification'] ?? '',
                'mcr_number'  => $doctor['mcr_number'] ?? '',
                'department'  => $doctor['department'] ?? '',
                'is_admin'    => false,
            ]);

            return redirect()->to('/')->with('success', 'Successfully logged in as ' . $doctor['name']);
        }

        return redirect()->to('/login')->with('error', 'Invalid role.');
    }

    /**
     * Process Standard Form Login
     */
    public function login()
    {
        $role = $this->request->getPost('role');

        if ($role === 'admin') {
            return $this->dummyLogin('admin');
        }

        $doctorId = (int) $this->request->getPost('doctor_id');
        return $this->dummyLogin('doctor', $doctorId);
    }

    /**
     * Log Out
     */
    public function logout()
    {
        session()->destroy();
        return redirect()->to('/login')->with('info', 'You have been logged out successfully.');
    }
}
