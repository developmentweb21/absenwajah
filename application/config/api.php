<?php
defined('BASEPATH') OR exit('No direct script access allowed');

$config['api_jwt_secret'] = getenv('ABSENSI_JWT_SECRET');
if (!$config['api_jwt_secret']) {
    $config['api_jwt_secret'] = 'CHANGE_THIS_ABSENSI_WAJAH_JWT_SECRET_IN_PRODUCTION';
}
$config['api_jwt_issuer'] = 'absenwajah';
$config['api_jwt_ttl'] = 86400;
$config['api_timezone'] = 'Asia/Makassar';
