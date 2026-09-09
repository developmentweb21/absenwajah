<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Api_pegawai_model extends CI_Model
{
    public function get_by_id($id_pegawai)
    {
        return $this->db
            ->where('ID_PEGAWAI', $id_pegawai)
            ->limit(1)
            ->get('pegawai')
            ->row();
    }
}
