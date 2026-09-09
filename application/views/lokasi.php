<div class="card shadow mb-4">

    <div class="card-header py-3">

        <button
            class="btn btn-primary"
            data-toggle="modal"
            data-target="#modalTambah">

            <i class="fas fa-plus"></i>
            Tambah Lokasi

        </button>

    </div>

    <div class="card-body">

        <div class="table-responsive">

            <table
                class="table table-bordered datatable">

                <thead>

                    <tr>

                        <th width="5%">No</th>

                        <th>Nama Lokasi</th>

                        <th>Latitude</th>

                        <th>Longitude</th>

                        <th>Radius</th>

                        <th>Status</th>

                        <th width="10%">Aksi</th>

                    </tr>

                </thead>

                <tbody>

                    <?php $no=1; foreach($lokasi as $row){ ?>

                    <tr>

                        <td><?= $no++ ?></td>

                        <td><?= $row->nama_lokasi ?></td>

                        <td><?= $row->latitude ?></td>

                        <td><?= $row->longitude ?></td>

                        <td><?= $row->radius ?> Meter</td>

                        <td>

                            <?php if($row->status=='Aktif'){ ?>

                                <span class="badge badge-success">
                                    Aktif
                                </span>

                            <?php }else{ ?>

                                <span class="badge badge-danger">
                                    Tidak Aktif
                                </span>

                            <?php } ?>

                        </td>

                        <td>

    <button
        class="btn btn-warning btn-sm"
        data-toggle="modal"
        data-target="#edit<?= $row->id ?>">

        <i class="fas fa-edit"></i>

    </button>

    <a
        href="<?= base_url('lokasi/hapus/'.$row->id) ?>"
        onclick="return confirm('Hapus lokasi ini?')"
        class="btn btn-danger btn-sm">

        <i class="fas fa-trash"></i>

    </a>

</td>

                    </tr>

                    <?php } ?>

                </tbody>

            </table>

        </div>

    </div>

</div>


<!-- MODAL TAMBAH -->

<div
    class="modal fade"
    id="modalTambah"
    tabindex="-1">

    <div class="modal-dialog modal-lg">

        <form
            action="<?= base_url('lokasi/simpan') ?>"
            method="post">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title">

                        Tambah Lokasi Absensi

                    </h5>

                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal">

                        <span>&times;</span>

                    </button>

                </div>

                <div class="modal-body">

                    <div class="row">

                        <div class="col-md-6">

                            <div class="form-group">

                                <label>Nama Lokasi</label>

                                <input
                                    type="text"
                                    name="nama_lokasi"
                                    class="form-control"
                                    required>

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="form-group">

                                <label>Radius (Meter)</label>

                                <select
                                    name="radius"
                                    class="form-control">

                                    <option value="25">
                                        25 Meter
                                    </option>

                                    <option value="50">
                                        50 Meter
                                    </option>

                                    <option value="100" selected>
                                        100 Meter
                                    </option>

                                    <option value="200">
                                        200 Meter
                                    </option>

                                    <option value="500">
                                        500 Meter
                                    </option>

                                </select>

                            </div>

                        </div>

                    </div>


                    <div class="form-group">

                        <button
                            type="button"
                            class="btn btn-info"
                            onclick="ambilLokasi()">

                            <i class="fas fa-map-marker-alt"></i>

                            Ambil Lokasi Saya

                        </button>

                    </div>

                    <div id="status_gps"></div>


                    <div class="row">

                        <div class="col-md-6">

                            <div class="form-group">

                                <label>Latitude</label>

                                <input
                                    type="text"
                                    name="latitude"
                                    id="latitude"
                                    class="form-control"
                                    required>

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="form-group">

                                <label>Longitude</label>

                                <input
                                    type="text"
                                    name="longitude"
                                    id="longitude"
                                    class="form-control"
                                    required>

                            </div>

                        </div>

                    </div>


                    <div class="form-group">

                        <label>Status</label>

                        <select
                            name="status"
                            class="form-control">

                            <option value="Aktif">
                                Aktif
                            </option>

                            <option value="Tidak Aktif">
                                Tidak Aktif
                            </option>

                        </select>

                    </div>


                    <div
                        id="preview_maps"
                        style="display:none">

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="button"
                        class="btn btn-secondary"
                        data-dismiss="modal">

                        Tutup

                    </button>

                    <button
                        type="submit"
                        class="btn btn-success">

                        Simpan

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>


<?php foreach($lokasi as $row){ ?>

<div
    class="modal fade"
    id="edit<?= $row->id ?>">

    <div class="modal-dialog modal-lg">

        <form
            action="<?= base_url('lokasi/update/'.$row->id) ?>"
            method="post">

            <div class="modal-content">

                <div class="modal-header">

                    <h5>Edit Lokasi</h5>

                    <button
                        type="button"
                        class="close"
                        data-dismiss="modal">

                        <span>&times;</span>

                    </button>

                </div>

                <div class="modal-body">

                    <div class="form-group">

                        <label>Nama Lokasi</label>

                        <input
                            type="text"
                            name="nama_lokasi"
                            class="form-control"
                            value="<?= $row->nama_lokasi ?>"
                            required>

                    </div>

                    <div class="row">
					<button
    type="button"
    class="btn btn-info btn-sm"
    onclick="ambilLokasiEdit(<?= $row->id ?>)">

    Ambil Lokasi Saya

</button>

                        <div class="col-md-6">

                            <div class="form-group">

                                <label>Latitude</label>

                                <input
    type="text"
    id="latitude<?= $row->id ?>"
    name="latitude"
    value="<?= $row->latitude ?>">

                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="form-group">

                                <label>Longitude</label>

                                <input
    type="text"
    id="longitude<?= $row->id ?>"
    name="longitude"
    value="<?= $row->longitude ?>">

                            </div>

                        </div>

                    </div>

                    <div class="form-group">

                        <label>Radius</label>

                        <input
                            type="number"
                            name="radius"
                            class="form-control"
                            value="<?= $row->radius ?>"
                            required>

                    </div>

                    <div class="form-group">

                        <label>Status</label>

                        <select
                            name="status"
                            class="form-control">

                            <option
                                value="Aktif"
                                <?= ($row->status=='Aktif') ? 'selected' : '' ?>>

                                Aktif

                            </option>

                            <option
                                value="Tidak Aktif"
                                <?= ($row->status=='Tidak Aktif') ? 'selected' : '' ?>>

                                Tidak Aktif

                            </option>

                        </select>

                    </div>

                </div>

                <div class="modal-footer">

                    <button
                        type="submit"
                        class="btn btn-success">

                        Update

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>

<?php } ?>