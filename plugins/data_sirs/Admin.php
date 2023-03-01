<?php

namespace Plugins\Data_Sirs;

use session;
use Systems\AdminModule;

class Admin extends AdminModule
{

    public function init()
    {
        $this->id_sirs = $this->settings->get('sirs_online.id_sirs');
        $this->password = $this->settings->get('sirs_online.password');
        $this->email = $this->settings->get('sirs_online.email');
        $this->password_v3 = $this->settings->get('sirs_online.password_v3');
        $this->url = $this->settings->get('sirs_online.url');
        $this->url_v3 = $this->settings->get('sirs_online.url_v3');
    }

    public function navigation()
    {
        return [
            'Index' => 'manage',
            'Data Obat' => 'index',
            'Data Covid 19' => 'covid',
            'Pengaturan' => 'settings',
        ];
    }

    public function getManage()
    {
        $sub_modules = [
            ['name' => 'Data Obat', 'url' => url([ADMIN, 'data_sirs', 'index']), 'icon' => 'medkit', 'desc' => 'Data Obat'],
            ['name' => 'Pasien Masuk Covid 19', 'url' => url([ADMIN, 'data_sirs', 'covid']), 'icon' => 'user-plus', 'desc' => 'Tambah data pasien masuk covid 19'],
            ['name' => 'Pasien Keluar Covid 19', 'url' => url([ADMIN, 'data_sirs', 'pasienkeluar']), 'icon' => 'minus-square', 'desc' => 'Tambah data pasien keluar covid 19'],
            ['name' => 'Data Bed Covid 19', 'url' => url([ADMIN, 'data_sirs', 'bedcovid']), 'icon' => 'bed', 'desc' => 'Data Bed covid 19'],
            ['name' => 'Data Bed Non Covid 19', 'url' => url([ADMIN, 'data_sirs', 'bednoncovid']), 'icon' => 'bed', 'desc' => 'Data Bed non covid 19'],
            ['name' => 'Data APD Covid 19', 'url' => url([ADMIN, 'data_sirs', 'apdcovid']), 'icon' => 'shield', 'desc' => 'Data APD covid 19'],
            ['name' => 'Laporan RL 1.2', 'url' => url([ADMIN, 'data_sirs', 'rl12']), 'icon' => 'shield', 'desc' => 'Data Laporan RL 1.2'],
            ['name' => 'Laporan RL 1.3', 'url' => url([ADMIN, 'data_sirs', 'rl13']), 'icon' => 'shield', 'desc' => 'Data Laporan RL 1.3'],
            ['name' => 'Laporan RL 3.1', 'url' => url([ADMIN, 'data_sirs', 'rl31']), 'icon' => 'shield', 'desc' => 'Data Laporan RL 3.1'],
            ['name' => 'Laporan RL 3.2', 'url' => url([ADMIN, 'data_sirs', 'rl32']), 'icon' => 'shield', 'desc' => 'Data Laporan RL 3.2'],
            ['name' => 'Laporan RL 3.14', 'url' => url([ADMIN, 'data_sirs', 'rl314']), 'icon' => 'shield', 'desc' => 'Data Laporan RL 3.14'],
            ['name' => 'Laporan RL 3.15', 'url' => url([ADMIN, 'data_sirs', 'rl315']), 'icon' => 'shield', 'desc' => 'Data Laporan RL 3.15'],
            ['name' => 'Laporan RL 4.A', 'url' => url([ADMIN, 'data_sirs', 'rl4a']), 'icon' => 'shield', 'desc' => 'Data Laporan RL 4.A'],
            ['name' => 'Laporan RL 4.B', 'url' => url([ADMIN, 'data_sirs', 'rl4b']), 'icon' => 'shield', 'desc' => 'Data Laporan RL 4.B'],
            ['name' => 'Pengaturan', 'url' => url([ADMIN, 'data_sirs', 'settings']), 'icon' => 'gear', 'desc' => 'Pengaturan Sirs Online'],
        ];
        return $this->draw('index.html', ['sub_modules' => $sub_modules]);
    }

    public function getIndex()
    {
        $this->assign['yesterday'] = date('Y-m-d', strtotime("-1 days"));
        $today = date('Y-m-d');
        $yesterday = $this->assign['yesterday'];
        $namaObat = array(
            'B000010146' => 'Remdesivir Inj 100 mg',
            'B000010182' => 'Favipiravir 200 mg',
            'OBT000000056' => 'Vit C (Asam askorbat) inj 1000 mg',
            'B000001456' => 'Vit C (Asam askorbat) tab 250 mg',
            'OBT000000167' => 'Vit C (Asam askorbat) tab 500 mg',
            'B000009484' => 'Zinc sirup 20 mg / 5 ml',
            'OBT0418' => 'Zinc tab dispersible 20 mg',
            'OBT000000069' => 'Oseltamivir tab 75 mg',
            'OBT0055' => 'Azitromisin tab 500mg',
            'B000010270' => 'Azitromisin 500 mg Inj',
            'OBT0250' => 'Levofloxacin infus 5 mg/mL',
            'OBT0473' => 'Levofloxacin tab 750 mg',
            'OBT0249' => 'Levofloxacin tab 500 mg',
            'OBT0133' => 'Deksametason Inj 5 mg/mL',
            'B000001325' => 'Deksametason tab 0.5mg',
            'OBT0047' => 'N- Asetil Sistein kap 200 mg',
            'OBT0547' => 'Heparin Na inj 5.000 IU/mL (i.v./s.k.)',
            'B000001324' => 'Enoksaparin sodium inj 10.000 IU/mL',

            'B000010173' => 'Fondaparinux inj 2,5 mg/0,5 mL'
        );

        $query = "SELECT SUM(gudangbarang.stok) as sum , databarang.kode_brng as kode , databarang.nama_brng as nama FROM gudangbarang JOIN databarang ON gudangbarang.kode_brng = databarang.kode_brng WHERE gudangbarang.kode_brng IN ('B000010146','B000010182','OBT000000056','B000001456','OBT000000167','B000009484','OBT0418','OBT000000069','OBT0055','B000010270','OBT0250','OBT0473','OBT0249','OBT0133','B000001325','OBT0047','OBT0547','B000001324','B000009543','B000010173','B000010147','B000010151','OBT0323','OBT0671','OBT0268','B000001172','OBT0677','B000010345') AND gudangbarang.kd_bangsal IN ('B0001','B0002','B0014','B0018') GROUP BY gudangbarang.kode_brng";
        $stmt = $this->db()->pdo()->prepare($query);
        $stmt->execute();
        $rows = $stmt->fetchAll();
        $this->assign['sedia'] = [];
        foreach ($rows as $row) {
            if ($namaObat[$row['kode']]) {
                $row['nama'] = $namaObat[$row['kode']];
            } else {
                $row['nama'] = $row['nama'];
            }
            $query1 = "SELECT SUM(detail_pemberian_obat.jml) FROM detail_pemberian_obat , kamar_inap , reg_periksa , diagnosa_pasien WHERE detail_pemberian_obat.no_rawat = kamar_inap.no_rawat AND kamar_inap.no_rawat = reg_periksa.no_rawat AND reg_periksa.no_rawat = diagnosa_pasien.no_rawat AND diagnosa_pasien.kd_penyakit IN ('B34.2','Z03.8') AND detail_pemberian_obat.kode_brng = '" . $row['kode'] . "' AND detail_pemberian_obat.tgl_perawatan = '$yesterday' GROUP BY detail_pemberian_obat.kode_brng";
            $stmt1 = $this->db()->pdo()->prepare($query1);
            $stmt1->execute();
            $rows1 = $stmt1->fetchColumn();
            $row['jml'] = $rows1;
            if ($row['jml'] == '') {
                $row['jml'] = '0';
            } else {
                $row['jml'] = $rows1;
            }
            $this->assign['sedia'][] = $row;
        }

        return $this->draw('manage.html', ['sirs' => $this->assign]);
    }

    public function anyCovid()
    {
        $tgl_masuk = '';
        $tgl_masuk_akhir = '';
        $status_pulang = '';
        $this->assign['stts_pulang'] = [];

        if (isset($_POST['periode_rawat_inap'])) {
            $tgl_masuk = $_POST['periode_rawat_inap'];
        }
        if (isset($_POST['periode_rawat_inap_akhir'])) {
            $tgl_masuk_akhir = $_POST['periode_rawat_inap_akhir'];
        }
        if (isset($_POST['status_pulang'])) {
            $status_pulang = $_POST['status_pulang'];
        }
        $this->_Display($tgl_masuk, $tgl_masuk_akhir, $status_pulang);
        return $this->draw('covidlist.html', ['rawat_inap' => $this->assign]);
    }

    public function _Display($tgl_masuk = '', $tgl_masuk_akhir = '', $status_pulang = '')
    {
        $this->_addHeaderFiles();

        $this->assign['kamar'] = $this->db('kamar')->join('bangsal', 'bangsal.kd_bangsal=kamar.kd_bangsal')->where('statusdata', '1')->toArray();
        $this->assign['dokter'] = $this->db('dokter')->where('status', '1')->toArray();
        $this->assign['penjab'] = $this->db('penjab')->toArray();
        $this->assign['no_rawat'] = '';

        $bangsal = str_replace(",", "','", $this->core->getUserInfo('cap', null, true));

        $sql = "SELECT
            kamar_inap.*,
            reg_periksa.*,
            pasien.*,
            kamar.*,
            bangsal.*,
            penjab.*
          FROM
            kamar_inap,
            reg_periksa,
            pasien,
            kamar,
            bangsal,
            penjab
          WHERE
            kamar_inap.no_rawat=reg_periksa.no_rawat
          AND
            reg_periksa.no_rkm_medis=pasien.no_rkm_medis
          AND
            kamar_inap.kd_kamar=kamar.kd_kamar
          AND
            bangsal.kd_bangsal=kamar.kd_bangsal
          AND
            reg_periksa.kd_pj=penjab.kd_pj";

        if ($this->core->getUserInfo('role') != 'admin') {
            $sql .= " AND bangsal.kd_bangsal IN ('$bangsal')";
        }
        if ($status_pulang == '') {
            $sql .= " AND kamar_inap.stts_pulang = '-'";
        }
        if ($status_pulang == 'all' && $tgl_masuk !== '' && $tgl_masuk_akhir !== '') {
            $sql .= " AND kamar_inap.stts_pulang = '-' AND kamar_inap.tgl_masuk BETWEEN '$tgl_masuk' AND '$tgl_masuk_akhir'";
        }
        if ($status_pulang == 'masuk' && $tgl_masuk !== '' && $tgl_masuk_akhir !== '') {
            $sql .= " AND kamar_inap.tgl_masuk BETWEEN '$tgl_masuk' AND '$tgl_masuk_akhir'";
        }
        if ($status_pulang == 'pulang' && $tgl_masuk !== '' && $tgl_masuk_akhir !== '') {
            $sql .= " AND kamar_inap.tgl_keluar BETWEEN '$tgl_masuk' AND '$tgl_masuk_akhir'";
        }

        $stmt = $this->db()->pdo()->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        $this->assign['list'] = [];
        foreach ($rows as $row) {
            $dpjp_ranap = $this->db('dpjp_ranap')
                ->join('dokter', 'dokter.kd_dokter=dpjp_ranap.kd_dokter')
                ->where('no_rawat', $row['no_rawat'])
                ->toArray();
            $row['dokter'] = $dpjp_ranap;
            $isBridging = $this->db('bridging_covid')->where('no_rawat', $row['no_rawat'])->oneArray();
            if (!$isBridging) {
                $row['status_bridging'] = false;
            } else {
                $row['status_bridging'] = true;
            }
            $this->assign['list'][] = $row;
        }

        if (isset($_POST['no_rawat'])) {
            $this->assign['kamar_inap'] = $this->db('kamar_inap')
                ->join('reg_periksa', 'reg_periksa.no_rawat=kamar_inap.no_rawat')
                ->join('pasien', 'pasien.no_rkm_medis=reg_periksa.no_rkm_medis')
                ->join('kamar', 'kamar.kd_kamar=kamar_inap.kd_kamar')
                ->join('dpjp_ranap', 'dpjp_ranap.no_rawat=kamar_inap.no_rawat')
                ->join('dokter', 'dokter.kd_dokter=dpjp_ranap.kd_dokter')
                ->join('penjab', 'penjab.kd_pj=reg_periksa.kd_pj')
                ->where('kamar_inap.no_rawat', $_POST['no_rawat'])
                ->oneArray();
        } else {
            $this->assign['kamar_inap'] = [
                'tgl_masuk' => date('Y-m-d'),
                'jam_masuk' => date('H:i:s'),
                'tgl_keluar' => date('Y-m-d'),
                'jam_keluar' => date('H:i:s'),
                'no_rkm_medis' => '',
                'nm_pasien' => '',
                'no_rawat' => '',
                'kd_dokter' => '',
                'kd_kamar' => '',
                'kd_pj' => '',
                'diagnosa_awal' => '',
                'diagnosa_akhir' => '',
                'stts_pulang' => '',
                'lama' => ''
            ];
        }
    }

    public function anyCovidForm()
    {
        function initials($str)
        {
            $words = explode(" ", $str);
            $acronym = "";
            $w = array();
            foreach ($words as $w) {
                $acronym .= substr($w, 0, 1);
            }

            return $acronym;
        }

        $rows = $this->db('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis = pasien.no_rkm_medis')
            ->where('no_rawat', $_POST['no_rawat'])
            ->oneArray();
        $result = [];
        $result = $rows;
        $inisial = substr($rows['nm_pasien'], strpos($rows['nm_pasien'], "BIN"));
        $inisialcut = str_replace($inisial, '', $rows['nm_pasien']);
        if ($inisialcut == '') {
            $result['inisial'] = initials($inisial);
        } else {
            $result['inisial'] = initials($inisialcut);
        }
        $result['kode'] = $this->getKecamatan($rows['kd_kec']);
        $result['tgl_inap_masuk'] = $this->db('kamar_inap')->select('tgl_masuk')->where('no_rawat', $_POST['no_rawat'])->oneArray();
        echo $this->draw('covidform.html', ['pasien' => $result]);
        exit();
    }

    public function getPasienKeluar()
    {
        $this->_addHeaderFiles();
        $this->assign['list'] = $this->db('bridging_covid')->join('reg_periksa','reg_periksa.no_rawat = bridging_covid.no_rawat')
        ->join('pasien','pasien.no_rkm_medis = reg_periksa.no_rkm_medis')->toArray();
        return $this->draw('pasienkeluar.html',['list' => $this->assign]);
    }

    public function anyPasienKeluarForm()
    {
        $rows = $this->db('reg_periksa')
            ->join('pasien', 'reg_periksa.no_rkm_medis = pasien.no_rkm_medis')
            ->where('no_rawat', $_POST['no_rawat'])
            ->oneArray();
        $result = [];
        $result = $rows;
        $result['tgl_inap_keluar'] = $this->db('kamar_inap')->select(['tgl_keluar' => 'tgl_keluar', 'stts_pulang' => 'stts_pulang'])->where('no_rawat', $_POST['no_rawat'])->desc('tgl_keluar')->oneArray();
        $result['form'] = $this->db('bridging_covid')->where('no_rawat', $rows['no_rawat'])->oneArray();
        echo $this->draw('pasienkeluarform.html',['form' => $result]);
        exit();
    }

    public function getToken()
    {
        $url = $this->url_v3 . "api/rslogin";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_POST, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
            "Content-Type: application/json",
            "accept: */*",
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        if ($this->url_v3 == "http://202.70.136.86:3020/") {
            $data = '{"kode_rs": "' . $this->id_sirs . '", "password": "' . $this->password . '"}';
        } else {
            $data = '{"userName": "' . $this->email . '", "password": "' . $this->password_v3 . '"}';
        }


        curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);
        // var_dump($resp);
        return $resp;
    }

    public function getTokenStored()
    {
        if (isset($_COOKIE['tokenCookie'])) {
            return $_COOKIE['tokenCookie'];
        } else {
            $token = $this->getToken();
            $token = json_decode($token, true);
            setcookie("tokenCookie", $token['data']['access_token'], time() + 400);
            return $_COOKIE['tokenCookie'];
        }
    }

    public function bridgingSirsV3($param_url)
    {
        $token = $this->getTokenStored();
        $url = $this->url_v3 . $param_url;

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
            "Content-Type: application/json",
            "accept: */*",
            'Authorization: Bearer ' . $token,
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);
        $json = json_decode($resp, true);
        return $json;
    }

    public function getVarianCovid()
    {
        $json = $this->bridgingSirsV3('api/variancovid?page=1&limit=1000');
        $code = $json['status'];
        $message = $json['message'];
        if ($json != null) {
            echo '{
                    "metaData": {
                        "code": "' . $code . '",
                        "message": "' . $message . '"
                    },
                    "response": ' . json_encode($json['data']) . '}';
        } else {
            echo '{
                    "metaData": {
                        "code": "5000",
                        "message": "ERROR"
                    },
                    "response": "ADA KESALAHAN ATAU SAMBUNGAN KE SERVER KEMENKES TERPUTUS."}';
        }
        exit();
    }

    public function getStatusKeluar()
    {
        $token = $this->getTokenStored();
        $url = $this->url_v3 . "api/kecamatan?page=1&limit=1000";

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
            "Content-Type: application/json",
            "accept: */*",
            'Authorization: Bearer ' . $token,
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);
        $json = json_decode($resp, true);
        $code = $json['status'];
        $message = $json['message'];
        if ($json != null) {
            echo '{
                    "metaData": {
                        "code": "' . $code . '",
                        "message": "' . $message . '"
                    },
                    "response": ' . json_encode($json['data']) . '}';
        } else {
            echo '{
                    "metaData": {
                        "code": "5000",
                        "message": "ERROR"
                    },
                    "response": "ADA KESALAHAN ATAU SAMBUNGAN KE SERVER KEMENKES TERPUTUS."}';
        }
        exit();
    }

    public function getKecamatan($kecamatan)
    {
        switch ($kecamatan) {
            case '3520':
                $kode['kecamatan'] = '630708 - BATANG ALAI UTARA';
                $kode['kabupaten'] = '6307 - KAB. HULU SUNGAI TENGAH';
                $kode['provinsi'] = '63 - KALIMANTAN SELATAN';
                $kode['warganegara'] = 'ID - INDONESIA';
                return $kode;
                break;
            case '3519':
                $kode['kecamatan'] = '630707 - BATANG ALAI SELATAN';
                $kode['kabupaten'] = '6307 - KAB. HULU SUNGAI TENGAH';
                $kode['provinsi'] = '63 - KALIMANTAN SELATAN';
                $kode['warganegara'] = 'ID - INDONESIA';
                return $kode;
                break;
            case '4337':
                $kode['kecamatan'] = '630710 - BATANG ALAI TIMUR';
                $kode['kabupaten'] = '6307 - KAB. HULU SUNGAI TENGAH';
                $kode['provinsi'] = '63 - KALIMANTAN SELATAN';
                $kode['warganegara'] = 'ID - INDONESIA';
                return $kode;
                break;
            case '3523':
                $kode['kecamatan'] = '630701 - HARUYAN';
                $kode['kabupaten'] = '6307 - KAB. HULU SUNGAI TENGAH';
                $kode['provinsi'] = '63 - KALIMANTAN SELATAN';
                $kode['warganegara'] = 'ID - INDONESIA';
                return $kode;
                break;
            case '3521':
                $kode['kecamatan'] = '630702 - BATU BENAWA';
                $kode['kabupaten'] = '6307 - KAB. HULU SUNGAI TENGAH';
                $kode['provinsi'] = '63 - KALIMANTAN SELATAN';
                $kode['warganegara'] = 'ID - INDONESIA';
                return $kode;
                break;
            case '3524':
                $kode['kecamatan'] = '630703 - LABUAN AMAS SELATAN';
                $kode['kabupaten'] = '6307 - KAB. HULU SUNGAI TENGAH';
                $kode['provinsi'] = '63 - KALIMANTAN SELATAN';
                $kode['warganegara'] = 'ID - INDONESIA';
                return $kode;
                break;
            case '3525':
                $kode['kecamatan'] = '630704 - LABUAN AMAS UTARA';
                $kode['kabupaten'] = '6307 - KAB. HULU SUNGAI TENGAH';
                $kode['provinsi'] = '63 - KALIMANTAN SELATAN';
                $kode['warganegara'] = 'ID - INDONESIA';
                return $kode;
                break;
            case '3526':
                $kode['kecamatan'] = '630705 - PANDAWAN';
                $kode['kabupaten'] = '6307 - KAB. HULU SUNGAI TENGAH';
                $kode['provinsi'] = '63 - KALIMANTAN SELATAN';
                $kode['warganegara'] = 'ID - INDONESIA';
                return $kode;
                break;
            case '3518':
                $kode['kecamatan'] = '630706 - BARABAI';
                $kode['kabupaten'] = '6307 - KAB. HULU SUNGAI TENGAH';
                $kode['provinsi'] = '63 - KALIMANTAN SELATAN';
                $kode['warganegara'] = 'ID - INDONESIA';
                return $kode;
                break;
            case '3522':
                $kode['kecamatan'] = '630709 - HANTAKAN';
                $kode['kabupaten'] = '6307 - KAB. HULU SUNGAI TENGAH';
                $kode['provinsi'] = '63 - KALIMANTAN SELATAN';
                $kode['warganegara'] = 'ID - INDONESIA';
                return $kode;
                break;
            case '4300':
                $kode['kecamatan'] = '630711 - LIMPASU';
                $kode['kabupaten'] = '6307 - KAB. HULU SUNGAI TENGAH';
                $kode['provinsi'] = '63 - KALIMANTAN SELATAN';
                $kode['warganegara'] = 'ID - INDONESIA';
                return $kode;
                break;
        }
    }

    public function postApiSirs($url_api , $datafield = [])
    {
        $token = $this->getTokenStored();
        $url = $this->url_v3 . $url_api;

        $curl = curl_init($url);
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "POST");
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);

        $headers = array(
            "Content-Type: application/json",
            "accept: */*",
            'Authorization: Bearer ' . $token,
        );
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);

        curl_setopt($curl, CURLOPT_POSTFIELDS, $datafield);
        //for debug only!
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);

        $resp = curl_exec($curl);
        curl_close($curl);
        $ret = json_decode($resp, true);
        return $ret;
    }

    public function postSaveCovid()
    {
        $data = [
            'kewarganegaraanId' => substr($_POST['warga'], 0, 2),
            'nik' => $_POST['nik'],
            'noPassport' => null,
            'asalPasienId' => substr($_POST['asal_pasien'], 0, 1),
            'noRM' => $_POST['no_rkm_medis'],
            'namaLengkapPasien' => $_POST['nm_pasien'],
            'namaInisialPasien' => $_POST['inisial'],
            'tanggalLahir' => $_POST['tgl_lahir'],
            'email' => null,
            'noTelp' => $_POST['no_telp'],
            'jenisKelaminId' => $_POST['jk'],
            'domisiliKecamatanId' => substr($_POST['kec'], 0, 6),
            'domisiliKabKotaId' => substr($_POST['kab'], 0, 4),
            'domisiliProvinsiId' => substr($_POST['prov'], 0, 2),
            'pekerjaanId' => substr($_POST['pekerjaan_pasien'], 0, 1),
            'tanggalMasuk' => $_POST['tgl_perawatan'],
            'jenisPasienId' => substr($_POST['jenis_rawat'], 0, 1),
            'varianCovidId' => substr($_POST['varian_covid'], 0, 1),
            'statusPasienId' => substr($_POST['status_pasien'], 0, 1),
            'statusCoInsidenId' => $_POST['coinsiden'],
            'statusRawatId' => substr($_POST['status_rawat'], 0, 2),
            'alatOksigenId' => $_POST['oksigen'] == "" ? null : substr($_POST['oksigen'], 0, 1),
            'penyintasId' => $_POST['penyintas'],
            'tanggalOnsetGejala' => $_POST['tgl_gejala'],
            'kelompokGejalaId' => substr($_POST['status_gejala'], 0, 1),
            'gejala' => [
                'demamId' => $_POST['demam'],
                'batukId' => $_POST['batuk'],
                'pilekId' => $_POST['pilek'],
                'sakitTenggorokanId' => $_POST['tenggorokan'],
                'sesakNapasId' => $_POST['sesak_nafas'],
                'lemasId' => $_POST['lemas'],
                'nyeriOtotId' => $_POST['nyeri'],
                'mualMuntahId' => $_POST['mual'],
                'diareId' => $_POST['diare'],
                'anosmiaId' => $_POST['anosmia'],
                'napasCepatId' => $_POST['nafas_cepat'],
                'frekNapas30KaliPerMenitId' => $_POST['frek'],
                'distresPernapasanBeratId' => $_POST['nafas_berat'],
                'lainnyaId' => $_POST['lainnya']
            ]
        ];

        $data = json_encode($data);
        $ret = $this->postApiSirs('api/laporancovid19versi3', $data);
        if ($ret == NULL) {
            $data = '{
                    "status": "false",
                    "message": "Koneksi ke server Kemenkes terputus. Silahkan ulangi beberapa saat lagi!"}';
            $data = json_decode($data, true);
            $ret = json_encode($data);
            echo $ret;
        } else if ($ret['status'] == true) {
            $code = $ret['status'];
            if ($ret != null) {

                $_POST['id'] = $ret['data']['id'];
                $simpan = $this->db('bridging_covid')->save([
                    'id' => $_POST['id'],
                    'no_rawat' => $_POST['no_rawat'],
                    'no_passport' => '',
                    'inisial' => $_POST['inisial'],
                    'tgl_onset' => $_POST['tgl_gejala'],
                    'warga' => substr($_POST['warga'], 0, 2),
                    'asal_pasien' => substr($_POST['asal_pasien'], 0, 1),
                    'jenis_pasien' => substr($_POST['jenis_rawat'], 0, 1),
                    'status_pasien' => substr($_POST['status_pasien'], 0, 1),
                    'status_rawat' => substr($_POST['status_rawat'], 0, 2),
                    'pekerjaan' => substr($_POST['pekerjaan_pasien'], 0, 1),
                    'kelompok_gejala' => substr($_POST['status_gejala'], 0, 1),
                    'varian_covid' => substr($_POST['varian_covid'], 0, 1),
                    'alat_oksigen' => $_POST['oksigen'] == "" ? '' : substr($_POST['oksigen'], 0, 1),
                    'penyintas' => $_POST['penyintas'],
                    'status_co' => $_POST['coinsiden'],
                    'demam' => $_POST['demam'],
                    'batuk' => $_POST['batuk'],
                    'pilek' => $_POST['pilek'],
                    'tenggorokan' => $_POST['tenggorokan'],
                    'sesak' => $_POST['sesak_nafas'],
                    'lemas' => $_POST['lemas'],
                    'nyeri' => $_POST['nyeri'],
                    'mual' => $_POST['mual'],
                    'diare' => $_POST['diare'],
                    'anosmia' => $_POST['anosmia'],
                    'nafas_cepat' => $_POST['nafas_cepat'],
                    'lainnya' => $_POST['lainnya'],
                    'distres' => $_POST['nafas_berat'],
                    'frekuensi' => $_POST['frek']
                ]);

                if ($simpan) {
                    $data = '{
                        "status": "' . $code . '",
                        "message": "Berhasil Menyimpan dengan Id : ' . $ret['data']['id'] . '"}';
                    $data = json_decode($data, true);
                    $ret = json_encode($data);
                    echo $ret;
                }
            } else {
                $data = '{
                    "status": "false",
                    "message": "Koneksi ke server Kemenkes terputus. Silahkan ulangi beberapa saat lagi!"}';
                $data = json_decode($data, true);
                $ret = json_encode($data);
                echo $ret;
            }
        } else {
            $ret = json_encode($ret);
            echo $ret;
        }
        exit();
    }

    public function postSaveCovidKeluar()
    {
        function cutString($string)
        {
            $word = ':';
            $wordPos = strpos($string, $word);
            $wordPosCut = substr($string, 0, $wordPos);
            if ($wordPos != '') {
                return $wordPosCut;
            }else{
                return null;
            }
        }

        function checkNullKu($string)
        {
            if ($string != '') {
                return substr($string, 0, 1);
            }else{
                return null;
            }
        }

        $data = [
            'laporanCovid19Versi3Id' => $_POST['id_covid'],
            'tanggalKeluar' => $_POST['tgl_keluar'],
            'statusKeluarId' => substr($_POST['status_keluar'], 0, 1),
            'penyebabKematianId' => checkNullKu($_POST['penyebab_kematian']),
            'penyebabKematianLangsungId' => cutString($_POST['penyebab_kematian_lgs']),
            'statusPasienSaatMeninggalId' => checkNullKu($_POST['status_pasien_meninggal']),
            'komorbidCoInsidenId' => cutString($_POST['komorbid']),
        ];

        $data = json_encode($data);
        $ret = $this->postApiSirs('api/laporancovid19versi3statuskeluar', $data);
        if ($ret == NULL) {
            $data = '{
                    "status": "false",
                    "message": "Koneksi ke server Kemenkes terputus. Silahkan ulangi beberapa saat lagi!"}';
            $data = json_decode($data, true);
            $ret = json_encode($data);
            echo $ret;
        } else if ($ret['status'] == true) {
            $code = $ret['status'];
            if ($ret != null) {
                $simpan['tgl_keluar'] = $_POST['tgl_keluar'];
                $simpan['status_keluar'] = substr($_POST['status_keluar'], 0, 1);
                $simpan['penyebab_kematian'] = checkNullKu($_POST['penyebab_kematian']);
                $simpan['penyebab_kematian_lgs'] = cutString($_POST['penyebab_kematian_lgs']);
                $simpan['status_pasien_meninggal'] = checkNullKu($_POST['status_pasien_meninggal']);
                $simpan['komorbid'] = cutString($_POST['komorbid']);

                $cari = $this->db('bridging_covid')->where('id',$_POST['id_covid'])->where('no_rawat',$_POST['no_rawat'])->save($simpan);

                if ($cari) {
                    $data = '{
                        "status": "' . $code . '",
                        "message": "Berhasil Memulangkan dengan Id : ' . $ret['data']['id'] . '"}';
                    $data = json_decode($data, true);
                    $ret = json_encode($data);
                    echo $ret;
                }
            } else {
                $data = '{
                    "status": "false",
                    "message": "Koneksi ke server Kemenkes terputus. Silahkan ulangi beberapa saat lagi!"}';
                $data = json_decode($data, true);
                $ret = json_encode($data);
                echo $ret;
            }
        } else {
            $ret = json_encode($ret);
            echo $ret;
        }
        exit();
    }

    public function bridgingSirs($param_url)
    {
        date_default_timezone_set('UTC');
        $tStamp = strval(time() - strtotime("1970-01-01 00:00:00"));


        $url = $this->url.$param_url;
        $headers = [
            "X-rs-id: " . $this->id_sirs,
            "X-Timestamp: " . $tStamp,
            "X-pass: " . $this->password,
            "Content-type: application/json"
        ];
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_URL, $url);
        curl_setopt($curl, CURLOPT_HEADER, false);
        curl_setopt($curl, CURLOPT_CUSTOMREQUEST, "GET");

        curl_setopt($curl, CURLOPT_FOLLOWLOCATION, true);
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, false);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, false);
        curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
        $err = curl_error($curl);
        $result = curl_exec($curl);
        curl_close($curl);

        $json = json_decode($result,true);
        return $json;
    }

    public function getBedCovid()
    {
        $this->_addHeaderFiles();
        $json = $this->bridgingSirs('Fasyankes');
        return $this->draw('bedcovidlist.html', ['rawat_inap' => $json['fasyankes']]);
    }

    public function getBedNonCovid()
    {
        $this->_addHeaderFiles();
        $json = $this->bridgingSirs('Fasyankes');
        return $this->draw('bednoncovid.html', ['rawat_inap' => $json['fasyankes']]);
    }

    public function getApdCovid()
    {
        $this->_addHeaderFiles();
        $json = $this->bridgingSirs('Fasyankes/apd');
        return $this->draw('apd.html', ['apd' => $json['apd']]);
    }

    public function getSettings()
    {
        $this->_addHeaderFiles();
        $this->assign['title'] = 'Pengaturan Modul Sirs Online';
        $this->assign['sirs_online'] = htmlspecialchars_array($this->settings('sirs_online'));
        return $this->draw('settings.html', ['settings' => $this->assign]);
    }

    public function postSaveSettings()
    {
        foreach ($_POST['sirs_online'] as $key => $val) {
            $this->settings('sirs_online', $key, $val);
        }
        $this->notify('success', 'Pengaturan telah disimpan');
        redirect(url([ADMIN, 'data_sirs', 'settings']));
    }

    public function getRl12()
    {
        $this->_addHeaderFiles();
        if (isset($_GET['y'])) {
            $tahun = $_GET['y'];
        } else {
            $tahun = date('Y');
        }

        $jmlKamar = $this->db('kamar')->select('kd_kamar')->where('statusdata','1')->count();
        $lama = $this->db('kamar_inap')->select(['lama' => 'SUM(lama)'])->like('tgl_masuk','%'.$tahun.'%')->oneArray();
        $bor = $lama['lama'] / ($jmlKamar * 365);
        $this->assign['bor'] = number_format($bor*100,2);

        $jmlPasien = $this->db('kamar_inap')->select('no_rawat')->like('tgl_masuk','%'.$tahun.'%')->count();
        $los = $lama['lama'] / $jmlPasien;
        $this->assign['los'] = number_format($los,2);

        $bto = $jmlPasien / $jmlKamar;
        $this->assign['bto'] = number_format($bto,2);

        $toi = (($jmlKamar * 365) - $lama['lama']) / $jmlPasien;
        $this->assign['toi'] = number_format($toi,2);

        $ndr = $this->db('kamar_inap')->select('no_rawat')->where('lama','>','2')->where('stts_pulang','Meninggal')->like('tgl_masuk','%'.$tahun.'%')->count();
        $ndr = ($ndr / $jmlPasien) * 1000;
        $this->assign['ndr'] = number_format($ndr,2);

        $gdr = $this->db('kamar_inap')->select('no_rawat')->where('stts_pulang','Meninggal')->like('tgl_masuk','%'.$tahun.'%')->count();
        $gdr = ($gdr / $jmlPasien) * 1000;
        $this->assign['gdr'] = number_format($gdr,2);

        $avg = $jmlPasien / 365;
        $this->assign['avg'] = number_format($avg,2);
        $this->assign['tahun'] = $tahun;

        return $this->draw('rl1_2.html',['rl12' => $this->assign]);
    }

    public function cariKamar($bangsal,$iso,$kelas = ''){
        $in = '';
        foreach ($bangsal as $value) {
            $in .= "'".$value."',";
        }
        $in = rtrim($in,",");
        $sql = "SELECT COUNT(kd_kamar) as jml FROM kamar WHERE kd_bangsal NOT IN ($in) AND statusdata = '1' ";
        if ($iso == '0'){
            $sql .= "AND kd_kamar NOT LIKE '%iso%'";
        } else {
            $sql .= "AND kd_kamar LIKE '%iso%'";
        }
        if ($kelas != '') {
            $sql .= "AND kelas = '{$kelas}'";
        }
        $stmt = $this->db()->pdo()->prepare($sql);
        $stmt->execute();
        $find = $stmt->fetch();
        $cari = $find['jml'];
        return $cari;
    }

    public function getRl13()
    {
        $this->_addHeaderFiles();
        if (isset($_GET['y'])) {
            $tahun = $_GET['y'];
        } else {
            $tahun = date('Y');
        }
        // $this->assign['jns_layan'] = array();
        // $jns_layanan = array();
        $this->assign['tahun'] = $tahun;
        $jns_layanan = [
            'Penyakit Dalam',
            'Kesehatan Anak',
            'Obstetri',
            'Genekologi',
            'Bedah',
            'Bedah Orthopedi',
            'Bedah Saraf',
            'Luka Bakar',
            'Saraf',
            'Jiwa',
            'Psikologi',
            'Penatalaksana Pnyguna NAPZA',
            'THT',
            'Mata',
            'Kulit dan Kelamin',
            'Kardiologi',
            'Paru - Paru',
            'Geriatri',
            'Radioterapi',
            'Kedokteran Nuklir',
            'Kusta',
            'Rehabilitasi Medik',
            'Isolasi',
            'ICU',
            'ICCU',
            'NICU/PICU',
            'Umum',
            'Gigi & Mulut',
            'Pelayanan Rawat Darurat',
            'Perinatologi/Bayi'
        ];

        $total = 0;
        for ($i=0; $i < count($jns_layanan); $i++) {
            switch ($jns_layanan[$i]) {
                case 'Perinatologi/Bayi':
                    $kd_bangsal = 'B0006';
                    $vip = 0;
                    $kls1 = 0;
                    $kls2 = 0;
                    $kls3 = 0;
                    $cekjml = $this->db('kamar')->select('kamar.kd_kamar')->join('bangsal','kamar.kd_bangsal = bangsal.kd_bangsal')->like('bangsal.kd_bangsal','%'.$kd_bangsal.'%')->where('statusdata','1')->count();
                    $klsk = $this->db('kamar')->select('kamar.kd_kamar')->join('bangsal','kamar.kd_bangsal = bangsal.kd_bangsal')->like('bangsal.kd_bangsal','%'.$kd_bangsal.'%')->where('statusdata','1')->count();
                    break;
                case 'ICU':
                    $kd_bangsal = 'B0007';
                    $vip = 0;
                    $kls1 = 0;
                    $kls2 = 0;
                    $kls3 = 0;
                    $cekjml = $this->db('kamar')->select('kamar.kd_kamar')->join('bangsal','kamar.kd_bangsal = bangsal.kd_bangsal')->like('bangsal.kd_bangsal','%'.$kd_bangsal.'%')->where('statusdata','1')->count();
                    $klsk = $this->db('kamar')->select('kamar.kd_kamar')->join('bangsal','kamar.kd_bangsal = bangsal.kd_bangsal')->like('bangsal.kd_bangsal','%'.$kd_bangsal.'%')->where('statusdata','1')->count();
                    break;
                case 'NICU/PICU':
                    $kd_bangsal = 'B0008';
                    $vip = 0;
                    $kls1 = 0;
                    $kls2 = 0;
                    $kls3 = 0;
                    $cekjml = $this->db('kamar')->select('kamar.kd_kamar')->join('bangsal','kamar.kd_bangsal = bangsal.kd_bangsal')->like('bangsal.kd_bangsal','%'.$kd_bangsal.'%')->where('statusdata','1')->count();
                    $klsk = $this->db('kamar')->select('kamar.kd_kamar')->join('bangsal','kamar.kd_bangsal = bangsal.kd_bangsal')->like('bangsal.kd_bangsal','%'.$kd_bangsal.'%')->where('statusdata','1')->count();
                    break;
                case 'Jiwa':
                    $kd_bangsal = 'B0102';
                    $vip = 0;
                    $cekjml = $this->db('kamar')->select('kamar.kd_kamar')->join('bangsal','kamar.kd_bangsal = bangsal.kd_bangsal')->like('bangsal.kd_bangsal','%'.$kd_bangsal.'%')->where('statusdata','1')->count();
                    $kls1 = $this->db('kamar')->select('kamar.kd_kamar')->join('bangsal','kamar.kd_bangsal = bangsal.kd_bangsal')->like('bangsal.kd_bangsal','%'.$kd_bangsal.'%')->where('kelas','Kelas 1')->where('statusdata','1')->count();
                    $kls2 = $this->db('kamar')->select('kamar.kd_kamar')->join('bangsal','kamar.kd_bangsal = bangsal.kd_bangsal')->like('bangsal.kd_bangsal','%'.$kd_bangsal.'%')->where('kelas','Kelas 2')->where('statusdata','1')->count();
                    $kls3 = $this->db('kamar')->select('kamar.kd_kamar')->join('bangsal','kamar.kd_bangsal = bangsal.kd_bangsal')->like('bangsal.kd_bangsal','%'.$kd_bangsal.'%')->where('kelas','Kelas 3')->where('statusdata','1')->count();
                    $klsk = 0;
                    break;
                case 'Umum':
                    $kd_bangsal = array("B0102","B0008","B0007","B0006");
                    $cekjml = $this->cariKamar($kd_bangsal,'0');
                    $vip = $this->cariKamar($kd_bangsal,'0','Kelas VIP');
                    $kls1 = $this->cariKamar($kd_bangsal,'0','Kelas 1');
                    $kls2 = $this->cariKamar($kd_bangsal,'0','Kelas 2');
                    $kls3 = $this->cariKamar($kd_bangsal,'0','Kelas 3');
                    $klsk = 0;
                    break;
                case 'Isolasi':
                    $kd_bangsal = array("B0102","B0008","B0007","B0006");
                    $vip = 0;
                    $cekjml = $this->cariKamar($kd_bangsal,'1');
                    $kls1 = $this->cariKamar($kd_bangsal,'1','Kelas 1');
                    $kls2 = $this->cariKamar($kd_bangsal,'1','Kelas 2');
                    $kls3 = $this->cariKamar($kd_bangsal,'1','Kelas 3');
                    $klsk = 0;
                    break;

                default:
                    $cekjml = 0;
                    $vip = 0;
                    $kls1 = 0;
                    $kls2 = 0;
                    $kls3 = 0;
                    $klsk = 0;
                    break;
            }
            $total = $total + $cekjml;
            $this->assign['total'] = $total;
            $jns_layan['jml_kamar'] = $cekjml;
            $jns_layan['vip'] = $vip;
            $jns_layan['kls1'] = $kls1;
            $jns_layan['kls2'] = $kls2;
            $jns_layan['kls3'] = $kls3;
            $jns_layan['klsk'] = $klsk;
            $jns_layan['jns_layanan']= $jns_layanan[$i];
            $this->assign['jns_layan'][] = $jns_layan;
        }
        return $this->draw('rl1_3.html',['rl13' => $this->assign]);
    }

    function pasien31($type,$kd_dokter,$tahun,$stts_pulang = '',$cari_lama = '' ,$kelas = '')
    {
        $tahunBefore = $tahun - 1;
        $tahunAfter = $tahun + 1;
        $sql = "SELECT COUNT(kamar_inap.no_rawat) as hitung , SUM(kamar_inap.lama) as hari FROM kamar_inap JOIN dpjp_ranap ON kamar_inap.no_rawat = dpjp_ranap.no_rawat ";
        if ($cari_lama == '1') {
            $sql .= " JOIN kamar ON kamar_inap.kd_kamar = kamar.kd_kamar ";
        }
        $sql .= " WHERE ";

        if ($type == '0') {
            $sql .= " YEAR(kamar_inap.tgl_masuk) = '{$tahunBefore}' AND YEAR(kamar_inap.tgl_keluar) = '{$tahun}' AND dpjp_ranap.kd_dokter = '{$kd_dokter}' ";
        } else if ($type == '1'){
            $sql .= " YEAR(kamar_inap.tgl_masuk) = '{$tahun}' AND dpjp_ranap.kd_dokter = '{$kd_dokter}' ";
        } else if ($type == '2'){
            $sql .= " YEAR(kamar_inap.tgl_keluar) = '{$tahun}' AND dpjp_ranap.kd_dokter = '{$kd_dokter}' ";
        } else if ($type == '3'){
            $sql .= " YEAR(kamar_inap.tgl_masuk) = '{$tahun}' AND YEAR(kamar_inap.tgl_keluar) = '{$tahunAfter}' AND dpjp_ranap.kd_dokter = '{$kd_dokter}' ";
        }

        if ($kelas == 'vip') {
            $sql .= " AND kamar.kelas = 'Kelas VIP' AND kamar.kd_bangsal NOT IN ('B0006','B0007','B0008') ";
        } else if ($kelas == '1') {
            $sql .= " AND kamar.kelas = 'Kelas 1' AND kamar.kd_bangsal NOT IN ('B0006','B0007','B0008') ";
        } else if ($kelas == '2') {
            $sql .= " AND kamar.kelas = 'Kelas 2' AND kamar.kd_bangsal NOT IN ('B0006','B0007','B0008') ";
        } else if ($kelas == '3') {
            $sql .= " AND kamar.kelas = 'Kelas 3' AND kamar.kd_bangsal NOT IN ('B0006','B0007','B0008') ";
        } else if ($kelas == 'icu') {
            $sql .= " AND kamar.kd_bangsal = 'B0007' ";
        } else if ($kelas == 'picu') {
            $sql .= " AND kamar.kd_bangsal = 'B0008' ";
        } else if ($kelas == 'peri') {
            $sql .= " AND kamar.kd_bangsal = 'B0006' ";
        } else if ($kelas == 'all') {
            $sql .= " AND kamar.kd_bangsal NOT IN ('B0006','B0007','B0008') ";
        }

        if ($stts_pulang == '') {
            $sql .= " AND kamar_inap.stts_pulang NOT IN ('Pindah Kamar','-','+')";
        } else if ($stts_pulang == 'Hidup'){
            $sql .= " AND kamar_inap.stts_pulang IN ('Membaik','Dirujuk','APS')";
        } else if ($stts_pulang == 'Mati1'){
            $sql .= " AND kamar_inap.stts_pulang IN ('Meninggal')AND kamar_inap.lama <= '2' ";
        } else if ($stts_pulang == 'Mati2'){
            $sql .= " AND kamar_inap.stts_pulang IN ('Meninggal') AND kamar_inap.lama >= '2' ";
        } else if ($stts_pulang == 'Pindah'){
            $sql .= " AND kamar_inap.stts_pulang NOT IN ('-','+')";
        }

        $stmt = $this->db()->pdo()->prepare($sql);
        $stmt->execute();
        $find = $stmt->fetch();
        $cari = $find;
        return $cari;
    }

    function pasien31hari($tahun,$kd_dokter,$kelas){
        # code...
        for ($i=1; $i < 13 ; $i++) {
            if (strlen((string)$i) > 1) {
                # code...
                $i = $i;
            } else {
                $i = '0'.$i;
            }
            $check = $this->db('temp_rl')->where('field',$kd_dokter)->where('field2',$kelas)->like('field3','%'.$tahun.'-'.$i.'%')->oneArray();
            if (!$check) {
                # code...
                $tahunKurang = $tahun - 1;
                $begin = new \DateTime($tahun.'-'.$i.'-01');
                $end = new \DateTime($tahun.'-'.$i.'-31');

                $interval = \DateInterval::createFromDateString('1 day');
                $period = new \DatePeriod($begin, $interval, $end);
                $cari = 0;
                foreach ($period as $dt) {
                    $date = $dt->format("Y-m-d");
                    $sql = "SELECT COUNT(kamar_inap.no_rawat) as jml FROM `kamar_inap` JOIN dpjp_ranap ON kamar_inap.no_rawat = dpjp_ranap.no_rawat JOIN kamar ON kamar_inap.kd_kamar = kamar.kd_kamar WHERE kamar_inap.tgl_masuk BETWEEN '$tahunKurang-11-01' AND '$date' AND kamar_inap.tgl_keluar > '$date' AND kamar_inap.stts_pulang != 'Pindah Kamar' AND dpjp_ranap.kd_dokter = '{$kd_dokter}' ";
                    if ($kelas == 'vip') {
                        $sql .= " AND kamar.kelas = 'Kelas VIP' AND kamar.kd_bangsal NOT IN ('B0006','B0007','B0008') ";
                    } else if ($kelas == '1') {
                        $sql .= " AND kamar.kelas = 'Kelas 1' AND kamar.kd_bangsal NOT IN ('B0006','B0007','B0008') ";
                    } else if ($kelas == '2') {
                        $sql .= " AND kamar.kelas = 'Kelas 2' AND kamar.kd_bangsal NOT IN ('B0006','B0007','B0008') ";
                    } else if ($kelas == '3') {
                        $sql .= " AND kamar.kelas = 'Kelas 3' AND kamar.kd_bangsal NOT IN ('B0006','B0007','B0008') ";
                    } else if ($kelas == 'icu') {
                        $sql .= " AND kamar.kd_bangsal = 'B0007' ";
                    } else if ($kelas == 'picu') {
                        $sql .= " AND kamar.kd_bangsal = 'B0008' ";
                    } else if ($kelas == 'peri') {
                        $sql .= " AND kamar.kd_bangsal = 'B0006' ";
                    }
                    // $sql .= " AND kamar.statusdata = '1'";
                    $stmt = $this->db()->pdo()->prepare($sql);
                    $stmt->execute();
                    $find = $stmt->fetch();
                    $cari = $cari + $find['jml'];
                }
                $this->db('temp_rl')->save([
                    'field' => $kd_dokter,
                    'field2' => $kelas,
                    'field3' => $tahun.'-'.$i,
                    'value' => $cari
                ]);
            }
        }
        $tampil = $this->db('temp_rl')->select(['cari' => 'SUM(value)'])->where('field',$kd_dokter)->where('field2',$kelas)->like('field3','%'.$tahun.'%')->oneArray();
        return $tampil['cari'];
    }

    public function getRl31()
    {
        $this->_addHeaderFiles();
        if (isset($_GET['y'])) {
            $tahun = $_GET['y'];
        } else {
            $tahun = date('Y');
        }
        $this->assign['tahun'] = $tahun;
        $jns_layanan = [
            'Penyakit Dalam',
            'Kesehatan Anak',
            'Obstetri',
            'Genekologi',
            'Bedah',
            'Bedah Orthopedi',
            'Bedah Saraf',
            'Luka Bakar',
            'Saraf',
            'Jiwa',
            'Psikologi',
            'Penatalaksana Pnyguna NAPZA',
            'THT',
            'Mata',
            'Kulit dan Kelamin',
            'Kardiologi',
            'Paru - Paru',
            'Geriatri',
            'Radioterapi',
            'Kedokteran Nuklir',
            'Kusta',
            'Rehabilitasi Medik',
            'Isolasi',
            'ICU',
            'ICCU',
            'NICU/PICU',
            'Umum',
            'Gigi & Mulut',
            'Pelayanan Rawat Darurat',
            'Perinatologi/Bayi'
        ];

        $total = 0;$pat = 0;$pm = 0;$pk = 0;$pmatik = 0;$pmatil = 0;$lama=0;$pak=0;$pkvip=0;$pk1=0;$pk2=0;$pk3=0;$pkh = 0;
        for ($i=0; $i < count($jns_layanan); $i++) {
            switch ($jns_layanan[$i]) {
                case 'Penyakit Dalam':
                    $kd_sp = 'S0013';
                    $pat = 0;$pm = 0;$pk = 0;$pmatik = 0;$pmatil = 0;$lama=0;$pak=0;$pkvip=0;$pk1=0;$pk2=0;$pk3=0;$pkh = 0;
                    $findDokter = $this->db('dokter')->where('kd_sps',$kd_sp)->toArray();
                    foreach ($findDokter as $value) {
                        $findPasienAwal = $this->pasien31('0',$value['kd_dokter'],$tahun,'','1','all');
                        $pat = $pat + $findPasienAwal['hitung'];
                        $findPasienMasuk = $this->pasien31('1',$value['kd_dokter'],$tahun,'','1','all');
                        $pm = $pm + $findPasienMasuk['hitung'];
                        $findPasienKeluar = $this->pasien31('2',$value['kd_dokter'],$tahun,'Hidup','1','all');
                        $pk = $pk + $findPasienKeluar['hitung'];
                        $findPasienKeluarMati1 = $this->pasien31('2',$value['kd_dokter'],$tahun,'Mati1','1','all');
                        $pmatik = $pmatik + $findPasienKeluarMati1['hitung'];
                        $findPasienKeluarMati2 = $this->pasien31('2',$value['kd_dokter'],$tahun,'Mati2','1','all');
                        $pmatil = $pmatil + $findPasienKeluarMati2['hitung'];
                        $findLama = $this->pasien31('2',$value['kd_dokter'],$tahun,'','1','all');
                        $lama = $lama + $findLama['hari'];
                        $findPasienKelasVip = $this->pasien31hari($tahun,$value['kd_dokter'],'vip');
                        $pkvip = $pkvip + $findPasienKelasVip;
                        $findPasienKelas1 = $this->pasien31hari($tahun,$value['kd_dokter'],'1');
                        $pk1 = $pk1 + $findPasienKelas1;
                        $findPasienKelas2 = $this->pasien31hari($tahun,$value['kd_dokter'],'2');
                        $pk2 = $pk2 + $findPasienKelas2;
                        $findPasienKelas3 = $this->pasien31hari($tahun,$value['kd_dokter'],'3');
                        $pk3 = $pk3 + $findPasienKelas3;
                    }
                    $jns_layan['pat'] = $pat;
                    $jns_layan['pm'] = $pm;
                    $jns_layan['pk'] = $pk;
                    $jns_layan['pmatik'] = $pmatik;
                    $jns_layan['pmatil'] = $pmatil;
                    $jns_layan['lama'] = $lama;
                    $jns_layan['pak'] = $pat + $pm - $pk - $pmatik - $pmatil;
                    $jns_layan['hp'] = $pkvip + $pk1 + $pk2 + $pk3 + $pkh;
                    $jns_layan['pkvip'] = $pkvip;
                    $jns_layan['pk1'] = $pk1;
                    $jns_layan['pk2'] = $pk2;
                    $jns_layan['pk3'] = $pk3;
                    $jns_layan['pkh'] = $pkh;
                    break;
                case 'Kesehatan Anak':
                    $kd_sp = 'S0011';
                    $pat = 0;$pm = 0;$pk = 0;$pmatik = 0;$pmatil = 0;$lama=0;$pak=0;$pkvip=0;$pk1=0;$pk2=0;$pk3=0;$pkh = 0;
                    $findDokter = $this->db('dokter')->where('kd_sps',$kd_sp)->toArray();
                    foreach ($findDokter as $value) {
                        $findPasienAwal = $this->pasien31('0',$value['kd_dokter'],$tahun,'','1','all');
                        $pat = $pat + $findPasienAwal['hitung'];
                        $findPasienMasuk = $this->pasien31('1',$value['kd_dokter'],$tahun,'','1','all');
                        $pm = $pm + $findPasienMasuk['hitung'];
                        $findPasienKeluar = $this->pasien31('2',$value['kd_dokter'],$tahun,'Hidup','1','all');
                        $pk = $pk + $findPasienKeluar['hitung'];
                        $findPasienKeluarMati1 = $this->pasien31('2',$value['kd_dokter'],$tahun,'Mati1','1','all');
                        $pmatik = $pmatik + $findPasienKeluarMati1['hitung'];
                        $findPasienKeluarMati2 = $this->pasien31('2',$value['kd_dokter'],$tahun,'Mati2','1','all');
                        $pmatil = $pmatil + $findPasienKeluarMati2['hitung'];
                        $findLama = $this->pasien31('2',$value['kd_dokter'],$tahun,'','1','all');
                        $lama = $lama + $findLama['hari'];
                        $findPasienKelasVip = $this->pasien31hari($tahun,$value['kd_dokter'],'vip');
                        $pkvip = $pkvip + $findPasienKelasVip;
                        $findPasienKelas1 = $this->pasien31hari($tahun,$value['kd_dokter'],'1');
                        $pk1 = $pk1 + $findPasienKelas1;
                        $findPasienKelas2 = $this->pasien31hari($tahun,$value['kd_dokter'],'2');
                        $pk2 = $pk2 + $findPasienKelas2;
                        $findPasienKelas3 = $this->pasien31hari($tahun,$value['kd_dokter'],'3');
                        $pk3 = $pk3 + $findPasienKelas3;
                    }
                    $jns_layan['pat'] = $pat;
                    $jns_layan['pm'] = $pm;
                    $jns_layan['pk'] = $pk;
                    $jns_layan['pmatik'] = $pmatik;
                    $jns_layan['pmatil'] = $pmatil;
                    $jns_layan['lama'] = $lama;
                    $jns_layan['pak'] = $pat + $pm - $pk - $pmatik - $pmatil;
                    $jns_layan['hp'] = $pkvip + $pk1 + $pk2 + $pk3 + $pkh;
                    $jns_layan['pkvip'] = $pkvip;
                    $jns_layan['pk1'] = $pk1;
                    $jns_layan['pk2'] = $pk2;
                    $jns_layan['pk3'] = $pk3;
                    $jns_layan['pkh'] = $pkh;
                    break;
                case 'Obstetri':
                    $kd_sp = 'S0012';
                    $pat = 0;$pm = 0;$pk = 0;$pmatik = 0;$pmatil = 0;$lama=0;$pak=0;$pkvip=0;$pk1=0;$pk2=0;$pk3=0;$pkh = 0;
                    $findDokter = $this->db('dokter')->where('kd_sps',$kd_sp)->toArray();
                    foreach ($findDokter as $value) {
                        $findPasienAwal = $this->pasien31('0',$value['kd_dokter'],$tahun,'','1','all');
                        $pat = $pat + $findPasienAwal['hitung'];
                        $findPasienMasuk = $this->pasien31('1',$value['kd_dokter'],$tahun,'','1','all');
                        $pm = $pm + $findPasienMasuk['hitung'];
                        $findPasienKeluar = $this->pasien31('2',$value['kd_dokter'],$tahun,'Hidup','1','all');
                        $pk = $pk + $findPasienKeluar['hitung'];
                        $findPasienKeluarMati1 = $this->pasien31('2',$value['kd_dokter'],$tahun,'Mati1','1','all');
                        $pmatik = $pmatik + $findPasienKeluarMati1['hitung'];
                        $findPasienKeluarMati2 = $this->pasien31('2',$value['kd_dokter'],$tahun,'Mati2','1','all');
                        $pmatil = $pmatil + $findPasienKeluarMati2['hitung'];
                        $findLama = $this->pasien31('2',$value['kd_dokter'],$tahun,'','1','all');
                        $lama = $lama + $findLama['hari'];
                        $findPasienKelasVip = $this->pasien31hari($tahun,$value['kd_dokter'],'vip');
                        $pkvip = $pkvip + $findPasienKelasVip;
                        $findPasienKelas1 = $this->pasien31hari($tahun,$value['kd_dokter'],'1');
                        $pk1 = $pk1 + $findPasienKelas1;
                        $findPasienKelas2 = $this->pasien31hari($tahun,$value['kd_dokter'],'2');
                        $pk2 = $pk2 + $findPasienKelas2;
                        $findPasienKelas3 = $this->pasien31hari($tahun,$value['kd_dokter'],'3');
                        $pk3 = $pk3 + $findPasienKelas3;
                    }
                    $jns_layan['pat'] = $pat;
                    $jns_layan['pm'] = $pm;
                    $jns_layan['pk'] = $pk;
                    $jns_layan['pmatik'] = $pmatik;
                    $jns_layan['pmatil'] = $pmatil;
                    $jns_layan['lama'] = $lama;
                    $jns_layan['pak'] = $pat + $pm - $pk - $pmatik - $pmatil;
                    $jns_layan['hp'] = $pkvip + $pk1 + $pk2 + $pk3 + $pkh;
                    $jns_layan['pkvip'] = $pkvip;
                    $jns_layan['pk1'] = $pk1;
                    $jns_layan['pk2'] = $pk2;
                    $jns_layan['pk3'] = $pk3;
                    $jns_layan['pkh'] = $pkh;
                    break;
                case 'Bedah':
                    $kd_sp = 'S0006';
                    $pat = 0;$pm = 0;$pk = 0;$pmatik = 0;$pmatil = 0;$lama=0;$pak=0;$pkvip=0;$pk1=0;$pk2=0;$pk3=0;$pkh = 0;
                    $findDokter = $this->db('dokter')->where('kd_sps',$kd_sp)->toArray();
                    foreach ($findDokter as $value) {
                        $findPasienAwal = $this->pasien31('0',$value['kd_dokter'],$tahun,'','1','all');
                        $pat = $pat + $findPasienAwal['hitung'];
                        $findPasienMasuk = $this->pasien31('1',$value['kd_dokter'],$tahun,'','1','all');
                        $pm = $pm + $findPasienMasuk['hitung'];
                        $findPasienKeluar = $this->pasien31('2',$value['kd_dokter'],$tahun,'Hidup','1','all');
                        $pk = $pk + $findPasienKeluar['hitung'];
                        $findPasienKeluarMati1 = $this->pasien31('2',$value['kd_dokter'],$tahun,'Mati1','1','all');
                        $pmatik = $pmatik + $findPasienKeluarMati1['hitung'];
                        $findPasienKeluarMati2 = $this->pasien31('2',$value['kd_dokter'],$tahun,'Mati2','1','all');
                        $pmatil = $pmatil + $findPasienKeluarMati2['hitung'];
                        $findLama = $this->pasien31('2',$value['kd_dokter'],$tahun,'','1','all');
                        $lama = $lama + $findLama['hari'];
                        $findPasienKelasVip = $this->pasien31hari($tahun,$value['kd_dokter'],'vip');
                        $pkvip = $pkvip + $findPasienKelasVip;
                        $findPasienKelas1 = $this->pasien31hari($tahun,$value['kd_dokter'],'1');
                        $pk1 = $pk1 + $findPasienKelas1;
                        $findPasienKelas2 = $this->pasien31hari($tahun,$value['kd_dokter'],'2');
                        $pk2 = $pk2 + $findPasienKelas2;
                        $findPasienKelas3 = $this->pasien31hari($tahun,$value['kd_dokter'],'3');
                        $pk3 = $pk3 + $findPasienKelas3;
                    }
                    $jns_layan['pat'] = $pat;
                    $jns_layan['pm'] = $pm;
                    $jns_layan['pk'] = $pk;
                    $jns_layan['pmatik'] = $pmatik;
                    $jns_layan['pmatil'] = $pmatil;
                    $jns_layan['lama'] = $lama;
                    $jns_layan['pak'] = $pat + $pm - $pk - $pmatik - $pmatil;
                    $jns_layan['hp'] = $pkvip + $pk1 + $pk2 + $pk3 + $pkh;
                    $jns_layan['pkvip'] = $pkvip;
                    $jns_layan['pk1'] = $pk1;
                    $jns_layan['pk2'] = $pk2;
                    $jns_layan['pk3'] = $pk3;
                    $jns_layan['pkh'] = $pkh;
                    break;
                case 'Bedah Orthopedi':
                    $kd_sp = 'S0005';
                    $pat = 0;$pm = 0;$pk = 0;$pmatik = 0;$pmatil = 0;$lama=0;$pak=0;$pkvip=0;$pk1=0;$pk2=0;$pk3=0;$pkh = 0;
                    $findDokter = $this->db('dokter')->where('kd_sps',$kd_sp)->toArray();
                    foreach ($findDokter as $value) {
                        $findPasienAwal = $this->pasien31('0',$value['kd_dokter'],$tahun,'','1','all');
                        $pat = $pat + $findPasienAwal['hitung'];
                        $findPasienMasuk = $this->pasien31('1',$value['kd_dokter'],$tahun,'','1','all');
                        $pm = $pm + $findPasienMasuk['hitung'];
                        $findPasienKeluar = $this->pasien31('2',$value['kd_dokter'],$tahun,'Hidup','1','all');
                        $pk = $pk + $findPasienKeluar['hitung'];
                        $findPasienKeluarMati1 = $this->pasien31('2',$value['kd_dokter'],$tahun,'Mati1','1','all');
                        $pmatik = $pmatik + $findPasienKeluarMati1['hitung'];
                        $findPasienKeluarMati2 = $this->pasien31('2',$value['kd_dokter'],$tahun,'Mati2','1','all');
                        $pmatil = $pmatil + $findPasienKeluarMati2['hitung'];
                        $findLama = $this->pasien31('2',$value['kd_dokter'],$tahun,'','1','all');
                        $lama = $lama + $findLama['hari'];
                        $findPasienKelasVip = $this->pasien31hari($tahun,$value['kd_dokter'],'vip');
                        $pkvip = $pkvip + $findPasienKelasVip;
                        $findPasienKelas1 = $this->pasien31hari($tahun,$value['kd_dokter'],'1');
                        $pk1 = $pk1 + $findPasienKelas1;
                        $findPasienKelas2 = $this->pasien31hari($tahun,$value['kd_dokter'],'2');
                        $pk2 = $pk2 + $findPasienKelas2;
                        $findPasienKelas3 = $this->pasien31hari($tahun,$value['kd_dokter'],'3');
                        $pk3 = $pk3 + $findPasienKelas3;
                    }
                    $jns_layan['pat'] = $pat;
                    $jns_layan['pm'] = $pm;
                    $jns_layan['pk'] = $pk;
                    $jns_layan['pmatik'] = $pmatik;
                    $jns_layan['pmatil'] = $pmatil;
                    $jns_layan['lama'] = $lama;
                    $jns_layan['pak'] = $pat + $pm - $pk - $pmatik - $pmatil;
                    $jns_layan['hp'] = $pkvip + $pk1 + $pk2 + $pk3 + $pkh;
                    $jns_layan['pkvip'] = $pkvip;
                    $jns_layan['pk1'] = $pk1;
                    $jns_layan['pk2'] = $pk2;
                    $jns_layan['pk3'] = $pk3;
                    $jns_layan['pkh'] = $pkh;
                break;
                case 'Jiwa':
                    $kd_sp = 'S0021';
                    $pat = 0;$pm = 0;$pk = 0;$pmatik = 0;$pmatil = 0;$lama=0;$pak=0;$pkvip=0;$pk1=0;$pk2=0;$pk3=0;$pkh = 0;
                    $findDokter = $this->db('dokter')->where('kd_sps',$kd_sp)->toArray();
                    foreach ($findDokter as $value) {
                        $findPasienAwal = $this->pasien31('0',$value['kd_dokter'],$tahun,'','1','all');
                        $pat = $pat + $findPasienAwal['hitung'];
                        $findPasienMasuk = $this->pasien31('1',$value['kd_dokter'],$tahun,'','1','all');
                        $pm = $pm + $findPasienMasuk['hitung'];
                        $findPasienKeluar = $this->pasien31('2',$value['kd_dokter'],$tahun,'Hidup','1','all');
                        $pk = $pk + $findPasienKeluar['hitung'];
                        $findPasienKeluarMati1 = $this->pasien31('2',$value['kd_dokter'],$tahun,'Mati1','1','all');
                        $pmatik = $pmatik + $findPasienKeluarMati1['hitung'];
                        $findPasienKeluarMati2 = $this->pasien31('2',$value['kd_dokter'],$tahun,'Mati2','1','all');
                        $pmatil = $pmatil + $findPasienKeluarMati2['hitung'];
                        $findLama = $this->pasien31('2',$value['kd_dokter'],$tahun,'','1','all');
                        $lama = $lama + $findLama['hari'];
                        $findPasienKelasVip = $this->pasien31hari($tahun,$value['kd_dokter'],'vip');
                        $pkvip = $pkvip + $findPasienKelasVip;
                        $findPasienKelas1 = $this->pasien31hari($tahun,$value['kd_dokter'],'1');
                        $pk1 = $pk1 + $findPasienKelas1;
                        $findPasienKelas2 = $this->pasien31hari($tahun,$value['kd_dokter'],'2');
                        $pk2 = $pk2 + $findPasienKelas2;
                        $findPasienKelas3 = $this->pasien31hari($tahun,$value['kd_dokter'],'3');
                        $pk3 = $pk3 + $findPasienKelas3;
                    }
                    $jns_layan['pat'] = $pat;
                    $jns_layan['pm'] = $pm;
                    $jns_layan['pk'] = $pk;
                    $jns_layan['pmatik'] = $pmatik;
                    $jns_layan['pmatil'] = $pmatil;
                    $jns_layan['lama'] = $lama;
                    $jns_layan['pak'] = $pat + $pm - $pk - $pmatik - $pmatil;
                    $jns_layan['hp'] = $pkvip + $pk1 + $pk2 + $pk3 + $pkh;
                    $jns_layan['pkvip'] = $pkvip;
                    $jns_layan['pk1'] = $pk1;
                    $jns_layan['pk2'] = $pk2;
                    $jns_layan['pk3'] = $pk3;
                    $jns_layan['pkh'] = $pkh;
                    break;
                case 'Bedah Saraf':
                    $kd_sp = 'S0024';
                    $pat = 0;$pm = 0;$pk = 0;$pmatik = 0;$pmatil = 0;$lama=0;$pak=0;$pkvip=0;$pk1=0;$pk2=0;$pk3=0;$pkh = 0;
                    $findDokter = $this->db('dokter')->where('kd_sps',$kd_sp)->toArray();
                    foreach ($findDokter as $value) {
                        $findPasienAwal = $this->pasien31('0',$value['kd_dokter'],$tahun,'','1','all');
                        $pat = $pat + $findPasienAwal['hitung'];
                        $findPasienMasuk = $this->pasien31('1',$value['kd_dokter'],$tahun,'','1','all');
                        $pm = $pm + $findPasienMasuk['hitung'];
                        $findPasienKeluar = $this->pasien31('2',$value['kd_dokter'],$tahun,'Hidup','1','all');
                        $pk = $pk + $findPasienKeluar['hitung'];
                        $findPasienKeluarMati1 = $this->pasien31('2',$value['kd_dokter'],$tahun,'Mati1','1','all');
                        $pmatik = $pmatik + $findPasienKeluarMati1['hitung'];
                        $findPasienKeluarMati2 = $this->pasien31('2',$value['kd_dokter'],$tahun,'Mati2','1','all');
                        $pmatil = $pmatil + $findPasienKeluarMati2['hitung'];
                        $findLama = $this->pasien31('2',$value['kd_dokter'],$tahun,'','1','all');
                        $lama = $lama + $findLama['hari'];
                        $findPasienKelasVip = $this->pasien31hari($tahun,$value['kd_dokter'],'vip');
                        $pkvip = $pkvip + $findPasienKelasVip;
                        $findPasienKelas1 = $this->pasien31hari($tahun,$value['kd_dokter'],'1');
                        $pk1 = $pk1 + $findPasienKelas1;
                        $findPasienKelas2 = $this->pasien31hari($tahun,$value['kd_dokter'],'2');
                        $pk2 = $pk2 + $findPasienKelas2;
                        $findPasienKelas3 = $this->pasien31hari($tahun,$value['kd_dokter'],'3');
                        $pk3 = $pk3 + $findPasienKelas3;
                    }
                    $jns_layan['pat'] = $pat;
                    $jns_layan['pm'] = $pm;
                    $jns_layan['pk'] = $pk;
                    $jns_layan['pmatik'] = $pmatik;
                    $jns_layan['pmatil'] = $pmatil;
                    $jns_layan['lama'] = $lama;
                    $jns_layan['pak'] = $pat + $pm - $pk - $pmatik - $pmatil;
                    $jns_layan['hp'] = $pkvip + $pk1 + $pk2 + $pk3 + $pkh;
                    $jns_layan['pkvip'] = $pkvip;
                    $jns_layan['pk1'] = $pk1;
                    $jns_layan['pk2'] = $pk2;
                    $jns_layan['pk3'] = $pk3;
                    $jns_layan['pkh'] = $pkh;
                    break;
                case 'Saraf':
                    $kd_sp = 'S0007';
                    $pat = 0;$pm = 0;$pk = 0;$pmatik = 0;$pmatil = 0;$lama=0;$pak=0;$pkvip=0;$pk1=0;$pk2=0;$pk3=0;$pkh = 0;
                    $findDokter = $this->db('dokter')->where('kd_sps',$kd_sp)->toArray();
                    foreach ($findDokter as $value) {
                        $findPasienAwal = $this->pasien31('0',$value['kd_dokter'],$tahun,'','1','all');
                        $pat = $pat + $findPasienAwal['hitung'];
                        $findPasienMasuk = $this->pasien31('1',$value['kd_dokter'],$tahun,'','1','all');
                        $pm = $pm + $findPasienMasuk['hitung'];
                        $findPasienKeluar = $this->pasien31('2',$value['kd_dokter'],$tahun,'Hidup','1','all');
                        $pk = $pk + $findPasienKeluar['hitung'];
                        $findPasienKeluarMati1 = $this->pasien31('2',$value['kd_dokter'],$tahun,'Mati1','1','all');
                        $pmatik = $pmatik + $findPasienKeluarMati1['hitung'];
                        $findPasienKeluarMati2 = $this->pasien31('2',$value['kd_dokter'],$tahun,'Mati2','1','all');
                        $pmatil = $pmatil + $findPasienKeluarMati2['hitung'];
                        $findLama = $this->pasien31('2',$value['kd_dokter'],$tahun,'','1','all');
                        $lama = $lama + $findLama['hari'];
                        $findPasienKelasVip = $this->pasien31hari($tahun,$value['kd_dokter'],'vip');
                        $pkvip = $pkvip + $findPasienKelasVip;
                        $findPasienKelas1 = $this->pasien31hari($tahun,$value['kd_dokter'],'1');
                        $pk1 = $pk1 + $findPasienKelas1;
                        $findPasienKelas2 = $this->pasien31hari($tahun,$value['kd_dokter'],'2');
                        $pk2 = $pk2 + $findPasienKelas2;
                        $findPasienKelas3 = $this->pasien31hari($tahun,$value['kd_dokter'],'3');
                        $pk3 = $pk3 + $findPasienKelas3;
                    }
                    $jns_layan['pat'] = $pat;
                    $jns_layan['pm'] = $pm;
                    $jns_layan['pk'] = $pk;
                    $jns_layan['pmatik'] = $pmatik;
                    $jns_layan['pmatil'] = $pmatil;
                    $jns_layan['lama'] = $lama;
                    $jns_layan['pak'] = $pat + $pm - $pk - $pmatik - $pmatil;
                    $jns_layan['hp'] = $pkvip + $pk1 + $pk2 + $pk3 + $pkh;
                    $jns_layan['pkvip'] = $pkvip;
                    $jns_layan['pk1'] = $pk1;
                    $jns_layan['pk2'] = $pk2;
                    $jns_layan['pk3'] = $pk3;
                    $jns_layan['pkh'] = $pkh;
                    break;
                case 'THT':
                    $kd_sp = 'S0009';
                    $pat = 0;$pm = 0;$pk = 0;$pmatik = 0;$pmatil = 0;$lama=0;$pak=0;$pkvip=0;$pk1=0;$pk2=0;$pk3=0;$pkh = 0;
                    $findDokter = $this->db('dokter')->where('kd_sps',$kd_sp)->toArray();
                    foreach ($findDokter as $value) {
                        $findPasienAwal = $this->pasien31('0',$value['kd_dokter'],$tahun,'','1','all');
                        $pat = $pat + $findPasienAwal['hitung'];
                        $findPasienMasuk = $this->pasien31('1',$value['kd_dokter'],$tahun,'','1','all');
                        $pm = $pm + $findPasienMasuk['hitung'];
                        $findPasienKeluar = $this->pasien31('2',$value['kd_dokter'],$tahun,'Hidup','1','all');
                        $pk = $pk + $findPasienKeluar['hitung'];
                        $findPasienKeluarMati1 = $this->pasien31('2',$value['kd_dokter'],$tahun,'Mati1','1','all');
                        $pmatik = $pmatik + $findPasienKeluarMati1['hitung'];
                        $findPasienKeluarMati2 = $this->pasien31('2',$value['kd_dokter'],$tahun,'Mati2','1','all');
                        $pmatil = $pmatil + $findPasienKeluarMati2['hitung'];
                        $findLama = $this->pasien31('2',$value['kd_dokter'],$tahun,'','1','all');
                        $lama = $lama + $findLama['hari'];
                        $findPasienKelasVip = $this->pasien31hari($tahun,$value['kd_dokter'],'vip');
                        $pkvip = $pkvip + $findPasienKelasVip;
                        $findPasienKelas1 = $this->pasien31hari($tahun,$value['kd_dokter'],'1');
                        $pk1 = $pk1 + $findPasienKelas1;
                        $findPasienKelas2 = $this->pasien31hari($tahun,$value['kd_dokter'],'2');
                        $pk2 = $pk2 + $findPasienKelas2;
                        $findPasienKelas3 = $this->pasien31hari($tahun,$value['kd_dokter'],'3');
                        $pk3 = $pk3 + $findPasienKelas3;
                    }
                    $jns_layan['pat'] = $pat;
                    $jns_layan['pm'] = $pm;
                    $jns_layan['pk'] = $pk;
                    $jns_layan['pmatik'] = $pmatik;
                    $jns_layan['pmatil'] = $pmatil;
                    $jns_layan['lama'] = $lama;
                    $jns_layan['pak'] = $pat + $pm - $pk - $pmatik - $pmatil;
                    $jns_layan['hp'] = $pkvip + $pk1 + $pk2 + $pk3 + $pkh;
                    $jns_layan['pkvip'] = $pkvip;
                    $jns_layan['pk1'] = $pk1;
                    $jns_layan['pk2'] = $pk2;
                    $jns_layan['pk3'] = $pk3;
                    $jns_layan['pkh'] = $pkh;
                    break;
                case 'Mata':
                    $kd_sp = 'S0010';
                    $pat = 0;$pm = 0;$pk = 0;$pmatik = 0;$pmatil = 0;$lama=0;$pak=0;$pkvip=0;$pk1=0;$pk2=0;$pk3=0;$pkh = 0;
                    $findDokter = $this->db('dokter')->where('kd_sps',$kd_sp)->toArray();
                    foreach ($findDokter as $value) {
                        $findPasienAwal = $this->pasien31('0',$value['kd_dokter'],$tahun,'','1','all');
                        $pat = $pat + $findPasienAwal['hitung'];
                        $findPasienMasuk = $this->pasien31('1',$value['kd_dokter'],$tahun,'','1','all');
                        $pm = $pm + $findPasienMasuk['hitung'];
                        $findPasienKeluar = $this->pasien31('2',$value['kd_dokter'],$tahun,'Hidup','1','all');
                        $pk = $pk + $findPasienKeluar['hitung'];
                        $findPasienKeluarMati1 = $this->pasien31('2',$value['kd_dokter'],$tahun,'Mati1','1','all');
                        $pmatik = $pmatik + $findPasienKeluarMati1['hitung'];
                        $findPasienKeluarMati2 = $this->pasien31('2',$value['kd_dokter'],$tahun,'Mati2','1','all');
                        $pmatil = $pmatil + $findPasienKeluarMati2['hitung'];
                        $findLama = $this->pasien31('2',$value['kd_dokter'],$tahun,'','1','all');
                        $lama = $lama + $findLama['hari'];
                        $findPasienKelasVip = $this->pasien31hari($tahun,$value['kd_dokter'],'vip');
                        $pkvip = $pkvip + $findPasienKelasVip;
                        $findPasienKelas1 = $this->pasien31hari($tahun,$value['kd_dokter'],'1');
                        $pk1 = $pk1 + $findPasienKelas1;
                        $findPasienKelas2 = $this->pasien31hari($tahun,$value['kd_dokter'],'2');
                        $pk2 = $pk2 + $findPasienKelas2;
                        $findPasienKelas3 = $this->pasien31hari($tahun,$value['kd_dokter'],'3');
                        $pk3 = $pk3 + $findPasienKelas3;
                    }
                    $jns_layan['pat'] = $pat;
                    $jns_layan['pm'] = $pm;
                    $jns_layan['pk'] = $pk;
                    $jns_layan['pmatik'] = $pmatik;
                    $jns_layan['pmatil'] = $pmatil;
                    $jns_layan['lama'] = $lama;
                    $jns_layan['pak'] = $pat + $pm - $pk - $pmatik - $pmatil;
                    $jns_layan['hp'] = $pkvip + $pk1 + $pk2 + $pk3 + $pkh;
                    $jns_layan['pkvip'] = $pkvip;
                    $jns_layan['pk1'] = $pk1;
                    $jns_layan['pk2'] = $pk2;
                    $jns_layan['pk3'] = $pk3;
                    $jns_layan['pkh'] = $pkh;
                    break;
                case 'Kulit dan Kelamin':
                    $kd_sp = 'S0014';
                    $pat = 0;$pm = 0;$pk = 0;$pmatik = 0;$pmatil = 0;$lama=0;$pak=0;$pkvip=0;$pk1=0;$pk2=0;$pk3=0;$pkh = 0;
                    $findDokter = $this->db('dokter')->where('kd_sps',$kd_sp)->toArray();
                    foreach ($findDokter as $value) {
                        $findPasienAwal = $this->pasien31('0',$value['kd_dokter'],$tahun,'','1','all');
                        $pat = $pat + $findPasienAwal['hitung'];
                        $findPasienMasuk = $this->pasien31('1',$value['kd_dokter'],$tahun,'','1','all');
                        $pm = $pm + $findPasienMasuk['hitung'];
                        $findPasienKeluar = $this->pasien31('2',$value['kd_dokter'],$tahun,'Hidup','1','all');
                        $pk = $pk + $findPasienKeluar['hitung'];
                        $findPasienKeluarMati1 = $this->pasien31('2',$value['kd_dokter'],$tahun,'Mati1','1','all');
                        $pmatik = $pmatik + $findPasienKeluarMati1['hitung'];
                        $findPasienKeluarMati2 = $this->pasien31('2',$value['kd_dokter'],$tahun,'Mati2','1','all');
                        $pmatil = $pmatil + $findPasienKeluarMati2['hitung'];
                        $findLama = $this->pasien31('2',$value['kd_dokter'],$tahun,'','1','all');
                        $lama = $lama + $findLama['hari'];
                        $findPasienKelasVip = $this->pasien31hari($tahun,$value['kd_dokter'],'vip');
                        $pkvip = $pkvip + $findPasienKelasVip;
                        $findPasienKelas1 = $this->pasien31hari($tahun,$value['kd_dokter'],'1');
                        $pk1 = $pk1 + $findPasienKelas1;
                        $findPasienKelas2 = $this->pasien31hari($tahun,$value['kd_dokter'],'2');
                        $pk2 = $pk2 + $findPasienKelas2;
                        $findPasienKelas3 = $this->pasien31hari($tahun,$value['kd_dokter'],'3');
                        $pk3 = $pk3 + $findPasienKelas3;
                    }
                    $jns_layan['pat'] = $pat;
                    $jns_layan['pm'] = $pm;
                    $jns_layan['pk'] = $pk;
                    $jns_layan['pmatik'] = $pmatik;
                    $jns_layan['pmatil'] = $pmatil;
                    $jns_layan['lama'] = $lama;
                    $jns_layan['pak'] = $pat + $pm - $pk - $pmatik - $pmatil;
                    $jns_layan['hp'] = $pkvip + $pk1 + $pk2 + $pk3 + $pkh;
                    $jns_layan['pkvip'] = $pkvip;
                    $jns_layan['pk1'] = $pk1;
                    $jns_layan['pk2'] = $pk2;
                    $jns_layan['pk3'] = $pk3;
                    $jns_layan['pkh'] = $pkh;
                    break;
                case 'Paru - Paru':
                    $kd_sp = 'S0027';
                    $pat = 0;$pm = 0;$pk = 0;$pmatik = 0;$pmatil = 0;$lama=0;$pak=0;$pkvip=0;$pk1=0;$pk2=0;$pk3=0;$pkh = 0;
                    $findDokter = $this->db('dokter')->where('kd_sps',$kd_sp)->toArray();
                    foreach ($findDokter as $value) {
                        $findPasienAwal = $this->pasien31('0',$value['kd_dokter'],$tahun,'','1','all');
                        $pat = $pat + $findPasienAwal['hitung'];
                        $findPasienMasuk = $this->pasien31('1',$value['kd_dokter'],$tahun,'','1','all');
                        $pm = $pm + $findPasienMasuk['hitung'];
                        $findPasienKeluar = $this->pasien31('2',$value['kd_dokter'],$tahun,'Hidup','1','all');
                        $pk = $pk + $findPasienKeluar['hitung'];
                        $findPasienKeluarMati1 = $this->pasien31('2',$value['kd_dokter'],$tahun,'Mati1','1','all');
                        $pmatik = $pmatik + $findPasienKeluarMati1['hitung'];
                        $findPasienKeluarMati2 = $this->pasien31('2',$value['kd_dokter'],$tahun,'Mati2','1','all');
                        $pmatil = $pmatil + $findPasienKeluarMati2['hitung'];
                        $findLama = $this->pasien31('2',$value['kd_dokter'],$tahun,'','1','all');
                        $lama = $lama + $findLama['hari'];
                        $findPasienKelasVip = $this->pasien31hari($tahun,$value['kd_dokter'],'vip');
                        $pkvip = $pkvip + $findPasienKelasVip;
                        $findPasienKelas1 = $this->pasien31hari($tahun,$value['kd_dokter'],'1');
                        $pk1 = $pk1 + $findPasienKelas1;
                        $findPasienKelas2 = $this->pasien31hari($tahun,$value['kd_dokter'],'2');
                        $pk2 = $pk2 + $findPasienKelas2;
                        $findPasienKelas3 = $this->pasien31hari($tahun,$value['kd_dokter'],'3');
                        $pk3 = $pk3 + $findPasienKelas3;
                    }
                    $jns_layan['pat'] = $pat;
                    $jns_layan['pm'] = $pm;
                    $jns_layan['pk'] = $pk;
                    $jns_layan['pmatik'] = $pmatik;
                    $jns_layan['pmatil'] = $pmatil;
                    $jns_layan['lama'] = $lama;
                    $jns_layan['pak'] = $pat + $pm - $pk - $pmatik - $pmatil;
                    $jns_layan['hp'] = $pkvip + $pk1 + $pk2 + $pk3 + $pkh;
                    $jns_layan['pkvip'] = $pkvip;
                    $jns_layan['pk1'] = $pk1;
                    $jns_layan['pk2'] = $pk2;
                    $jns_layan['pk3'] = $pk3;
                    $jns_layan['pkh'] = $pkh;
                    break;
                case 'ICU':
                    $pat = 0;$pm = 0;$pk = 0;$pmatik = 0;$pmatil = 0;$lama=0;$pak=0;$pkvip=0;$pk1=0;$pk2=0;$pk3=0;$pkh = 0;
                    $findDokter = $this->db('dokter')->toArray();
                    foreach ($findDokter as $value) {
                        $findPasienAwal = $this->pasien31('0',$value['kd_dokter'],$tahun,'','1','icu');
                        $pat = $pat + $findPasienAwal['hitung'];
                        $findPasienMasuk = $this->pasien31('1',$value['kd_dokter'],$tahun,'','1','icu');
                        $pm = $pm + $findPasienMasuk['hitung'];
                        $findPasienKeluar = $this->pasien31('2',$value['kd_dokter'],$tahun,'Hidup','1','icu');
                        $pk = $pk + $findPasienKeluar['hitung'];
                        $findPasienKeluarMati1 = $this->pasien31('2',$value['kd_dokter'],$tahun,'Mati1','1','icu');
                        $pmatik = $pmatik + $findPasienKeluarMati1['hitung'];
                        $findPasienKeluarMati2 = $this->pasien31('2',$value['kd_dokter'],$tahun,'Mati2','1','icu');
                        $pmatil = $pmatil + $findPasienKeluarMati2['hitung'];
                        $findLama = $this->pasien31('2',$value['kd_dokter'],$tahun,'','1','icu');
                        $lama = $lama + $findLama['hari'];
                        $findPasienKhusus = $this->pasien31hari($tahun,$value['kd_dokter'],'icu');
                        $pkh = $pkh + $findPasienKhusus;
                    }
                    $jns_layan['pat'] = $pat;
                    $jns_layan['pm'] = $pm;
                    $jns_layan['pk'] = $pk;
                    $jns_layan['pmatik'] = $pmatik;
                    $jns_layan['pmatil'] = $pmatil;
                    $jns_layan['lama'] = $lama;
                    $jns_layan['pak'] = $pat + $pm - $pk - $pmatik - $pmatil;
                    $jns_layan['hp'] = $pkvip + $pk1 + $pk2 + $pk3 + $pkh;
                    $jns_layan['pkvip'] = $pkvip;
                    $jns_layan['pk1'] = $pk1;
                    $jns_layan['pk2'] = $pk2;
                    $jns_layan['pk3'] = $pk3;
                    $jns_layan['pkh'] = $pkh;
                    break;
                case 'NICU/PICU':
                    $pat = 0;$pm = 0;$pk = 0;$pmatik = 0;$pmatil = 0;$lama=0;$pak=0;$pkvip=0;$pk1=0;$pk2=0;$pk3=0;$pkh = 0;
                    $findDokter = $this->db('dokter')->toArray();
                    foreach ($findDokter as $value) {
                        $findPasienAwal = $this->pasien31('0',$value['kd_dokter'],$tahun,'','1','picu');
                        $pat = $pat + $findPasienAwal['hitung'];
                        $findPasienMasuk = $this->pasien31('1',$value['kd_dokter'],$tahun,'','1','picu');
                        $pm = $pm + $findPasienMasuk['hitung'];
                        $findPasienKeluar = $this->pasien31('2',$value['kd_dokter'],$tahun,'Hidup','1','picu');
                        $pk = $pk + $findPasienKeluar['hitung'];
                        $findPasienKeluarMati1 = $this->pasien31('2',$value['kd_dokter'],$tahun,'Mati1','1','picu');
                        $pmatik = $pmatik + $findPasienKeluarMati1['hitung'];
                        $findPasienKeluarMati2 = $this->pasien31('2',$value['kd_dokter'],$tahun,'Mati2','1','picu');
                        $pmatil = $pmatil + $findPasienKeluarMati2['hitung'];
                        $findLama = $this->pasien31('2',$value['kd_dokter'],$tahun,'','1','picu');
                        $lama = $lama + $findLama['hari'];
                        $findPasienKhusus = $this->pasien31hari($tahun,$value['kd_dokter'],'picu');
                        $pkh = $pkh + $findPasienKhusus;
                    }
                    $jns_layan['pat'] = $pat;
                    $jns_layan['pm'] = $pm;
                    $jns_layan['pk'] = $pk;
                    $jns_layan['pmatik'] = $pmatik;
                    $jns_layan['pmatil'] = $pmatil;
                    $jns_layan['lama'] = $lama;
                    $jns_layan['pak'] = $pat + $pm - $pk - $pmatik - $pmatil;
                    $jns_layan['hp'] = $pkvip + $pk1 + $pk2 + $pk3 + $pkh;
                    $jns_layan['pkvip'] = $pkvip;
                    $jns_layan['pk1'] = $pk1;
                    $jns_layan['pk2'] = $pk2;
                    $jns_layan['pk3'] = $pk3;
                    $jns_layan['pkh'] = $pkh;
                    break;
                case 'Perinatologi/Bayi':
                    $pat = 0;$pm = 0;$pk = 0;$pmatik = 0;$pmatil = 0;$lama=0;$pak=0;$pkvip=0;$pk1=0;$pk2=0;$pk3=0;$pkh = 0;
                    $findDokter = $this->db('dokter')->toArray();
                    foreach ($findDokter as $value) {
                        $findPasienAwal = $this->pasien31('0',$value['kd_dokter'],$tahun,'','1','peri');
                        $pat = $pat + $findPasienAwal['hitung'];
                        $findPasienMasuk = $this->pasien31('1',$value['kd_dokter'],$tahun,'','1','peri');
                        $pm = $pm + $findPasienMasuk['hitung'];
                        $findPasienKeluar = $this->pasien31('2',$value['kd_dokter'],$tahun,'Hidup','1','peri');
                        $pk = $pk + $findPasienKeluar['hitung'];
                        $findPasienKeluarMati1 = $this->pasien31('2',$value['kd_dokter'],$tahun,'Mati1','1','peri');
                        $pmatik = $pmatik + $findPasienKeluarMati1['hitung'];
                        $findPasienKeluarMati2 = $this->pasien31('2',$value['kd_dokter'],$tahun,'Mati2','1','peri');
                        $pmatil = $pmatil + $findPasienKeluarMati2['hitung'];
                        $findLama = $this->pasien31('2',$value['kd_dokter'],$tahun,'','1','peri');
                        $lama = $lama + $findLama['hari'];
                        $findPasienKhusus = $this->pasien31hari($tahun,$value['kd_dokter'],'peri');
                        $pkh = $pkh + $findPasienKhusus;
                    }
                    $jns_layan['pat'] = $pat;
                    $jns_layan['pm'] = $pm;
                    $jns_layan['pk'] = $pk;
                    $jns_layan['pmatik'] = $pmatik;
                    $jns_layan['pmatil'] = $pmatil;
                    $jns_layan['lama'] = $lama;
                    $jns_layan['pak'] = $pat + $pm - $pk - $pmatik - $pmatil;
                    $jns_layan['hp'] = $pkvip + $pk1 + $pk2 + $pk3 + $pkh;
                    $jns_layan['pkvip'] = $pkvip;
                    $jns_layan['pk1'] = $pk1;
                    $jns_layan['pk2'] = $pk2;
                    $jns_layan['pk3'] = $pk3;
                    $jns_layan['pkh'] = $pkh;
                    break;

                default:
                $jns_layan['pat'] = 0;
                $jns_layan['pm'] = 0;
                $jns_layan['pk'] = 0;
                $jns_layan['pmatik'] = 0;
                $jns_layan['pmatil'] = 0;
                $jns_layan['lama'] = 0;
                $jns_layan['pak'] = 0;
                $jns_layan['hp'] = 0;
                $jns_layan['pkvip'] = 0;
                $jns_layan['pk1'] = 0;
                $jns_layan['pk2'] = 0;
                $jns_layan['pk3'] = 0;
                $jns_layan['pkh'] = 0;
                    break;
            }
            $jns_layan['jns_layanan']= $jns_layanan[$i];
            $this->assign['jns_layan'][] = $jns_layan;
        }
        return $this->draw('rl3_1.html',['rl31' => $this->assign]);
    }

    public function getRl32()
    {
        $this->_addHeaderFiles();
        if (isset($_GET['y'])) {
            $tahun = $_GET['y'];
        } else {
            $tahun = date('Y');
        }
        // $tahun = '2022';
        $this->assign['jns_layan'] = [];
        // $jns_layanan = array();
        $this->assign['tahun'] = $tahun;
        $jns_layanan = [
            'Bedah',
            'Non Bedah',
            'Kebidanan',
            'Psikiatrik',
            'Anak'
        ];
        $total = 0;
        $dirawatBedah = 0;$dirawatNBedah=0;$dirawatbidan=0;$dirawatanak = 0;
        $dirujukBedah = 0;$dirujukNBedah=0;$dirujukbidan=0;$dirujukanak = 0;
        $pulangBedah = 0;$pulangNBedah=0;$pulangbidan=0;$pulanganak = 0;
        $matiBedah = 0;$matiNBedah=0;$matibidan=0;$matianak = 0;
        $rujukanbedah = 0;$nrujukanbedah = 0;
        $rujukannbedah = 0;$nrujukannbedah = 0;
        $rujukanbidan = 0;$nrujukanbidan = 0;
        $rujukananak = 0;$nrujukananak = 0;
        $reg = $this->db('reg_periksa')->like('tgl_registrasi',$tahun.'%')->where('kd_poli','IGDK')->where('stts','!=','Batal')->toArray();
        foreach ($reg as $value) {
            switch ($value['stts']) {
                case 'Dirawat':
                    $dirawat = $this->db('kategori_pasien_igd')->where('no_rawat',$value['no_rawat'])->oneArray();
                    $cekrujuk = $this->db('rujuk_masuk')->where('no_rawat',$value['no_rawat'])->notLike('perujuk','%datang%')->oneArray();
                    $ceknrujuk = $this->db('rujuk_masuk')->where('no_rawat',$value['no_rawat'])->like('perujuk','%datang%')->oneArray();
                    switch ($dirawat['kategori']) {
                        case 'BEDAH':
                            $dirawatBedah = $dirawatBedah + 1;
                            if ($cekrujuk) {
                                $rujukanbedah = $rujukanbedah + 1;
                            }
                            if ($ceknrujuk) {
                                $nrujukanbedah = $nrujukanbedah + 1;
                            }
                            break;
                        case 'NON-BEDAH':
                            $dirawatNBedah = $dirawatNBedah + 1;
                            if ($cekrujuk) {
                                $rujukannbedah = $rujukannbedah + 1;
                            }
                            if ($ceknrujuk) {
                                $nrujukannbedah = $nrujukannbedah + 1;
                            }
                            break;
                        case 'KEBIDANAN':
                            $dirawatbidan = $dirawatbidan + 1;
                            if ($cekrujuk) {
                                $rujukanbidan = $rujukanbidan + 1;
                            }
                            if ($ceknrujuk) {
                                $nrujukanbidan = $nrujukanbidan + 1;
                            }
                            break;
                        case 'ANAK':
                            $dirawatanak = $dirawatanak + 1;
                            if ($cekrujuk) {
                                $rujukananak = $rujukananak + 1;
                            }
                            if ($ceknrujuk) {
                                $nrujukananak = $nrujukananak + 1;
                            }
                            break;
                        default:
                            $countDirawat = 0;
                            break;
                    }
                    break;
                case 'Dirujuk':
                    $dirawat = $this->db('kategori_pasien_igd')->where('no_rawat',$value['no_rawat'])->oneArray();
                    $cekrujuk = $this->db('rujuk_masuk')->where('no_rawat',$value['no_rawat'])->notLike('perujuk','%datang%')->oneArray();
                    $ceknrujuk = $this->db('rujuk_masuk')->where('no_rawat',$value['no_rawat'])->like('perujuk','%datang%')->oneArray();
                    switch ($dirawat['kategori']) {
                        case 'BEDAH':
                            $dirujukBedah = $dirujukBedah + 1;
                            if ($cekrujuk) {
                                $rujukanbedah = $rujukanbedah + 1;
                            }
                            if ($ceknrujuk) {
                                $nrujukanbedah = $nrujukanbedah + 1;
                            }
                            break;
                        case 'NON-BEDAH':
                            $dirujukNBedah = $dirujukNBedah + 1;
                            if ($cekrujuk) {
                                $rujukannbedah = $rujukannbedah + 1;
                            }
                            if ($ceknrujuk) {
                                $nrujukannbedah = $nrujukannbedah + 1;
                            }
                            break;
                        case 'KEBIDANAN':
                            $dirujukbidan = $dirujukbidan + 1;
                            if ($cekrujuk) {
                                $rujukanbidan = $rujukanbidan + 1;
                            }
                            if ($ceknrujuk) {
                                $nrujukanbidan = $nrujukanbidan + 1;
                            }
                            break;
                        case 'ANAK':
                            $dirujukanak = $dirujukanak + 1;
                            if ($cekrujuk) {
                                $rujukananak = $rujukananak + 1;
                            }
                            if ($ceknrujuk) {
                                $nrujukananak = $nrujukananak + 1;
                            }
                            break;
                        default:
                            $countDirawat = 0;
                            break;
                    }
                    break;
                case 'BLPL':
                    $dirawat = $this->db('kategori_pasien_igd')->where('no_rawat',$value['no_rawat'])->oneArray();
                    $cekrujuk = $this->db('rujuk_masuk')->where('no_rawat',$value['no_rawat'])->notLike('perujuk','%datang%')->oneArray();
                    $ceknrujuk = $this->db('rujuk_masuk')->where('no_rawat',$value['no_rawat'])->like('perujuk','%datang%')->oneArray();
                    switch ($dirawat['kategori']) {
                        case 'BEDAH':
                            $pulangBedah = $pulangBedah + 1;
                            if ($cekrujuk) {
                                $rujukanbedah = $rujukanbedah + 1;
                            }
                            if ($ceknrujuk) {
                                $nrujukanbedah = $nrujukanbedah + 1;
                            }
                            break;
                        case 'NON-BEDAH':
                            $pulangNBedah = $pulangNBedah + 1;
                            if ($cekrujuk) {
                                $rujukannbedah = $rujukannbedah + 1;
                            }
                            if ($ceknrujuk) {
                                $nrujukannbedah = $nrujukannbedah + 1;
                            }
                            break;
                        case 'KEBIDANAN':
                            $pulangbidan = $pulangbidan + 1;
                            if ($cekrujuk) {
                                $rujukanbidan = $rujukanbidan + 1;
                            }
                            if ($ceknrujuk) {
                                $nrujukanbidan = $nrujukanbidan + 1;
                            }
                            break;
                        case 'ANAK':
                            $pulanganak = $pulanganak + 1;
                            if ($cekrujuk) {
                                $rujukananak = $rujukananak + 1;
                            }
                            if ($ceknrujuk) {
                                $nrujukananak = $nrujukananak + 1;
                            }
                            break;
                        default:
                            $countDirawat = 0;
                            break;
                    }
                    break;
                case 'Meninggal':
                    $dirawat = $this->db('kategori_pasien_igd')->where('no_rawat',$value['no_rawat'])->oneArray();
                    $cekrujuk = $this->db('rujuk_masuk')->where('no_rawat',$value['no_rawat'])->notLike('perujuk','%datang%')->oneArray();
                    $ceknrujuk = $this->db('rujuk_masuk')->where('no_rawat',$value['no_rawat'])->like('perujuk','%datang%')->oneArray();
                    switch ($dirawat['kategori']) {
                        case 'BEDAH':
                            $matiBedah = $matiBedah + 1;
                            if ($cekrujuk) {
                                $rujukanbedah = $rujukanbedah + 1;
                            }
                            if ($ceknrujuk) {
                                $nrujukanbedah = $nrujukanbedah + 1;
                            }
                            break;
                        case 'NON-BEDAH':
                            $matiNBedah = $matiNBedah + 1;
                            if ($cekrujuk) {
                                $rujukannbedah = $rujukannbedah + 1;
                            }
                            if ($ceknrujuk) {
                                $nrujukannbedah = $nrujukannbedah + 1;
                            }
                            break;
                        case 'KEBIDANAN':
                            $matibidan = $matibidan + 1;
                            if ($cekrujuk) {
                                $rujukanbidan = $rujukanbidan + 1;
                            }
                            if ($ceknrujuk) {
                                $nrujukanbidan = $nrujukanbidan + 1;
                            }
                            break;
                        case 'ANAK':
                            $matianak = $matianak + 1;
                            if ($cekrujuk) {
                                $rujukananak = $rujukananak + 1;
                            }
                            if ($ceknrujuk) {
                                $nrujukananak = $nrujukananak + 1;
                            }
                            break;
                        default:
                            $countDirawat = 0;
                            break;
                    }
                    break;

                default:
                    # code...
                    break;
            }
        }
        $jns_layan = [];
        for ($i=0; $i < count($jns_layanan); $i++) {
            switch ($jns_layanan[$i]) {
                case 'Bedah':
                    $jns_layan['rujukan'] = $rujukanbedah;
                    $jns_layan['nrujukan'] = $nrujukanbedah;
                    $jns_layan['dirawat'] = $dirawatBedah;
                    $jns_layan['dirujuk'] = $dirujukBedah;
                    $jns_layan['pulang'] = $pulangBedah;
                    $jns_layan['mati'] = $matiBedah;
                    break;
                case 'Non Bedah':
                    $jns_layan['rujukan'] = $rujukannbedah;
                    $jns_layan['nrujukan'] = $nrujukannbedah;
                    $jns_layan['dirawat'] = $dirawatNBedah;
                    $jns_layan['dirujuk'] = $dirujukNBedah;
                    $jns_layan['pulang'] = $pulangNBedah;
                    $jns_layan['mati'] = $matiNBedah;
                    break;
                case 'Kebidanan':
                    $jns_layan['rujukan'] = $rujukanbidan;
                    $jns_layan['nrujukan'] = $nrujukanbidan;
                    $jns_layan['dirawat'] = $dirawatbidan;
                    $jns_layan['dirujuk'] = $dirujukbidan;
                    $jns_layan['pulang'] = $pulangbidan;
                    $jns_layan['mati'] = $matibidan;
                    break;
                case 'Anak':
                    $jns_layan['rujukan'] = $rujukananak;
                    $jns_layan['nrujukan'] = $nrujukananak;
                    $jns_layan['dirawat'] = $dirawatanak;
                    $jns_layan['dirujuk'] = $dirujukanak;
                    $jns_layan['pulang'] = $pulanganak;
                    $jns_layan['mati'] = $matianak;
                    break;

                default:
                    $jns_layan['rujukan'] = 0;
                    $jns_layan['nrujukan'] = 0;
                    $jns_layan['dirawat'] = 0;
                    $jns_layan['dirujuk'] = 0;
                    $jns_layan['pulang'] = 0;
                    $jns_layan['mati'] = 0;
                    break;
            }
            $jns_layan['jns_layanan']= $jns_layanan[$i];
            $this->assign['jns_layan'][] = $jns_layan;
        }
        return $this->draw('rl3_2.html',['rl32' => $this->assign]);
    }

    function cariRujukan($poli,$perujuk,$tahun,$type,$stts = ''){
        $cari = 0;
        if ($type == '1') {
            $sql = "SELECT COUNT(reg_periksa.no_rawat) as jml FROM `rujuk_masuk` JOIN reg_periksa ON rujuk_masuk.no_rawat = reg_periksa.no_rawat WHERE ";
            foreach ($perujuk as $value) {
                $sql .= " rujuk_masuk.perujuk NOT LIKE '%{$value}%' AND ";
            }
            $sql .= " reg_periksa.tgl_registrasi LIKE '%{$tahun}%' AND reg_periksa.kd_poli IN ('{$poli['0']}','{$poli['1']}')";
            if ($stts != '') {
                $sql .= " AND reg_periksa.stts = '{$stts}'";
            }
            $stmt = $this->db()->pdo()->prepare($sql);
            $stmt->execute();
            $find = $stmt->fetch();
            $found = $find['jml'];
            $cari = $found;
        } else {
            foreach ($perujuk as $value) {
                $sql = "SELECT COUNT(reg_periksa.no_rawat) as jml FROM `rujuk_masuk` JOIN reg_periksa ON rujuk_masuk.no_rawat = reg_periksa.no_rawat WHERE rujuk_masuk.perujuk LIKE '%{$value}%' AND reg_periksa.tgl_registrasi LIKE '%{$tahun}%' AND reg_periksa.kd_poli IN ('{$poli['0']}','{$poli['1']}')";
                if ($stts != '') {
                    $sql .= " AND reg_periksa.stts = '{$stts}'";
                }
                $stmt = $this->db()->pdo()->prepare($sql);
                $stmt->execute();
                $find = $stmt->fetch();
                $found = $find['jml'];
                $cari = $cari + $found;
            }
        }
        // $cari = $this->db('reg_periksa')->select('reg_periksa.no_rawat')->join('rujuk_masuk','reg_periksa.no_rawat = rujuk_masuk.no_rawat')->orIn('reg_periksa.kd_poli',$poli)->orIn('rujuk_masuk.perujuk',$perujuk)->like('tgl_registrasi',$tahun.'%')->count();
        return $cari;
    }

    function cariRujukan1($poli,$perujuk,$tahun,$type){
        $tahunBefore = $tahun - 1;$cari = 0;
        if ($type == '1') {
            foreach ($perujuk as $value) {
            $sql = "SELECT COUNT(rujuk_masuk.no_rawat) as jml FROM bridging_sep JOIN rujuk_masuk ON bridging_sep.no_rawat = rujuk_masuk.no_rawat JOIN reg_periksa ON rujuk_masuk.no_rawat = reg_periksa.no_rawat WHERE bridging_sep.tglsep LIKE '%{$tahun}%' AND bridging_sep.tglrujukan LIKE '%{$tahunBefore}%' AND bridging_sep.jnspelayanan = '2' and reg_periksa.kd_poli IN ('{$poli['0']}','{$poli['1']}') ";
            $sql .= " AND bridging_sep.nmppkrujukan LIKE '%{$value}%'";
            $stmt = $this->db()->pdo()->prepare($sql);
            $stmt->execute();
            $find = $stmt->fetch();
            $found = $find['jml'];
            $cari = $cari + $found;
            }
        } else {
            $sql = "SELECT COUNT(rujuk_masuk.no_rawat) as jml FROM bridging_sep JOIN rujuk_masuk ON bridging_sep.no_rawat = rujuk_masuk.no_rawat JOIN reg_periksa ON rujuk_masuk.no_rawat = reg_periksa.no_rawat WHERE bridging_sep.tglsep LIKE '%{$tahun}%' AND bridging_sep.tglrujukan LIKE '%{$tahunBefore}%' AND bridging_sep.jnspelayanan = '2' and reg_periksa.kd_poli IN ('{$poli['0']}','{$poli['1']}') ";
            foreach ($perujuk as $value) {
                $sql .= " AND bridging_sep.nmppkrujukan NOT LIKE '%{$value}%'";
            }
            $stmt = $this->db()->pdo()->prepare($sql);
            $stmt->execute();
            $find = $stmt->fetch();
            $found = $find['jml'];
            $cari = $cari + $found;
        }
        return $cari;
    }

    public function getRl314()
    {
        $this->_addHeaderFiles();
        if (isset($_GET['y'])) {
            $tahun = $_GET['y'];
        } else {
            $tahun = date('Y');
        }
        // $tahun = '2022';
        $this->assign['jns_layan'] = [];
        // $jns_layanan = array();
        $this->assign['tahun'] = $tahun;
        $jns_layanan = array(
            'Penyakit Dalam' => array('U0002','U0048'),
            'Bedah' => array('U0005','U0050'),
            'Kesehatan Anak' => array('U0004','U0046'),
            'Obsterik & Ginekologi' => array('U0001','U0047'),
            'Keluarga Berencana' => '',
            'Saraf' => array('U0020','U0055'),
            'Jiwa' => array('U0036','U0057'),
            'THT' => array('U0051','U0009'),
            'Mata' => array('U0003','U0049'),
            'Kulit & Kelamin' => array('U0016','U0053'),
            'Gigi & Mulut' => array('U0017','U0054'),
            'Radiologi' => '',
            'Paru-Paru' => array('U0010','U0061'),
            'Spesialisasi Lain' => array('U0003','U0049'),
        );
        $perujuk = array(
            '1' => array('klinik','dr.','rs','-'),
            '2' => array('klinik','dr.'),
            '3' => array('rs','rumah'),
            '4' => array('datang')
        );

        $jns_layan = [];
        $no = 1;
        foreach ($jns_layanan as $key => $value) {
            $jns_layan['no'] = $no;
            $jns_layan['kode']= $key;
            $jns_layan['ket']= $value;
            switch ($key) {
                case 'Penyakit Dalam':
                    # code...
                    $jns_layan['dpkm'] = $this->cariRujukan($value,$perujuk['1'],$tahun,'1');
                    $jns_layan['dfk'] = $this->cariRujukan($value,$perujuk['2'],$tahun,'0');
                    $jns_layan['drs'] = $this->cariRujukan($value,$perujuk['3'],$tahun,'0');

                    $jns_layan['kpkm'] = $this->cariRujukan1($value,$perujuk['1'],$tahun,'0');
                    $jns_layan['kfk'] = $this->cariRujukan1($value,$perujuk['2'],$tahun,'1');
                    $jns_layan['krs'] = $this->cariRujukan1($value,$perujuk['3'],$tahun,'1');

                    $jns_layan['drjk'] = $this->cariRujukan($value,$perujuk['4'],$tahun,'1','Dirujuk');
                    $jns_layan['dsdr'] = $this->cariRujukan($value,$perujuk['4'],$tahun,'0','Dirujuk');
                    break;
                case 'Bedah':
                    # code...
                    $jns_layan['dpkm'] = $this->cariRujukan($value,$perujuk['1'],$tahun,'1');
                    $jns_layan['dfk'] = $this->cariRujukan($value,$perujuk['2'],$tahun,'0');
                    $jns_layan['drs'] = $this->cariRujukan($value,$perujuk['3'],$tahun,'0');

                    $jns_layan['kpkm'] = $this->cariRujukan1($value,$perujuk['1'],$tahun,'0');
                    $jns_layan['kfk'] = $this->cariRujukan1($value,$perujuk['2'],$tahun,'1');
                    $jns_layan['krs'] = $this->cariRujukan1($value,$perujuk['3'],$tahun,'1');

                    $jns_layan['drjk'] = $this->cariRujukan($value,$perujuk['4'],$tahun,'1','Dirujuk');
                    $jns_layan['dsdr'] = $this->cariRujukan($value,$perujuk['4'],$tahun,'0','Dirujuk');
                    break;
                case 'Kesehatan Anak':
                    # code...
                    $jns_layan['dpkm'] = $this->cariRujukan($value,$perujuk['1'],$tahun,'1');
                    $jns_layan['dfk'] = $this->cariRujukan($value,$perujuk['2'],$tahun,'0');
                    $jns_layan['drs'] = $this->cariRujukan($value,$perujuk['3'],$tahun,'0');

                    $jns_layan['kpkm'] = $this->cariRujukan1($value,$perujuk['1'],$tahun,'0');
                    $jns_layan['kfk'] = $this->cariRujukan1($value,$perujuk['2'],$tahun,'1');
                    $jns_layan['krs'] = $this->cariRujukan1($value,$perujuk['3'],$tahun,'1');

                    $jns_layan['drjk'] = $this->cariRujukan($value,$perujuk['4'],$tahun,'1','Dirujuk');
                    $jns_layan['dsdr'] = $this->cariRujukan($value,$perujuk['4'],$tahun,'0','Dirujuk');
                    break;
                case 'Obsterik & Ginekologi':
                    # code...
                    $jns_layan['dpkm'] = $this->cariRujukan($value,$perujuk['1'],$tahun,'1');
                    $jns_layan['dfk'] = $this->cariRujukan($value,$perujuk['2'],$tahun,'0');
                    $jns_layan['drs'] = $this->cariRujukan($value,$perujuk['3'],$tahun,'0');

                    $jns_layan['kpkm'] = $this->cariRujukan1($value,$perujuk['1'],$tahun,'0');
                    $jns_layan['kfk'] = $this->cariRujukan1($value,$perujuk['2'],$tahun,'1');
                    $jns_layan['krs'] = $this->cariRujukan1($value,$perujuk['3'],$tahun,'1');

                    $jns_layan['drjk'] = $this->cariRujukan($value,$perujuk['4'],$tahun,'1','Dirujuk');
                    $jns_layan['dsdr'] = $this->cariRujukan($value,$perujuk['4'],$tahun,'0','Dirujuk');
                    break;
                case 'Saraf':
                    # code...
                    $jns_layan['dpkm'] = $this->cariRujukan($value,$perujuk['1'],$tahun,'1');
                    $jns_layan['dfk'] = $this->cariRujukan($value,$perujuk['2'],$tahun,'0');
                    $jns_layan['drs'] = $this->cariRujukan($value,$perujuk['3'],$tahun,'0');

                    $jns_layan['kpkm'] = $this->cariRujukan1($value,$perujuk['1'],$tahun,'0');
                    $jns_layan['kfk'] = $this->cariRujukan1($value,$perujuk['2'],$tahun,'1');
                    $jns_layan['krs'] = $this->cariRujukan1($value,$perujuk['3'],$tahun,'1');

                    $jns_layan['drjk'] = $this->cariRujukan($value,$perujuk['4'],$tahun,'1','Dirujuk');
                    $jns_layan['dsdr'] = $this->cariRujukan($value,$perujuk['4'],$tahun,'0','Dirujuk');
                    break;
                case 'Jiwa':
                    # code...
                    $jns_layan['dpkm'] = $this->cariRujukan($value,$perujuk['1'],$tahun,'1');
                    $jns_layan['dfk'] = $this->cariRujukan($value,$perujuk['2'],$tahun,'0');
                    $jns_layan['drs'] = $this->cariRujukan($value,$perujuk['3'],$tahun,'0');

                    $jns_layan['kpkm'] = $this->cariRujukan1($value,$perujuk['1'],$tahun,'0');
                    $jns_layan['kfk'] = $this->cariRujukan1($value,$perujuk['2'],$tahun,'1');
                    $jns_layan['krs'] = $this->cariRujukan1($value,$perujuk['3'],$tahun,'1');

                    $jns_layan['drjk'] = $this->cariRujukan($value,$perujuk['4'],$tahun,'1','Dirujuk');
                    $jns_layan['dsdr'] = $this->cariRujukan($value,$perujuk['4'],$tahun,'0','Dirujuk');
                    break;
                case 'THT':
                    # code...
                    $jns_layan['dpkm'] = $this->cariRujukan($value,$perujuk['1'],$tahun,'1');
                    $jns_layan['dfk'] = $this->cariRujukan($value,$perujuk['2'],$tahun,'0');
                    $jns_layan['drs'] = $this->cariRujukan($value,$perujuk['3'],$tahun,'0');

                    $jns_layan['kpkm'] = $this->cariRujukan1($value,$perujuk['1'],$tahun,'0');
                    $jns_layan['kfk'] = $this->cariRujukan1($value,$perujuk['2'],$tahun,'1');
                    $jns_layan['krs'] = $this->cariRujukan1($value,$perujuk['3'],$tahun,'1');

                    $jns_layan['drjk'] = $this->cariRujukan($value,$perujuk['4'],$tahun,'1','Dirujuk');
                    $jns_layan['dsdr'] = $this->cariRujukan($value,$perujuk['4'],$tahun,'0','Dirujuk');
                    break;
                case 'Mata':
                    # code...
                    $jns_layan['dpkm'] = $this->cariRujukan($value,$perujuk['1'],$tahun,'1');
                    $jns_layan['dfk'] = $this->cariRujukan($value,$perujuk['2'],$tahun,'0');
                    $jns_layan['drs'] = $this->cariRujukan($value,$perujuk['3'],$tahun,'0');

                    $jns_layan['kpkm'] = $this->cariRujukan1($value,$perujuk['1'],$tahun,'0');
                    $jns_layan['kfk'] = $this->cariRujukan1($value,$perujuk['2'],$tahun,'1');
                    $jns_layan['krs'] = $this->cariRujukan1($value,$perujuk['3'],$tahun,'1');

                    $jns_layan['drjk'] = $this->cariRujukan($value,$perujuk['4'],$tahun,'1','Dirujuk');
                    $jns_layan['dsdr'] = $this->cariRujukan($value,$perujuk['4'],$tahun,'0','Dirujuk');
                    break;
                case 'Kulit & Kelamin':
                    # code...
                    $jns_layan['dpkm'] = $this->cariRujukan($value,$perujuk['1'],$tahun,'1');
                    $jns_layan['dfk'] = $this->cariRujukan($value,$perujuk['2'],$tahun,'0');
                    $jns_layan['drs'] = $this->cariRujukan($value,$perujuk['3'],$tahun,'0');

                    $jns_layan['kpkm'] = $this->cariRujukan1($value,$perujuk['1'],$tahun,'0');
                    $jns_layan['kfk'] = $this->cariRujukan1($value,$perujuk['2'],$tahun,'1');
                    $jns_layan['krs'] = $this->cariRujukan1($value,$perujuk['3'],$tahun,'1');

                    $jns_layan['drjk'] = $this->cariRujukan($value,$perujuk['4'],$tahun,'1','Dirujuk');
                    $jns_layan['dsdr'] = $this->cariRujukan($value,$perujuk['4'],$tahun,'0','Dirujuk');
                    break;
                case 'Gigi & Mulut':
                    # code...
                    $jns_layan['dpkm'] = $this->cariRujukan($value,$perujuk['1'],$tahun,'1');
                    $jns_layan['dfk'] = $this->cariRujukan($value,$perujuk['2'],$tahun,'0');
                    $jns_layan['drs'] = $this->cariRujukan($value,$perujuk['3'],$tahun,'0');

                    $jns_layan['kpkm'] = $this->cariRujukan1($value,$perujuk['1'],$tahun,'0');
                    $jns_layan['kfk'] = $this->cariRujukan1($value,$perujuk['2'],$tahun,'1');
                    $jns_layan['krs'] = $this->cariRujukan1($value,$perujuk['3'],$tahun,'1');

                    $jns_layan['drjk'] = $this->cariRujukan($value,$perujuk['4'],$tahun,'1','Dirujuk');
                    $jns_layan['dsdr'] = $this->cariRujukan($value,$perujuk['4'],$tahun,'0','Dirujuk');
                    break;
                case 'Paru-Paru':
                    # code...
                    $jns_layan['dpkm'] = $this->cariRujukan($value,$perujuk['1'],$tahun,'1');
                    $jns_layan['dfk'] = $this->cariRujukan($value,$perujuk['2'],$tahun,'0');
                    $jns_layan['drs'] = $this->cariRujukan($value,$perujuk['3'],$tahun,'0');

                    $jns_layan['kpkm'] = $this->cariRujukan1($value,$perujuk['1'],$tahun,'0');
                    $jns_layan['kfk'] = $this->cariRujukan1($value,$perujuk['2'],$tahun,'1');
                    $jns_layan['krs'] = $this->cariRujukan1($value,$perujuk['3'],$tahun,'1');

                    $jns_layan['drjk'] = $this->cariRujukan($value,$perujuk['4'],$tahun,'1','Dirujuk');
                    $jns_layan['dsdr'] = $this->cariRujukan($value,$perujuk['4'],$tahun,'0','Dirujuk');
                    break;

                default:
                    $jns_layan['dpkm'] = 0;
                    $jns_layan['dfk'] = 0;
                    $jns_layan['drs'] = 0;
                    $jns_layan['kpkm'] = 0;
                    $jns_layan['kfk'] = 0;
                    $jns_layan['krs'] = 0;
                    $jns_layan['drjk'] = 0;
                    $jns_layan['dsdr'] = 0;
                    break;
            }
            $this->assign['jns_layan'][] = $jns_layan;
            $no++;
        }
        return $this->draw('rl3_14.html',['rl32' => $this->assign]);
    }

    function cariPerCaraBayar($kd_pj,$tahun,$poli,$status){
        switch ($status) {
            case '0':
                $cari = $this->db('reg_periksa')->select('no_rawat')->where('kd_pj',$kd_pj)->like('tgl_registrasi',$tahun.'%')->notIn('kd_poli',$poli)->where('stts','!=','Batal')->count();
                break;
            case '1':
                $cari = $this->db('reg_periksa')->select('no_rawat')->where('kd_pj',$kd_pj)->like('tgl_registrasi',$tahun.'%')->where('kd_poli',$poli)->where('stts','!=','Batal')->count();
                break;
            case '2':
                $cari = $this->db('reg_periksa')->select('no_rawat')->notIn('kd_pj',$kd_pj)->like('tgl_registrasi',$tahun.'%')->notIn('kd_poli',$poli)->where('stts','!=','Batal')->count();
                break;
            case 'inap1':
                $cari = $this->db('reg_periksa')->select('reg_periksa.no_rawat')->join('kamar_inap','kamar_inap.no_rawat = reg_periksa.no_rawat')->notIn('kd_pj',$kd_pj)->like('tgl_registrasi',$tahun.'%')->notIn('stts_pulang',$poli)->count();
                break;
            case 'inap2':
                $cari = $this->db('reg_periksa')->select('reg_periksa.no_rawat')->join('kamar_inap','kamar_inap.no_rawat = reg_periksa.no_rawat')->where('kd_pj',$kd_pj)->like('tgl_registrasi',$tahun.'%')->notIn('stts_pulang',$poli)->count();
                break;
            case 'lama1':
                $cari = $this->db('reg_periksa')->select(['lama' => 'SUM(kamar_inap.lama)'])->join('kamar_inap','kamar_inap.no_rawat = reg_periksa.no_rawat')->where('kd_pj',$kd_pj)->like('tgl_registrasi',$tahun.'%')->notIn('stts_pulang',$poli)->oneArray();
                break;
            case 'lama2':
                $cari = $this->db('reg_periksa')->select(['lama' => 'SUM(kamar_inap.lama)'])->join('kamar_inap','kamar_inap.no_rawat = reg_periksa.no_rawat')->notIn('kd_pj',$kd_pj)->like('tgl_registrasi',$tahun.'%')->notIn('stts_pulang',$poli)->oneArray();
                break;

            default:
            $cari = $this->db('reg_periksa')->select('no_rawat')->notIn('kd_pj',$kd_pj)->like('tgl_registrasi',$tahun.'%')->where('kd_poli',$poli)->where('stts','!=','Batal')->count();
                break;
        }
        return $cari;
    }

    public function getRl315()
    {
        $this->_addHeaderFiles();
        if (isset($_GET['y'])) {
            $tahun = $_GET['y'];
        } else {
            $tahun = date('Y');
        }
        // $tahun = '2022';
        $this->assign['jns_layan'] = [];
        // $jns_layanan = array();
        $this->assign['tahun'] = $tahun;
        $jns_layanan = array(
            '1' => 'Membayar Sendiri',
            '2' => 'Asuransi :',
            '2.1' => 'Asuransi Pemerintah',
            '2.2' => 'Asuransi Swasta',
            '3' => 'Keringanan',
            '4' => 'Gratis',
            '4.1' => 'Kartu Sehat',
            '4.2' => 'Keterangan Tidak Mampu',
            '4.3' => 'Lain-Lain',
        );
        $poli = array(
            'U0026','U0024','IGDK','U0027'
        );
        $kd_pj = array('BPJ','A01');
        $stts_pulang = array('Pindah Kamar','+','-');
        $jns_layan = [];
        // $total = 0;$lab = 0;$rad = 0;$poli=0;
        foreach ($jns_layanan as $key => $value) {
            $jns_layan['kode']= $key;
            $jns_layan['ket']= $value;
            switch ($key) {
                case '1':
                    $jns_layan['inap'] = $this->cariPerCaraBayar($kd_pj['1'],$tahun,$stts_pulang,'inap2');
                    $jns_layan['lama'] = $this->cariPerCaraBayar($kd_pj['1'],$tahun,$stts_pulang,'lama1');
                    $jns_layan['lab'] = $this->cariPerCaraBayar($kd_pj['1'],$tahun,$poli['1'],'1');
                    $jns_layan['rad'] = $this->cariPerCaraBayar($kd_pj['1'],$tahun,$poli['0'],'1');
                    $jns_layan['poli'] = $this->cariPerCaraBayar($kd_pj['1'],$tahun,$poli,'0');
                    $jns_layan['lain'] = 0;
                    break;
                case '2.1':
                    $jns_layan['inap'] = $this->cariPerCaraBayar($kd_pj['0'],$tahun,$stts_pulang,'inap2');
                    $jns_layan['lama'] = $this->cariPerCaraBayar($kd_pj['0'],$tahun,$stts_pulang,'lama1');
                    $jns_layan['lab'] = $this->cariPerCaraBayar($kd_pj['0'],$tahun,$poli['1'],'1');
                    $jns_layan['rad'] = $this->cariPerCaraBayar($kd_pj['0'],$tahun,$poli['0'],'1');
                    $jns_layan['poli'] = $this->cariPerCaraBayar($kd_pj['0'],$tahun,$poli,'0');
                    $jns_layan['lain'] = 0;
                    break;
                case '4.3':
                    $jns_layan['inap'] = $this->cariPerCaraBayar($kd_pj,$tahun,$stts_pulang,'inap1');
                    $jns_layan['lama'] = $this->cariPerCaraBayar($kd_pj,$tahun,$stts_pulang,'lama2');
                    $jns_layan['lab'] = $this->cariPerCaraBayar($kd_pj,$tahun,$poli['1'],'3');
                    $jns_layan['rad'] = $this->cariPerCaraBayar($kd_pj,$tahun,$poli['0'],'3');
                    $jns_layan['poli'] = $this->cariPerCaraBayar($kd_pj,$tahun,$poli,'2');
                    $jns_layan['lain'] = 0;
                    break;

                default:
                    $jns_layan['inap'] = 0;
                    $jns_layan['lama']['lama'] = 0;
                    $jns_layan['lab'] = 0;
                    $jns_layan['rad'] = 0;
                    $jns_layan['poli'] = 0;
                    $jns_layan['lain'] = 0;
                    break;
            }
            $this->assign['jns_layan'][] = $jns_layan;
        }
        return $this->draw('rl3_15.html',['rl32' => $this->assign]);
    }

    function pasienRl4a($kode,$tahun){
        $cari = array();
        $cari['l6k'] = 0; $cari['p6k'] = 0;$cari['l28k'] = 0;$cari['p28k']=0;$cari['l1th']=0; $cari['p1th']=0;
        $cari['l4th']=0; $cari['p4th']=0;$cari['l14th']=0; $cari['p14th']=0;$cari['l24th']=0; $cari['p24th']=0;
        $cari['l44th']=0; $cari['p44th']=0;$cari['l64th']=0; $cari['p64th']=0;$cari['l65th']=0; $cari['p65th']=0;
        $cari['mati'] = 0;
        if (is_array($kode)) {
            // $cari = 0;
            foreach ($kode as $value) {
                $sql = "SELECT kamar_inap.no_rawat as no_rawat , kamar_inap.stts_pulang as stts_pulang FROM kamar_inap JOIN diagnosa_pasien ON kamar_inap.no_rawat = diagnosa_pasien.no_rawat WHERE diagnosa_pasien.kd_penyakit LIKE '%$value%' AND diagnosa_pasien.prioritas IN ('1','2') AND diagnosa_pasien.status = 'Ranap' AND YEAR(kamar_inap.tgl_masuk) = '$tahun' AND stts_pulang NOT IN ('Pindah Kamar','+','-')";
                $stmt = $this->db()->pdo()->prepare($sql);
                $stmt->execute();
                $find = $stmt->fetchAll();
                foreach ($find as $value) {
                    $no_rkm_medis = $this->core->getRegPeriksaInfo('no_rkm_medis',$value['no_rawat']);
                    $umur = $this->core->getRegPeriksaInfo('umurdaftar',$value['no_rawat']);
                    $sttsumur = $this->core->getRegPeriksaInfo('sttsumur',$value['no_rawat']);
                    $jk = $this->core->getPasienInfo('jk',$no_rkm_medis);
                    if ($jk == 'L' && $umur < '7' && $sttsumur == 'Hr') {
                        $cari['l6k'] = $cari['l6k'] + 1;
                    } else if ($jk == 'P' && $umur < '7' && $sttsumur == 'Hr') {
                        $cari['p6k'] = $cari['p6k'] + 1;
                    } else if ($jk == 'L' && $umur > '6' && $umur < '29' && $sttsumur == 'Hr') {
                        $cari['l28k'] = $cari['l28k'] + 1;
                    } else if ($jk == 'P' && $umur > '6' && $umur < '29' && $sttsumur == 'Hr') {
                        $cari['p28k'] = $cari['p28k'] + 1;
                    } else if ($jk == 'L' && $umur < '13' && $sttsumur == 'Bl') {
                        $cari['l1th'] = $cari['l1th'] + 1;
                    } else if ($jk == 'P' && $umur < '13' && $sttsumur == 'Bl') {
                        $cari['p1th'] = $cari['p1th'] + 1;
                    } else if ($jk == 'L' && $umur < '5' && $sttsumur == 'Th') {
                        $cari['l4th'] = $cari['l4th'] + 1;
                    } else if ($jk == 'P' && $umur < '5' && $sttsumur == 'Th') {
                        $cari['p4th'] = $cari['p4th'] + 1;
                    } else if ($jk == 'L' && $umur > '4' && $umur < '15' && $sttsumur == 'Th') {
                        $cari['l14th'] = $cari['l14th'] + 1;
                    } else if ($jk == 'P' && $umur > '4' && $umur < '15' && $sttsumur == 'Th') {
                        $cari['p14th'] = $cari['p14th'] + 1;
                    } else if ($jk == 'L' && $umur > '14' && $umur < '25' && $sttsumur == 'Th') {
                        $cari['l24th'] = $cari['l24th'] + 1;
                    } else if ($jk == 'P' && $umur > '14' && $umur < '25' && $sttsumur == 'Th') {
                        $cari['p24th'] = $cari['p24th'] + 1;
                    } else if ($jk == 'L' && $umur > '24' && $umur < '45' && $sttsumur == 'Th') {
                        $cari['l44th'] = $cari['l44th'] + 1;
                    } else if ($jk == 'P' && $umur > '24' && $umur < '45' && $sttsumur == 'Th') {
                        $cari['p44th'] = $cari['p44th'] + 1;
                    } else if ($jk == 'L' && $umur > '44' && $umur < '65' && $sttsumur == 'Th') {
                        $cari['l64th'] = $cari['l64th'] + 1;
                    } else if ($jk == 'P' && $umur > '44' && $umur < '65' && $sttsumur == 'Th') {
                        $cari['p64th'] = $cari['p64th'] + 1;
                    } else if ($jk == 'L' && $umur > '64' && $sttsumur == 'Th') {
                        $cari['l65th'] = $cari['l65th'] + 1;
                    } else if ($jk == 'P' && $umur > '64' && $sttsumur == 'Th') {
                        $cari['p65th'] = $cari['p65th'] + 1;
                    }

                    if($value['stts_pulang'] == 'Meninggal'){
                        $cari['mati'] = $cari['mati'] + 1;
                    }
                }
            }
            $cari['lttl'] = $cari['l6k'] + $cari['l28k'] + $cari['l1th'] + $cari['l4th'] + $cari['l14th'] + $cari['l24th'] + $cari['l44th'] + $cari['l64th'] + $cari['l65th'];
            $cari['pttl'] = $cari['p6k'] + $cari['p28k'] + $cari['p1th'] + $cari['p4th'] + $cari['p14th'] + $cari['p24th'] + $cari['p44th'] + $cari['p64th'] + $cari['p65th'];
            $cari['ttl'] = $cari['lttl'] + $cari['pttl'] - $cari['mati'];
        } else {
            $sql = "SELECT kamar_inap.no_rawat as no_rawat , kamar_inap.stts_pulang as stts_pulang FROM kamar_inap JOIN diagnosa_pasien ON kamar_inap.no_rawat = diagnosa_pasien.no_rawat WHERE diagnosa_pasien.kd_penyakit = '$kode' AND YEAR(kamar_inap.tgl_masuk) = '$tahun' AND stts_pulang NOT IN ('Pindah Kamar','+','-')";
            $stmt = $this->db()->pdo()->prepare($sql);
            $stmt->execute();
            $find = $stmt->fetchAll();
            foreach ($find as $value) {
                $no_rkm_medis = $this->core->getRegPeriksaInfo('no_rkm_medis',$value['no_rawat']);
                $umur = $this->core->getRegPeriksaInfo('umurdaftar',$value['no_rawat']);
                $sttsumur = $this->core->getRegPeriksaInfo('sttsumur',$value['no_rawat']);
                $jk = $this->core->getPasienInfo('jk',$no_rkm_medis);
                if ($jk == 'L' && $umur < '7' && $sttsumur == 'Hr') {
                    $cari['l6k'] = $cari['l6k'] + 1;
                } else if ($jk == 'P' && $umur < '7' && $sttsumur == 'Hr') {
                    $cari['p6k'] = $cari['p6k'] + 1;
                } else if ($jk == 'L' && $umur > '6' && $umur < '29' && $sttsumur == 'Hr') {
                    $cari['l28k'] = $cari['l28k'] + 1;
                } else if ($jk == 'P' && $umur > '6' && $umur < '29' && $sttsumur == 'Hr') {
                    $cari['p28k'] = $cari['p28k'] + 1;
                } else if ($jk == 'L' && $umur < '13' && $sttsumur == 'Bl') {
                    $cari['l1th'] = $cari['l1th'] + 1;
                } else if ($jk == 'P' && $umur < '13' && $sttsumur == 'Bl') {
                    $cari['p1th'] = $cari['p1th'] + 1;
                } else if ($jk == 'L' && $umur < '5' && $sttsumur == 'Th') {
                    $cari['l4th'] = $cari['l4th'] + 1;
                } else if ($jk == 'P' && $umur < '5' && $sttsumur == 'Th') {
                    $cari['p4th'] = $cari['p4th'] + 1;
                } else if ($jk == 'L' && $umur > '4' && $umur < '15' && $sttsumur == 'Th') {
                    $cari['l14th'] = $cari['l14th'] + 1;
                } else if ($jk == 'P' && $umur > '4' && $umur < '15' && $sttsumur == 'Th') {
                    $cari['p14th'] = $cari['p14th'] + 1;
                } else if ($jk == 'L' && $umur > '14' && $umur < '25' && $sttsumur == 'Th') {
                    $cari['l24th'] = $cari['l24th'] + 1;
                } else if ($jk == 'P' && $umur > '14' && $umur < '25' && $sttsumur == 'Th') {
                    $cari['p24th'] = $cari['p24th'] + 1;
                } else if ($jk == 'L' && $umur > '24' && $umur < '45' && $sttsumur == 'Th') {
                    $cari['l44th'] = $cari['l44th'] + 1;
                } else if ($jk == 'P' && $umur > '24' && $umur < '45' && $sttsumur == 'Th') {
                    $cari['p44th'] = $cari['p44th'] + 1;
                } else if ($jk == 'L' && $umur > '44' && $umur < '65' && $sttsumur == 'Th') {
                    $cari['l64th'] = $cari['l64th'] + 1;
                } else if ($jk == 'P' && $umur > '44' && $umur < '65' && $sttsumur == 'Th') {
                    $cari['p64th'] = $cari['p64th'] + 1;
                } else if ($jk == 'L' && $umur > '64' && $sttsumur == 'Th') {
                    $cari['l65th'] = $cari['l65th'] + 1;
                } else if ($jk == 'P' && $umur > '64' && $sttsumur == 'Th') {
                    $cari['p65th'] = $cari['p65th'] + 1;
                }

                if($value['stts_pulang'] == 'Meninggal'){
                    $cari['mati'] = $cari['mati'] + 1;
                }
            }
            $cari['lttl'] = $cari['l6k'] + $cari['l28k'] + $cari['l1th'] + $cari['l4th'] + $cari['l14th'] + $cari['l24th'] + $cari['l44th'] + $cari['l64th'] + $cari['l65th'];
            $cari['pttl'] = $cari['p6k'] + $cari['p28k'] + $cari['p1th'] + $cari['p4th'] + $cari['p14th'] + $cari['p24th'] + $cari['p44th'] + $cari['p64th'] + $cari['p65th'];
            $cari['ttl'] = $cari['lttl'] + $cari['pttl'] - $cari['mati'];
        }
        return $cari;
    }

    public function getRl4a()
    {
        $database = MODULES . '/data_sirs/js/admin/jns_penyakit.json';
        $this->_addHeaderFiles();
        if (isset($_GET['y'])) {
            $tahun = $_GET['y'];
        } else {
            $tahun = date('Y');
        }
        $this->assign['jns_layan'] = [];
        $this->assign['tahun'] = $tahun;
        $jns_layanan = file_get_contents($database);
        $jns_layanan = json_decode($jns_layanan);
        $total = 0;
        $jns_layan = [];
        foreach ($jns_layanan as $value) {
            $jns_layan['ket']= $value->nm_penyakit;
            $codes = $value->kd_penyakit;
            $kodeQuery = ''; $kodeView = array();$listKode = '';
            if (is_array($codes)) {
                for ($i=0; $i < count($codes); $i++) {
                    $listKode = $listKode . " , '" . $codes[$i] . "' ";
                    $jns_layan['kode'] = $listKode;
                    $kodeView[] = $codes[$i];
                }
                $kodeQuery = $kodeView;
                $jns_layan['kode'] = str_replace("'"," ",$jns_layan['kode']);
                $jns_layan['kode'] = str_replace("' , '"," , ",$jns_layan['kode']);
                $jns_layan['kode'] = ltrim($jns_layan['kode']," , ");
            } else {
                $kodeQuery = $codes;
                $jns_layan['kode'] = $kodeQuery;
                $jns_layan['kode'] = str_replace("'"," ",$jns_layan['kode']);
            }

            $jns_layan['list'] = $this->pasienRl4a($kodeQuery,$tahun);
            $this->assign['jns_layan'][] = $jns_layan;
        }
        return $this->draw('rl4_a.html',['rl32' => $this->assign]);
    }

    function pasienRl4b($kode,$tahun){
        $cari = array();
        $cari['l6k'] = 0; $cari['p6k'] = 0;$cari['l28k'] = 0;$cari['p28k']=0;$cari['l1th']=0; $cari['p1th']=0;
        $cari['l4th']=0; $cari['p4th']=0;$cari['l14th']=0; $cari['p14th']=0;$cari['l24th']=0; $cari['p24th']=0;
        $cari['l44th']=0; $cari['p44th']=0;$cari['l64th']=0; $cari['p64th']=0;$cari['l65th']=0; $cari['p65th']=0;
        $cari['mati'] = 0;
        if (is_array($kode)) {
            // $cari = 0;
            foreach ($kode as $value) {
                $sql = "SELECT reg_periksa.umurdaftar as umurdaftar , reg_periksa.sttsumur as sttsumur , reg_periksa.no_rkm_medis as no_rkm_medis , diagnosa_pasien.status_penyakit as barulama FROM reg_periksa  JOIN diagnosa_pasien ON reg_periksa.no_rawat = diagnosa_pasien.no_rawat WHERE diagnosa_pasien.kd_penyakit LIKE '%$value%' AND diagnosa_pasien.prioritas IN ('1','2') AND diagnosa_pasien.status = 'Ralan' AND YEAR(reg_periksa.tgl_registrasi) = '$tahun' AND reg_periksa.kd_poli NOT IN ('IGDK','U0027') AND diagnosa_pasien.status_penyakit = 'Baru'";
                $stmt = $this->db()->pdo()->prepare($sql);
                $stmt->execute();
                $find = $stmt->fetchAll();
                foreach ($find as $value) {
                    $no_rkm_medis = $value['no_rkm_medis'];
                    $umur = $value['umurdaftar'];
                    $sttsumur = $value['sttsumur'];
                    $statusbarulama = $value['barulama'];
                    $jk = $this->core->getPasienInfo('jk',$no_rkm_medis);
                    if ($jk == 'L' && $umur < '7' && $sttsumur == 'Hr' && $statusbarulama == 'Baru') {
                        $cari['l6k'] = $cari['l6k'] + 1;
                    } else if ($jk == 'P' && $umur < '7' && $sttsumur == 'Hr' && $statusbarulama == 'Baru') {
                        $cari['p6k'] = $cari['p6k'] + 1;
                    } else if ($jk == 'L' && $umur > '6' && $umur < '29' && $sttsumur == 'Hr' && $statusbarulama == 'Baru') {
                        $cari['l28k'] = $cari['l28k'] + 1;
                    } else if ($jk == 'P' && $umur > '6' && $umur < '29' && $sttsumur == 'Hr' && $statusbarulama == 'Baru') {
                        $cari['p28k'] = $cari['p28k'] + 1;
                    } else if ($jk == 'L' && $umur < '13' && $sttsumur == 'Bl' && $statusbarulama == 'Baru') {
                        $cari['l1th'] = $cari['l1th'] + 1;
                    } else if ($jk == 'P' && $umur < '13' && $sttsumur == 'Bl' && $statusbarulama == 'Baru') {
                        $cari['p1th'] = $cari['p1th'] + 1;
                    } else if ($jk == 'L' && $umur < '5' && $sttsumur == 'Th' && $statusbarulama == 'Baru') {
                        $cari['l4th'] = $cari['l4th'] + 1;
                    } else if ($jk == 'P' && $umur < '5' && $sttsumur == 'Th' && $statusbarulama == 'Baru') {
                        $cari['p4th'] = $cari['p4th'] + 1;
                    } else if ($jk == 'L' && $umur > '4' && $umur < '15' && $sttsumur == 'Th' && $statusbarulama == 'Baru') {
                        $cari['l14th'] = $cari['l14th'] + 1;
                    } else if ($jk == 'P' && $umur > '4' && $umur < '15' && $sttsumur == 'Th' && $statusbarulama == 'Baru') {
                        $cari['p14th'] = $cari['p14th'] + 1;
                    } else if ($jk == 'L' && $umur > '14' && $umur < '25' && $sttsumur == 'Th' && $statusbarulama == 'Baru') {
                        $cari['l24th'] = $cari['l24th'] + 1;
                    } else if ($jk == 'P' && $umur > '14' && $umur < '25' && $sttsumur == 'Th' && $statusbarulama == 'Baru') {
                        $cari['p24th'] = $cari['p24th'] + 1;
                    } else if ($jk == 'L' && $umur > '24' && $umur < '45' && $sttsumur == 'Th' && $statusbarulama == 'Baru') {
                        $cari['l44th'] = $cari['l44th'] + 1;
                    } else if ($jk == 'P' && $umur > '24' && $umur < '45' && $sttsumur == 'Th' && $statusbarulama == 'Baru') {
                        $cari['p44th'] = $cari['p44th'] + 1;
                    } else if ($jk == 'L' && $umur > '44' && $umur < '65' && $sttsumur == 'Th' && $statusbarulama == 'Baru') {
                        $cari['l64th'] = $cari['l64th'] + 1;
                    } else if ($jk == 'P' && $umur > '44' && $umur < '65' && $sttsumur == 'Th' && $statusbarulama == 'Baru') {
                        $cari['p64th'] = $cari['p64th'] + 1;
                    } else if ($jk == 'L' && $umur > '64' && $sttsumur == 'Th' && $statusbarulama == 'Baru') {
                        $cari['l65th'] = $cari['l65th'] + 1;
                    } else if ($jk == 'P' && $umur > '64' && $sttsumur == 'Th' && $statusbarulama == 'Baru') {
                        $cari['p65th'] = $cari['p65th'] + 1;
                    }

                    if($statusbarulama == 'Lama'){
                        $cari['mati'] = $cari['mati'] + 1;
                    }
                }
            }
            $cari['lttl'] = $cari['l6k'] + $cari['l28k'] + $cari['l1th'] + $cari['l4th'] + $cari['l14th'] + $cari['l24th'] + $cari['l44th'] + $cari['l64th'] + $cari['l65th'];
            $cari['pttl'] = $cari['p6k'] + $cari['p28k'] + $cari['p1th'] + $cari['p4th'] + $cari['p14th'] + $cari['p24th'] + $cari['p44th'] + $cari['p64th'] + $cari['p65th'];
            $cari['ttl'] = $cari['lttl'] + $cari['pttl'];
            $cari['mati'] = $cari['mati'] + $cari['ttl'];
        } else {
            $sql = "SELECT reg_periksa.umurdaftar as umurdaftar , reg_periksa.sttsumur as sttsumur , reg_periksa.no_rkm_medis as no_rkm_medis , diagnosa_pasien.status_penyakit as barulama FROM reg_periksa  JOIN diagnosa_pasien ON reg_periksa.no_rawat = diagnosa_pasien.no_rawat WHERE diagnosa_pasien.kd_penyakit = '$kode' AND YEAR(reg_periksa.tgl_registrasi) = '$tahun' AND reg_periksa.kd_poli NOT IN ('IGDK','U0027') ";
            $stmt = $this->db()->pdo()->prepare($sql);
            $stmt->execute();
            $find = $stmt->fetchAll();
            foreach ($find as $value) {
                $no_rkm_medis = $value['no_rkm_medis'];
                $umur = $value['umurdaftar'];
                $sttsumur = $value['sttsumur'];
                $statusbarulama = $value['barulama'];
                $jk = $this->core->getPasienInfo('jk',$no_rkm_medis);
                if ($jk == 'L' && $umur < '7' && $sttsumur == 'Hr' && $statusbarulama == 'Baru') {
                    $cari['l6k'] = $cari['l6k'] + 1;
                } else if ($jk == 'P' && $umur < '7' && $sttsumur == 'Hr' && $statusbarulama == 'Baru') {
                    $cari['p6k'] = $cari['p6k'] + 1;
                } else if ($jk == 'L' && $umur > '6' && $umur < '29' && $sttsumur == 'Hr' && $statusbarulama == 'Baru') {
                    $cari['l28k'] = $cari['l28k'] + 1;
                } else if ($jk == 'P' && $umur > '6' && $umur < '29' && $sttsumur == 'Hr' && $statusbarulama == 'Baru') {
                    $cari['p28k'] = $cari['p28k'] + 1;
                } else if ($jk == 'L' && $umur < '13' && $sttsumur == 'Bl' && $statusbarulama == 'Baru') {
                    $cari['l1th'] = $cari['l1th'] + 1;
                } else if ($jk == 'P' && $umur < '13' && $sttsumur == 'Bl' && $statusbarulama == 'Baru') {
                    $cari['p1th'] = $cari['p1th'] + 1;
                } else if ($jk == 'L' && $umur < '5' && $sttsumur == 'Th' && $statusbarulama == 'Baru') {
                    $cari['l4th'] = $cari['l4th'] + 1;
                } else if ($jk == 'P' && $umur < '5' && $sttsumur == 'Th' && $statusbarulama == 'Baru') {
                    $cari['p4th'] = $cari['p4th'] + 1;
                } else if ($jk == 'L' && $umur > '4' && $umur < '15' && $sttsumur == 'Th' && $statusbarulama == 'Baru') {
                    $cari['l14th'] = $cari['l14th'] + 1;
                } else if ($jk == 'P' && $umur > '4' && $umur < '15' && $sttsumur == 'Th' && $statusbarulama == 'Baru') {
                    $cari['p14th'] = $cari['p14th'] + 1;
                } else if ($jk == 'L' && $umur > '14' && $umur < '25' && $sttsumur == 'Th' && $statusbarulama == 'Baru') {
                    $cari['l24th'] = $cari['l24th'] + 1;
                } else if ($jk == 'P' && $umur > '14' && $umur < '25' && $sttsumur == 'Th' && $statusbarulama == 'Baru') {
                    $cari['p24th'] = $cari['p24th'] + 1;
                } else if ($jk == 'L' && $umur > '24' && $umur < '45' && $sttsumur == 'Th' && $statusbarulama == 'Baru') {
                    $cari['l44th'] = $cari['l44th'] + 1;
                } else if ($jk == 'P' && $umur > '24' && $umur < '45' && $sttsumur == 'Th' && $statusbarulama == 'Baru') {
                    $cari['p44th'] = $cari['p44th'] + 1;
                } else if ($jk == 'L' && $umur > '44' && $umur < '65' && $sttsumur == 'Th' && $statusbarulama == 'Baru') {
                    $cari['l64th'] = $cari['l64th'] + 1;
                } else if ($jk == 'P' && $umur > '44' && $umur < '65' && $sttsumur == 'Th' && $statusbarulama == 'Baru') {
                    $cari['p64th'] = $cari['p64th'] + 1;
                } else if ($jk == 'L' && $umur > '64' && $sttsumur == 'Th' && $statusbarulama == 'Baru') {
                    $cari['l65th'] = $cari['l65th'] + 1;
                } else if ($jk == 'P' && $umur > '64' && $sttsumur == 'Th' && $statusbarulama == 'Baru') {
                    $cari['p65th'] = $cari['p65th'] + 1;
                }

                if($statusbarulama == 'Lama'){
                    $cari['mati'] = $cari['mati'] + 1;
                }
            }
            $cari['lttl'] = $cari['l6k'] + $cari['l28k'] + $cari['l1th'] + $cari['l4th'] + $cari['l14th'] + $cari['l24th'] + $cari['l44th'] + $cari['l64th'] + $cari['l65th'];
            $cari['pttl'] = $cari['p6k'] + $cari['p28k'] + $cari['p1th'] + $cari['p4th'] + $cari['p14th'] + $cari['p24th'] + $cari['p44th'] + $cari['p64th'] + $cari['p65th'];
            $cari['ttl'] = $cari['lttl'] + $cari['pttl'];
            $cari['mati'] = $cari['mati'] + $cari['ttl'];
        }
        return $cari;
    }

    public function getRl4b()
    {
        $database = MODULES . '/data_sirs/js/admin/jns_penyakit.json';
        $this->_addHeaderFiles();
        if (isset($_GET['y'])) {
            $tahun = $_GET['y'];
        } else {
            $tahun = date('Y');
        }
        $this->assign['jns_layan'] = [];
        $this->assign['tahun'] = $tahun;
        $jns_layanan = file_get_contents($database);
        $jns_layanan = json_decode($jns_layanan);
        $total = 0;
        $jns_layan = [];
        foreach ($jns_layanan as $value) {
            $jns_layan['ket']= $value->nm_penyakit;
            $codes = $value->kd_penyakit;
            $kodeQuery = ''; $kodeView = array();$listKode = '';
            if (is_array($codes)) {
                for ($i=0; $i < count($codes); $i++) {
                    $listKode = $listKode . " , '" . $codes[$i] . "' ";
                    $jns_layan['kode'] = $listKode;
                    $kodeView[] = $codes[$i];
                }
                $kodeQuery = $kodeView;
                $jns_layan['kode'] = str_replace("'"," ",$jns_layan['kode']);
                $jns_layan['kode'] = str_replace("' , '"," , ",$jns_layan['kode']);
                $jns_layan['kode'] = ltrim($jns_layan['kode']," , ");
            } else {
                $kodeQuery = $codes;
                $jns_layan['kode'] = $kodeQuery;
                $jns_layan['kode'] = str_replace("'"," ",$jns_layan['kode']);
            }

            $jns_layan['list'] = $this->pasienRl4b($kodeQuery,$tahun);
            $this->assign['jns_layan'][] = $jns_layan;
        }
        return $this->draw('rl4_b.html',['rl32' => $this->assign]);
    }

    public function getJavascript()
    {
        header('Content-type: text/javascript');
        echo $this->draw(MODULES . '/data_sirs/js/admin/data_sirs.js');
        exit();
    }

    private function _addHeaderFiles()
    {
        $this->core->addCSS(url('assets/css/dataTables.bootstrap.min.css'));

        $this->core->addCSS(url('https://cdn.datatables.net/1.10.15/css/jquery.dataTables.min.css'));
        $this->core->addCSS(url('https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css'));
        // CSS
        $this->core->addCSS(url('assets/css/jquery-ui.css'));
        $this->core->addCSS(url('assets/jscripts/lightbox/lightbox.min.css'));
        $this->core->addCSS(url('assets/css/bootstrap-datetimepicker.css'));

        $this->core->addJS(url('https://cdnjs.cloudflare.com/ajax/libs/jquery/3.3.1/jquery.min.js'), 'footer');
        $this->core->addJS(url('assets/jscripts/jquery.dataTables.min.js'), 'footer');
        $this->core->addJS(url('assets/jscripts/dataTables.bootstrap.min.js'), 'footer');
        // JS
        $this->core->addJS(url('assets/jscripts/jquery-ui.js'), 'footer');
        $this->core->addJS(url('assets/jscripts/lightbox/lightbox.min.js'), 'footer');
        $this->core->addJS(url('assets/jscripts/moment-with-locales.js'));
        $this->core->addJS(url('assets/jscripts/bootstrap-datetimepicker.js'));

        $this->core->addJS(url('https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js'), 'footer');
        $this->core->addJS(url('https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js'), 'footer');
        $this->core->addJS(url('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js'), 'footer');
        $this->core->addJS(url('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js'), 'footer');
        $this->core->addJS(url('https://cdn.datatables.net/buttons/1.3.1/js/buttons.html5.min.js'), 'footer');
        $this->core->addJS(url([ADMIN, 'data_sirs', 'javascript']), 'footer');
    }
}
