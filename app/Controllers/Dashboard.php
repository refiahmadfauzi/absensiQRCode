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
        $userBuilder = $db->table('tbl_attendance');

        // Dapatkan tanggal hari ini
        $today = date('Y-m-d');

        // Cari user berdasarkan barcode dan scan hari ini
        $user = $userBuilder
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

            // Hapus kode unik yang sudah kadaluarsa (lebih dari 1 menit)
            $codeBuilder
                ->where('created_at <', date('Y-m-d H:i:s', strtotime('-1 minutes')))
                ->where('id_user', $id)
                ->delete();

            // Cari user berdasarkan ID dan code uniq
            $user = $codeBuilder->getWhere(['id_user' => $id, 'code_uniq' => $code_uniq])->getRowArray();

            if ($user) {
                // Cek apakah user sudah hadir di hari ini
                $attendanceBuilder = $db->table('tbl_attendance');
                $today = date('Y-m-d'); // Mengambil tanggal hari ini (format Y-m-d)

                // Pengecekan apakah ada absensi dengan user_id dan tanggal hari ini
                $existingAttendance = $attendanceBuilder->getWhere([
                    'id_user' => $user['id'],
                    'DATE(scan_time)' => $today // Menggunakan DATE() untuk mencocokkan hanya tanggal
                ])->getRowArray();

                if ($existingAttendance) {
                    // Jika sudah ada, kembalikan pesan bahwa sudah tercatat
                    return $this->response->setJSON([
                        'status' => 'error',
                        'message' => 'Attendance already logged for today.'
                    ]);
                }

                // Jika tidak ada, simpan data absensi
                $attendanceData = [
                    'id_user'   => $user['id'],
                    'scan_time' => date('Y-m-d H:i:s'),
                    'status'    => 'Hadir',
                    'id_admin'  => \session('id')
                ];

                $attendanceBuilder->insert($attendanceData);

                return $this->response->setJSON([
                    'status' => 'success',
                    'message' => 'Attendance logged successfully',
                    'user' => [
                        'id' => $user['id'],
                        'name' => $user['name']
                    ]
                ]);
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
}
