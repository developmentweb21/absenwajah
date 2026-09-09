<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Gps_service
{
    protected $CI;

    public function __construct()
    {
        $this->CI = &get_instance();

        $this->CI->load->model('M_lokasi');
    }

    /**
     * Validasi GPS pegawai terhadap lokasi absensi aktif.
     *
     * @param float $latitude
     * @param float $longitude
     * @param float $accuracy
     * @param int   $mock
     * @return array
     */
    public function validate(
        $latitude,
        $longitude,
        $accuracy = NULL,
        $mock = 0
    ) {
        /*
         * ============================
         * VALIDASI INPUT
         * ============================
         */

        if ($latitude === NULL || $longitude === NULL) {
            return array(
                'valid' => FALSE,
                'code' => 'GPS_UNAVAILABLE',
                'message' => 'Koordinat GPS tidak tersedia.'
            );
        }

        if (!is_numeric($latitude) || !is_numeric($longitude)) {
            return array(
                'valid' => FALSE,
                'code' => 'GPS_UNAVAILABLE',
                'message' => 'Koordinat GPS tidak valid.'
            );
        }

        $latitude = (float) $latitude;
        $longitude = (float) $longitude;

        if ($latitude < -90 || $latitude > 90) {
            return array(
                'valid' => FALSE,
                'code' => 'GPS_UNAVAILABLE',
                'message' => 'Latitude tidak valid.'
            );
        }

        if ($longitude < -180 || $longitude > 180) {
            return array(
                'valid' => FALSE,
                'code' => 'GPS_UNAVAILABLE',
                'message' => 'Longitude tidak valid.'
            );
        }

        /*
         * ============================
         * MOCK LOCATION
         * ============================
         */

        if ((int) $mock === 1) {
            return array(
                'valid' => FALSE,
                'code' => 'MOCK_LOCATION',
                'message' => 'Lokasi palsu/mock location terdeteksi.'
            );
        }

        /*
         * ============================
         * LOKASI AKTIF
         * ============================
         */

        $lokasi = $this->CI->M_lokasi->lokasi_aktif();

        if (!$lokasi) {
            return array(
                'valid' => FALSE,
                'code' => 'GPS_UNAVAILABLE',
                'message' => 'Lokasi absensi aktif belum tersedia.'
            );
        }

        /*
         * ============================
         * AMBIL KOORDINAT LOKASI
         * ============================
         */

        $target_latitude = NULL;
        $target_longitude = NULL;
        $radius = NULL;

        /*
         * Struktur aktual mst_lokasi_absensi
         * sudah digunakan oleh aplikasi.
         *
         * Untuk saat ini kita gunakan:
         * latitude
         * longitude
         * radius
         */

        if (
            !isset($lokasi->latitude) ||
            !isset($lokasi->longitude)
        ) {
            return array(
                'valid' => FALSE,
                'code' => 'GPS_UNAVAILABLE',
                'message' => 'Koordinat lokasi absensi belum dikonfigurasi.'
            );
        }

        $target_latitude = (float) $lokasi->latitude;
        $target_longitude = (float) $lokasi->longitude;

        if (isset($lokasi->radius)) {
            $radius = (float) $lokasi->radius;
        }

        if ($radius === NULL || $radius <= 0) {
            return array(
                'valid' => FALSE,
                'code' => 'GPS_UNAVAILABLE',
                'message' => 'Radius lokasi absensi belum dikonfigurasi.'
            );
        }

        /*
         * ============================
         * ACCURACY
         * ============================
         *
         * Accuracy tidak digunakan untuk
         * menghitung jarak.
         *
         * Tetapi akan menjadi salah satu
         * parameter validasi.
         *
         * Threshold sementara:
         * 50 meter.
         *
         * Nilai ini nanti sebaiknya
         * dijadikan konfigurasi.
         */

        if ($accuracy !== NULL && is_numeric($accuracy)) {

            $accuracy = (float) $accuracy;

            if ($accuracy <= 0) {
                return array(
                    'valid' => FALSE,
                    'code' => 'GPS_INACCURATE',
                    'message' => 'Akurasi GPS tidak valid.'
                );
            }

            if ($accuracy > 50) {
                return array(
                    'valid' => FALSE,
                    'code' => 'GPS_INACCURATE',
                    'message' => 'Akurasi GPS terlalu rendah.',
                    'accuracy' => $accuracy,
                    'required_accuracy' => 50
                );
            }
        }

        /*
         * ============================
         * HITUNG JARAK
         * ============================
         */

        $distance = $this->calculate_distance(
            $latitude,
            $longitude,
            $target_latitude,
            $target_longitude
        );

        /*
         * ============================
         * CEK RADIUS
         * ============================
         */

        if ($distance > $radius) {

            return array(
                'valid' => FALSE,
                'code' => 'OUTSIDE_RADIUS',
                'message' => 'Anda berada di luar radius lokasi absensi.',
                'distance' => round($distance, 2),
                'radius' => $radius,
                'accuracy' => $accuracy,
                'location' => array(
                    'id' => isset($lokasi->id)
                        ? $lokasi->id
                        : NULL,
                    'nama' => isset($lokasi->nama_lokasi)
                        ? $lokasi->nama_lokasi
                        : (
                            isset($lokasi->nama)
                            ? $lokasi->nama
                            : NULL
                        ),
                    'latitude' => $target_latitude,
                    'longitude' => $target_longitude
                )
            );
        }

        /*
         * ============================
         * VALID
         * ============================
         */

        return array(
            'valid' => TRUE,
            'code' => 'GPS_VALID',
            'message' => 'Lokasi GPS valid.',
            'distance' => round($distance, 2),
            'radius' => $radius,
            'accuracy' => $accuracy,
            'mock' => (int) $mock,
            'location' => array(
                'id' => isset($lokasi->id)
                    ? $lokasi->id
                    : NULL,
                'nama' => isset($lokasi->nama_lokasi)
                    ? $lokasi->nama_lokasi
                    : (
                        isset($lokasi->nama)
                        ? $lokasi->nama
                        : NULL
                    ),
                'latitude' => $target_latitude,
                'longitude' => $target_longitude
            )
        );
    }

    /**
     * Haversine distance.
     *
     * Hasil dalam meter.
     */
    protected function calculate_distance(
        $latitude1,
        $longitude1,
        $latitude2,
        $longitude2
    ) {
        $earth_radius = 6371000;

        $lat1 = deg2rad($latitude1);
        $lat2 = deg2rad($latitude2);

        $delta_lat = deg2rad(
            $latitude2 - $latitude1
        );

        $delta_longitude = deg2rad(
            $longitude2 - $longitude1
        );

        $a =
            sin($delta_lat / 2) *
            sin($delta_lat / 2)
            +
            cos($lat1) *
            cos($lat2) *
            sin($delta_longitude / 2) *
            sin($delta_longitude / 2);

        $c = 2 * atan2(
            sqrt($a),
            sqrt(1 - $a)
        );

        return $earth_radius * $c;
    }
}
