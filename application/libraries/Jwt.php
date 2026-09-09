<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Jwt
{
    protected $secret;
    protected $issuer;

    public function __construct()
    {
        $CI =& get_instance();
        $CI->config->load('api');
        $this->secret = $CI->config->item('api_jwt_secret');
        $this->issuer = $CI->config->item('api_jwt_issuer');
    }

    public function encode(array $claims)
    {
        $header = array('typ' => 'JWT', 'alg' => 'HS256');
        $now = time();
        $payload = array_merge(array('iss' => $this->issuer, 'iat' => $now), $claims);

        $h = $this->base64url_encode(json_encode($header, JSON_UNESCAPED_SLASHES));
        $p = $this->base64url_encode(json_encode($payload, JSON_UNESCAPED_SLASHES));
        $s = hash_hmac('sha256', $h . '.' . $p, $this->secret, true);

        return $h . '.' . $p . '.' . $this->base64url_encode($s);
    }

    public function decode($token)
    {
        if (!$token) return FALSE;

        $parts = explode('.', $token);
        if (count($parts) !== 3) return FALSE;

        list($h, $p, $s) = $parts;
        $header = json_decode($this->base64url_decode($h), TRUE);
        $payload = json_decode($this->base64url_decode($p), TRUE);
        $signature = $this->base64url_decode($s);

        if (!is_array($header) || !is_array($payload) || $signature === FALSE) return FALSE;
        if (!isset($header['alg']) || $header['alg'] !== 'HS256') return FALSE;
        if (!hash_equals(hash_hmac('sha256', $h . '.' . $p, $this->secret, true), $signature)) return FALSE;
        if (isset($payload['iss']) && $payload['iss'] !== $this->issuer) return FALSE;
        if (!isset($payload['exp']) || (int)$payload['exp'] < time()) return FALSE;

        return $payload;
    }

    protected function base64url_encode($data)
    {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    protected function base64url_decode($data)
    {
        $remainder = strlen($data) % 4;
        if ($remainder) $data .= str_repeat('=', 4 - $remainder);
        return base64_decode(strtr($data, '-_', '+/'), true);
    }
}
