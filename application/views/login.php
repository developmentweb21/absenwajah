<!DOCTYPE html>
<html lang="id">

<head>

    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Login Absensi Mobile</title>

    <link href="<?= base_url('assets/vendor/fontawesome-free/css/all.min.css'); ?>" rel="stylesheet">

    <link href="<?= base_url('assets/css/sb-admin-2.min.css'); ?>" rel="stylesheet">

</head>

<body class="bg-gradient-primary">

<div class="container">

    <div class="row justify-content-center">

        <div class="col-xl-4 col-lg-5 col-md-6">

            <div class="card o-hidden border-0 shadow-lg my-5">

                <div class="card-body p-4">

                    <div class="text-center mb-4">

                        <h4 class="text-gray-900">
                            LOGIN ABSENSI
                        </h4>

                    </div>

                    <?php if($this->session->flashdata('error')){ ?>

                        <div class="alert alert-danger">

                            <?= $this->session->flashdata('error'); ?>

                        </div>

                    <?php } ?>

                    <form
                        method="post"
                        action="<?= base_url('login/proses'); ?>">

                        <div class="form-group">

                            <label>NIP</label>

                            <input
                                type="text"
                                name="nip"
                                class="form-control"
                                required>

                        </div>

                        <div class="form-group">

                            <label>Password</label>

                            <input
                                type="password"
                                name="password"
                                class="form-control"
                                required>

                        </div>

                        <button
                            type="submit"
                            class="btn btn-primary btn-block">

                            Login

                        </button>

                    </form>

                </div>

            </div>

        </div>

    </div>

</div>

<script src="<?= base_url('assets/vendor/jquery/jquery.min.js'); ?>"></script>

<script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>

</body>
</html>