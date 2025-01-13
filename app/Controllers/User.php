<?php

namespace App\Controllers;

class User extends BaseController
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
        $builder->select('tbl_user.*, tbl_type_user.name AS type_name');
        $builder->join('tbl_type_user', 'tbl_user.type = tbl_type_user.id');
        $data = $builder->get()->getResult();

        $builder_type = $db->table('tbl_type_user');
        $data_type = $builder_type->get()->getResult(); // Mengambil semua data dari tabel tbl_user

        return view('usermanagementView', ['users' => $data, 'type_users' => $data_type]); // Mengirimkan data ke view

    }


    public function create()
    {
        $session = session();

        $file = $this->request->getFile('image');
        $newName = $file->getRandomName(); // Generate nama file acak
        $file->move('assets/uploads', $newName); // Simpan file ke folder uploads

        // Data untuk disimpan ke database
        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email'),
            'password' => md5($this->request->getPost('password')),
            'type' => $this->request->getPost('type'),
            'image' => $newName,
            'status' => $this->request->getPost('status'),
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

        $db = \Config\Database::connect();
        $builder = $db->table('tbl_user');
        $builder->insert($data);

        echo json_encode(array("status" => TRUE, "notif" => "Data berhasil ditambahkan"));
    }

    public function getbyid($id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('tbl_user');

        // Mengambil data berdasarkan ID
        $user = $builder->where('id', $id)->get()->getRow();
        echo json_encode($user);
    }

    public function update()
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
            'type' => $this->request->getPost('type'),
            'status' => $this->request->getPost('status'),
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

    public function delete($id)
    {
        $db = \Config\Database::connect();
        $builder = $db->table('tbl_user');

        // Cari pengguna berdasarkan ID
        $user = $builder->getWhere(['id' => $id])->getRow();

        if ($user) {
            // Hapus file gambar jika ada
            $imagePath = 'assets/uploads/' . $user->image;
            if (is_file($imagePath)) {
                unlink($imagePath); // Hapus file
            }

            // Hapus data dari database
            $builder->where('id', $id);
            $builder->delete();

            return json_encode([
                'status' => true,
                'message' => 'Akun berhasil dihapus.'
            ]);
        } else {
            return json_encode([
                'status' => false,
                'message' => 'Pengguna tidak ditemukan.'
            ]);
        }
    }
}
