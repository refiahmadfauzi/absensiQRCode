<?= $this->extend('main_layout'); ?>
<?= $this->section('content'); ?>

<div class="row">
    <div class="col-xl-8 mx-auto">
        <div class="card">
            <div class="card-header px-4 py-3">
                <h5 class="mb-0">Form Add User</h5>
            </div>
            <div class="card-body p-4">
                <form class="row g-3 needs-validation" novalidate id="formAdd">
                    <div class="col-md-12">
                        <label for="bsValidation1" class="form-label">Full Name</label>
                        <input type="text" class="form-control" id="bsValidation1" name="name" placeholder="Full Name" required>
                        <div class="valid-feedback">
                            Looks good!
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label for="bsValidation4" class="form-label">Email</label>
                        <input type="email" class="form-control" id="bsValidation4" name="email" placeholder="Email" required>
                        <div class="invalid-feedback">
                            Please provide a valid email.
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label for="bsValidation5" class="form-label">Password</label>
                        <input type="password" class="form-control" id="bsValidation5" name="password" placeholder="Password" required>
                        <div class="invalid-feedback">
                            Please choose a password.
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label for="bsValidation9" class="form-label">Type User</label>
                        <select id="bsValidation9" class="form-select" required name="type">
                            <?php foreach ($type_users as $key) { ?>
                                <option value="<?= $key->id; ?>"><?= $key->name; ?></option>
                            <?php }; ?>
                        </select>
                        <div class="invalid-feedback">
                            Please select a valid Type User.
                        </div>
                    </div>
                    <div class="col-md-12">
                        <label for="image" class="form-label">Image Profile</label>
                        <input type="file" class="form-control" id="image" name="image" accept="image/png, image/jpg, image/jpeg" required>
                        <div class="invalid-feedback">
                            Please choose a image.
                        </div>
                    </div>


                    <div class="col-md-12">
                        <div class="d-md-flex d-grid align-items-center gap-3">
                            <button type="submit" class="btn btn-primary px-4" id="btnAdd">Submit</button>
                            <button type="reset" class="btn btn-light px-4">Reset</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/temp/'); ?>assets/js/jquery.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11.4.8/dist/sweetalert2.all.min.js"></script>
<script>
    $(document).on('submit', '#formAdd', function(event) {
        event.preventDefault(); // Prevent form default submission
        $('#btnAdd').text('saving...'); // Change button text
        $('#btnAdd').attr('disabled', true); // Disable button

        var url = "<?php echo site_url('user/create') ?>";

        // Ajax adding data to database
        var formData = new FormData($('#formAdd')[0]);
        $.ajax({
            url: url,
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            dataType: "JSON",
            success: function(data) {
                if (data.status == true) {
                    // Redirect to dashboard on success
                    window.location.replace("user");
                } else {
                    // Display error messages
                    const Toast = Swal.mixin({
                        toast: true,
                        position: 'top-end',
                        showConfirmButton: false,
                        timer: 3000,
                        timerProgressBar: true,
                        didOpen: (toast) => {
                            toast.addEventListener('mouseenter', Swal.stopTimer);
                            toast.addEventListener('mouseleave', Swal.resumeTimer);
                        }
                    });

                    for (var i = 0; i < data.inputerror.length; i++) {
                        $('[name="' + data.inputerror[i] + '"]').addClass('is-invalid'); // Highlight invalid fields
                        Toast.fire({
                            icon: 'warning',
                            title: data.error_string[i]
                        });
                    }
                }

                $('#btnAdd').text('Submit'); // Reset button text
                $('#btnAdd').attr('disabled', false); // Enable button
            },
            error: function(jqXHR, textStatus, errorThrown) {
                alert('Error adding / update data');
                $('#btnAdd').text('Submit'); // Reset button text
                $('#btnAdd').attr('disabled', false); // Enable button
            }
        });
    });
</script>

<?= $this->endSection(); ?>