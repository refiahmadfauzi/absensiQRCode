<?php

namespace App\Controllers;

use Endroid\QrCode\QrCode;
use Endroid\QrCode\Writer\PngWriter;

class Dashboard extends BaseController
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
        $id = \session('id');
        $qrcode = $this->generate($id);
        $db = \Config\Database::connect();
        $attendanceBuilder = $db->table('tbl_attendance');
        $userBuilder = $db->table('tbl_user');

        // Contoh kondisi: hanya hitung data dengan status 'Cuti'
        if (\session('type' == 'Admin')) {
            $totalDataCuti = $attendanceBuilder->where('status', 'Cuti')->countAllResults();
            $totalDataHadir = $attendanceBuilder->where('status', 'Hadir')->countAllResults();
            $totalDataSakit = $attendanceBuilder->where('status', 'Sakit')->countAllResults();
            $totalDatauser = $userBuilder->where('status', 1)->countAllResults();
        } else {
            $totalDataCuti = $attendanceBuilder->where('id_user', $id)->where('status', 'Cuti')->countAllResults();
            $totalDataHadir = $attendanceBuilder->where('id_user', $id)->where('status', 'Hadir')->countAllResults();
            $totalDataSakit = $attendanceBuilder->where('id_user', $id)->where('status', 'Sakit')->countAllResults();
            $totalDatauser = $userBuilder->where('status', 1)->countAllResults();
        }
        $totalDataHadirNow = $attendanceBuilder->where('status', 'Hadir')->where('DATE(scan_time)', date('Y-m-d'))->countAllResults();

        // Dapatkan tanggal hari ini
        $today = date('Y-m-d');

        // Cari user berdasarkan barcode dan scan hari ini
        $user = $attendanceBuilder
            ->where('id_user', $id)
            ->where('DATE(scan_time)', $today)
            ->get()
            ->getRowArray();

        // Periksa apakah ada data
        $absen = '';
        if ($user) {
            $absen = "Terimakasih telah absen hari ini";
        } else {
            $absen = "Anda belum melakukan scan hari ini. Silahkan scan ke admin";
        }

        $data['qrcode'] = $qrcode;
        $data['cekabsen'] = $absen;
        $data['cuti'] = $totalDataCuti;
        $data['hadir'] = $totalDataHadir;
        $data['sakit'] = $totalDataSakit;
        $data['user'] = $totalDatauser;
        $data['hadirnow'] = $totalDataHadirNow;

        return view('dashboardView', $data);
    }

    public function logout()
    {
        $session = session(); // Memuat library session
        $session->destroy();  // Menghapus semua data sesi

        return redirect()->to('/'); // Arahkan ke halaman login
    }

    public function generate($id)
    {
        date_default_timezone_set('Asia/Jakarta');
        $db = \Config\Database::connect();
        $codeBuilder = $db->table('tbl_code');

        // Cari kode unik yang berlaku untuk user saat ini dalam 1 menit terakhir
        $existingCode = $codeBuilder
            ->where('id_user', $id)
            ->where('created_at >=', date('Y-m-d H:i:s', strtotime('-1 minutes')))
            ->get()
            ->getRowArray();

        if (!$existingCode) {
            // Generate kode unik baru
            $uniqueCode = bin2hex(random_bytes(8)); // 16 karakter kode unik
            $codeBuilder->insert([
                'code_uniq'       => $uniqueCode,
                'created_at' => date('Y-m-d H:i:s'),
                'id_user'    => $id,
            ]);
        } else {
            $uniqueCode = $existingCode['code_uniq'];
        }

        // Data yang akan di-encode ke QR code
        $data = [
            'id_user'   => $id,
            'code_uniq'  => $uniqueCode,
            'created_at' => date('Y-m-d H:i:s'),
        ];

        $qrData = json_encode($data);

        // Buat QR Code
        $qrCode = new QrCode($qrData);
        $qrCode->setSize(300);
        $qrCode->setMargin(10);

        // Generate QR Code sebagai base64 string
        $writer = new PngWriter();
        $result = $writer->write($qrCode);
        $base64 = base64_encode($result->getString());

        return 'data:image/png;base64,' . $base64;
    }

    public function processScan()
    {
        // mengatur default timezone ke indonesia
        date_default_timezone_set('Asia/Jakarta');
        $request = $this->request->getJSON();

        if (isset($request->qr_data)) {
            $qrString = $request->qr_data;
            // Decode JSON dari string
            $decodedData = json_decode($qrString, true);
            $id = $decodedData['id_user'];
            $code_uniq = $decodedData['code_uniq'];

            // Koneksi database dan query builder
            $db = \Config\Database::connect();
            $codeBuilder = $db->table('tbl_code');
            $userBuilder = $db->table('tbl_user');
            $jamBuilder = $db->table('tbl_jam');

            // Hapus kode unik yang sudah kadaluarsa (lebih dari 1 menit)
            $codeBuilder
                ->where('created_at <', date('Y-m-d H:i:s', strtotime('-1 minutes')))
                ->where('id_user', $id)
                ->delete();

            // Cari user berdasarkan ID dan code uniq
            $code = $codeBuilder->getWhere(['id_user' => $id, 'code_uniq' => $code_uniq])->getRowArray();
            $user = $userBuilder->getWhere(['id' => $id])->getRowArray();
            $jamMasuk = $jamBuilder->getWhere(['id' => 1])->getRowArray();
            $jamKeluar = $jamBuilder->getWhere(['id' => 2])->getRowArray();

            if ($code) {
                $attendanceBuilder = $db->table('tbl_attendance');
                $today = date('Y-m-d');
                $currentTime = date('H:i:s');

                // Ambil semua absensi user hari ini
                $attendanceToday = $attendanceBuilder->getWhere([
                    'id_user' => $user['id'],
                    'DATE(scan_time)' => $today
                ])->getResultArray();

                // Cek jumlah absensi hari ini
                if (count($attendanceToday) >= 2) {
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Attendance is already completed for today.'
                    ]);
                }

                // Jika belum ada absensi masuk
                if (empty($attendanceToday)) {
                    $status = ($currentTime > $jamMasuk['time']) ? 'Terlambat' : 'Hadir';

                    $attendanceData = [
                        'id_user'   => $user['id'],
                        'scan_time' => date('Y-m-d H:i:s'),
                        'status'    => $status,
                        'id_admin'  => \session('id'),
                        'id_jam'    => $jamMasuk['id']
                    ];

                    $attendanceBuilder->insert($attendanceData);

                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => $status === 'Terlambat' ? 'You are late!' : 'Attendance logged successfully.',
                        'user' => [
                            'id' => $user['id'],
                            'name' => $user['name']
                        ]
                    ]);
                }

                // Jika sudah ada absensi masuk, cek untuk absensi pulang
                if (count($attendanceToday) === 1) {
                    $attendanceData = [
                        'id_user'   => $user['id'],
                        'scan_time' => date('Y-m-d H:i:s'),
                        'status'    => 'Pulang',
                        'id_admin'  => \session('id'),
                        'id_jam'    => $jamKeluar['id']
                    ];

                    $attendanceBuilder->insert($attendanceData);

                    return $this->response->setJSON([
                        'status' => 'success',
                        'message' => 'Your check-out has been logged.',
                        'user' => [
                            'id' => $user['id'],
                            'name' => $user['name']
                        ]
                    ]);
                }
            } else {
                return $this->response->setJSON([
                    'status' => 'error',
                    'message' => 'Maaf QR Code sudah tidak berlaku'
                ]);
            }
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Invalid request data.'
        ]);
    }

    public function profile()
    {
        $id = \session('id');
        $db = \Config\Database::connect();
        $builder = $db->table('tbl_user');
        $builder->where('id', \session('id'));
        $data['profile'] = $builder->get()->getRow();

        return view('profileView', $data);
    }

    public function updateprofile()
    {
        $session = session();
        $db = \Config\Database::connect();
        $builder = $db->table('tbl_user');

        // Ambil data file
        $file = $this->request->getFile('image');
        $id = $this->request->getPost('iduser');
        // Siapkan array untuk data update
        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'create_at' => \date('Y-m-d'),
            'alamat' => $this->request->getPost('alamat'),
            'tempat_lahir' => $this->request->getPost('tempat_lahir'),
            'tanggal_lahir' => $this->request->getPost('tanggal_lahir'),
            'jenis_kelamin' => $this->request->getPost('jk'),
            'agama' => $this->request->getPost('agama'),
            'no_ktp' => $this->request->getPost('no_ktp'),
            'pendidikan_terakhir' => $this->request->getPost('pendidikan'),
            'no_hp' => $this->request->getPost('no_hp'),
        ];

        // Periksa apakah password diubah
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = md5($password); // Enkripsi password jika diisi
        }

        // Periksa apakah ada file yang diunggah
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName(); // Generate nama file acak
            $file->move('assets/uploads', $newName); // Simpan file ke folder uploads
            $data['image'] = $newName; // Simpan nama file ke dalam array data
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
}
