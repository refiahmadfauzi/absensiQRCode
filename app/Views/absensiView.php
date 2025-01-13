<?= $this->extend('main_layout'); ?>
<?= $this->section('content'); ?>

<h6 class="mb-0 text-uppercase">Data Absensi</h6>
<hr />
<div class="card">
    <div class="card-body">
        <form action="javascript:;" id="formCari">
            <div class="d-flex align-items-center mb-3">
                <div class="me-3">
                    <label for="dari_tgl" class="form-label">Dari Tanggal</label>
                    <input type="date" name="dari_tgl" id="dari_tgl" class="form-control">
                </div>
                <div class="me-3">
                    <label for="sampai_tgl" class="form-label">Sampai Tanggal</label>
                    <input type="date" name="sampai_tgl" id="sampai_tgl" class="form-control">
                </div>
                <div class="">
                    <button type="submit" id="btnCari" class="btn btn-primary">Cari data</button>
                    <button type="button" class="btn btn-warning" onclick="resetcari()">Reset data</button>
                </div>
            </div>
        </form>
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

    $(document).on('submit', '#formCari', function(event) {
        $('#btnCari').text('Mencari data...'); //change button text
        $('#btnCari').attr('disabled', true); //set button disable 
        var url;

        url = "<?php echo site_url('absensi/cari_data') ?>";

        // ajax adding data to database

        var formData = new FormData($('#formCari')[0]);
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
                $('#btnCari').text('Cari Data'); //change button text
                $('#btnCari').attr('disabled', false); //set button enable 


            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error adding / update data');
                $('#btnCari').text('Cari Data'); //change button text
                $('#btnCari').attr('disabled', false); //set button enable 

            }
        });
    });

    function resetcari() {
        window.setTimeout(function() {
            location.reload(true);
        }, 1000);
    }
</script>
<?= $this->endSection(); ?>