<?= $this->extend('main_layout'); ?>
<?= $this->section('content'); ?>

<h6 class="mb-0 text-uppercase">Data Absensi</h6>
<hr />
<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table id="example2" class="table table-striped table-bordered datatable">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>Absensi</th>
                        <th>Status</th>
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
                            <td><?= date('d-F-Y H:i:s', strtotime($key->scan_time)); ?></td>
                            <td><?= $key->status; ?></td>
                        </tr>
                    <?php }; ?>
                </tbody>
                <tfoot>
                    <tr>
                        <th>No</th>
                        <th>Name</th>
                        <th>Absensi</th>
                        <th>Status</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>


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