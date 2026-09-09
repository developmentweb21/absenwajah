<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Absensi extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('M_absensi');
		$this->load->model('M_lokasi');
    }

    public function index()
    {
        redirect('dashboard');
    }

    public function simpan()
    {
		$lokasi = $this -> M_lokasi ->lokasi_aktif();
		$latitude = $this->input->post('latitude');
		$longitude = $this->input->post('longitude');
		$jarak = $this->hitungJarak($latitude,$longitude,$lokasi->latitude,$lokasi->longitude);
        $jam_absen = $this->input->post('jam_absen');

		if($jarak > $lokasi->radius)
{
    echo json_encode([
        'status' => false,
        'message' => 'Anda berada di luar area absensi'
    ]);
    return;
}

		$scan_date = date('Y-m-d') . ' ' . $jam_absen . ':00';

        $data = [
            'sn'             => 'MOBILE',
            'scan_date'      => $scan_date,
            'pin'            => $this->session->userdata('id'),
            'verify_mode'    => '0',
            'io_mode'        => '0',
            'work_code'      => '0',
            'ex_id'          => '0',
            'flag'           => '0',
            'rowguid'        => '',
            'io_mode_update' => '0',
            'kd_absensi'     => '0',
            'latitude'       => $this->input->post('latitude'),
            'longitude'      => $this->input->post('longitude'),
            'status'         => '1',
            'mock'           => '0'
        ];

        $this->M_absensi->simpan($data);

        echo json_encode([
            'status' => true,
            'message' => 'Absensi berhasil'
        ]);
    }

    public function riwayat()
    {
        $data['title'] = 'Riwayat Absensi';

        $data['riwayat'] =
            $this->M_absensi->riwayat(
                $this->session->userdata('id')
            );

        $this->load->view('templates/header',$data);
        $this->load->view('riwayat_absensi',$data);
        $this->load->view('templates/footer');
    }
	

    public function jadwal()
    {
        $data['title'] = 'Jadwal Absensi';

        $data['absensi'] =
            $this->M_absensi->bulan_ini(
                $this->session->userdata('id')
            );

        $this->load->view('templates/header',$data);
        $this->load->view('jadwal_absensi',$data);
        $this->load->view('templates/footer');
    }
	
	private function hitungJarak(
    $lat1,
    $lon1,
    $lat2,
    $lon2
)
{
    $earth = 6371000;

    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    $a =
        sin($dLat/2) * sin($dLat/2)
        +
        cos(deg2rad($lat1))
        *
        cos(deg2rad($lat2))
        *
        sin($dLon/2)
        *
        sin($dLon/2);

    $c = 2 * atan2(
        sqrt($a),
        sqrt(1-$a)
    );

    return $earth * $c;
}
	public function cek_jarak()
{
    $this->load->model('M_lokasi');

    $lokasi = $this->M_lokasi->lokasi_aktif();

    if(!$lokasi)
    {
        echo json_encode([
            'status' => false,
            'message' => 'Lokasi absensi belum diatur'
        ]);
        return;
    }

    $latitude  = $this->input->post('latitude');
    $longitude = $this->input->post('longitude');

    $jarak = $this->hitungJarak(
        $latitude,
        $longitude,
        $lokasi->latitude,
        $lokasi->longitude
    );

    echo json_encode([
        'status' => ($jarak <= $lokasi->radius),
        'jarak'  => round($jarak),
        'radius' => $lokasi->radius,
        'lokasi' => $lokasi->nama_lokasi
    ]);
}
}