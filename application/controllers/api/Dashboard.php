<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'core/API_Controller.php';

class Dashboard extends API_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('api/Api_dashboard_model');
    }

    /**
     * GET /api/dashboard
     */
    public function index()
    {
        if (!$this->require_auth()) {
            return;
        }

        $id_pegawai = isset($this->api_user['id_pegawai'])
            ? (int) $this->api_user['id_pegawai']
            : 0;

        if ($id_pegawai <= 0) {
            return $this->json_response(
                FALSE,
                'Identitas pegawai tidak ditemukan.',
                array(
                    'code' => 'PEGAWAI_NOT_FOUND'
                ),
                404
            );
        }

        /*
         * Gunakan waktu server.
         */
        $tanggal = date('Y-m-d');
        $server_time = date('Y-m-d H:i:s');

        /*
         * ============================
         * PEGAWAI
         * ============================
         */
        $pegawai = $this->Api_dashboard_model
            ->get_pegawai($id_pegawai);

        if (!$pegawai) {
            return $this->json_response(
                FALSE,
                'Data pegawai tidak ditemukan.',
                array(
                    'code' => 'PEGAWAI_NOT_FOUND'
                ),
                404
            );
        }

        /*
         * ============================
         * JADWAL
         * ============================
         */
        $rows = $this->Api_dashboard_model
            ->get_jadwal_hari_ini(
                $id_pegawai,
                $tanggal
            );

        $jadwal = array();

        foreach ($rows as $row) {

            $has_in = !empty($row->jam_datang);
            $has_out = !empty($row->jam_keluar);

            if ($has_in && $has_out) {
                $status = 'COMPLETE';
            } elseif ($has_in) {
                $status = 'CHECKED_IN';
            } else {
                $status = 'NOT_CHECKED_IN';
            }

            $jadwal[] = array(
                'kd_jadwal' => (int) $row->kd_jadwal,

                'shift' => array(
                    'kode' => $row->kd_standarisasi,
                    'nama' => $row->STANDARISASI,
                    'jam_masuk' => $row->JAM_MASUK,
                    'jam_pulang' => $row->JAM_PULANG,
                    'toleransi' => $row->TOLERANSI,
                    'pendek' => $row->PENDEK,
                    'total_jam' => $row->TOTAL_JAM,
                    'jenis_jadwal' => $row->JENIS_JADWAL
                ),

                'actual' => array(
                    'jam_datang' => $row->jam_datang,
                    'jam_keluar' => $row->jam_keluar
                ),

                'status' => $status,

                'keterangan' => $row->keterangan,
                'lembur' => (int) $row->lembur,
                'active' => $row->active
            );
        }

        /*
         * ============================
         * STATUS ABSENSI
         * ============================
         */
        if (empty($jadwal)) {

            $attendance_status = 'NO_SCHEDULE';
        } else {

            $attendance_status = $jadwal[0]['status'];
        }

        /*
         * ============================
         * LOKASI
         * ============================
         */
        $lokasi = $this->Api_dashboard_model
            ->get_lokasi_aktif();

        $lokasi_data = NULL;

        if ($lokasi) {

            $lokasi_data = array(
                'id' => isset($lokasi->id)
                    ? $lokasi->id
                    : NULL,

                'nama' => isset($lokasi->nama_lokasi)
                    ? $lokasi->nama_lokasi
                    : (
                        isset($lokasi->nama)
                        ? $lokasi->nama
                        : NULL
                    ),

                'latitude' => isset($lokasi->latitude)
                    ? $lokasi->latitude
                    : NULL,

                'longitude' => isset($lokasi->longitude)
                    ? $lokasi->longitude
                    : NULL,

                'radius' => isset($lokasi->radius)
                    ? $lokasi->radius
                    : NULL,

                'status' => isset($lokasi->status)
                    ? $lokasi->status
                    : NULL
            );
        }

        /*
         * ============================
         * RESPONSE
         * ============================
         */
        return $this->json_response(
            TRUE,
            'Dashboard berhasil diambil.',
            array(
                'tanggal' => $tanggal,

                'server_time' => $server_time,

                'pegawai' => array(
                    'id_pegawai' => (int) $pegawai->ID_PEGAWAI,
                    'nip' => $pegawai->NIP,
                    'nama' => $pegawai->NAMA,
                    'foto' => $pegawai->FOTO
                ),

                'jadwal' => $jadwal,

                'absensi' => array(
                    'status' => $attendance_status
                ),

                'lokasi' => $lokasi_data
            )
        );
    }
}
