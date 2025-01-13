<!--sidebar wrapper -->
<div class="sidebar-wrapper" data-simplebar="true">
    <div class="sidebar-header">
        <div>
            <img src="<?= base_url('assets/pt-kahatex-logo.jpeg'); ?>" class="img-fluid" alt="logo icon">
        </div>
        <div class="toggle-icon ms-auto"><i class='bx bx-arrow-back'></i>
        </div>
    </div>
    <!--navigation-->
    <ul class="metismenu" id="menu">
        <li>
            <a href="/dashboard">
                <div class="parent-icon"><i class='bx bx-home-alt'></i>
                </div>
                <div class="menu-title">Dashboard</div>
            </a>
        </li>
        <?php if (session('type') == 'Admin') { ?>
            <li>
                <a href="/user">
                    <div class="parent-icon"><i class='bx bx-user'></i>
                    </div>
                    <div class="menu-title">User Management</div>
                </a>
            </li>
        <?php }; ?>
        <li>
            <a href="/absensi">
                <div class="parent-icon"><i class='bx bx-notepad'></i>
                </div>
                <div class="menu-title">Absensi</div>
            </a>
        </li>
        <li>
            <a href="/pengajuan">
                <div class="parent-icon"><i class='bx bx-notepad'></i>
                </div>
                <div class="menu-title">Pengajuan Cuti & Sakit</div>
            </a>
        </li>

    </ul>
    <!--end navigation-->
</div>
<!--end sidebar wrapper -->