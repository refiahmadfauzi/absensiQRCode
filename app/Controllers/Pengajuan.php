<?php

namespace App\Controllers;

class Pengajuan extends BaseController
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
        if (\session('type') == 'Admin') {
            $builder = $db->table('tbl_pengajuan');
            $builder->select('tbl_pengajuan.*, tbl_user.name');
            $builder->join('tbl_user', 'tbl_user.id = tbl_pengajuan.id_user');
            $data = $builder->get()->getResult();
        } else {
            $builder = $db->table('tbl_pengajuan');
            $builder->select('tbl_pengajuan.*, tbl_user.name');
            $builder->join('tbl_user', 'tbl_user.id = tbl_pengajuan.id_user');
            $builder->where('tbl_user.id', \session('id'));
            $data = $builder->get()->getResult();
        }

        return view('pengajuanView', ['users' => $data]); // Mengirimkan data ke view

    }


    public function create()
    {
        $request = $this->request;

        $jenisPengajuan = $request->getPost('jenis_pengajuan');
        $tanggalMulai = $request->getPost('tanggal_mulai');
        $tanggalSelesai = $request->getPost('tanggal_selesai');
        $alasan = $request->getPost('alasan');
        $dokumen = $request->getFile('dokumen');

        if ($dokumen->isValid() && !$dokumen->hasMoved()) {
            $newName = $dokumen->getRandomName();
            $dokumen->move('assets/dokumen', $newName);
        } else {
            return redirect()->back()->with('error', 'Dokumen gagal diunggah.');
        }

        $db = \Config\Database::connect();
        $builder = $db->table('tbl_pengajuan');
        $builder->insert([
            'id_user' => session('id'), // Sesuaikan dengan session user login
            'jenis_pengajuan' => $jenisPengajuan,
            'tanggal_mulai' => $tanggalMulai,
            'tanggal_selesai' => $tanggalSelesai,
            'alasan' => $alasan,
            'dokumen' => $newName,
            'status'    => 1,
            'created_at'    => \date('Y-m-d')
        ]);

        echo json_encode(array("status" => TRUE, "notif" => "Data berhasil ditambahkan"));
    }

    public function getbyid($id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('tbl_pengajuan');

        // Mengambil data berdasarkan ID
        $user = $builder->where('id', $id)->get()->getRow();
        echo json_encode($user);
    }

    public function update()
    {
        $session = session();
        $db = \Config\Database::connect();
        $builder = $db->table('tbl_pengajuan');

        // Ambil data file
        $file = $this->request->getFile('dokumen');
        $id = $this->request->getPost('idpengajuan');
        // Siapkan array untuk data update
        $data = [
            'jenis_pengajuan' => $this->request->getPost('jenis_pengajuan'),
            'tanggal_mulai' => $this->request->getPost('tanggal_mulai'),
            'tanggal_selesai' => $this->request->getPost('tanggal_selesai'),
            'alasan' => $this->request->getPost('alasan'),
            'created_at' => \date('Y-m-d'),
        ];

        // Periksa apakah ada file yang diunggah
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName(); // Generate nama file acak
            $file->move('assets/dokumen', $newName); // Simpan file ke folder uploads
            $data['dokumen'] = $newName; // Simpan nama file ke dalam array data
        }

        // Update data berdasarkan ID
        $builder->where('id', $id);
        $builder->update($data);

        // Response JSON
        echo json_encode([
            "status" => TRUE,
            "notif" => "Data berhasil diperbarui"
        ]);
    }

    public function delete($id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('tbl_pengajuan');

        // Cari pengguna berdasarkan ID
        $user = $builder->getWhere(['id' => $id])->getRow();

        if ($user) {
            // Hapus file gambar jika ada
            $imagePath = 'assets/dokumen/' . $user->dokumen;
            if (is_file($imagePath)) {
                unlink($imagePath); // Hapus file
            }

            // Hapus data dari database
            $builder->where('id', $id);
            $builder->delete();

            return json_encode([
                'status' => true,
                'message' => 'Pengajuan berhasil dihapus.'
            ]);
        } else {
            return json_encode([
                'status' => false,
                'message' => 'Pengajuan tidak ditemukan.'
            ]);
        }
    }

    public function terima($id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('tbl_pengajuan');
        $pengajuan = $builder->getWhere(['id' => $id])->getRowArray();
        $data = [
            'status' => 2,
            'created_at' => \date('Y-m-d'),
        ];

        // Update data berdasarkan ID
        $builder->where('id', $id);
        $builder->update($data);


        // Tanggal awal dan akhir
        $tanggalMulai = $pengajuan['tanggal_mulai'];
        $tanggalSelesai = $pengajuan['tanggal_selesai'];

        // Hitung jumlah hari
        $startDate = new \DateTime($tanggalMulai);
        $endDate = new \DateTime($tanggalSelesai);
        $interval = $startDate->diff($endDate);
        $jumlahHari = $interval->days + 1; // Tambahkan 1 agar inklusif

        $attendanceBuilder = $db->table('tbl_attendance');

        // Insert data untuk setiap hari
        $currentDate = $startDate;
        for ($i = 0; $i < $jumlahHari; $i++) {
            $attendanceData = [
                'id_user'   => $pengajuan['id_user'],
                'scan_time' => $currentDate->format('Y-m-d H:i:s'),
                'status'    => $pengajuan['jenis_pengajuan'],
                'id_admin'  => \session('id')
            ];

            $attendanceBuilder->insert($attendanceData);

            // Tambahkan 1 hari
            $currentDate->modify('+1 day');
        }
        return json_encode([
            'status' => true,
            'message' => 'Pengajuan berhasil diterima.'
        ]);
    }

    public function tolak($id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('tbl_pengajuan');
        $data = [
            'status' => 0,
            'created_at' => \date('Y-m-d'),
        ];

        // Update data berdasarkan ID
        $builder->where('id', $id);
        $builder->update($data);

        return json_encode([
            'status' => true,
            'message' => 'Pengajuan berhasil ditolak.'
        ]);
    }
}
