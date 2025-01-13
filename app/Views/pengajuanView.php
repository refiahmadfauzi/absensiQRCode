<?= $this->extend('main_layout'); ?>
<?= $this->section('content'); ?>

<h6 class="mb-0 text-uppercase">Data Management Cuti & Sakit</h6>
<hr />
<div class="card">
    <div class="card-body">
        <?php
        if (session('type') != 'Admin') {
        ?>
            <div class="text-end my-2">
                <button type="button" onclick="add()" class="btn btn-outline-success px-5"><i class="bx bx-plus mr-1"></i>Add</button>
            </div>
        <?php }; ?>

        <div class="table-responsive">
            <table id="example2" class="table table-striped table-bordered datatable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>Pengajuan</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Alasan</th>
                        <th>Dokumen</th>
                        <th>Status</th>
                        <th>Update at</th>
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
                            <td><?= $key->name; ?></td>
                            <td><?= $key->jenis_pengajuan; ?></td>
                            <td><?= date('d-M-Y', strtotime($key->tanggal_mulai)); ?></td>
                            <td><?= date('d-M-Y', strtotime($key->tanggal_selesai)); ?></td>
                            <td><?= $key->alasan; ?></td>
                            <td><a href="<?= base_url('assets/dokumen/') . $key->dokumen; ?>" target="_blank" rel="noopener noreferrer">Buka Dokumen</a></td>
                            <td>
                                <?php if ($key->status == 1) { ?>
                                    <span class="badge rounded-pill bg-primary">
                                        Mengajukan
                                    </span>
                                <?php } elseif ($key->status == 2) { ?>
                                    <span class="badge rounded-pill bg-success">
                                        Di Terima
                                    </span>
                                <?php } elseif ($key->status == 0) { ?>
                                    <span class="badge rounded-pill bg-danger">
                                        Di Tolak
                                    </span>
                                <?php }; ?>
                            </td>
                            <td><?= date('d-M-Y', strtotime($key->created_at)); ?></td>
                            <td>
                                <?php
                                if (session('type') == 'Admin') {
                                    if ($key->status == 1) { ?>
                                        <button type="button" class="btn btn-outline-success" onclick="terima(<?= $key->id; ?>)"><i class="bx bx-check-circle me-0"></i></button>
                                        <button type="button" class="btn btn-outline-danger" onclick="tolak(<?= $key->id; ?>)"><i class="bx bx-x me-0"></i></button>
                                    <?php }
                                    ?>
                                    <?php
                                } else {
                                    if ($key->status == 1) { ?>
                                        <button type="button" class="btn btn-outline-primary" onclick="edit(<?= $key->id; ?>)"><i class="bx bx-edit-alt me-0"></i></button>
                                        <button type="button" class="btn btn-outline-danger" onclick="hapus(<?= $key->id; ?>)"><i class="bx bx-trash me-0"></i></button>
                                <?php };
                                }; ?>
                            </td>
                        </tr>
                    <?php }; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>Pengajuan</th>
                        <th>Tanggal Mulai</th>
                        <th>Tanggal Selesai</th>
                        <th>Alasan</th>
                        <th>Dokumen</th>
                        <th>Status</th>
                        <th>Update At</th>
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
                    <input type="hidden" name="idpengajuan" id="idpengajuan" class="form-control" required>
                    <div class="mb-3">
                        <label for="jenis_pengajuan" class="form-label">Jenis Pengajuan</label>
                        <select name="jenis_pengajuan" id="jenis_pengajuan" class="form-select" required>
                            <option value="Cuti">Cuti</option>
                            <option value="Sakit">Sakit</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_mulai" class="form-label">Tanggal Mulai</label>
                        <input type="date" name="tanggal_mulai" id="tanggal_mulai" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="tanggal_selesai" class="form-label">Tanggal Selesai</label>
                        <input type="date" name="tanggal_selesai" id="tanggal_selesai" class="form-control" required>
                    </div>
                    <div class="mb-3">
                        <label for="alasan" class="form-label">Alasan</label>
                        <textarea name="alasan" id="alasan" class="form-control" rows="3"></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="dokumen" class="form-label">Unggah Dokumen</label>
                        <input type="file" name="dokumen" id="dokumen" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                        <small class="text-danger" id="showgantiimg">Silahkan isi untuk ganti dokumen</small>
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
            url = "<?php echo site_url('pengajuan/create') ?>";
        } else {
            url = "<?php echo site_url('pengajuan/update') ?>";
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
            url: "<?php echo site_url('pengajuan/getbyid') ?>/" + id,
            type: "GET",
            dataType: "JSON",
            success: function(data) {

                $('[name="idpengajuan"]').val(data.id);
                $('[name="jenis_pengajuan"]').val(data.jenis_pengajuan);
                $('[name="tanggal_mulai"]').val(data.tanggal_mulai);
                $('[name="tanggal_selesai"]').val(data.tanggal_selesai);
                $('[name="alasan"]').val(data.alasan);

                $('#showgantiimg').show();
                $('[name="dokumen"]').removeAttr('required');

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
                    url: "<?php echo site_url('pengajuan/delete') ?>/" + id,
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
    };

    function terima(id) {
        Swal.fire({
            title: 'Apakah anda yakin?',
            text: "Terima pengajuan ini, pengajuan tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "<?php echo site_url('pengajuan/terima') ?>/" + id,
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
    };

    function tolak(id) {
        Swal.fire({
            title: 'Apakah anda yakin?',
            text: "Tolak pengajuan ini, pengajuan tidak dapat dikembalikan!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Ya'
        }).then((result) => {
            if (result.isConfirmed) {
                $.ajax({
                    url: "<?php echo site_url('pengajuan/tolak') ?>/" + id,
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