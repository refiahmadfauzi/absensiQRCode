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

        return view('dashboardView', ['qrcode' => $qrcode]);
    }

    public function logout()
    {
        $session = session(); // Memuat library session
        $session->destroy();  // Menghapus semua data sesi

        return redirect()->to('/'); // Arahkan ke halaman login
    }

    public function generate($id)
    {
        $db = \Config\Database::connect();
        $userBuilder = $db->table('tbl_user');

        // Cari user berdasarkan barcode
        $user = $userBuilder->getWhere(['id' => $id])->getRowArray();

        if (!$user) {
            return "User not found";
        }

        // Data yang akan di-encode ke QR code
        $data = [
            'id'    => $user['id'],
            'name'  => $user['name'],
            'email' => $user['email'],
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
            $id = $decodedData['id'];

            // Koneksi database dan query builder
            $db = \Config\Database::connect();
            $userBuilder = $db->table('tbl_user');

            // Cari user berdasarkan ID
            $user = $userBuilder->getWhere(['id' => $id])->getRowArray();

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
                    'status'    => 'present'
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
                    'message' => 'Invalid QR Code or user not found.'
                ]);
            }
        }

        return $this->response->setJSON([
            'status' => 'error',
            'message' => 'Invalid request data.'
        ]);
    }
}
