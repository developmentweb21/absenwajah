<?php
class M_login extends CI_Model
{
    public function cek_login($nip,$password)
    {
        return $this->db
            ->where('nip',$nip)
            ->where('pass_login',$password)
            ->where('status_user','valid')
            ->get('tb_users');
    }
}