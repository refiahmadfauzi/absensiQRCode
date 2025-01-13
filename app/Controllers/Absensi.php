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
        $dari_tgl = \session('dari_tgl');
        $sampai_tgl = \session('sampai_tgl');

        if (\session('type') == 'Admin') {
            $builder = $db->table('tbl_user');
            $builder->select('tbl_user.*, tbl_attendance.scan_time, tbl_attendance.status');
            $builder->join('tbl_attendance', 'tbl_user.id = tbl_attendance.id_user');

            // Tambahkan filter tanggal jika kedua variabel memiliki nilai
            if ($dari_tgl && $sampai_tgl) {
                $builder->where('tbl_attendance.scan_time >=', $dari_tgl);
                // Tambahkan waktu akhir untuk tanggal sampai_tgl
                $builder->where('tbl_attendance.scan_time <=', $sampai_tgl . ' 23:59:59');
            }

            $data = $builder->get()->getResult();
        } else {
            $builder = $db->table('tbl_user');
            $builder->select('tbl_user.*, tbl_attendance.scan_time, tbl_attendance.status');
            $builder->join('tbl_attendance', 'tbl_user.id = tbl_attendance.id_user');

            // Tambahkan filter tanggal jika kedua variabel memiliki nilai
            if ($dari_tgl && $sampai_tgl) {
                $builder->where('tbl_attendance.scan_time >=', $dari_tgl);
                // Tambahkan waktu akhir untuk tanggal sampai_tgl
                $builder->where('tbl_attendance.scan_time <=', $sampai_tgl . ' 23:59:59');
            }

            // Filter untuk user dengan ID tertentu
            $builder->where('tbl_user.id', \session('id'));
            $data = $builder->get()->getResult();
        }

        // Hapus session setelah data dicari
        session()->remove('dari_tgl');
        session()->remove('sampai_tgl');
        return view('absensiView', ['users' => $data]); // Mengirimkan data ke view
    }

    public function cari_data()
    {
        $session = \session();

        $newdata = [
            'dari_tgl'        => $this->request->getVar('dari_tgl'),
            'sampai_tgl'      => $this->request->getVar('sampai_tgl'),
        ];

        $session->set($newdata);

        echo json_encode(array("status" => TRUE, "notif" => "Data berhasil dicari"));
    }
}
