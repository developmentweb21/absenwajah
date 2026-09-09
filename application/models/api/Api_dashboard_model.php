<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Api_dashboard_model extends CI_Model
{
    public function get_pegawai($id_pegawai)
    {
        return $this->db
            ->select('
                ID_PEGAWAI,
                NIP,
                NAMA,
                FOTO
            ')
            ->where('ID_PEGAWAI', $id_pegawai)
            ->limit(1)
            ->get('pegawai')
            ->row();
    }

    public function get_jadwal_hari_ini($id_pegawai, $tanggal)
    {
        return $this->db
            ->select('
                h.kd_jadwal,
                h.tanggal_jadwal,
                h.kd_standarisasi,
                h.keterangan,
                h.jam_datang,
                h.jam_keluar,
                h.id_scan_masuk,
                h.id_scan_keluar,
                h.lembur,
                h.active,

                s.STANDARISASI,
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

    public function get_lokasi_aktif()
    {
        return $this->db
            ->where('status', 'Aktif')
            ->limit(1)
            ->get('mst_lokasi_absensi')
            ->row();
    }
}
