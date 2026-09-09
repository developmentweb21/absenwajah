<?php

class M_absensi extends CI_Model
{
    public function simpan($data)
    {
        return $this->db
            ->insert('att_log_mobile',$data);
    }

    public function riwayat($pin)
    {
        return $this->db
            ->where('pin',$pin)
            ->order_by('scan_date','DESC')
            ->get('att_log_mobile')
            ->result();
    }

    public function cek_hari_ini($pin)
    {
        return $this->db
            ->where('id_pegawai',$pin)
            ->where('DATE(tanggal_jadwal)',date('Y-m-d'))
            ->get('t_perencanaan_harikerja')
            ->result();
    }
	
	public function bulan_ini($pin)
{
    return $this->db
        ->where('id_pegawai', $pin)
        ->where('MONTH(tanggal_jadwal)', date('m'))
        ->where('YEAR(tanggal_jadwal)', date('Y'))
		->order_by('tanggal_jadwal','ASC')
        ->get('t_perencanaan_harikerja')
		->result();
}
}