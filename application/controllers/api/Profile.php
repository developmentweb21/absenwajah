<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'core/API_Controller.php';

class Profile extends API_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('api/Api_pegawai_model');
    }

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

        $pegawai = $this->Api_pegawai_model
            ->get_by_id($id_pegawai);

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

        return $this->json_response(
            TRUE,
            'Data profile berhasil diambil.',
            array(
                'profile' => array(
                    'id_pegawai' => (int) $pegawai->ID_PEGAWAI,
                    'nip'        => $pegawai->NIP,
                    'nama'       => $pegawai->NAMA,
                    'foto'       => $pegawai->FOTO
                )
            )
        );
    }
}
