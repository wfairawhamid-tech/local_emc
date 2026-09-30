<?php

namespace App\Models;

use CodeIgniter\Model;

class DoctorModel extends Model
{
    protected $table         = 'doctors';
    protected $primaryKey    = 'id';
    protected $allowedFields = ['name', 'qualification', 'mcr_number', 'department', 'is_active'];
    protected $useTimestamps = false;

    public function getActiveDoctors(): array
    {
        return $this->where('is_active', 1)->findAll();
    }

    public function getAllDepartments(): array
    {
        $rows = $this->select('department')
                     ->where('department IS NOT NULL')
                     ->where("department != ''")
                     ->distinct()
                     ->orderBy('department', 'ASC')
                     ->findAll();
        return array_values(array_filter(array_column($rows, 'department')));
    }
}
