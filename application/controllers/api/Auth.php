<?php
defined('BASEPATH') or exit('No direct script access allowed');
require_once APPPATH . 'core/API_Controller.php';

class Auth extends API_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('api/Api_user_model');
        $this->config->load('api');
    }

    public function login()
    {
        if (strtoupper($this->input->method(TRUE)) !== 'POST') {
            return $this->json_response(
                FALSE,
                'Method tidak diizinkan.',
                array('code' => 'METHOD_NOT_ALLOWED'),
                405
            );
        }

        $body = json_decode(trim($this->input->raw_input_stream), TRUE);
        if (!is_array($body)) $body = $this->input->post(NULL, TRUE);

        $nip = isset($body['nip']) ? trim($body['nip']) : '';
        $password = isset($body['password']) ? (string)$body['password'] : '';

        if ($nip === '' || $password === '') {
            return $this->json_response(
                FALSE,
                'NIP dan password wajib diisi.',
                array('code' => 'AUTH_INVALID'),
                422
            );
        }

        $user = $this->Api_user_model->find_by_nip($nip);

        if (!$user) {
            return $this->json_response(
                FALSE,
                'NIP atau password salah.',
                array('code' => 'AUTH_INVALID'),
                401
            );
        }

        if ($user->status_user !== 'valid') {
            return $this->json_response(
                FALSE,
                'Akun tidak aktif.',
                array('code' => 'USER_INACTIVE'),
                403
            );
        }

        // Kompatibilitas dengan tb_users.pass_login existing.
        if (!hash_equals((string)$user->pass_login, md5($password))) {
            return $this->json_response(
                FALSE,
                'NIP atau password salah.',
                array('code' => 'AUTH_INVALID'),
                401
            );
        }

        $ttl = (int)$this->config->item('api_jwt_ttl');

        $token = $this->jwt->encode(array(
            'sub' => (int)$user->id,
            'id_pegawai' => (int)$user->ID_PEGAWAI,
            'nip' => $user->nip,
            'role' => $user->level_akses,
            'exp' => time() + $ttl
        ));

        return $this->json_response(TRUE, 'Login berhasil.', array(
            'token' => $token,
            'token_type' => 'Bearer',
            'expires_in' => $ttl,
            'user' => array(
                'id' => (int)$user->id,
                'id_pegawai' => (int)$user->ID_PEGAWAI,
                'nip' => $user->nip,
                'nama' => $user->NAMA ?: $user->nama_lengkap,
                'foto' => $user->FOTO,
                'role' => $user->level_akses
            )
        ));
    }

    public function me()
    {
        if (!$this->require_auth()) return;

        return $this->json_response(TRUE, 'Token valid.', array(
            'user' => array(
                'id' => (int)$this->api_user['sub'],
                'id_pegawai' => (int)$this->api_user['id_pegawai'],
                'nip' => $this->api_user['nip'],
                'role' => $this->api_user['role']
            )
        ));
    }

    public function logout()
    {
        if (!$this->require_auth()) return;

        return $this->json_response(
            TRUE,
            'Logout berhasil. Hapus token pada perangkat.'
        );
    }
}
