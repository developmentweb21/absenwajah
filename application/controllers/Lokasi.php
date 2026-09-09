<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Lokasi extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('M_lokasi');
    }

    public function index()
    {
        $data['title'] = 'Master Lokasi';

        $data['lokasi'] =
            $this->M_lokasi->get_all();

        $this->load->view(
            'templates/header',
            $data
        );

        $this->load->view(
            'lokasi',
            $data
        );

        $this->load->view(
            'templates/footer'
        );
    }

    public function simpan()
    {
        $data = [

            'nama_lokasi' =>
                $this->input->post('nama_lokasi'),

            'latitude' =>
                $this->input->post('latitude'),

            'longitude' =>
                $this->input->post('longitude'),

            'radius' =>
                $this->input->post('radius'),

            'status' =>
                $this->input->post('status')

        ];

        $this->M_lokasi->insert($data);

        redirect('lokasi');
    }

   public function update($id)
{
    if($this->input->post('status') == 'Aktif')
    {
        $this->db->update(
            'mst_lokasi_absensi',
            ['status' => 'Tidak Aktif']
        );
    }

    $data = [

        'nama_lokasi' =>
            $this->input->post('nama_lokasi'),

        'latitude' =>
            $this->input->post('latitude'),

        'longitude' =>
            $this->input->post('longitude'),

        'radius' =>
            $this->input->post('radius'),

        'status' =>
            $this->input->post('status')

    ];

    $this->M_lokasi->update(
        $id,
        $data
    );

    redirect('lokasi');
}

    public function hapus($id)
    {
        $this->M_lokasi->delete($id);

        redirect('lokasi');
    }
}