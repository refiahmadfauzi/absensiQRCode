<?php

namespace App\Controllers;

class Absensi extends BaseController
{
    public function __construct()
    {
        if (!session('login')) {
            return redirect()->to('/');
        } else {
            return view('dashboardView');
        }
    }

    public function index()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('tbl_user');
        $builder->select('tbl_user.*, tbl_attendance.scan_time,tbl_attendance.status');
        $builder->join('tbl_attendance', 'tbl_user.id = tbl_attendance.id_user');
        $data = $builder->get()->getResult();

        return view('absensiView', ['users' => $data]); // Mengirimkan data ke view
    }
}
