
<div class="container-fluid">

    <div class="card shadow mb-3">
        <div class="card-body">

            <h4>
                Selamat Datang
            </h4>

            <h5>
                <?= $this->session->userdata('nama'); ?>
            </h5>

            <p>
                NIP :
                <?= $this->session->userdata('nip'); ?>
            </p>

</div>
    </div>
<div class="alert alert-info">

    <b>Status Lokasi</b><br>

    <span id="jarak_kantor">
        Mengambil koordinat...
    </span>

</div>

    <div class="row">

        <div class="col-md-6 mb-3">

    <div class="form-group">
    <label>Jam Absensi</label>
    <input type="time"
		step="1"
           id="jam_absen"
           class="form-control"
           value="07:30">
</div>

<button class="btn btn-success"
        onclick="absen('IN')">
    ABSEN
</button>

        </div>

    </div>
	

    <div class="card">
<?php foreach($absensi as $r){ ?>
        <div class="card-header">
            Absensi Hari ini - <?= $r->tanggal_jadwal;?>
        </div>

        <div class="card-body">

            <table class="table table-bordered">

                <thead>

                    <tr>
                       
                        <th>Masuk</th>
                        <th>Pulang</th>
                    </tr>

                </thead>

                <tbody>

                

                    <tr>

                  

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
    </div>


