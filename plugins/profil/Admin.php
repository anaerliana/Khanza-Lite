<?php

namespace Plugins\Profil;

use Systems\AdminModule;

class Admin extends AdminModule
{

    public function navigation()
    {
        return [
            'Kelola' => 'manage',
            'Biodata' => 'biodata',
            'Cuti' => 'cuti',
            'Presensi Masuk' => 'presensi',
            'Rekap Presensi' => 'rekap_presensi',
            'Jadwal Pegawai' => 'jadwal',
            'Ganti Password' => 'ganti_pass'
        ];
    }

    public function getManage()
    {
        $sub_modules = [
            ['name' => 'Biodata', 'url' => url([ADMIN, 'profil', 'biodata']), 'icon' => 'cubes', 'desc' => 'Biodata Pegawai'],
            ['name' => 'Izin', 'url' => url([ADMIN, 'profil', 'cuti']), 'icon' => 'list', 'desc' => 'Permintaan Izin / Cuti '],
            ['name' => 'Presensi', 'url' => url([ADMIN, 'profil', 'presensi']), 'icon' => 'cubes', 'desc' => 'Presensi Pegawai'],
            ['name' => 'Rekap Presensi', 'url' => url([ADMIN, 'profil', 'rekap_presensi']), 'icon' => 'cubes', 'desc' => 'Rekap Presensi Pegawai'],
            ['name' => 'Jadwal', 'url' => url([ADMIN, 'profil', 'jadwal']), 'icon' => 'cubes', 'desc' => 'Jadwal Pegawai'],
            ['name' => 'Ganti Password', 'url' => url([ADMIN, 'profil', 'ganti_pass']), 'icon' => 'cubes', 'desc' => 'Ganti Pasword'],
        ];
        $username = $this->core->getUserInfo('username', null, true);
        $profil = $this->db('pegawai')->where('nik', $username)->oneArray();
        $tanggal = getDayIndonesia(date('Y-m-d')) . ', ' . dateIndonesia(date('Y-m-d'));
        $presensi = $this->db('rekap_presensi')->where('id', $profil['id'])->where('photo', '!=', '')->like('jam_datang', date('Y-m') . '%')->toArray();
        $absensi = $this->db('rekap_presensi')->where('id', $profil['id'])->where('photo', '')->like('jam_datang', date('Y-m') . '%')->toArray();
        $fotoURL = url(MODULES . '/kepegawaian/img/default.png');
        if (!empty($profil['photo'])) {
            $fotoURL = WEBAPPS_URL . '/penggajian/' . $profil['photo'];
        }
        return $this->draw('manage.html', ['sub_modules' => $sub_modules, 'profil' => $profil, 'tanggal' => $tanggal, 'presensi' => $presensi, 'absensi' => $absensi, 'fotoURL' => $fotoURL]);
    }

    public function getBiodata()
    {
        $this->_addHeaderFiles();
        $username = $this->core->getUserInfo('username', null, true);

        $row = $this->db('pegawai')->where('nik', $username)->oneArray();
        $this->assign['form'] = $row;
        $this->assign['title'] = 'Edit Biodata';
        $this->assign['jk'] = ['Pria', 'Wanita'];
        $this->assign['departemen'] = $this->db('departemen')->toArray();
        $this->assign['bidang'] = $this->db('bidang')->toArray();
        $this->assign['stts_wp'] = $this->db('stts_wp')->toArray();
        $this->assign['pendidikan'] = $this->db('pendidikan')->toArray();
        $this->assign['jnj_jabatan'] = $this->db('jnj_jabatan')->toArray();

        $this->assign['fotoURL'] = url(WEBAPPS_PATH . '/penggajian/' . $row['photo']);

        return $this->draw('biodata.html', ['biodata' => $this->assign]);
    }

    public function postBiodataSave($id = null)
    {
        $errors = 0;

        if (!$id) {
            $location = url([ADMIN, 'profil', 'biodata']);
        } else {
            $location = url([ADMIN, 'profil', 'biodata', $id]);
        }

        if (checkEmptyFields(['nama'], $_POST)) {
            $this->notify('failure', 'Isian kosong');
            redirect($location, $_POST);
        }

        if (!$errors) {
            unset($_POST['save']);

            if (($photo = isset_or($_FILES['photo']['tmp_name'], false)) || !$id) {
                $img = new \Systems\Lib\Image;

                if (empty($photo) && !$id) {
                    $photo = MODULES . '/profil/img/default.png';
                }
                if ($img->load($photo)) {
                    if ($img->getInfos('width') < $img->getInfos('height')) {
                        $img->crop(0, 0, $img->getInfos('width'), $img->getInfos('width'));
                    } else {
                        $img->crop(0, 0, $img->getInfos('height'), $img->getInfos('height'));
                    }

                    if ($img->getInfos('width') > 512) {
                        $img->resize(512, 512);
                    }

                    if ($id) {
                        $pegawai = $this->db('pegawai')->oneArray($id);
                    }

                    $_POST['photo'] = "pages/pegawai/photo/" . $pegawai['nik'] . "." . $img->getInfos('type');
                }
            }

            if (!$id) {    // new
                $query = $this->db('pegawai')->save($_POST);
            } else {        // edit
                $query = $this->db('pegawai')->where('id', $id)->save($_POST);
            }

            if ($query) {
                if (isset($img) && $img->getInfos('width')) {
                    if (isset($pegawai)) {
                        unlink(WEBAPPS_PATH . "/penggajian/" . $pegawai['photo']);
                    }

                    $img->save(WEBAPPS_PATH . "/penggajian/" . $_POST['photo']);
                }

                $this->notify('success', 'Simpan sukes');
            } else {
                $this->notify('failure', 'Simpan gagal');
            }

            redirect($location);
        }

        redirect($location, $_POST);
    }

    public function getJadwal($page = 1)
    {

        $array_hari = array(1 => 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu', 'Minggu');
        $array_bulan = array(1 => 'Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');

        $this->_addHeaderFiles();
        $perpage = '10';
        $phrase = '';
        if (isset($_GET['s']))
            $phrase = $_GET['s'];

        $status = '1';
        if (isset($_GET['status']))
            $status = $_GET['status'];

        $username = $this->core->getUserInfo('username', null, true);

        // pagination
        if ($this->core->getUserInfo('id') == 1) {
            $totalRecords = $this->db('jadwal_pegawai')
                ->join('pegawai', 'pegawai.id=jadwal_pegawai.id')
                ->where('jadwal_pegawai.tahun', date('Y'))
                ->where('jadwal_pegawai.bulan', date('m'))
                ->like('pegawai.nama', '%' . $phrase . '%')
                ->toArray();
        } else {
            $totalRecords = $this->db('jadwal_pegawai')
                ->join('pegawai', 'pegawai.id=jadwal_pegawai.id')
                ->where('jadwal_pegawai.tahun', date('Y'))
                ->where('jadwal_pegawai.bulan', date('m'))
                ->where('nik', $username)
                // ->like('pegawai.nama', '%'.$phrase.'%')
                ->toArray();
        }
        $pagination = new \Systems\Lib\Pagination($page, count($totalRecords), 10, url([ADMIN, 'profil', 'jadwal', '%d']));
        $this->assign['pagination'] = $pagination->nav('pagination', '5');
        $this->assign['totalRecords'] = $totalRecords;

        // list
        $offset = $pagination->offset();
        if ($this->core->getUserInfo('id') == 1) {
            $rows = $this->db('jadwal_pegawai')
                ->join('pegawai', 'pegawai.id=jadwal_pegawai.id')
                ->where('jadwal_pegawai.tahun', date('Y'))
                ->where('jadwal_pegawai.bulan', date('m'))
                ->like('pegawai.nama', '%' . $phrase . '%')
                ->offset($offset)
                ->limit($perpage)
                ->toArray();
        } else {
            $rows = $this->db('jadwal_pegawai')
                ->join('pegawai', 'pegawai.id=jadwal_pegawai.id')
                ->where('jadwal_pegawai.tahun', date('Y'))
                ->where('jadwal_pegawai.bulan', date('m'))
                ->where('nik', $username)
                // ->like('pegawai.nama', '%'.$phrase.'%')
                ->offset($offset)
                ->limit($perpage)
                ->toArray();
        }
        $this->assign['list'] = [];
        if (count($rows)) {
            foreach ($rows as $row) {
                $row = htmlspecialchars_array($row);
                // $row['editURL'] = url([ADMIN, 'presensi', 'jadwaledit', $row['id']]);
                // $row['delURL']  = url([ADMIN, 'master', 'petugasdelete', $row['nip']]);
                // $row['restoreURL']  = url([ADMIN, 'master', 'petugasrestore', $row['nip']]);
                // $row['viewURL'] = url([ADMIN, 'master', 'petugasview', $row['nip']]);
                $this->assign['list'][] = $row;
            }
        }

        $year = date('Y');
        $month = date('m');
        $day = cal_days_in_month(CAL_GREGORIAN, $month, $year);

        for ($i = 1; $i < $day + 1; $i++) {
            $i;
        }

        $this->assign['getStatus'] = isset($_GET['status']);
        // $this->assign['addURL'] = url([ADMIN, 'presensi', 'jadwaladd']);
        // $this->assign['printURL'] = url([ADMIN, 'master', 'petugasprint']);

        return $this->draw('jadwal.manage.html', ['jadwal' => $this->assign, 'array_hari' => $array_hari, 'array_bulan' => $array_bulan]);
    }

    public function getRekap_Presensi($page = 1)
    {
        $this->_addHeaderFiles();
        $perpage = '10';
        $phrase = '';
        $year = date('Y');
        if (isset($_GET['s']))
            $phrase = $_GET['s'];

        $status = '1';
        if (isset($_GET['status']))
            $status = $_GET['status'];

        $bulan = date('m');
        if (isset($_GET['b'])) {
            $bulan = $_GET['b'];
        }

        $username = $this->core->getUserInfo('username', null, true);

        if ($this->core->getUserInfo('id') == 1 and isset($_GET['bulan'])) {
            $totalRecords = $this->db('rekap_presensi')
                ->join('pegawai', 'pegawai.id = rekap_presensi.id')
                ->where('jam_datang', '>', date('Y-' . $bulan) . '-01')
                ->where('jam_datang', '<', date('Y-' . $bulan) . '-31')
                ->like('nama', '%' . $phrase . '%')
                ->orLike('shift', '%' . $phrase . '%')
                ->asc('jam_datang')
                ->toArray();
        } elseif (isset($_GET['bulan'])) {
            $totalRecords = $this->db('rekap_presensi')
                ->join('pegawai', 'pegawai.id = rekap_presensi.id')
                ->where('jam_datang', '>', date('Y-' . $bulan) . '-01')
                ->where('jam_datang', '<', date('Y-' . $bulan) . '-31')
                ->where('nik', $username)
                ->asc('jam_datang')
                ->toArray();
        } else {
            $totalRecords = $this->db('rekap_presensi')
                ->join('pegawai', 'pegawai.id = rekap_presensi.id')
                ->where('jam_datang', '>', date('Y-' . $bulan) . '-01')
                ->where('jam_datang', '<', date('Y-' . $bulan) . '-31')
                ->where('nik', $username)
                ->asc('jam_datang')
                ->toArray();
        }
        $pagination = new \Systems\Lib\Pagination($page, count($totalRecords), 10, url([ADMIN, 'profil', 'rekap_presensi', '%d?b=' . $bulan . '&s=' . $phrase]));
        $this->assign['pagination'] = $pagination->nav('pagination', '5');
        $this->assign['totalRecords'] = $totalRecords;

        // list
        $offset = $pagination->offset();

        if ($this->core->getUserInfo('id') == 1 and isset($_GET['bulan'])) {
            $rows = $this->db('rekap_presensi')
                ->select([
                    'nama' => 'pegawai.nama',
                    'departemen' => 'pegawai.departemen',
                    'id' => 'rekap_presensi.id',
                    'shift' => 'rekap_presensi.shift',
                    'jam_datang' => 'rekap_presensi.jam_datang',
                    'jam_pulang' => 'rekap_presensi.jam_pulang',
                    'status' => 'rekap_presensi.status',
                    'durasi' => 'rekap_presensi.durasi',
                    'photo' => 'rekap_presensi.photo'
                ])
                ->join('pegawai', 'pegawai.id = rekap_presensi.id')
                ->where('jam_datang', '>', date('Y-' . $bulan) . '-01')
                ->where('jam_datang', '<', date('Y-' . $bulan) . '-31')
                ->like('nama', '%' . $phrase . '%')
                ->orLike('shift', '%' . $phrase . '%')
                ->asc('jam_datang')
                ->offset($offset)
                ->limit($perpage)
                ->toArray();
        } elseif (isset($_GET['bulan'])) {
            $rows = $this->db('rekap_presensi')
                ->select([
                    'nama' => 'pegawai.nama',
                    'departemen' => 'pegawai.departemen',
                    'id' => 'rekap_presensi.id',
                    'shift' => 'rekap_presensi.shift',
                    'jam_datang' => 'rekap_presensi.jam_datang',
                    'jam_pulang' => 'rekap_presensi.jam_pulang',
                    'status' => 'rekap_presensi.status',
                    'durasi' => 'rekap_presensi.durasi',
                    'photo' => 'rekap_presensi.photo'
                ])
                ->join('pegawai', 'pegawai.id = rekap_presensi.id')
                ->where('jam_datang', '>', date('Y-' . $bulan) . '-01')
                ->where('jam_datang', '<', date('Y-' . $bulan) . '-31')
                ->where('nik', $username)
                ->asc('jam_datang')
                ->toArray();
        } else {
            $rows = $this->db('rekap_presensi')
                ->select([
                    'nama' => 'pegawai.nama',
                    'departemen' => 'pegawai.departemen',
                    'id' => 'rekap_presensi.id',
                    'shift' => 'rekap_presensi.shift',
                    'jam_datang' => 'rekap_presensi.jam_datang',
                    'jam_pulang' => 'rekap_presensi.jam_pulang',
                    'status' => 'rekap_presensi.status',
                    'durasi' => 'rekap_presensi.durasi',
                    'photo' => 'rekap_presensi.photo'
                ])
                ->join('pegawai', 'pegawai.id = rekap_presensi.id')
                ->where('jam_datang', '>', date('Y-' . $bulan) . '-01')
                ->where('jam_datang', '<', date('Y-' . $bulan) . '-31')
                ->where('nik', $username)
                ->asc('jam_datang')
                ->toArray();
        }

        $this->assign['list'] = [];
        if (count($rows)) {
            foreach ($rows as $row) {
                $row = htmlspecialchars_array($row);
                $row['mapURL']  = url([ADMIN, 'profil', 'googlemap', $row['id'], date('Y-m-d', strtotime($row['jam_datang']))]);
                $beritaAcara = url([ADMIN, 'profil', 'beritaacara', $row['id'], $bulan]);
                $cek = $this->db('rekap_ba')->where('id',$row['id'])->where('bulan',$bulan)->where('tahun',$year)->oneArray();
                $this->assign['list'][] = $row;
            }
        }


        $this->assign['getStatus'] = isset($_GET['status']);
        $this->assign['getBulan'] = $bulan;
        $this->assign['beritaAcara'] = $beritaAcara;
        $this->assign['checkBa'] = $cek;
        $this->assign['getUser'] = $username;
        $this->assign['bulan'] = array('', '01', '02', '03', '04', '05', '06', '07', '08', '09', '10', '11', '12');
        return $this->draw('rekap_presensi.html', ['rekap' => $this->assign]);
    }

    public function getGoogleMap($id, $tanggal)
    {
        $geo = $this->db('mlite_geolocation_presensi')->where('id', $id)->where('tanggal', $tanggal)->oneArray();
        $pegawai = $this->db('pegawai')->where('id', $id)->oneArray();

        $this->tpl->set('geo', $geo);
        $this->tpl->set('pegawai', $pegawai);
        echo $this->tpl->draw(MODULES . '/profil/view/admin/google_map.html', true);
        exit();
    }

    public function getBeritaAcara($id, $bulan)
    {
        $year = date('Y');
        $pegawai = $this->db('pegawai')->where('id', $id)->oneArray();
        $ba = $this->db('rekap_ba')->where('id',$id)->where('bulan',$bulan)->where('tahun',$year)->oneArray();
        $this->tpl->set('tahun',$year);
        $this->tpl->set('pegawai', $pegawai);
        $this->tpl->set('bulan', $bulan);
        $this->tpl->set('ba',$ba);
        echo $this->tpl->draw(MODULES . '/profil/view/admin/berita_acara.html', true);
        exit();
    }

    public function postRekapBkdSimpan($id = null)
    {
        $errors = 0;
        if (!$id) {
            $location = url([ADMIN, 'profil', 'rekap_presensi']);
        } else {
            $location = url([ADMIN, 'profil', 'rekap_presensi', $id , $_POST['bulan'] , $_POST['tahun']]);
        }

        if (!$errors) {
            unset($_POST['save']);
            $_POST['created_at'] = date('Y-m-d H:i:s');
            $_POST['updated_at'] = date('Y-m-d H:i:s');
            if (!$id) {
                $query = $this->db('rekap_bkd')->save($_POST);
                $query2 = $this->db('rekap_ba')->save($_POST);
            } else {
                $query = $this->db('rekap_bkd')->where('id', $id)->where('tahun', $_POST['tahun'])->where('bulan', $_POST['bulan'])->save($_POST);
                $query = $this->db('rekap_ba')->where('id', $id)->where('tahun', $_POST['tahun'])->where('bulan', $_POST['bulan'])->save($_POST);
            }
            if ($query) {
                $this->notify('success', 'Simpan sukses');
            } else {
                $this->notify('failure', 'Simpan gagal');
            }
            redirect($location);
            print_r($_POST);
            print_r($query);
        }

        redirect($location, $_POST);
    }

    public function getPresensi($page = 1)
    {
        $this->_addHeaderFiles();

        $perpage = '10';
        $phrase = '';
        if (isset($_GET['s']))
            $phrase = $_GET['s'];

        $username = $this->core->getUserInfo('username', null, true);

        // pagination
        if ($this->core->getUserInfo('id') == 1) {
            $totalRecords = $this->db('temporary_presensi')
                ->join('pegawai', 'pegawai.id = temporary_presensi.id')
                ->like('nama', '%' . $phrase . '%')
                // ->orLike('jam_datang', '%'.date('Y-m').'%')
                ->asc('jam_datang')
                ->toArray();
        } else {
            $totalRecords = $this->db('temporary_presensi')
                ->join('pegawai', 'pegawai.id = temporary_presensi.id')
                ->where('nik', $username)
                ->like('nama', '%' . $phrase . '%')
                ->asc('jam_datang')
                ->toArray();
        }
        $pagination = new \Systems\Lib\Pagination($page, count($totalRecords), 10, url([ADMIN, 'presensi', 'presensi', '%d']));
        $this->assign['pagination'] = $pagination->nav('pagination', '5');
        $this->assign['totalRecords'] = $totalRecords;

        // list
        $offset = $pagination->offset();
        if ($this->core->getUserInfo('id') == 1) {
            $rows = $this->db('temporary_presensi')
                ->select([
                    'nama' => 'pegawai.nama',
                    'id' => 'temporary_presensi.id',
                    'shift' => 'temporary_presensi.shift',
                    'jam_datang' => 'temporary_presensi.jam_datang',
                    'status' => 'temporary_presensi.status',
                    'photo' => 'temporary_presensi.photo'
                ])
                ->join('pegawai', 'pegawai.id = temporary_presensi.id')
                ->like('nama', '%' . $phrase . '%')
                ->asc('jam_datang')
                ->offset($offset)
                ->limit($perpage)
                ->toArray();
        } else {
            $rows = $this->db('temporary_presensi')
                ->select([
                    'nama' => 'pegawai.nama',
                    'id' => 'temporary_presensi.id',
                    'shift' => 'temporary_presensi.shift',
                    'jam_datang' => 'temporary_presensi.jam_datang',
                    'status' => 'temporary_presensi.status',
                    'photo' => 'temporary_presensi.photo'
                ])
                ->join('pegawai', 'pegawai.id = temporary_presensi.id')
                ->where('nik', $username)
                ->like('nama', '%' . $phrase . '%')
                ->asc('jam_datang')
                ->offset($offset)
                ->limit($perpage)
                ->toArray();
        }
        $this->assign['list'] = [];
        if (count($rows)) {
            foreach ($rows as $row) {
                $row = htmlspecialchars_array($row);
                $row['mapURL']  = url([ADMIN, 'profil', 'googlemap', $row['id'], date('Y-m-d', strtotime($row['jam_datang']))]);
                $this->assign['list'][] = $row;
            }
        }

        return $this->draw('presensi.html', ['presensi' => $this->assign]);
    }

    public function getGanti_Pass()
    {
        $this->_addHeaderFiles();
        $username = $this->core->getUserInfo('username', null, true);
        $this->assign['username'] = $username;
        $this->assign['title'] = 'Ganti Password';

        return $this->draw('ganti_pass.html', ['ganti_pass' => $this->assign]);
    }

    public function postGanti_Save($id = null)
    {
        $errors = 0;

        $row_user = $this->db('mlite_users')->where('id', $this->core->getUserInfo('id'))->oneArray();

        // location to redirect
        if (!$id) {
            $location = url([ADMIN, 'profil', 'ganti_pass']);
        } else {
            $location = url([ADMIN, 'profil', 'ganti_pass', $id]);
        }

        // check if required fields are empty
        if (checkEmptyFields(['pass_lama', 'pass_baru'], $_POST)) {
            $this->notify('failure', 'Isian kosong');
            redirect($location, $_POST);
        }

        // check if password is longer than 5 characters
        if ($_POST['pass_baru'] == $_POST['pass_lama']) {
            $errors++;
            $this->notify('failure', 'Kata kunci sama');
        }

        // CREATE / EDIT
        if (!$errors) {
            unset($_POST['save']);

            if ($row_user && password_verify(trim($_POST['pass_lama']), $row_user['password'])) {
                $password = password_hash($_POST['pass_baru'], PASSWORD_BCRYPT);
                $query = $this->db('mlite_users')->where('id', $this->core->getUserInfo('id'))->save(['password' => $password]);
            }

            if ($query) {
                $this->notify('success', 'Simpan sukses');
            } else {
                $this->notify('failure', 'Kata kunci lama salah');
            }

            redirect($location);
        }

        redirect($location, $_POST);
    }

    public function postBridgingBkd()
    {
        $jadwal = 0;
        $jml_pot_tl1 = 0;
        $jml_pot_tl2 = 0;
        $jml_pot_tl3 = 0;
        $jml_pot_tl4 = 0;
        $jml_pot_psw1 = 0;
        $jml_pot_psw2 = 0;
        $jml_pot_psw3 = 0;
        $jml_pot_psw4 = 0;
        $year = date('Y');
        $biodata = $this->db('pegawai')->select(['id' => 'id', 'nama' => 'nama', 'nip' => 'nik', 'status' => 'stts_kerja'])->where('nik', $_POST['nik'])->oneArray();
        $day = cal_days_in_month(CAL_GREGORIAN, $_POST['bulan'], $year);
        for ($i = 1; $i <= $day; $i++) {
            $jad = $this->db('jadwal_pegawai')->select('h' . $i)->where('id', $biodata['id'])->where('tahun', $year)->where('bulan', $_POST['bulan'])->oneArray();
            if ($jad['h' . $i] != "") {
                $jadwal = $jadwal + 1;
            }
        }
        // $absen = $this->db('rekap_presensi')->where('id', $biodata['id'])->where('jam_datang', 'LIKE', $year . '-' . $_POST['bulan'] . '%')->toArray();
        $jlh = count($_POST['shift']);

        // $no = 1;
        for ($i=0; $i < count($_POST['shift']); $i++) {
            $jamMasukShift = $this->db('jam_jaga')->where('shift',$_POST['shift'][$i])->oneArray();
            if (strtotime($_POST['jam_datang'][$i]) > strtotime(substr($_POST['jam_datang'][$i],0,10) .' '. $jamMasukShift['jam_masuk'])) {
                if((strtotime($_POST['jam_datang'][$i]) - strtotime(substr($_POST['jam_datang'][$i],0,10) .' '. $jamMasukShift['jam_masuk'])) > (10 * 60) && (strtotime($_POST['jam_datang'][$i]) - strtotime(substr($_POST['jam_datang'][$i],0,10) .' '. $jamMasukShift['jam_masuk'])) < (31 * 60)){
                    $jml_pot_tl1 = $jml_pot_tl1 + 1;
                }
                if((strtotime($_POST['jam_datang'][$i]) - strtotime(substr($_POST['jam_datang'][$i],0,10) .' '. $jamMasukShift['jam_masuk'])) > (30 * 60) && (strtotime($_POST['jam_datang'][$i]) - strtotime(substr($_POST['jam_datang'][$i],0,10) .' '. $jamMasukShift['jam_masuk'])) < (61 * 60)){
                    $jml_pot_tl2 = $jml_pot_tl2 + 1;
                }
                if((strtotime($_POST['jam_datang'][$i]) - strtotime(substr($_POST['jam_datang'][$i],0,10) .' '. $jamMasukShift['jam_masuk'])) > (60 * 60) && (strtotime($_POST['jam_datang'][$i]) - strtotime(substr($_POST['jam_datang'][$i],0,10) .' '. $jamMasukShift['jam_masuk'])) < (91 * 60)){
                    $jml_pot_tl3 = $jml_pot_tl3 + 1;
                }
                if((strtotime($_POST['jam_datang'][$i]) - strtotime(substr($_POST['jam_datang'][$i],0,10) .' '. $jamMasukShift['jam_masuk'])) > (90 * 60)){
                    $jml_pot_tl4 = $jml_pot_tl4 + 1;
                }
            }
            if (strtotime($_POST['jam_pulang'][$i]) < strtotime(substr($_POST['jam_pulang'][$i],0,10) .' '. $jamMasukShift['jam_pulang'])) {
                if((strtotime(substr($_POST['jam_pulang'][$i],0,10) .' '. $jamMasukShift['jam_pulang']) - strtotime($_POST['jam_pulang'][$i])) > (10 * 60) && (strtotime(substr($_POST['jam_pulang'][$i],0,10) .' '. $jamMasukShift['jam_pulang']) - strtotime($_POST['jam_pulang'][$i])) < (31 * 60)){
                    $jml_pot_psw1 = $jml_pot_psw1 + 1;
                }
                if((strtotime(substr($_POST['jam_pulang'][$i],0,10) .' '. $jamMasukShift['jam_pulang']) - strtotime($_POST['jam_pulang'][$i])) > (30 * 60) && (strtotime(substr($_POST['jam_pulang'][$i],0,10) .' '. $jamMasukShift['jam_pulang']) - strtotime($_POST['jam_pulang'][$i])) < (61 * 60)){
                    $jml_pot_psw2 = $jml_pot_psw2 + 1;
                }
                if((strtotime(substr($_POST['jam_pulang'][$i],0,10) .' '. $jamMasukShift['jam_pulang']) - strtotime($_POST['jam_pulang'][$i])) > (60 * 60) && (strtotime(substr($_POST['jam_pulang'][$i],0,10) .' '. $jamMasukShift['jam_pulang']) - strtotime($_POST['jam_pulang'][$i])) < (91 * 60)){
                    $jml_pot_psw3 = $jml_pot_psw3 + 1;
                }
                if((strtotime(substr($_POST['jam_pulang'][$i],0,10) .' '. $jamMasukShift['jam_pulang']) - strtotime($_POST['jam_pulang'][$i])) > (90 * 60)){
                    $jml_pot_psw4 = $jml_pot_psw4 + 1;
                }
            }
        }

        $jml_pot_tl1 = $jml_pot_tl1 * (0.5 / 100);
        $jml_pot_tl2 = $jml_pot_tl2 * (1 / 100);
        $jml_pot_tl3 = $jml_pot_tl3 * (1.25 / 100);
        $jml_pot_tl4 = $jml_pot_tl4 * (1.5 / 100);
        $jml_pot_terlambat = $jml_pot_tl1 + $jml_pot_tl2 + $jml_pot_tl3 + $jml_pot_tl4;

        $jml_pot_psw1 = $jml_pot_psw1 * (0.5 / 100);
        $jml_pot_psw2 = $jml_pot_psw2 * (1 / 100);
        $jml_pot_psw3 = $jml_pot_psw3 * (1.25 / 100);
        $jml_pot_psw4 = $jml_pot_psw4 * (1.5 / 100);
        $jml_pot_pulang = $jml_pot_psw1 + $jml_pot_psw2 + $jml_pot_psw3 + $jml_pot_psw4;

        $date = new \DateTime('now');
        $date->modify('last day of this month');
        $cekTanggal = $date->format('Y-m-d');
        $cekMalam = $this->db('temporary_presensi')->where('id',$biodata['id'])->like('jam_datang','%'.$cekTanggal.'%')->like('shift','%malam%')->oneArray();
        if ($cekMalam) {
            $jlh = $jlh + 1;
        }

        $cekBkd = $this->db('bridging_bkd_presensi')->where('id',$biodata['id'])->where('bulan',$_POST['bulan'])->where('tahun',$year)->oneArray();
        if (!$cekBkd) {
            $query = $this->db('bridging_bkd_presensi')->save([
                'id' => $biodata['id'],
                'nama' => $biodata['nama'],
                'nip' => $biodata['nip'],
                'tahun' => $year,
                'bulan' => $_POST['bulan'],
                'jumlah_kehadiran' => $jlh,
                'jumlah_hari_kerja' => $jadwal,
                'persentase_hari_kerja' => '0.33',
                'jml_pot_keterlambatan' => $jml_pot_terlambat,
                'jml_pot_pulang_lebih_awal' => $jml_pot_pulang,
                'status' => $biodata['status'],
            ]);
        }else{
            $query = $this->db('bridging_bkd_presensi')->where('id',$biodata['id'])->where('bulan',$_POST['bulan'])->where('tahun',$year)->update([
                'jumlah_kehadiran' => $jlh,
                'jumlah_hari_kerja' => $jadwal,
                'persentase_hari_kerja' => '0.33',
                'jml_pot_keterlambatan' => $jml_pot_terlambat,
                'jml_pot_pulang_lebih_awal' => $jml_pot_pulang,
            ]);
        }
        // print_r($query);
        if ($query) {
            echo 'Sukses';
            $this->notify('success', 'Simpan Sukses');
        } else {
            $this->notify('failure', 'Gagal Simpan');
        }
        exit();
    }

    public function getCuti()
    {
        $this->_addHeaderFiles();
        $this->assign['title'] = 'Pengajuan Izin';
        $this->assign['nik'] = $this->core->getUserInfo('username', null, true);
        $this->assign['pilihCuti'] = array('0'=>'-- Pilih Izin --','1' => 'Cuti Tahunan', '2'=>'Cuti Besar', '3'=>'Cuti Sakit', '4'=>'Cuti Melahirkan', '5'=>'Cuti Karena Alasan Penting', '6'=>'Cuti Di Luar Tanggungan Negara', '7'=>'Izin');
        return $this->draw('cuti.html',['cuti' => $this->assign]);
    }

    public function postSimpanCuti()
    {
        $numberDays = '';
        $kodeSurat = '';
        $noSurat = '01';
        $noCuti = '';

        $tanggalAwal = strtotime($_POST['tanggal_awal']);
        $tanggalAkhir = strtotime($_POST['tanggal_akhir']);
        $timeDiff = abs($tanggalAkhir - $tanggalAwal);
        $numberDays = $timeDiff/86400;
        $numberDays = $numberDays + 1;

        switch ($_POST['jenis_cuti']) {
            case '1':
                $kodeSurat = '851';
                $noCuti = $kodeSurat.'/'.$noSurat.'/'.'RSUD-UMPEG'.'/'.date('Y');
                break;
            case '5':
                $kodeSurat = '850';
                $noCuti = $kodeSurat.'/'.$noSurat.'/'.'RSUD-UMPEG'.'/'.date('Y');
                break;
            case '4':
                $kodeSurat = '854';
                $noCuti = $kodeSurat.'/'.$noSurat.'/'.'RSUD-UMPEG'.'/'.date('Y');
                break;

            default:
                $noCuti = '';
                break;
        }

        $simpanIzinCuti = [
            'nip' => $_POST['nip'],
            'jenis_cuti' => $_POST['jenis_cuti'],
            'alasan' => $_POST['alasan'],
            'no_telp' => $_POST['telp'],
            'lama' => $numberDays,
            'tanggal_awal' => $_POST['tanggal_awal'],
            'tanggal_akhir' => $_POST['tanggal_akhir'],
            'alamat' => $_POST['alamat'],
            'tgl_surat' => date('Y-m-d'),
            'no_surat' => $noCuti,
            'status' => '',
            'created_at' => date('Y-m-d'),
            'updated_at' => ''
        ];

        echo json_encode($simpanIzinCuti);
    }

    public function getJavascript()
    {
        header('Content-type: text/javascript');
        echo $this->draw(MODULES . '/profil/js/admin/app.js');
        exit();
    }

    private function _addHeaderFiles()
    {
        // CSS
        //$this->core->addCSS(url('assets/css/jquery-ui.css'));
        $this->core->addCSS(url('plugins/profil/css/admin/timeline.min.css'));
        $this->core->addCSS(url('assets/jscripts/lightbox/lightbox.min.css'));

        // JS
        //$this->core->addJS(url('assets/jscripts/jquery-ui.js'), 'footer');
        $this->core->addJs(url('plugins/profil/js/admin/timeline.min.js'), 'footer');
        $this->core->addJS(url('assets/jscripts/lightbox/lightbox.min.js'), 'footer');

        $this->core->addCSS(url('assets/css/bootstrap-datetimepicker.css'));
        $this->core->addJS(url('assets/jscripts/moment-with-locales.js'));
        $this->core->addJS(url('assets/jscripts/bootstrap-datetimepicker.js'));

        // MODULE SCRIPTS
        $this->core->addJS(url([ADMIN, 'profil', 'javascript']), 'footer');
    }
}
