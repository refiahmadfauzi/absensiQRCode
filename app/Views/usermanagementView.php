<?= $this->extend('main_layout'); ?>
<?= $this->section('content'); ?>

<h6 class="mb-0 text-uppercase">Data User Management</h6>
<hr />
<div class="card">
    <div class="card-body">
        <div class="text-end my-2">
            <button type="button" onclick="add()" class="btn btn-outline-success px-5"><i class="bx bx-plus mr-1"></i>Add</button>
        </div>
        <div class="table-responsive">
            <table id="example2" class="table table-striped table-bordered datatable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Profile</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Tempat, Tanggal Lahir</th>
                        <th>Jenis Kelamin</th>
                        <th>Agama</th>
                        <th>No KTP</th>
                        <th>No HP</th>
                        <th>Pendidikan Terakhir</th>
                        <th>Created At</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $no = 0;
                    foreach ($users as $key) {
                        $no++;
                    ?>
                        <tr>
                            <td><?= $no; ?></td>
                            <td><img src="<?= base_url('assets/uploads/') . $key->image; ?>" class="user-img" alt="user avatar"></td>
                            <td><?= $key->name; ?></td>
                            <td><?= $key->email; ?></td>
                            <td><?= $key->type_name; ?></td>
                            <td><span class="badge rounded-pill <?= $key->status == 1 ? 'bg-success' : 'bg-secondary'; ?>"><?= $key->status == 1 ? 'Aktif' : 'Non-Aktif'; ?></span></td>
                            <td><?= $key->tempat_lahir . ', ' . $key->tanggal_lahir; ?></td>
                            <td><?= $key->jenis_kelamin; ?></td>
                            <td><?= $key->agama; ?></td>
                            <td><?= $key->no_ktp; ?></td>
                            <td><?= $key->no_hp; ?></td>
                            <td><?= $key->pendidikan_terakhir; ?></td>
                            <td><?= date('d-M-Y', strtotime($key->create_at)); ?></td>
                            <td>
                                <button type="button" class="btn btn-outline-primary" onclick="edit(<?= $key->id; ?>)"><i class="bx bx-edit-alt me-0"></i></button>
                                <button type="button" class="btn btn-outline-danger" onclick="hapus(<?= $key->id; ?>)"><i class="bx bx-trash me-0"></i></button>
                            </td>
                        </tr>
                    <?php }; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>No</th>
                        <th>Profile</th>
                        <th>Name</th>
                        <th>Email</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th>Tempat, Tanggal Lahir</th>
                        <th>Jenis Kelamin</th>
                        <th>Agama</th>
                        <th>No KTP</th>
                        <th>No HP</th>
                        <th>Pendidikan Terakhir</th>
                        <th>Create At</th>
                        <th>Action</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

<!-- Bootstrap modal -->
<div class="modal fade" id="modal_form" role="dialog">
    <div class="modal-dialog modal-lg">
        <div class=" modal-content">
            <form class="row g-3 needs-validation" novalidate id="formAdd" action="javascript:;">
                <div class="modal-header">
                    <h3 class="modal-title">Person Form</h3>
                </div>
                <div class="modal-body form">
                    <div class="col-md-12 mb-3">
                        <label for="fullname" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="fullname" name="name" placeholder="Full Name" required>
                        <input type="hidden" class="form-control" id="iduser" name="iduser" required>
                        <div class="valid-feedback">
                            Looks good!
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="Email" required>
                        <div class="invalid-feedback">
                            Please provide a valid email.
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="password" class="form-label">Password</label>
                        <input type="password" class="form-control" id="password" name="password" placeholder="Password" required>
                        <div class="invalid-feedback">
                            Please choose a password.
                        </div>
                        <small class="text-danger" id="showgantipw">Silahkan isi untuk ganti password</small>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="type" class="form-label">Type User</label>
                        <select id="type" class="form-select" required name="type">
                            <option value="">Pilih</option>
                            <?php foreach ($type_users as $key) { ?>
                                <option value="<?= $key->id; ?>"><?= $key->name; ?></option>
                            <?php }; ?>
                        </select>
                        <div class="invalid-feedback">
                            Please select a valid Type User.
                        </div>
                    </div>
                    <div class="col-md-12 mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select id="status" class="form-select" required name="status">
                            <option value="1">Aktif</option>
                            <option value="2">Non-Aktif</option>
                        </select>
                        <div class="invalid-feedback">
                            Please select a valid Type User.
                        </div>
                    </div>
                    <hr>
                    <div class="row">
                        <h4 class="text-center">Profile</h4>
                        <div class="col-md-12 mb-3">
                            <label for="image" class="form-label">Image Profile</label>
                            <input type="file" class="form-control" id="image" name="image" accept="image/png, image/jpg, image/jpeg" required>
                            <div class="invalid-feedback">
                                Please choose a image.
                            </div>
                            <small class="text-danger" id="showgantiimg">Silahkan isi untuk ganti iamge</small>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tempat_lahir" class="form-label">Tempat Lahir</label>
                            <input type="text" class="form-control" id="tempat_lahir" name="tempat_lahir" placeholder="Tempat lahir" required>
                            <div class="valid-feedback">
                                Looks good!
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="tanggal_lahir" class="form-label">Tanggal Lahir</label>
                            <input type="date" class="form-control" id="tanggal_lahir" name="tanggal_lahir" required>
                            <div class="valid-feedback">
                                Looks good!
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label for="jk" class="form-label">Jenis Kelamin</label>
                            <br />
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jk" id="Laki-laki" value="Laki-laki" required>
                                <label class="form-check-label" for="Laki-laki">Laki-laki</label>
                            </div>
                            <div class="form-check form-check-inline">
                                <input class="form-check-input" type="radio" name="jk" id="Perempuan" value="Perempuan" required>
                                <label class="form-check-label" for="Perempuan">Perempuan</label>
                            </div>
                        </div>
                        <div class="col-md-6 mb-3">
                            <select class="form-select" name="agama" id="agama">
                                <option value="">Pilih Agama</option>
                                <option value="Islam">Islam</option>
                                <option value="Hindu">Hindu</option>
                                <option value="Budha">Budha</option>
                                <option value="Kristen">Kristen</option>
                                <option value="Katolik">Katolik</option>
                            </select>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="no_ktp" class="form-label">No KTP</label>
                            <input type="text" class="form-control" id="no_ktp" name="no_ktp" required>
                            <div class="valid-feedback">
                                Looks good!
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="no_hp" class="form-label">No HP</label>
                            <input type="text" class="form-control" id="no_hp" name="no_hp" required>
                            <div class="valid-feedback">
                                Looks good!
                            </div>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label for="pendidikan" class="form-label">Pendidikan Terakhir</label>
                            <select class="form-select" name="pendidikan" id="pendidikan">
                                <option value="">Pilih Pendidikan</option>
                                <option value="SD">SD</option>
                                <option value="SMP/MTS">SMP/MTS</option>
                                <option value="SMA/SMK">SMA/SMK</option>
                                <option value="D3">D3</option>
                                <option value="S1">S1</option>
                                <option value="S2">S2</option>
                                <option value="S3">S3</option>
                            </select>
                            <div class="valid-feedback">
                                Looks good!
                            </div>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label for="alamat" class="form-label">Alamat</label>
                            <textarea name="alamat" id="alamat" class="form-control"></textarea>
                            <div class="valid-feedback">
                                Looks good!
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="submit" id="btnSave" class="btn btn-primary">Simpan</button>
                    <button type="button" class="btn btn-danger" onclick="tutup()">Cancel</button>
                </div>
            </form>
        </div><!-- /.modal-content -->
    </div><!-- /.modal-dialog -->
</div><!-- /.modal -->

<script src="<?= base_url('assets/temp/'); ?>assets/js/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.8/dist/sweetalert2.all.min.js"></script>
<script>
    var save_method; //for save method string
    var table;
    var base_url = '<?php echo base_url(); ?>';


    function add() {
        save_method = 'add';
        $('#formAdd')[0].reset(); // reset form on modals
        $('#modal_form').modal('show'); // show bootstrap modal
        $('.modal-title').text('Form User'); // Set Title to Bootstrap modal title
        $('#showgantipw').hide();
        $('#showgantiimg').hide();
    }

    function tutup() {
        save_method = 'add';
        $('#formAdd')[0].reset(); // reset form on modals
        $('#modal_form').modal('hide'); // show bootstrap modal
        $('.modal-title').text('Form User'); // Set Title to Bootstrap modal title
    }

    $(document).on('submit', '#formAdd', function(event) {
        $('#btnSave').text('saving...'); //change button text
        $('#btnSave').attr('disabled', true); //set button disable 
        var url;

        if (save_method == 'add') {
            url = "<?php echo site_url('user/create') ?>";
        } else {
            url = "<?php echo site_url('user/update') ?>";
        }

        // ajax adding data to database

        var formData = new FormData($('#formAdd')[0]);
        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            dataType: "JSON",
            success: function(data) {
                const Toast = Swal.mixin({
                    toast: true,
                    position: 'top-end',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true,
                    didOpen: (toast) => {
                        toast.addEventListener('mouseenter', Swal.stopTimer)
                        toast.addEventListener('mouseleave', Swal.resumeTimer)
                    }
                })

                if (data.status) //if success close modal and reload ajax table
                {

                    Toast.fire({
                        icon: 'success',
                        title: data.notif
                    })
                    $('#modal_form').modal('hide');
                    window.setTimeout(function() {
                        location.reload(true);
                    }, 1000);
                } else {
                    for (var i = 0; i < data.inputerror.length; i++) {
                        $('[name="' + data.inputerror[i] + '"]').addClass('is-invalid'); //select parent twice to select div form-group class and add has-error class
                        // $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]); //select span help-block class set text error string
                        Toast.fire({
                            icon: 'warning',
                            title: data.error_string[i]
                        })
                    }
                }
                $('#btnSave').text('Simpan'); //change button text
                $('#btnSave').attr('disabled', false); //set button enable 


            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error adding / update data');
                $('#btnSave').text('Simpan'); //change button text
                $('#btnSave').attr('disabled', false); //set button enable 

            }
        });
    });


    function edit(id) {
        save_method = 'update';
        $('#formAdd')[0].reset(); // reset form on modals

        //Ajax Load data from ajax
        $.ajax({
            url: "<?php echo site_url('user/getbyid') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {

                $('[name="iduser"]').val(data.id);
                $('[name="name"]').val(data.name);
                $('[name="email"]').val(data.email);
                $('[name="type"]').val(data.type);
                $('[name="status"]').val(data.status);
                $('[name="tempat_lahir"]').val(data.tempat_lahir);
                $('[name="tanggal_lahir"]').val(data.tanggal_lahir);
                // Misalkan nilai data.jenis_kelamin adalah "Laki-laki" atau "Perempuan"
                $('[name="jk"][value="' + data.jenis_kelamin + '"]').prop('checked', true);

                $('[name="agama"]').val(data.agama);
                $('[name="no_ktp"]').val(data.no_ktp);
                $('[name="no_hp"]').val(data.no_hp);
                $('[name="pendidikan"]').val(data.pendidikan_terakhir);
                $('[name="alamat"]').val(data.alamat);
                $('[name="password"]').next().empty();
                $('[name="password"]').removeAttr('required');
                $('[name="image"]').removeAttr('required');
                $('#showgantipw').show();
                $('#showgantiimg').show();
                $('#modal_form').modal('show'); // show bootstrap modal when complete loaded
                $('.modal-title').text('Edit User'); // Set title to Bootstrap modal title

            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error get data from ajax');
            }
        });
    };

    function hapus(id) {
        Swal.fire({
            title: 'Apakah anda yakin?',
            text: "Menghapus data berarti data akan hilang!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "<?php echo site_url('user/delete') ?>/" + id,
                    type: "POST",
                    dataType: "JSON",
                    success: function(data) {
                        //if success reload ajax table
                        const Toast = Swal.mixin({
                            toast: true,
                            position: 'top-end',
                            showConfirmButton: false,
                            timer: 3000,
                            timerProgressBar: true,
                            didOpen: (toast) => {
                                toast.addEventListener('mouseenter', Swal.stopTimer)
                                toast.addEventListener('mouseleave', Swal.resumeTimer)
                            }
                        })
                        if (data.status == true) //if success close modal and reload ajax table
                        {

                            Toast.fire({
                                icon: 'success',
                                title: data.message
                            });
                            window.setTimeout(function() {
                                location.reload(true);
                            }, 1000);
                        } else {
                            for (var i = 0; i < data.inputerror.length; i++) {
                                $('[name="' + data.inputerror[i] + '"]').addClass('is-invalid'); //select parent twice to select div form-group class and add has-error class
                                // $('[name="' + data.inputerror[i] + '"]').next().text(data.error_string[i]); //select span help-block class set text error string
                                Toast.fire({
                                    icon: 'warning',
                                    title: data.error_string[i]
                                })
                            }
                        }
                    },
                    error: function(jqXHR, textStatus, errorThrown) {
                        alert('Error deleting data');
                    }
                });
            }
        })
    }
</script>
<?= $this->endSection(); ?>