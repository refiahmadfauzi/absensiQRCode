<?php

namespace App\Controllers;

class Login extends BaseController
{
    public function index()
    {
        return view('loginView');
    }

    public function login()
    {
        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        $this->_validate();

        $session = session();
        $db      = \Config\Database::connect();
        $builder = $db->table('tbl_user');
        $builder->select('tbl_user.*, tbl_type_user.name AS type_name');
        $builder->join('tbl_type_user', 'tbl_user.type = tbl_type_user.id');
        $builder->where('tbl_user.email', $email);
        $builder->where('tbl_user.password', md5($password));
        $builder->where('tbl_user.status', 1);
        $login = $builder->get()->getRow();


        if ($login) {
            $newdata = [
                'id'        => $login->id,
                'name'      => $login->name,
                'email'     => $login->email,
                'type'      => $login->type_name,
                'image'      => $login->image,
                'login'     => true,
            ];

            $session->set($newdata);

            echo json_encode(array("status" => TRUE));
        } else {
            echo view('viewLogin');
        }
    }

    private function _validate()
    {
        $data = array();
        $data['error_string'] = array();
        $data['inputerror'] = array();
        $data['status'] = TRUE;

        $email = $this->request->getVar('email');
        $password = $this->request->getVar('password');

        if ($email == '') {
            $data['inputerror'][] = 'email';
            $data['error_string'][] = 'Email wajib diisi!';
            $data['status'] = FALSE;
        }

        if ($this->request->getVar('password') == '') {
            $data['inputerror'][] = 'password';
            $data['error_string'][] = 'Password wajib diisi!';
            $data['status'] = FALSE;
        }
        $db      = \Config\Database::connect();
        $builder = $db->table('tbl_user');
        $login =  $builder->getWhere(['email' => $email, 'password' => md5($password)])->getRow();

        if ($email != '' and $this->request->getVar('password') != '') {
            if ($login != true) {
                $data['inputerror'][] = 'login_username';
                $data['error_string'][] = 'No HP atau Password salah';
                $data['status'] = FALSE;
            }
        }


        if ($data['status'] === FALSE) {
            echo json_encode($data);
            exit();
        }
    }

    public function scan()
    {
        $db = \Config\Database::connect();
        $builder = $db->table('attendance');
        $userBuilder = $db->table('tbl_user');

        $barcode = $this->request->getPost('barcode'); // QR Code data

        // Cari user berdasarkan barcode
        $user = $userBuilder->getWhere(['barcode' => $barcode])->getRow();

        if ($user) {
            // Simpan data absensi
            $data = [
                'user_id' => $user->id,
                'scan_time' => date('Y-m-d H:i:s'),
                'status' => 'present',
            ];
            $builder->insert($data);

            return json_encode([
                'status' => true,
                'message' => 'Absensi berhasil, selamat datang ' . $user->name
            ]);
        } else {
            return json_encode([
                'status' => false,
                'message' => 'QR Code tidak valid atau tidak terdaftar.'
            ]);
        }
    }
}
