<!doctype html>
<html lang="en">


<!-- Mirrored from codervent.com/rocker/demo/vertical/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 04 May 2023 18:27:07 GMT -->

<head>
    <!-- Required meta tags -->
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!--favicon-->
    <link rel="icon" href="<?= base_url('assets/') ?>fav-logo.jpg" />
    <!--plugins-->
    <link href="<?= base_url('assets/temp/') ?>assets/plugins/vectormap/jquery-jvectormap-2.0.2.css" rel="stylesheet" />
    <link href="<?= base_url('assets/temp/') ?>assets/plugins/simplebar/css/simplebar.css" rel="stylesheet" />
    <link href="<?= base_url('assets/temp/') ?>assets/plugins/perfect-scrollbar/css/perfect-scrollbar.css" rel="stylesheet" />
    <link href="<?= base_url('assets/temp/') ?>assets/plugins/metismenu/css/metisMenu.min.css" rel="stylesheet" />
    <!-- Bootstrap CSS -->
    <link href="<?= base_url('assets/temp/') ?>assets/css/bootstrap.min.css" rel="stylesheet">
    <link href="<?= base_url('assets/temp/') ?>assets/css/bootstrap-extended.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&amp;display=swap" rel="stylesheet">
    <link href="<?= base_url('assets/temp/') ?>assets/css/app.css" rel="stylesheet">
    <link href="<?= base_url('assets/temp/') ?>assets/css/icons.css" rel="stylesheet">
    <link href="<?= base_url('assets/temp/') ?>assets/plugins/datatable/css/dataTables.bootstrap5.min.css" rel="stylesheet" />
    <!-- Theme Style CSS -->
    <link rel="stylesheet" href="<?= base_url('assets/temp/') ?>assets/css/dark-theme.css" />
    <link rel="stylesheet" href="<?= base_url('assets/temp/') ?>assets/css/semi-dark.css" />
    <link rel="stylesheet" href="<?= base_url('assets/temp/') ?>assets/css/header-colors.css" />
    <title>Absensi | PT. Kahatex</title>
</head>

<body>
    <!--wrapper-->
    <div class="wrapper">
        <!-- Sidebar -->
        <?= $this->include('component/sidebarView'); ?>
        <!-- Header -->
        <?= $this->include('component/headerView'); ?>

        <div class="page-wrapper">
            <div class="page-content">
                <?= $this->renderSection('content'); ?>
            </div>
        </div>
        <?= $this->include('component/footerView'); ?>
    </div>

    <!-- Bootstrap JS -->
    <script src="<?= base_url('assets/temp/') ?>assets/js/bootstrap.bundle.min.js"></script>
    <!--plugins-->
    <script src="<?= base_url('assets/temp/') ?>assets/js/jquery.min.js"></script>
    <script src="<?= base_url('assets/temp/') ?>assets/plugins/simplebar/js/simplebar.min.js"></script>
    <script src="<?= base_url('assets/temp/') ?>assets/plugins/metismenu/js/metisMenu.min.js"></script>
    <!-- <script src="<?= base_url('assets/temp/') ?>assets/plugins/perfect-scrollbar/js/perfect-scrollbar.js"></script> -->
    <script src="<?= base_url('assets/temp/') ?>assets/plugins/vectormap/jquery-jvectormap-2.0.2.min.js"></script>
    <script src="<?= base_url('assets/temp/') ?>assets/plugins/vectormap/jquery-jvectormap-world-mill-en.js"></script>
    <script src="<?= base_url('assets/temp/') ?>assets/plugins/chartjs/js/chart.js"></script>
    <!-- <script src="<?= base_url('assets/temp/') ?>assets/js/index.js"></script> -->
    <!--app JS-->
    <script src="<?= base_url('assets/temp/') ?>assets/js/app.js"></script>
    <!-- <script>
        new PerfectScrollbar(".app-container")
    </script> -->

    <script src="<?= base_url('assets/temp/') ?>assets/plugins/datatable/js/jquery.dataTables.min.js"></script>
    <script src="<?= base_url('assets/temp/') ?>assets/plugins/datatable/js/dataTables.bootstrap5.min.js"></script>

    <script>
        $(document).ready(function() {
            var table = $('#example2').DataTable({
                lengthChange: false,
                buttons: ['excel', 'pdf']
            });

            table.buttons().container()
                .appendTo('#example2_wrapper .col-md-6:eq(0)');
        });
    </script>
    <script>
        // Example starter JavaScript for disabling form submissions if there are invalid fields
        (function() {
            'use strict'

            // Fetch all the forms we want to apply custom Bootstrap validation styles to
            var forms = document.querySelectorAll('.needs-validation')

            // Loop over them and prevent submission
            Array.prototype.slice.call(forms)
                .forEach(function(form) {
                    form.addEventListener('submit', function(event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }

                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    </script>

</body>


<!-- Mirrored from codervent.com/rocker/demo/vertical/index.html by HTTrack Website Copier/3.x [XR&CO'2014], Thu, 04 May 2023 18:27:07 GMT -->

</html>