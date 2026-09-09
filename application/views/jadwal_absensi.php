<div class="container-fluid">

    <div class="card">

        <div class="card-header">
            Absensi Bulan ini
        </div>

        <div class="card-body">

            <div class="table-responsive">
                                <table class="table table-bordered datatable" width="100%" cellspacing="0">

                <thead>

                    <tr>
                        <th>Tanggal</th>
						<th>Pin</th>
                        <th>Jam Datang</th>
                        <th>Jam Keluar</th>
                    </tr>

                </thead>

                <tbody>

                <?php foreach($absensi as $r){ ?>

                    <tr>

                        <td>
                            <?= $r->tanggal_jadwal;?>
                        </td>

                        <td>
                            <?= $r->id_pegawai;?>
                        </td>

                        <td>
                            <?= $r->jam_datang;?>
                        </td>
						<td>
                            <?= $r->jam_keluar;?>
                        </td>

                    </tr>

                <?php } ?>

                </tbody>

            </table>

        </div>
		</div>

    </div>

</div>