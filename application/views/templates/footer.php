            </div>
            <!-- /.container-fluid -->

        <footer class="sticky-footer bg-white">

            <div class="container my-auto">

                <div class="copyright text-center my-auto">

                    <span>

                        Copyright &copy;
                        <?= date('Y'); ?>
                        Absensi Mobile

                    </span>

                </div>

            </div>

        </footer>

    </div>

</div>

<!-- Scroll Top -->

<a class="scroll-to-top rounded"
   href="#page-top">

    <i class="fas fa-angle-up"></i>

        </a>

<div class="mobile-bottom-menu">

    <a href="<?= base_url('dashboard');?>">

        <i class="fas fa-home"></i>

        <span>Home</span>

    </a>

    <a href="javascript:void(0)"
       onclick="showMenuAbsen()">

        <i class="fas fa-fingerprint"></i>

        <span>Absen</span>

    </a>

    <a href="<?= base_url('absensi/jadwal');?>">

        <i class="fas fa-history"></i>

        <span>Jadwal</span>

    </a>
    <a href="<?= base_url('absensi/riwayat');?>">

        <i class="fas fa-history"></i>

        <span>Log</span>

    </a>

    <a href="<?= base_url('logout');?>">

        <i class="fas fa-sign-out-alt"></i>

        <span>Keluar</span>

    </a>

</div>

<div class="modal fade"
     id="modalAbsen">

    <div class="modal-dialog">

        <div class="modal-content">

            <div class="modal-header">

                <h5>Absensi</h5>

            </div>

            <div class="modal-body">
			    <div class="form-group">
    <label>Jam Absensi</label>
    <input type="time"
           id="jam_absen"
           class="form-control"
           value="07:30">
</div>

                <button
                    class="btn btn-success btn-block mb-2"
                    onclick="absen('IN')">

                    ABSEN MASUK

                </button>

            </div>

        </div>

    </div>

</div>

<!-- JQuery -->
<script src="<?= base_url('assets/vendor/jquery/jquery.min.js'); ?>"></script>
<!-- Bootstrap -->
<script src="<?= base_url('assets/vendor/bootstrap/js/bootstrap.bundle.min.js'); ?>"></script>
<!-- Core -->
<script src="<?= base_url('assets/vendor/jquery-easing/jquery.easing.min.js'); ?>"></script>
<!-- SB Admin -->
<script src="<?= base_url('assets/js/sb-admin-2.min.js'); ?>"></script>
<!-- Datatables -->
<script src="<?= base_url('assets/vendor/datatables/jquery.dataTables.min.js'); ?>"></script>
<script src="<?= base_url('assets/vendor/datatables/dataTables.bootstrap4.min.js'); ?>"></script>


<script>
$(document).ready(function(){

    $('.datatable').DataTable({
        responsive:true
    });

});

function showMenuAbsen()
{
    $('#modalAbsen').modal('show');
}
</script>

<script>


function absen(mode)
{
	let jam_absen = document.getElementById('jam_absen').value;
    navigator.geolocation.getCurrentPosition(function(pos){

        $.ajax({
            url : "<?= base_url('absensi/simpan') ?>",
            type : "POST",
            data : {
                io_mode : mode,
				jam_absen:jam_absen,
                latitude : pos.coords.latitude,
                longitude : pos.coords.longitude
            },
            dataType : "json",
            success : function(res)
            {
                alert(res.message);

                if(res.status)
                {
                    location.reload();
                }
            }
        });

    });
}

</script>

<script>

function ambilLokasi()
{
    if (!navigator.geolocation)
    {
        alert('GPS tidak didukung');
        return;
    }

    navigator.geolocation.getCurrentPosition(

        function(position)
        {
            const lat = position.coords.latitude;
            const lng = position.coords.longitude;

            document.getElementById('latitude').value = lat;
            document.getElementById('longitude').value = lng;

            const maps = document.getElementById('preview_maps');

            maps.style.display = 'block';

            maps.innerHTML = `
                <iframe
                    width="100%"
                    height="300"
                    frameborder="0"
                    style="border:0"
                    src="https://maps.google.com/maps?q=${lat},${lng}&z=18&output=embed">
                </iframe>
            `;

            document.getElementById('status_gps').innerHTML =
                '<div class="alert alert-success">Lokasi berhasil diperoleh</div>';
        },

        function(error)
        {
            alert(error.message);
        },

        {
            enableHighAccuracy: true,
            timeout: 15000,
            maximumAge: 0
        }

    );
}
function ambilLokasiEdit(id)
{
    navigator.geolocation.getCurrentPosition(
        function(position)
        {
            document.getElementById(
                'latitude'+id
            ).value =
                position.coords.latitude;

            document.getElementById(
                'longitude'+id
            ).value =
                position.coords.longitude;
        }
    );
}

navigator.geolocation.getCurrentPosition(function(pos){

    $.ajax({
        url : "<?= base_url('absensi/cek_jarak') ?>",
        type : "POST",
        data : {
            latitude : pos.coords.latitude,
            longitude : pos.coords.longitude
        },
        dataType : "json",
        success : function(res){

            $('#jarak_kantor').html(
                res.jarak + ' Meter'
            );

        }
    });

});
</script>


</body>
</html>