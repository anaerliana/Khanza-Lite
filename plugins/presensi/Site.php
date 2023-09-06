<?php

namespace Plugins\Presensi;

use Systems\SiteModule;

class Site extends SiteModule
{

    public function routes()
    {
        $this->route('presensi/token', 'getToken');
        $this->route('presensi/ambil', 'getAmbilAntrian');
        $this->route('presensi/autopres', 'getAutoPresensi');
    }

    public function getToken()
    {
        echo $this->_resultToken();
        exit();
    }

    private function _resultToken()
    {
        header("Content-Type: application/json");
        $konten = trim(file_get_contents("php://input"));
        $header = apache_request_headers();
        $response = array();
        if ($header[$this->settings->get('presensi.header_username')] == $this->settings->get('presensi.x_username') && $header[$this->settings->get('presensi.header_password')] == $this->settings->get('presensi.x_password')) {
            $response = array(
                'status' => true,
                'response' => array(
                    'token' => $this->_getToken()
                ),
                'metadata' => array(
                    'message' => 'Ok',
                    'code' => 200
                )
            );
        } else {
            $response = array(
                'metadata' => array(
                    'message' => 'Access denied',
                    'code' => 201
                )
            );
        }
        echo json_encode($response);
    }

    public function getAmbilAntrian()
    {
        echo $this->_resultAmbilAntrian();
        exit();
    }

    private function _resultAmbilAntrian()
    {
        date_default_timezone_set($this->settings->get('settings.timezone'));

        header("Content-Type: application/json");
        $header = apache_request_headers();
        $konten = trim(file_get_contents("php://input"));
        $decode = json_decode($konten, true);
        $response = array();
        if ($header[$this->settings->get('presensi.header_token')] == false) {
            $response = array(
                'metadata' => array(
                    'message' => 'Token expired',
                    'code' => 201
                )
            );
            http_response_code(201);
        } else if ($header[$this->settings->get('presensi.header_token')] == $this->_getToken() && $header[$this->settings->get('presensi.header_username')] == $this->settings->get('presensi.x_username')) {

            $tanggalawal = $decode['periode'];
            if (!preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $tanggalawal)) {
                $errors[] = 'Format tanggal awal jadwal presensi tidak sesuai';
            }
            $yearmonth = date("Ym", strtotime($tanggalawal));
            $year = date("Y", strtotime($tanggalawal));
            $month = date("m", strtotime($tanggalawal));

            $absen = $this->db('bridging_bkd_presensi')->where('tahun', $year)->where('bulan',$month)->where('status','PNS')->limit(5)->toArray();
            if (!empty($errors)) {
                foreach ($errors as $error) {
                    $response = array(
                        'metadata' => array(
                            'message' => $this->_getErrors($error),
                            'code' => 201
                        )
                    );
                };
                http_response_code(201);
            } else {
                if (!$absen) {
                    $response = array(
                        'metadata' => array(
                            'message' =>  'Data presensi tidak ditemukan',
                            'code' => 202
                        )
                    );
                    http_response_code(202);
                } else {
                    $data = array();
                    foreach ($absen as $key) {
                        $rekap = $this->db('rekap_bkd')->where('id',$key['id'])->where('bulan',$month)->where('tahun', $year)->oneArray();
                        $data[] = array(
                            'rekap_finalisasi_id' => "",
                            'userid' => "",
                            'nip' => $key['nip'],
                            'jumlah_kehadiran' => (float)$key['jumlah_kehadiran'],
                            'jumlah_hari_kerja' => (float)$key['jumlah_hari_kerja'],
                            'persentase_hari_kerja' => 1,
                            'dl1' => "",
                            'dl2' => "",
                            'cuti_melahirkan' => (float)$rekap['cuti_melahirkan'],
                            'cuti_besar' => (float)$rekap['cuti'],
                            'izin' => (float)$rekap['izin'],
                            'sakit' => (float)$rekap['sakit'],
                            'sakit_lbh_10_hari' => "",
                            'sakit_4_10_hari' => "",
                            'sakit_10_hari' => "",
                            'jlh_izin_akumulasi_akhir' => "",
                            'jlh_pot_hari_izin_akhir' => 0,
                            'jlh_akumulasi_cuti' => "",
                            'jlh_over_cuti' => "",
                            'sakit_seb_10_hari' => "",
                            'pot_manunggal' => "",
                            'tk1' => 0,
                            'tk2' => "",
                            'jml_pot_keterlambatan' => (float)$key['jml_pot_keterlambatan'],
                            'jml_pot_pulang_lebih_awal' => (float)$key['jml_pot_pulang_lebih_awal'],
                            'jml_pot_lebih_10_hari' => "",
                            'jml_pot_sakit_4_10_hari' => "",
                            'jml_pot_sakit_10_hari' => "",
                            'jml_pot_over_akum_cuti' => "",
                            'jml_pot_over_cuti_bln_aktif' => 0,
                            'jml_pot_tanpa_keterangan' => "",
                            'jml_pot_lupa_absen_masuk' => "",
                            'jml_pot_kelebihan_cuti_akhir' => "",
                            'total_potongan' => $key['jml_pot_keterlambatan'] + $key['jml_pot_pulang_lebih_awal'],
                            'persentase_final' => (float)1 - ($key['jml_pot_keterlambatan'] + $key['jml_pot_pulang_lebih_awal']),
                            'defaultdeptid' => "40",
                            'periode' => $yearmonth,
                            'create_date' => $this->getTimestamp(),
                            'last_sync' => "",
                            'nama_pegawai' => $key['nama'],
                            'nm_skpd' => "RSUD H. DAMANHURI"
                        );
                    }
                    $response = array(
                        'status' => true,
                        'result' => array(
                            'data' => $data,
                        ),
                        'metadata' => array(
                            'message' => 'Ok',
                            'code' => 200
                        )
                    );
                    http_response_code(200);
                }
            }
        } else {
            $response = array(
                'metadata' => array(
                    'message' => 'Access denied',
                    'code' => 201
                )
            );
            http_response_code(201);
        }
        echo json_encode($response);
    }

    public function getAutoPresensi()
    {
        echo $this->_resultAutoPresensi();
        exit();
    }

    private function _resultAutoPresensi()
    {
        $check = $this->db('temporary_presensi')->toArray();
        echo "Cek Presensi : Initializing...<br><br>";
        for ($i=0; $i < count($check); $i++) {
            $nama = $this->db('pegawai')->where('id',$check[$i]['id'])->oneArray();
            if (stripos($check[$i]['shift'], 'malam') !== false) {
                if ((strtotime(date('Y-m-d H:i:s')) - strtotime($check[$i]['jam_datang'])) > (14*60*60)) {
                    echo "Karyawan dengan Nama ".$nama['nama']." Lewat 14 Jam Presensi<br>";
                    echo "Menjalankan Program Auto Verif ...<br>";
                    $awal  = new \DateTime($check[$i]['jam_datang']);
                    $akhir = new \DateTime();
                    $diff = $akhir->diff($awal, true); // to make the difference to be always positive.
                    $durasi = $diff->format('%H:%I:%S');
                    $simpanPresensi = $this->db('rekap_presensi')->save([
                        'id' => $check[$i]['id'],
                        'shift' => $check[$i]['shift'],
                        'jam_datang' => $check[$i]['jam_datang'],
                        'jam_pulang' => date('Y-m-d H:i:s'),
                        'status' => $check[$i]['status'],
                        'keterlambatan' => $check[$i]['keterlambatan'],
                        'durasi' => $durasi,
                        'keterangan' => 'Auto Verif',
                        'photo' => $check[$i]['photo']
                    ]);
                    if ($simpanPresensi) {
                        echo "Berhasil Menjalankan Program Auto Verif .<br>";
                        $this->db('temporary_presensi')->where('id', $check[$i]['id'])->delete();
                    }
                    echo "Program Shuting Down ...<br>";
                    echo "<br>";
                } else {
                    echo "Karyawan dengan Nama ".$nama['nama']." Shift ".$check[$i]['shift']." belum melebihi 14 Jam Presensi<br>";
                }
            } else {
                if ((strtotime(date('Y-m-d H:i:s')) - strtotime($check[$i]['jam_datang'])) > (8*60*60)) {
                    echo "Karyawan dengan Nama ".$nama['nama']." Lewat 8 Jam Presensi<br>";
                    echo "Menjalankan Program Auto Verif ...<br>";
                    $awal  = new \DateTime($check[$i]['jam_datang']);
                    $akhir = new \DateTime();
                    $diff = $akhir->diff($awal, true); // to make the difference to be always positive.
                    $durasi = $diff->format('%H:%I:%S');
                    $simpanPresensi = $this->db('rekap_presensi')->save([
                        'id' => $check[$i]['id'],
                        'shift' => $check[$i]['shift'],
                        'jam_datang' => $check[$i]['jam_datang'],
                        'jam_pulang' => date('Y-m-d H:i:s'),
                        'status' => $check[$i]['status'],
                        'keterlambatan' => $check[$i]['keterlambatan'],
                        'durasi' => $durasi,
                        'keterangan' => 'Auto Verif',
                        'photo' => $check[$i]['photo']
                    ]);
                    if ($simpanPresensi) {
                        echo "Berhasil Menjalankan Program Auto Verif .<br>";
                        $this->db('temporary_presensi')->where('id', $check[$i]['id'])->delete();
                    }
                    echo "Program Shuting Down ...<br>";
                    echo "<br>";
                } else {
                    echo "Karyawan dengan Nama ".$nama['nama']." Shift ".$check[$i]['shift']." belum melebihi 8 Jam Presensi<br>";
                }
            }
        }
        exit();
    }

    function getTimestamp()
    {
        $microtime = floatval(substr((string)microtime(), 1, 8));
        $rounded = round($microtime, 3);
        return date("Y-m-d H:i:s") . substr((string)$rounded, 1, strlen($rounded));
    }

    private function _getToken()
    {
        $header = json_encode(['typ' => 'JWT', 'alg' => 'HS256']);
        $payload = json_encode(['username' => $this->settings->get('presensi.x_username'), 'password' => $this->settings->get('presensi.x_password'), 'date' => strtotime(date('Y-m-d')) * 1000]);
        $base64UrlHeader = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($header));
        $base64UrlPayload = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($payload));
        $signature = hash_hmac('sha256', $base64UrlHeader . "." . $base64UrlPayload, 'abC123!', true);
        $base64UrlSignature = str_replace(['+', '/', '='], ['-', '_', ''], base64_encode($signature));
        $jwt = $base64UrlHeader . "." . $base64UrlPayload . "." . $base64UrlSignature;
        return $jwt;
    }

    private function _getErrors($error)
    {
        $errors = $error;
        return $errors;
    }
}
