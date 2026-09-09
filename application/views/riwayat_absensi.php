<div class="container-fluid">

    <div class="card">

        <div class="card-header">
            Riwayat Absensi
        </div>

        <div class="card-body">

            <table class="table table-bordered datatable">

                <thead>

                    <tr>
                        <th>Tanggal</th>
                        <th>Mode</th>
                        <th>Status</th>
                    </tr>

                </thead>

                <tbody>

                <?php foreach($riwayat as $r){ ?>

                    <tr>

                        <td>
                            <?= $r->scan_date;?>
                        </td>

                        <td>
                            <?= $r->pin;?>
                        </td>

                        <td>
                            <?= $r->status;?>
                        </td>

                    </tr>

                <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</div>   

