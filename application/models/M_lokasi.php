<?php

class M_lokasi extends CI_Model
{
    public function lokasi_aktif()
    {
        return $this->db
            ->where('status','Aktif')
            ->get('mst_lokasi_absensi')
            ->row();
    }
	
    public function get_all()
    {
        return $this->db
            ->order_by('id','DESC')
            ->get('mst_lokasi_absensi')
            ->result();
    }

    public function get_by_id($id)
    {
        return $this->db
            ->where('id',$id)
            ->get('mst_lokasi_absensi')
            ->row();
    }

    public function insert($data)
    {
        return $this->db
            ->insert('mst_lokasi_absensi',$data);
    }

    public function update($id,$data)
    {
        return $this->db
            ->where('id',$id)
            ->update('mst_lokasi_absensi',$data);
    }

    public function delete($id)
    {
        return $this->db
            ->where('id',$id)
            ->delete('mst_lokasi_absensi');
    }

}