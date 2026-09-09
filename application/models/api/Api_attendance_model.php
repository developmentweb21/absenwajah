<?php
defined('BASEPATH') or exit('No direct script access allowed');

class Api_attendance_model extends CI_Model
{
    /**
     * ============================================================
     * GET STATUS ABSENSI HARI INI
     * ============================================================
     */

    /**
     * Mengecek apakah nilai datetime benar-benar
     * sudah berisi waktu absensi.
     */
    private function is_valid_attendance_datetime($value)
    {
        if ($value === NULL) {
            return FALSE;
        }

        $value = trim($value);

        if ($value === '') {
            return FALSE;
        }

        if (
            $value === '0000-00-00 00:00:00' ||
            $value === '0000-00-00'
        ) {
            return FALSE;
        }

        return TRUE;
    }

    public function get_status($id_pegawai, $tanggal = NULL)
    {
        if (!$tanggal) {
            $tanggal = date('Y-m-d');
        }

        return $this->db
            ->select('
            h.kd_jadwal,
            h.tanggal_jadwal,
            h.kd_standarisasi,
            h.keterangan,
            h.id_pegawai,
            h.jam_datang,
            h.jam_keluar,
            h.id_scan_masuk,
            h.id_scan_keluar,
            h.lembur,
            h.active,

            s.KD_STANDARISASI,
            s.STANDARISASI,
            s.JAM_MASUK,
            s.JAM_PULANG,
            s.TOLERANSI,
            s.PENDEK,
            s.TOTAL_JAM,
            s.JENIS_JADWAL
        ')
            ->from('t_perencanaan_harikerja h')
            ->join(
                'standarisasi s',
                's.KD_STANDARISASI = h.kd_standarisasi',
                'left'
            )
            ->where('h.id_pegawai', $id_pegawai)
            ->where('h.tanggal_jadwal', $tanggal)
            ->order_by('h.kd_jadwal', 'ASC')
            ->get()
            ->result();
    }


    /**
     * ============================================================
     * MENENTUKAN TRANSAKSI ABSENSI
     * ============================================================
     *
     * Return:
     *
     * CHECK_IN
     * CHECK_OUT
     * ALREADY_ATTENDANCE
     * NO_SCHEDULE
     */
    public function get_transaction_status(
        $id_pegawai,
        $tanggal = NULL,
        $server_datetime = NULL
    ) {
        if (!$tanggal) {
            $tanggal = date('Y-m-d');
        }

        if (!$server_datetime) {
            $server_datetime = date('Y-m-d H:i:s');
        }

        /*
     * Ambil jadwal hari ini.
     */
        $rows = $this->get_status(
            $id_pegawai,
            $tanggal
        );

        /*
     * Tidak ada jadwal.
     */
        if (empty($rows)) {

            return array(
                'code' => 'NO_SCHEDULE',
                'can_check_in' => FALSE,
                'can_check_out' => FALSE,
                'schedule' => NULL
            );
        }


        /*
     * Gunakan jadwal pertama.
     */
        $row = $rows[0];


        /*
     * Normalisasi jam datang.
     */
        $jam_datang = $row->jam_datang;

        if (
            $jam_datang === NULL ||
            trim((string) $jam_datang) === '' ||
            $jam_datang === '0000-00-00 00:00:00' ||
            $jam_datang === '0000-00-00'
        ) {
            $has_in = FALSE;
        } else {
            $has_in = TRUE;
        }


        /*
     * Normalisasi jam keluar.
     */
        $jam_keluar = $row->jam_keluar;

        if (
            $jam_keluar === NULL ||
            trim((string) $jam_keluar) === '' ||
            $jam_keluar === '0000-00-00 00:00:00' ||
            $jam_keluar === '0000-00-00'
        ) {
            $has_out = FALSE;
        } else {
            $has_out = TRUE;
        }


        /*
     * ==========================================
     * BELUM MASUK DAN BELUM PULANG
     * ==========================================
     */

        if ($has_in === FALSE && $has_out === FALSE) {

            return array(
                'code' => 'CHECK_IN',
                'can_check_in' => TRUE,
                'can_check_out' => FALSE,
                'schedule' => $row
            );
        }


        /*
     * ==========================================
     * SUDAH MASUK, BELUM PULANG
     * ==========================================
     */

        if ($has_in === TRUE && $has_out === FALSE) {

            return array(
                'code' => 'CHECK_OUT',
                'can_check_in' => FALSE,
                'can_check_out' => TRUE,
                'schedule' => $row
            );
        }


        /*
     * ==========================================
     * SUDAH MASUK DAN SUDAH PULANG
     * ==========================================
     */

        if ($has_in === TRUE && $has_out === TRUE) {

            return array(
                'code' => 'ALREADY_ATTENDANCE',
                'can_check_in' => FALSE,
                'can_check_out' => FALSE,
                'schedule' => $row
            );
        }


        /*
     * Fallback.
     */

        return array(
            'code' => 'CHECK_IN',
            'can_check_in' => TRUE,
            'can_check_out' => FALSE,
            'schedule' => $row
        );
    }

    /**
     * ============================================================
     * INSERT ABSENSI
     * ============================================================
     */
    public function insert_attendance($data)
    {
        return $this->db->insert(
            'att_log_mobile',
            $data
        );
    }


    /**
     * ============================================================
     * CEK ADA ABSENSI HARI INI
     * ============================================================
     *
     * Method ini kita siapkan untuk
     * pengembangan duplicate check berikutnya.
     */
    public function has_attendance_today(
        $id_pegawai,
        $tanggal
    ) {
        return $this->db
            ->where(
                'pin',
                (string) $id_pegawai
            )
            ->where(
                'DATE(scan_date)',
                $tanggal
            )
            ->count_all_results(
                'att_log_mobile'
            ) > 0;
    }
}
