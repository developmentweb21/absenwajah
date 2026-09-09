<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Api_schedule_model extends CI_Model
{
    /**
     * Mengambil jadwal + aktual absensi pegawai pada tanggal tertentu.
     *
     * standarisasi:
     *   JAM_MASUK  = jadwal shift masuk
     *   JAM_PULANG = jadwal shift pulang
     *
     * t_perencanaan_harikerja:
     *   jam_datang = aktual masuk
     *   jam_keluar = aktual pulang
     */
    public function get_today($id_pegawai, $tanggal = NULL)
    {
        if (!$tanggal) {
            $tanggal = date('Y-m-d');
        }

        return $this->db
            ->select('
                h.kd_jadwal,
                h.tanggal_jadwal,
                h.kd_standarisasi,
                h.keterangan,
                h.id_pegawai,
                h.lembur,
                h.kd_tim,
                h.jam_datang,
                h.jam_keluar,
                h.id_scan_masuk,
                h.id_scan_keluar,
                h.active,

                s.KD_STANDARISASI,
                s.STANDARISASI,
                s.KD_KELOMPOK,
                s.JAM_MASUK,
                s.JAM_PULANG,
                s.TOLERANSI,
                s.PENDEK,
                s.TOTAL_JAM,
                s.JENIS_JADWAL
            ')
            ->from('t_perencanaan_harikerja h')
            ->join(
                'standarisasi s',
                's.KD_STANDARISASI = h.kd_standarisasi',
                'left'
            )
            ->where('h.id_pegawai', $id_pegawai)
            ->where('h.tanggal_jadwal', $tanggal)
            ->order_by('h.kd_jadwal', 'ASC')
            ->get()
            ->result();
    }
}
