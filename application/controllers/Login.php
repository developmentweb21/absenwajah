<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Login extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('M_login');
    }

    public function index()
    {
        if($this->session->userdata('login'))
        {
            redirect('dashboard');
        }

        $this->load->view('login');
    }

    public function proses()
    {
        $nip      = $this->input->post('nip');
        $password = md5($this->input->post('password'));

        $user = $this->M_login
                     ->cek_login($nip,$password)
                     ->row();

        if($user)
        {
            $session = [

                'id'       => $user->id,
                'nip'      => $user->nip,
				'foto' 	   => $user->foto,
                'nama'     => $user->nama_lengkap,
                'level'    => $user->level_akses,
                'login'    => TRUE

            ];

            $this->session->set_userdata($session);

            redirect('dashboard');
        }

        $this->session->set_flashdata(
            'error',
            'NIP atau Password salah'
        );

        redirect('login');
    }

    public function logout()
    {
        $this->session->sess_destroy();

        redirect('login');
    }
}