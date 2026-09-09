<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'core/API_Controller.php';

class Schedule extends API_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('api/Api_schedule_model');
    }

    /**
     * GET /api/schedule/today
     */
    public function today()
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

        $tanggal = date('Y-m-d');

        $rows = $this->Api_schedule_model
            ->get_today($id_pegawai, $tanggal);

        $schedule = array();

        foreach ($rows as $row) {

            $schedule[] = array(
                'kd_jadwal' => (int) $row->kd_jadwal,
                'tanggal'   => $row->tanggal_jadwal,

                'shift' => array(
                    'kode'        => $row->kd_standarisasi,
                    'nama'        => $row->STANDARISASI,
                    'jam_masuk'   => $row->JAM_MASUK,
                    'jam_pulang'  => $row->JAM_PULANG,
                    'toleransi'   => $row->TOLERANSI,
                    'pendek'      => $row->PENDEK,
                    'total_jam'   => $row->TOTAL_JAM,
                    'jenis_jadwal' => $row->JENIS_JADWAL
                ),

                'aktual' => array(
                    'jam_datang'  => $row->jam_datang,
                    'jam_keluar'  => $row->jam_keluar,
                    'scan_masuk'  => $row->id_scan_masuk,
                    'scan_keluar' => $row->id_scan_keluar
                ),

                'keterangan' => $row->keterangan,
                'lembur'     => (int) $row->lembur,
                'active'     => $row->active
            );
        }

        return $this->json_response(
            TRUE,
            count($schedule) > 0
                ? 'Jadwal hari ini berhasil diambil.'
                : 'Tidak ada jadwal hari ini.',
            array(
                'tanggal' => $tanggal,
                'schedule' => $schedule
            )
        );
    }
}
