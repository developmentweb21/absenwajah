<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <meta name="viewport"
          content="width=device-width, initial-scale=1, shrink-to-fit=no">

    <title>
        <?= isset($title) ? $title : 'Absensi Mobile'; ?>
    </title>

    <!-- Font Awesome -->
    <link href="<?= base_url('assets/vendor/fontawesome-free/css/all.min.css'); ?>"
          rel="stylesheet">

    <!-- SB Admin -->
    <link href="<?= base_url('assets/css/sb-admin-2.min.css'); ?>"
          rel="stylesheet">

    <!-- Datatables -->
    <link href="<?= base_url('assets/vendor/datatables/dataTables.bootstrap4.min.css'); ?>"
          rel="stylesheet">

	<style>
@media (max-width: 768px){

    .sidebar{
        display:none !important;
    }

    #content-wrapper{
        margin-left:0 !important;
        width:100% !important;
    }

}
body{
    padding-bottom:80px;
}


.mobile-bottom-menu{

    display:none;

}

@media(max-width:768px){

    .mobile-bottom-menu{

        position:fixed;

        bottom:0;

        left:0;

        width:100%;

        height:65px;

        background:#fff;

        border-top:1px solid #ddd;

        z-index:9999;

        display:flex;

        justify-content:space-around;

        align-items:center;

        box-shadow:0 -2px 10px rgba(0,0,0,.1);

    }

    .mobile-bottom-menu a{

        text-decoration:none;

        color:#555;

        display:flex;

        flex-direction:column;

        align-items:center;

        font-size:12px;

    }

    .mobile-bottom-menu i{

        font-size:20px;

        margin-bottom:4px;

    }

}

</style>

</head>

<body id="page-top">

<div id="wrapper">

    <!-- Sidebar -->

    <ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion"
        id="accordionSidebar">

        <a class="sidebar-brand d-flex align-items-center justify-content-center"
           href="<?= base_url('dashboard'); ?>">

            <div class="sidebar-brand-icon">
                <i class="fas fa-fingerprint"></i>
            </div>

            <div class="sidebar-brand-text mx-2">
                ABSENSI
            </div>

        </a>

        <hr class="sidebar-divider">

        <li class="nav-item">

            <a class="nav-link"
               href="<?= base_url('dashboard'); ?>">

                <i class="fas fa-home"></i>

                <span>Dashboard</span>

            </a>

        </li>

        <li class="nav-item">

            <a class="nav-link"
               href="<?= base_url('absensi/jadwal'); ?>">

                <i class="fas fa-history"></i>

                <span>Riwayat</span>

            </a>

        </li>

        <li class="nav-item">

            <a class="nav-link"
               href="<?= base_url('absensi/riwayat'); ?>">

                <i class="fas fa-history"></i>

                <span>Log Absensi</span>

            </a>

        </li>
		<li class="nav-item">

    <a class="nav-link"
       href="<?= base_url('lokasi'); ?>">

        <i class="fas fa-map-marker-alt"></i>

        <span>Lokasi Absensi</span>

    </a>

</li>

        <hr class="sidebar-divider">

        <li class="nav-item">

            <a class="nav-link"
               href="<?= base_url('logout'); ?>">

                <i class="fas fa-sign-out-alt"></i>

                <span>Logout</span>

            </a>

        </li>

    </ul>

    <!-- End Sidebar -->

    <div id="content-wrapper"
         class="d-flex flex-column">

        <div id="content">

            <!-- Topbar -->

            <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

                <button id="sidebarToggleTop"
                        class="btn btn-link d-md-none rounded-circle mr-3">

                    <i class="fa fa-bars"></i>

                </button>

                <ul class="navbar-nav ml-auto">

                    <li class="nav-item dropdown no-arrow">

                        <a class="nav-link dropdown-toggle"
                           href="#">

                            <span class="mr-2 d-none d-lg-inline text-gray-600 small">

                                <?= $this->session->userdata('nama'); ?>

                            </span>

                        </a>

                    </li>

                </ul>

            </nav>

            <!-- Begin Page Content -->

            <div class="container-fluid"></div>