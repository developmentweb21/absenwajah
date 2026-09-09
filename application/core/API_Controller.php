<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class API_Controller extends CI_Controller
{
    protected $api_user = NULL;

    public function __construct()
    {
        parent::__construct();
        date_default_timezone_set('Asia/Makassar');
        $this->load->library('Jwt');
    }

    protected function json_response($status, $message, $data = NULL, $httpCode = 200)
    {
        $response = array('status' => $status, 'message' => $message);
        if ($data !== NULL) $response['data'] = $data;

        return $this->output
            ->set_status_header($httpCode)
            ->set_content_type('application/json', 'utf-8')
            ->set_output(json_encode($response, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES));
    }

    protected function require_auth()
    {
        $header = $this->input->get_request_header('Authorization', TRUE);

        if (!$header || stripos($header, 'Bearer ') !== 0) {
            $this->json_response(FALSE, 'Token tidak ditemukan.',
                array('code' => 'AUTH_TOKEN_MISSING'), 401);
            return FALSE;
        }

        $payload = $this->jwt->decode(trim(substr($header, 7)));

        if ($payload === FALSE) {
            $this->json_response(FALSE, 'Token tidak valid atau sudah kedaluwarsa.',
                array('code' => 'AUTH_TOKEN_INVALID'), 401);
            return FALSE;
        }

        $this->api_user = $payload;
        return TRUE;
    }
}
