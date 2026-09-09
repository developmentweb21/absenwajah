<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Api_user_model extends CI_Model
{
    public function find_by_nip($nip)
    {
        return $this->db
            ->select('u.id, u.nip, u.nama_lengkap, u.pass_login, u.level_akses,
                      u.status_user, u.status, p.ID_PEGAWAI, p.NIP AS pegawai_nip,
                      p.NAMA, p.FOTO')
            ->from('tb_users u')
            ->join('pegawai p', 'p.ID_PEGAWAI = u.id', 'inner')
            ->where('u.nip', $nip)
            ->limit(1)
            ->get()
            ->row();
    }
}
