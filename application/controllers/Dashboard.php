<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Dashboard extends MY_Controller
{
	public function __construct()
    {
        parent::__construct();

        $this->load->model('M_absensi');
		 $this->load->model('M_lokasi');
    }
    public function index()
    {
        $data['title'] = 'Dashboard';
		$data['absensi'] =
            $this->M_absensi->cek_hari_ini(
                $this->session->userdata('id')
            );
		$data['lokasi'] = $this->M_lokasi->lokasi_aktif();
        $this->load->view('templates/header',$data);
        $this->load->view('home',$data);
        $this->load->view('templates/footer');
    }
}