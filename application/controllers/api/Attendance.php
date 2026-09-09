<?php
defined('BASEPATH') or exit('No direct script access allowed');

require_once APPPATH . 'core/API_Controller.php';

class Attendance extends API_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('api/Api_attendance_model');
        $this->load->library('Gps_service');
    }


    /**
     * ============================================================
     * GET /api/attendance/status
     * ============================================================
     *
     * Mengambil status absensi pegawai hari ini.
     *
     * Status:
     * - NOT_CHECKED_IN
     * - CHECKED_IN
     * - COMPLETE
     * - NO_SCHEDULE
     */
    public function status()
    {
        /*
         * ============================
         * AUTHENTICATION
         * ============================
         */

        if (!$this->require_auth()) {
            return;
        }


        /*
         * ============================
         * IDENTITAS PEGAWAI
         * ============================
         *
         * id_pegawai berasal dari JWT.
         * Tidak menerima pin dari client.
         */

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


        /*
         * ============================
         * SERVER DATE
         * ============================
         */

        $tanggal = date('Y-m-d');


        /*
         * ============================
         * AMBIL JADWAL
         * ============================
         */

        $rows = $this->Api_attendance_model
            ->get_status(
                $id_pegawai,
                $tanggal
            );


        /*
         * ============================
         * TIDAK ADA JADWAL
         * ============================
         */

        if (empty($rows)) {

            return $this->json_response(
                TRUE,
                'Tidak ada jadwal absensi hari ini.',
                array(
                    'tanggal' => $tanggal,
                    'has_schedule' => FALSE,
                    'status' => 'NO_SCHEDULE'
                )
            );
        }


        /*
         * ============================
         * FORMAT JADWAL
         * ============================
         */

        $schedule = array();


        foreach ($rows as $row) {

            $has_in = (
                $row->jam_datang !== NULL &&
                trim($row->jam_datang) !== '' &&
                $row->jam_datang !== '0000-00-00 00:00:00' &&
                $row->jam_datang !== '0000-00-00'
            );

            $has_out = (
                $row->jam_keluar !== NULL &&
                trim($row->jam_keluar) !== '' &&
                $row->jam_keluar !== '0000-00-00 00:00:00' &&
                $row->jam_keluar !== '0000-00-00'
            );


            if ($has_in && $has_out) {

                $attendance_status = 'COMPLETE';
            } elseif ($has_in) {

                $attendance_status = 'CHECKED_IN';
            } else {

                $attendance_status = 'NOT_CHECKED_IN';
            }


            $schedule[] = array(

                'kd_jadwal' => (int) $row->kd_jadwal,


                /*
                 * ============================
                 * SHIFT
                 * ============================
                 */

                'shift' => array(

                    'kode' => $row->kd_standarisasi,

                    'nama' => $row->STANDARISASI,

                    'jam_masuk' => $row->JAM_MASUK,

                    'jam_pulang' => $row->JAM_PULANG,

                    'toleransi' => $row->TOLERANSI,

                    'pendek' => $row->PENDEK,

                    'total_jam' => $row->TOTAL_JAM,

                    'jenis_jadwal' => $row->JENIS_JADWAL
                ),


                /*
                 * ============================
                 * AKTUAL
                 * ============================
                 */

                'actual' => array(

                    'jam_datang' => $row->jam_datang,

                    'jam_keluar' => $row->jam_keluar
                ),


                /*
                 * ============================
                 * STATUS
                 * ============================
                 */

                'status' => $attendance_status,


                'keterangan' => $row->keterangan,

                'lembur' => (int) $row->lembur,

                'active' => $row->active
            );
        }


        /*
         * ============================
         * STATUS KESELURUHAN
         * ============================
         *
         * Untuk sementara menggunakan
         * jadwal pertama.
         *
         * Dukungan multi-shift akan
         * kita finalisasi berdasarkan
         * data HRIS.
         */

        $overall_status = $schedule[0]['status'];


        /*
         * ============================
         * RESPONSE
         * ============================
         */

        return $this->json_response(
            TRUE,
            'Status absensi berhasil diambil.',
            array(

                'tanggal' => $tanggal,

                'has_schedule' => TRUE,

                'status' => $overall_status,

                'schedule' => $schedule
            )
        );
    }


    /**
     * ============================================================
     * POST /api/attendance
     * ============================================================
     *
     * Melakukan proses absensi.
     *
     * Tahap saat ini:
     *
     * 1. Authentication
     * 2. Identitas pegawai
     * 3. Server time
     * 4. Jadwal
     * 5. GPS
     * 6. Tentukan CHECK_IN / CHECK_OUT
     * 7. Insert att_log_mobile
     *
     * Face verification dan liveness akan ditambahkan
     * pada tahap berikutnya.
     */
    public function index()
    {
        /*
         * ============================
         * AUTHENTICATION
         * ============================
         */

        if (!$this->require_auth()) {
            return;
        }


        /*
         * ============================
         * IDENTITAS PEGAWAI
         * ============================
         */

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


        /*
         * ============================
         * INPUT JSON
         * ============================
         */

        $input = json_decode(
            $this->input->raw_input_stream,
            TRUE
        );


        if (!is_array($input)) {
            $input = array();
        }


        /*
         * ============================
         * GPS INPUT
         * ============================
         */

        $latitude = isset($input['latitude'])
            ? $input['latitude']
            : NULL;


        $longitude = isset($input['longitude'])
            ? $input['longitude']
            : NULL;


        $accuracy = isset($input['accuracy'])
            ? $input['accuracy']
            : NULL;


        $mock = isset($input['mock'])
            ? $input['mock']
            : 0;


        /*
         * ============================
         * DEVICE INPUT
         * ============================
         */

        $device = isset($input['device'])
            ? trim($input['device'])
            : 'Flutter';


        $browser = isset($input['browser'])
            ? trim($input['browser'])
            : 'Flutter';


        /*
         * ============================
         * SERVER TIME
         * ============================
         *
         * Server adalah sumber waktu resmi.
         */

        $server_time = date('Y-m-d H:i:s');

        $tanggal = date('Y-m-d');


        /*
         * ============================
         * CEK JADWAL
         * ============================
         */

        $rows = $this->Api_attendance_model
            ->get_status(
                $id_pegawai,
                $tanggal
            );


        if (empty($rows)) {

            return $this->json_response(
                FALSE,
                'Tidak ada jadwal absensi hari ini.',
                array(
                    'code' => 'NO_SCHEDULE',
                    'server_time' => $server_time
                ),
                422
            );
        }


        /*
         * ============================
         * VALIDASI GPS
         * ============================
         */

        $gps = $this->gps_service->validate(
            $latitude,
            $longitude,
            $accuracy,
            $mock
        );


        if (!$gps['valid']) {

            return $this->json_response(
                FALSE,
                $gps['message'],
                array(
                    'code' => $gps['code'],
                    'server_time' => $server_time,
                    'gps' => $gps
                ),
                422
            );
        }


        /*
         * ============================
         * STATUS TRANSAKSI
         * ============================
         *
         * Menentukan:
         *
         * CHECK_IN
         * CHECK_OUT
         * ALREADY_ATTENDANCE
         */

        $transaction = $this->Api_attendance_model
            ->get_transaction_status(
                $id_pegawai,
                $tanggal,
                $server_time
            );


        /*
         * ============================
         * NO SCHEDULE
         * ============================
         */

        if (
            !isset($transaction['code']) ||
            $transaction['code'] === 'NO_SCHEDULE'
        ) {

            return $this->json_response(
                FALSE,
                'Tidak ada jadwal absensi hari ini.',
                array(
                    'code' => 'NO_SCHEDULE',
                    'server_time' => $server_time
                ),
                422
            );
        }


        /*
         * ============================
         * SUDAH ABSEN LENGKAP
         * ============================
         */

        /*
 * ============================
 * WAIT CHECK IN
 * ============================
 */

        /*     if ($transaction['code'] === 'WAIT_CHECK_IN') {

            return $this->json_response(
                FALSE,
                'Tidak berada dalam waktu absensi masuk.',
                array(
                    'code' => 'WAIT_CHECK_IN',
                    'server_time' => $server_time,
                    'jam_masuk' => $transaction['schedule']->JAM_MASUK
                ),
                422
            );
        }
 */

        /*
 * ============================
 * WAIT CHECK OUT
 * ============================
 */
        /* 
        if ($transaction['code'] === 'WAIT_CHECK_OUT') {

            return $this->json_response(
                FALSE,
                'Belum memasuki waktu absensi pulang.',
                array(
                    'code' => 'WAIT_CHECK_OUT',
                    'server_time' => $server_time,
                    'jam_pulang' => $transaction['schedule']->JAM_PULANG
                ),
                422
            );
        }
 */

        /*
 * ============================
 * SUDAH ABSEN LENGKAP
 * ============================
 */

        if ($transaction['code'] === 'ALREADY_ATTENDANCE') {

            return $this->json_response(
                FALSE,
                'Absensi masuk dan pulang sudah tercatat.',
                array(
                    'code' => 'ALREADY_ATTENDANCE',

                    'server_time' => $server_time,

                    'attendance' => array(
                        'jam_datang' =>
                        $transaction['schedule']->jam_datang,

                        'jam_keluar' =>
                        $transaction['schedule']->jam_keluar
                    )
                ),
                422
            );
        }


        /*
         * ============================
         * JENIS TRANSAKSI
         * ============================
         */

        $transaction_type =
            $transaction['code'];


        /*
         * ============================
         * SIAPKAN DATA INSERT
         * ============================
         *
         * Konvensi HRIS:
         *
         * verify_mode = 1
         *
         * field lainnya default 0.
         */

        $data = array(

            /*
             * Device / mesin sumber.
             */
            'sn' => substr(
                $device,
                0,
                30
            ),


            /*
             * Waktu server.
             */
            'scan_date' => $server_time,


            /*
             * PIN berasal dari JWT.
             */
            'pin' => (string) $id_pegawai,


            /*
             * Verifikasi mobile.
             */
            'verify_mode' => 1,


            /*
             * Default HRIS.
             */
            'io_mode' => 0,

            'work_code' => 0,

            'ex_id' => 0,

            'flag' => 0,


            /*
             * GUID transaksi.
             */
            'rowguid' => md5(
                uniqid(
                    $id_pegawai .
                        microtime(TRUE),
                    TRUE
                )
            ),


            'io_mode_update' => 0,

            'kd_absensi' => 0,


            /*
             * GPS.
             */
            'latitude' => $latitude,

            'longitude' => $longitude,

            'accuracy' => $accuracy,

            'mock' => (int) $mock,

            'gps_status' => 1,


            /*
             * Face sementara.
             *
             * Akan diisi setelah
             * face verification.
             */
            'face_score' => NULL,

            'face_status' => 0,


            /*
             * Device information.
             */
            'device' => substr(
                $device,
                0,
                100
            ),

            'browser' => substr(
                $browser,
                0,
                100
            ),


            /*
             * Status transaksi.
             */
            'status' => 1
        );


        /*
         * ============================
         * INSERT
         * ============================
         */

        $insert = $this->Api_attendance_model
            ->insert_attendance($data);


        /*
         * ============================
         * INSERT GAGAL
         * ============================
         */

        if (!$insert) {

            return $this->json_response(
                FALSE,
                'Gagal menyimpan absensi.',
                array(

                    'code' => 'SERVER_ERROR',

                    'server_time' => $server_time,

                    'db_error' => $this->db->error()
                ),
                500
            );
        }


        /*
         * ============================
         * INSERT BERHASIL
         * ============================
         */

        return $this->json_response(
            TRUE,
            'Absensi berhasil disimpan.',
            array(

                'code' => 'ATTENDANCE_SAVED',

                'transaction' => array(

                    'type' => $transaction_type,

                    'can_check_in' =>
                    isset(
                        $transaction['can_check_in']
                    )
                        ? $transaction['can_check_in']
                        : FALSE,

                    'can_check_out' =>
                    isset(
                        $transaction['can_check_out']
                    )
                        ? $transaction['can_check_out']
                        : FALSE
                ),

                'server_time' => $server_time,

                'id_pegawai' => $id_pegawai,

                'tanggal' => $tanggal,

                'gps' => $gps
            )
        );
    }
}
