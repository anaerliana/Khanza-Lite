<?php

namespace Plugins\Kepegawaian;

use Systems\AdminModule;
use Systems\Lib\Fpdf\PDF_MC_Table;

class Admin extends AdminModule
{
    public function navigation()
    {
        return [
            'Kelola' => 'manage',
            'Data Pegawai PNS' => 'index',
            'Data Pegawai Kontrak' => 'indexkontrak',
            'Data Pegawai Tidak Aktif' => 'indexnon',
            'Tambah Baru' => 'add',
            //'Master Pegawai' => 'master',
        ];
    }

    public function getManage()
    {
        $this->_addHeaderFiles();
        $this->core->addCSS(url(MODULES.'/manajemen/css/admin/style.css'));
        $this->core->addJS(url(BASE_DIR.'/assets/jscripts/Chart.bundle.min.js'));
        // $this->core->addJS(url([ADMIN, 'kepegawaian', 'jschart']), 'footer');
        $sub_modules = [
            ['name' => 'Data Pegawai', 'url' => url([ADMIN, 'kepegawaian', 'index']), 'icon' => 'group', 'desc' => 'Data Pegawai'],
            ['name' => 'Data Pegawai Kontrak', 'url' => url([ADMIN, 'kepegawaian', 'indexkontrak']), 'icon' => 'group', 'desc' => 'Data Pegawai Kontrak'],
            ['name' => 'Data Pegawai Tidak Aktif', 'url' => url([ADMIN, 'kepegawaian', 'indexnon']), 'icon' => 'user-times', 'desc' => 'Data Pegawai Tidak Aktif'],
            ['name' => 'Tambah Pegawai', 'url' => url([ADMIN, 'kepegawaian', 'add']), 'icon' => 'user-plus', 'desc' => 'Tambah Data Pegawai'],
            ['name' => 'Data Izin / Cuti', 'url' => url([ADMIN, 'kepegawaian', 'cuti']), 'icon' => 'envelope', 'desc' => 'Daftar Izin / Cuti Pegawai'],
            ['name' => 'Ekspor Laporan', 'url' => url([ADMIN, 'kepegawaian', 'lap']), 'icon' => 'file', 'desc' => 'Ekspor Laporan Pegawai'],
            //['name' => 'Master Kepegawaian', 'url' => url([ADMIN, 'kepegawaian', 'master']), 'icon' => 'group', 'desc' => 'Master data Kepegawaian'],
        ];
        $stats['KunjunganTahunChart'] = $this->KunjunganTahunChart();
        $stats['KunjunganChart'] = $this->KunjunganChart();
        $stats['RanapTahunChart'] = $this->RanapTahunChart();
        $stats['RujukTahunChart'] = $this->RujukTahunChart();
        $this->assign['pns'] = $this->db('pegawai')->where('stts_aktif','AKTIF')->where('stts_kerja','PNS')->toArray();
        $this->assign['pr'] = $this->db('pegawai')->where('stts_aktif','AKTIF')->where('stts_kerja','PNS')->where('jk','Wanita')->toArray();
        $this->assign['lk'] = $this->db('pegawai')->where('stts_aktif','AKTIF')->where('stts_kerja','PNS')->where('jk','Pria')->toArray();
        $this->assign['kontrak'] = $this->db('pegawai')->where('stts_aktif','AKTIF')->where('stts_kerja','FT')->toArray();
        return $this->draw('manage.html', ['sub_modules' => $sub_modules , 'list' => $this->assign , 'stats' => $stats]);
    }

    public function postSearch(){
        if(isset($_POST["query"])){
            $output = '';
            $key = "%".$_POST["query"]."%";
            if (is_numeric($_POST["query"]) == 1) {
                # code...
                $rows = $this->db('pegawai')->like('nik', $key)->limit(10)->toArray();
            } else {
                # code...
                $rows = $this->db('pegawai')->like('nama', $key)->limit(10)->toArray();
            }
            $output = '';
            if(count($rows)){
              foreach ($rows as $row) {
                $output .= '<li class="list-group-item link-class">'.$row["nama"].'</li>';
              }
            }
            echo $output;
        }
        exit();
    }

    public function postSearchBy(){
        $key = "%".$_POST["nama"]."%";
        $rows = $this->db('pegawai')->like('nama', $key)->oneArray();
        $id = $rows['id'];
        redirect(url([ADMIN, 'profil', 'biodata', $id]));
    }

    public function getIndex($page = 1)
    {

        $this->_addHeaderFiles();

        $rows = $this->db('pegawai')->where('stts_aktif','AKTIF')->where('stts_kerja','PNS')->toArray();

        $this->assign['list'] = [];
        if (count($rows)) {
            foreach ($rows as $row) {
                $row = htmlspecialchars_array($row);
                $row['editURL'] = url([ADMIN, 'profil', 'biodata', $row['id']]);
                $row['viewURL'] = url([ADMIN, 'kepegawaian', 'view', $row['id']]);
                $row['tgl_lahir'] = dateIndonesia($row['tgl_lahir']);
                $this->assign['list'][] = $row;
            }
        }

        $this->assign['getStatus'] = isset($_GET['status']);
        $this->assign['printURL'] = url([ADMIN, 'kepegawaian', 'print']);

        return $this->draw('index.html', ['pegawai' => $this->assign]);
    }

    public function getIndexKontrak($page = 1)
    {

        $this->_addHeaderFiles();

        $rows = $this->db('pegawai')->where('stts_aktif','AKTIF')->where('stts_kerja','FT')->toArray();

        $this->assign['list'] = [];
        if (count($rows)) {
            foreach ($rows as $row) {
                $row = htmlspecialchars_array($row);
                $row['editURL'] = url([ADMIN, 'profil', 'biodata', $row['id']]);
                $row['viewURL'] = url([ADMIN, 'kepegawaian', 'view', $row['id']]);
                $row['tgl_lahir'] = dateIndonesia($row['tgl_lahir']);
                $this->assign['list'][] = $row;
            }
        }

        $this->assign['getStatus'] = isset($_GET['status']);
        $this->assign['printURL'] = url([ADMIN, 'kepegawaian', 'print']);

        return $this->draw('index.html', ['pegawai' => $this->assign]);
    }

    public function getIndexNon($page = 1)
    {

        $this->_addHeaderFiles();

        $rows = $this->db('pegawai')->where('stts_aktif','!=','AKTIF')->toArray();

        $this->assign['list'] = [];
        if (count($rows)) {
            foreach ($rows as $row) {
                $row = htmlspecialchars_array($row);
                $row['editURL'] = url([ADMIN, 'kepegawaian', 'edit', $row['id']]);
                $row['viewURL'] = url([ADMIN, 'kepegawaian', 'view', $row['id']]);
                $row['tgl_lahir'] = dateIndonesia($row['tgl_lahir']);
                $this->assign['list'][] = $row;
            }
        }
        $this->assign['printURL'] = url([ADMIN, 'kepegawaian', 'print']);

        return $this->draw('index.html', ['pegawai' => $this->assign]);
    }

    public function getAdd()
    {
        $this->_addHeaderFiles();
        if (!empty($redirectData = getRedirectData())) {
            $this->assign['form'] = filter_var_array($redirectData, FILTER_SANITIZE_STRING);
        } else {
            $this->assign['form'] = [
              'nik' => '',
              'nama' => '',
              'jk' => '',
              'jbtn' => '-',
              'jnj_jabatan' => '',
              'kode_kelompok' => '',
              'kode_resiko' => '',
              'kode_emergency' => '',
              'departemen' => '',
              'bidang' => '',
              'stts_wp' => '',
              'stts_kerja' => '',
              'npwp' => '0',
              'pendidikan' => '',
              'gapok' => '0',
              'tmp_lahir' => '',
              'tgl_lahir' => '',
              'alamat' => '',
              'kota' => '',
              'mulai_kerja' => date('Y-m-d'),
              'ms_kerja' => '',
              'indexins' => '',
              'bpd' => '',
              'rekening' => '0',
              'stts_aktif' => '',
              'wajibmasuk' => '0',
              'pengurang' => '0',
              'indek' => '0',
              'mulai_kontrak' => date('Y-m-d'),
              'cuti_diambil' => '0',
              'dankes' => '0',
              'photo' => '',
              'no_ktp' => '0',
              'qrCode' => ''
            ];
        }

        $this->assign['title'] = 'Tambah Pegawai';
        $this->assign['jk'] = ['Pria','Wanita'];
        $this->assign['ms_kerja'] = ['<1','PT','FT>1'];
        $this->assign['stts_aktif'] = ['AKTIF','CUTI','KELUAR','TENAGA LUAR'];
        $this->assign['jnj_jabatan'] = $this->db('jnj_jabatan')->toArray();
        $this->assign['kelompok_jabatan'] = $this->db('kelompok_jabatan')->toArray();
        $this->assign['resiko_kerja'] = $this->db('resiko_kerja')->toArray();
        $this->assign['departemen'] = $this->db('departemen')->toArray();
        $this->assign['bidang'] = $this->db('bidang')->toArray();
        $this->assign['stts_wp'] = $this->db('stts_wp')->toArray();
        $this->assign['stts_kerja'] = $this->db('stts_kerja')->toArray();
        $this->assign['pendidikan'] = $this->db('pendidikan')->toArray();
        $this->assign['bank'] = $this->db('bank')->toArray();
        $this->assign['emergency_index'] = $this->db('emergency_index')->toArray();

        $this->assign['fotoURL'] = url(MODULES.'/kepegawaian/img/default.png');

        return $this->draw('form.html', ['pegawai' => $this->assign]);
    }

    public function getEdit($id)
    {
        $this->_addHeaderFiles();
        $row = $this->db('pegawai')->oneArray($id);
        if (!empty($row)) {
            $this->assign['form'] = $row;
            $this->assign['title'] = 'Edit Pegawai';

            $this->assign['jk'] = ['Pria','Wanita'];
            $this->assign['ms_kerja'] = ['<1','PT','FT>1'];
            $this->assign['stts_aktif'] = ['AKTIF','CUTI','KELUAR','TENAGA LUAR'];
            $this->assign['jnj_jabatan'] = $this->db('jnj_jabatan')->toArray();
            $this->assign['kelompok_jabatan'] = $this->db('kelompok_jabatan')->toArray();
            $this->assign['resiko_kerja'] = $this->db('resiko_kerja')->toArray();
            $this->assign['departemen'] = $this->db('departemen')->toArray();
            $this->assign['bidang'] = $this->db('bidang')->toArray();
            $this->assign['stts_wp'] = $this->db('stts_wp')->toArray();
            $this->assign['stts_kerja'] = $this->db('stts_kerja')->toArray();
            $this->assign['pendidikan'] = $this->db('pendidikan')->toArray();
            $this->assign['bank'] = $this->db('bank')->toArray();
            $this->assign['emergency_index'] = $this->db('emergency_index')->toArray();

            $this->assign['fotoURL'] = WEBAPPS_URL.'/penggajian/'.$row['photo'];

            return $this->draw('form.html', ['pegawai' => $this->assign]);
        } else {
            redirect(url([ADMIN, 'kepegawaian', 'index']));
        }
    }

    public function getView($id)
    {
        $this->_addHeaderFiles();
        $row = $this->db('pegawai')->oneArray($id);

        if (!empty($row)) {
            $this->assign['pegawai'] = $row;
            $this->assign['petugas'] = $this->db('petugas')->where('nip',$row['nik'])->oneArray();
            $this->assign['stts_wp'] = $this->db('stts_wp')->where('stts',$row['stts_wp'])->oneArray();
            $this->assign['manageURL'] = url([ADMIN, 'kepegawaian', 'index']);

            $this->assign['fotoURL'] = url(MODULES.'/kepegawaian/img/default.png');
            if(!empty($row['photo'])) {
              $this->assign['fotoURL'] = WEBAPPS_URL.'/penggajian/'.$row['photo'];
            }

            return $this->draw('view.html', ['kepegawaian' => $this->assign]);
        } else {
            redirect(url([ADMIN, 'kepegawaian', 'index']));
        }
    }

    public function postSave($id = null)
    {
        $errors = 0;

        if (!$id) {
            $location = url([ADMIN, 'kepegawaian', 'add']);
        } else {
            $location = url([ADMIN, 'kepegawaian', 'edit', $id]);
        }

        if (checkEmptyFields(['nik', 'nama'], $_POST)) {
            $this->notify('failure', 'Isian kosong');
            redirect($location, $_POST);
        }

        if (!$errors) {
            unset($_POST['save']);

            if (($photo = isset_or($_FILES['photo']['tmp_name'], false)) || !$id) {
                $img = new \Systems\Lib\Image;

                if (empty($photo) && !$id) {
                    $photo = MODULES.'/kepegawaian/img/default.png';
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

                    $_POST['photo'] = "pages/pegawai/photo/".$pegawai['nik'].".".$img->getInfos('type');
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
                        unlink(WEBAPPS_PATH."/penggajian/".$pegawai['photo']);
                    }

                    $img->save(WEBAPPS_PATH."/penggajian/".$_POST['photo']);
                }

                $this->notify('success', 'Simpan sukes');
            } else {
                $this->notify('failure', 'Simpan gagal');
            }

            redirect($location);
        }

        redirect($location, $_POST);
    }

    public function getLap($page = 1)
    {

        $this->_addHeaderFiles();

        $this->core->addCSS(url('https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css'));
        $this->core->addJS(url('https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js'), 'footer');
        $this->core->addJS(url('https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js'), 'footer');
        $this->core->addJS(url('https://cdn.datatables.net/buttons/1.3.1/js/buttons.html5.min.js'), 'footer');

        $rows = $this->db('pegawai')->select(['NIP' => 'nik','NAMA' => 'nama','Bidang' => 'bidang'])->where('stts_aktif','AKTIF')->toArray();

        $this->assign['list'] = [];
        if (count($rows)) {
            foreach ($rows as $row) {
                $row = htmlspecialchars_array($row);
                $nipk_baru = $row['NIP'];
                $pdn = $pdnnot = $pdnnul = $sern = $sernnot = $sernnul = $dtn = $dtnnot = $dtnnul = $smn = $smnnot = $smnnul = 0;
                $pd = $this->db('simpeg_rpendum')->select(['countNo' => 'COUNT(NIP)'])->where('simpeg_rpendum.NIP',$nipk_baru)->group('NIP')->oneArray();
                $pdnot = $this->db('simpeg_rpendum')->select(['countNo' => 'COUNT(NIP)'])->where('NIP',$nipk_baru)->isNotNull('nm_file')->group('NIP')->oneArray();
                $pdnul = $this->db('simpeg_rpendum')->select(['countNo' => 'COUNT(NIP)'])->where('NIP',$nipk_baru)->isNull('nm_file')->group('NIP')->oneArray();
                $pg = $this->db('simpeg_rpangkat')->select(['countNo' => 'COUNT(NIP)'])->where('NIP',$nipk_baru)->group('NIP')->oneArray();
                $pgnot = $this->db('simpeg_rpangkat')->select(['countNo' => 'COUNT(NIP)'])->where('NIP',$nipk_baru)->isNotNull('nm_file')->group('NIP')->oneArray();
                $pgnul = $this->db('simpeg_rpangkat')->select(['countNo' => 'COUNT(NIP)'])->where('NIP',$nipk_baru)->isNull('nm_file')->group('NIP')->oneArray();
                $jb = $this->db('simpeg_rjabatan')->select(['countNo' => 'COUNT(NIP)'])->where('NIP',$nipk_baru)->group('NIP')->oneArray();
                $jbnot = $this->db('simpeg_rjabatan')->select(['countNo' => 'COUNT(NIP)'])->where('NIP',$nipk_baru)->isNotNull('nm_file')->group('NIP')->oneArray();
                $jbnul = $this->db('simpeg_rjabatan')->select(['countNo' => 'COUNT(NIP)'])->where('NIP',$nipk_baru)->isNull('nm_file')->group('NIP')->oneArray();
                $skp = $this->db('simpeg_rdppp')->select(['countNo' => 'COUNT(NIP)'])->where('NIP',$nipk_baru)->group('NIP')->oneArray();
                $skpnot = $this->db('simpeg_rdppp')->select(['countNo' => 'COUNT(NIP)'])->where('NIP',$nipk_baru)->isNotNull('nm_file')->group('NIP')->oneArray();
                $skpnul = $this->db('simpeg_rdppp')->select(['countNo' => 'COUNT(NIP)'])->where('NIP',$nipk_baru)->isNull('nm_file')->group('NIP')->oneArray();
                $gk = $this->db('simpeg_gkkhir')->select(['countNo' => 'COUNT(NIP)'])->where('NIP',$nipk_baru)->group('NIP')->oneArray();
                $gknot = $this->db('simpeg_gkkhir')->select(['countNo' => 'COUNT(NIP)'])->where('NIP',$nipk_baru)->isNotNull('nm_file')->group('NIP')->oneArray();
                $gknul = $this->db('simpeg_gkkhir')->select(['countNo' => 'COUNT(NIP)'])->where('NIP',$nipk_baru)->isNull('nm_file')->group('NIP')->oneArray();
                $akr = $this->db('simpeg_rjabfung')->select(['countNo' => 'COUNT(NIP)'])->where('NIP',$nipk_baru)->group('NIP')->oneArray();
                $akrnot = $this->db('simpeg_rjabfung')->select(['countNo' => 'COUNT(NIP)'])->where('NIP',$nipk_baru)->isNotNull('nm_file')->group('NIP')->oneArray();
                $akrnul = $this->db('simpeg_rjabfung')->select(['countNo' => 'COUNT(NIP)'])->where('NIP',$nipk_baru)->isNull('nm_file')->group('NIP')->oneArray();
                $ser = $this->db('simpeg_rsertifikasi')->select(['countNo' => 'COUNT(nip)'])->where('nip',$nipk_baru)->group('nip')->oneArray();
                $sernot = $this->db('simpeg_rsertifikasi')->select(['countNo' => 'COUNT(nip)'])->where('nip',$nipk_baru)->isNotNull('nm_file')->group('nip')->oneArray();
                $sernul = $this->db('simpeg_rsertifikasi')->select(['countNo' => 'COUNT(nip)'])->where('nip',$nipk_baru)->isNull('nm_file')->group('nip')->oneArray();
                $dns = $this->db('simpeg_rdiknstr')->select(['countNo' => 'COUNT(NIP)'])->where('NIP',$nipk_baru)->group('NIP')->oneArray();
                $dnsnot = $this->db('simpeg_rdiknstr')->select(['countNo' => 'COUNT(NIP)'])->where('NIP',$nipk_baru)->isNotNull('nm_file')->group('NIP')->oneArray();
                $dnsnul = $this->db('simpeg_rdiknstr')->select(['countNo' => 'COUNT(NIP)'])->where('NIP',$nipk_baru)->isNull('nm_file')->group('NIP')->oneArray();
                $ds = $this->db('simpeg_rdikstr')->select(['countNo' => 'COUNT(NIP)'])->where('NIP',$nipk_baru)->group('NIP')->oneArray();
                $dsnot = $this->db('simpeg_rdikstr')->select(['countNo' => 'COUNT(NIP)'])->where('NIP',$nipk_baru)->isNotNull('nm_file')->group('NIP')->oneArray();
                $dsnul = $this->db('simpeg_rdikstr')->select(['countNo' => 'COUNT(NIP)'])->where('NIP',$nipk_baru)->isNull('nm_file')->group('NIP')->oneArray();
                $df = $this->db('simpeg_rdikfung')->select(['countNo' => 'COUNT(NIP)'])->where('NIP',$nipk_baru)->group('NIP')->oneArray();
                $dfnot = $this->db('simpeg_rdikfung')->select(['countNo' => 'COUNT(NIP)'])->where('NIP',$nipk_baru)->isNotNull('nm_file')->group('NIP')->oneArray();
                $dfnul = $this->db('simpeg_rdikfung')->select(['countNo' => 'COUNT(NIP)'])->where('NIP',$nipk_baru)->isNull('nm_file')->group('NIP')->oneArray();
                $dt = $this->db('simpeg_rdiktek')->select(['countNo' => 'COUNT(NIP)'])->where('NIP',$nipk_baru)->group('NIP')->oneArray();
                $dtnot = $this->db('simpeg_rdiktek')->select(['countNo' => 'COUNT(NIP)'])->where('NIP',$nipk_baru)->isNotNull('nm_file')->group('NIP')->oneArray();
                $dtnul = $this->db('simpeg_rdiktek')->select(['countNo' => 'COUNT(NIP)'])->where('NIP',$nipk_baru)->isNull('nm_file')->group('NIP')->oneArray();
                $sm = $this->db('simpeg_rseminar')->select(['countNo' => 'COUNT(NIP)'])->where('NIP',$nipk_baru)->group('NIP')->oneArray();
                $smnot = $this->db('simpeg_rseminar')->select(['countNo' => 'COUNT(NIP)'])->where('NIP',$nipk_baru)->isNotNull('nm_file')->group('NIP')->oneArray();
                $smnul = $this->db('simpeg_rseminar')->select(['countNo' => 'COUNT(NIP)'])->where('NIP',$nipk_baru)->isNull('nm_file')->group('NIP')->oneArray();
                $bks = $this->db('simpeg_bkstambah')->select(['countNo' => 'COUNT(nip)'])->where('nip',$nipk_baru)->group('nip')->oneArray();
                $nipkBaru = $this->db('pegawai_mapping')->select('nipk')->where('nipk_baru',$row['NIP'])->oneArray();
                if ($nipkBaru) {
                    $username = $nipkBaru['nipk'];
                    $pdn = $this->db('simpeg_rpendum')->select(['countNo' => 'COUNT(NIP)'])->where('NIP',$username)->group('NIP')->oneArray();
                    $pdnnot = $this->db('simpeg_rpendum')->select(['countNo' => 'COUNT(NIP)'])->where('NIP',$username)->isNotNull('nm_file')->group('NIP')->oneArray();
                    $pdnnul = $this->db('simpeg_rpendum')->select(['countNo' => 'COUNT(NIP)'])->where('NIP',$username)->isNull('nm_file')->group('NIP')->oneArray();
                    $sern = $this->db('simpeg_rsertifikasi')->select(['countNo' => 'COUNT(nip)'])->where('nip',$username)->group('nip')->oneArray();
                    $sernnot = $this->db('simpeg_rsertifikasi')->select(['countNo' => 'COUNT(nip)'])->where('nip',$username)->isNotNull('nm_file')->group('nip')->oneArray();
                    $sernnul = $this->db('simpeg_rsertifikasi')->select(['countNo' => 'COUNT(nip)'])->where('nip',$username)->isNull('nm_file')->group('nip')->oneArray();
                    $dtn = $this->db('simpeg_rdiktek')->select(['countNo' => 'COUNT(NIP)'])->where('NIP',$username)->group('NIP')->oneArray();
                    $dtnnot = $this->db('simpeg_rdiktek')->select(['countNo' => 'COUNT(NIP)'])->where('NIP',$username)->isNotNull('nm_file')->group('NIP')->oneArray();
                    $dtnnul = $this->db('simpeg_rdiktek')->select(['countNo' => 'COUNT(NIP)'])->where('NIP',$username)->isNull('nm_file')->group('NIP')->oneArray();
                    $smn = $this->db('simpeg_rseminar')->select(['countNo' => 'COUNT(NIP)'])->where('NIP',$username)->group('NIP')->oneArray();
                    $smnnot = $this->db('simpeg_rseminar')->select(['countNo' => 'COUNT(NIP)'])->where('NIP',$username)->isNotNull('nm_file')->group('NIP')->oneArray();
                    $smnnul = $this->db('simpeg_rseminar')->select(['countNo' => 'COUNT(NIP)'])->where('NIP',$username)->isNull('nm_file')->group('NIP')->oneArray();
                }
                $row['pd'] = $pd['countNo'] + $pdn['countNo'] + $dns['countNo'] + $ds['countNo'] + $df['countNo'] + $dt['countNo'] + $dtn['countNo'] + $sm['countNo'] + $smn['countNo'];
                $row['pdnot'] = $pdnot['countNo'] + $pdnnot['countNo'] + $dnsnot['countNo'] + $dsnot['countNo'] + $dfnot['countNo'] + $dtnot['countNo'] + $dtnnot['countNo'] + $smnot['countNo'] + $smnnot['countNo'];
                $row['pdnul'] = $pdnul['countNo'] + $pdnnul['countNo'] + $dnsnul['countNo'] + $dsnul['countNo'] + $dfnul['countNo'] + $dtnul['countNo'] + $dtnnul['countNo'] + $smnul['countNo'] + $smnnul['countNo'];
                $row['pg'] = $pg['countNo'] + $jb['countNo'] + $skp['countNo'] + $gk['countNo'] + $akr['countNo'] + $ser['countNo'] + $sern['countNo'] + 0;
                $row['pgnot'] = $pgnot['countNo'] + $jbnot['countNo'] + $skpnot['countNo'] + $gknot['countNo'] + $akrnot['countNo'] + $sernot['countNo'] + $sernnot['countNo'] + 0;
                $row['pgnul'] = $pgnul['countNo'] + $jbnul['countNo'] + $skpnul['countNo'] + $gknul['countNo'] + $akrnul['countNo'] + $sernul['countNo'] + $sernnul['countNo'] + 0;
                $row['bks'] = $bks['countNo'] + 0;

                // $row['editURL'] = url([ADMIN, 'profil', 'biodata', $row['id']]);
                // $row['viewURL'] = url([ADMIN, 'kepegawaian', 'view', $row['id']]);
                // $row['tgl_lahir'] = dateIndonesia($row['tgl_lahir']);
                $this->assign['list'][] = $row;
            }
        }

        // $this->assign['getStatus'] = isset($_GET['status']);
        // $this->assign['printURL'] = url([ADMIN, 'kepegawaian', 'print']);

        return $this->draw('laporan.html', ['laporan' => $this->assign]);
    }

    public function getPrint()
    {
      $pasien = $this->db('pegawai')->toArray();
      $logo = $this->settings->get('settings.logo');

      $pdf = new PDF_MC_Table();
      $pdf->AddPage();
      $pdf->SetAutoPageBreak(true, 10);
      $pdf->SetTopMargin(10);
      $pdf->SetLeftMargin(10);
      $pdf->SetRightMargin(10);

      $pdf->Image('../'.$logo, 10, 8, '18', '18', 'png');
      $pdf->SetFont('Arial', '', 24);
      $pdf->Text(30, 16, $this->settings->get('settings.nama_instansi'));
      $pdf->SetFont('Arial', '', 10);
      $pdf->Text(30, 21, $this->settings->get('settings.alamat').' - '.$this->settings->get('settings.kota'));
      $pdf->Text(30, 25, $this->settings->get('settings.nomor_telepon').' - '.$this->settings->get('settings.email'));
      $pdf->Line(10, 30, 200, 30);
      $pdf->Line(10, 31, 200, 31);
      $pdf->Text(10, 40, 'DATA PEGAWAI');
      $pdf->Ln(34);
      $pdf->SetFont('Arial', '', 10);
      $pdf->SetWidths(array(50,70,25,25,20));
      $pdf->Row(array('Kode Pegawai','Nama Pegawai','Tempat Lahir', 'Tanggal Lahir', 'Status'));
      foreach ($pasien as $hasil) {
        $pdf->Row(array($hasil['nik'],$hasil['nama'],$hasil['tmp_lahir'],$hasil['tgl_lahir'],$hasil['stts_aktif']));
      }
      $pdf->Output('laporan_pegawai_'.date('Y-m-d').'.pdf','I');

    }

    public function RujukTahunChart()
    {

        $query = $this->db('pegawai')
            ->select([
              'count'       => 'COUNT(DISTINCT nik)',
              'label'       => 'pendidikan'
            ])
            ->where('stts_kerja', 'PNS')
            ->where('jk','Wanita')
            ->group('pendidikan');

            $data = $query->toArray();

            $return = [
                'labels'  => [],
                'visits'  => []
            ];
            foreach ($data as $value) {
                $return['labels'][] = $value['label'];
                $return['visits'][] = $value['count'];
            }

        return $return;
    }

    public function RanapTahunChart()
    {

        $query = $this->db('pegawai')
            ->select([
              'count'       => 'COUNT(DISTINCT nik)',
              'label'       => 'pendidikan'
            ])
            ->where('stts_kerja', 'PNS')
            ->where('jk','Pria')
            ->group('pendidikan');

            $data = $query->toArray();

            $return = [
                'labels'  => [],
                'visits'  => []
            ];
            foreach ($data as $value) {
                $return['labels'][] = $value['label'];
                $return['visits'][] = $value['count'];
            }

        return $return;
    }

    public function KunjunganTahunChart()
    {

        $query = $this->db('pegawai')
            ->select([
              'count'       => 'COUNT(DISTINCT nik)',
              'label'       => 'stts_aktif'
            ])
            ->where('stts_kerja', 'PNS')
            ->where('jk','Pria')
            ->group('stts_aktif');

            $data = $query->toArray();

            $return = [
                'labels'  => [],
                'visits'  => []
            ];
            foreach ($data as $value) {
                $return['labels'][] = $value['label'];
                $return['visits'][] = $value['count'];
            }

        return $return;
    }

    public function KunjunganChart()
    {

        $query = $this->db('pegawai')
            ->select([
              'count'       => 'COUNT(DISTINCT nik)',
              'label'       => 'stts_aktif'
            ])
            ->where('stts_kerja', 'PNS')
            ->where('jk','Wanita')
            ->group('stts_aktif');

            $data = $query->toArray();

            $return = [
                'labels'  => [],
                'visits'  => []
            ];
            foreach ($data as $value) {
                $return['labels'][] = $value['label'];
                $return['visits'][] = $value['count'];
            }

        return $return;
    }

    public function getCuti()
    {

        $this->_addHeaderFiles();

        $this->core->addCSS(url('https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css'));
        $this->core->addJS(url('https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js'), 'footer');
        $this->core->addJS(url('https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js'), 'footer');
        $this->core->addJS(url('https://cdn.datatables.net/buttons/1.3.1/js/buttons.html5.min.js'), 'footer');

        $rows = $this->db('izin_cuti')->toArray();

        $this->assign['list'] = [];
        if (count($rows)) {
            foreach ($rows as $row) {
                $row = htmlspecialchars_array($row);
                $checkData = $this->db('pegawai')->select('nama')->where('nik',$row['nip'])->oneArray();
                $row['nama'] = $checkData['nama'];
                $row['delURL']  = url([ADMIN, 'kepegawaian', 'delete', $row['id']]);
                $this->assign['list'][] = $row;
            }
        }
        $this->assign['listCuti'] = array('1' => 'Cuti Tahunan', '2'=>'Cuti Besar', '3'=>'Cuti Sakit', '4'=>'Cuti Melahirkan', '5'=>'Cuti Karena Alasan Penting', '6'=>'Cuti Di Luar Tanggungan Negara', '7'=>'Izin');
        $this->assign['getStatus'] = isset($_GET['status']);
        $this->assign['printURL'] = url([ADMIN, 'kepegawaian', 'print']);

        return $this->draw('index_cuti.html', ['cuti' => $this->assign]);
    }

    public function getTambahCuti()
    {
        $this->_addHeaderFiles();
        $this->_addInfoPegawai();
        $this->assign['pilihCuti'] = array('0'=>'-- Pilih Izin --','1' => 'Cuti Tahunan', '2'=>'Cuti Besar', '3'=>'Cuti Sakit', '4'=>'Cuti Melahirkan', '5'=>'Cuti Karena Alasan Penting', '6'=>'Cuti Di Luar Tanggungan Negara', '7'=>'Izin');
       
       return $this->draw('tambah_cuti.html', ['addcuti' => $this->assign]);
    }

    public function postTambahCuti()
    {
        $this->_addHeaderFiles();
        
        $numberDays = '';
        $kodeSurat = '';
        $noSurat = '';
        $noCuti = '';
        $cutiTahunan = 12;
        $sisaCuti = '';
        $location = url([ADMIN, 'kepegawaian', 'cuti']);

        $tanggalAwal = strtotime($_POST['tanggal_awal']);
        $tanggalAkhir = strtotime($_POST['tanggal_akhir']);
        $timeDiff = abs($tanggalAkhir - $tanggalAwal);
        $numberDays = $timeDiff/86400;
        $numberDays = $numberDays + 1;
        $tahun = date('Y',$tanggalAwal);

        $jenisCuti = $_POST['jenis_cuti'];
        $noSurat = $this->db()->pdo()->prepare("SELECT max(SUBSTRING(no_surat, 5, 2)) FROM izin_cuti WHERE jenis_cuti = '$jenisCuti'");
        $noSurat->execute();
        $noSurat = $noSurat->fetch();
        $noSurat = sprintf('%02s', ($noSurat[0] + 1));

        switch ($_POST['jenis_cuti']) {
            case '1':
                $kodeSurat = '851';
                $noCuti = $kodeSurat.'/'.$noSurat.'/'.'RSUD-UMPEG'.'/'.date('Y');
                $sisaCuti = $cutiTahunan - $numberDays;
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

        $lastId = $this->db('izin_cuti')->lastInsertId();
        $this->db('izin_cuti')->save([
            'id' => $lastId,
            'nip' => $_POST['nik'],
            'jenis_cuti' => $_POST['jenis_cuti'],
            'alasan' => $_POST['alasan'],
            'no_telp' => $_POST['telp'],
            'lama' => $numberDays,
            'sisa_cuti_tahunan' => $sisaCuti,
            'tahun' => $tahun,
            'tgl_buat' => $_POST['tanggal_buat'],
            'tgl_awal' => $_POST['tanggal_awal'],
            'tgl_akhir' => $_POST['tanggal_akhir'],
            'alamat' => $_POST['alamat'],
            'tgl_surat' => date('Y-m-d'),
            'no_surat' => $noCuti,
            'status' => 'Belum Disetujui',
            'created_at' => null,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        if (!$lastId) {
            $this->notify('success', 'Simpan sukses');
        } else {
            $this->notify('failure', 'Gagal Simpan');
        }
        redirect($location);
 
        exit();
    }

    private function _addInfoPegawai() {
        // get pegawai
        $rows = $this->db('pegawai')->where('stts_aktif', '!=', 'KELUAR')->toArray();

        if (count($rows)) {
          $this->assign['pegawai'] = [];
          foreach($rows as $row) {
              $this->assign['pegawai'][] = $row;
          }
        }
    }

    public function getEditCuti($id)
    {
        $this->_addHeaderFiles();
        $infopegawai = $this->db('pegawai')->where('stts_aktif', '!=', 'KELUAR')->toArray();
        $pegawai = $this->db('pegawai')->select('nama')->where('nik', $id)->oneArray();
        $cuti = $this->db('izin_cuti')->where('id', $id)->oneArray();
       // $pilihCuti = array('0'=>'-- Pilih Izin --','1' => 'Cuti Tahunan', '2'=>'Cuti Besar', '3'=>'Cuti Sakit', '4'=>'Cuti Melahirkan', '5'=>'Cuti Karena Alasan Penting', '6'=>'Cuti Di Luar Tanggungan Negara', '7'=>'Izin');
        $this->tpl->set('infopegawai', $infopegawai);
        $this->tpl->set('pegawai', $pegawai);
        $this->tpl->set('cuti', $cuti);
       // $this->tpl->set('pilihCuti', $pilihCuti);
      
        echo $this->tpl->draw(MODULES . '/kepegawaian/view/admin/editcuti.html', true);
        exit();
    }

    public function postEditCuti()
    {
       
        $numberDays = '';
        // $kodeSurat = '';
        // $noSurat = '';
       // $noCuti = '';
        $cutiTahunan = 12;
        $sisaCuti = '';
        $location = url([ADMIN, 'profil', 'cuti']);

        $tanggalAwal = strtotime($_POST['tanggal_awal']);
        $tanggalAkhir = strtotime($_POST['tanggal_akhir']);
        $timeDiff = abs($tanggalAkhir - $tanggalAwal);
        $numberDays = $timeDiff/86400;
        $numberDays = $numberDays + 1;
        $tahun = date('Y',$tanggalAwal);

        // switch ($_POST['jenis_cuti']) {
        //     case '1':
        //         $kodeSurat = '851';
        //         $noCuti = $kodeSurat.'/'.$noSurat.'/'.'RSUD-UMPEG'.'/'.date('Y');
        //         $sisaCuti = $cutiTahunan - $numberDays;
        //         break;
        //     case '5':
        //         $kodeSurat = '850';
        //         $noCuti = $kodeSurat.'/'.$noSurat.'/'.'RSUD-UMPEG'.'/'.date('Y');
        //         break;
        //     case '4':
        //         $kodeSurat = '854';
        //         $noCuti = $kodeSurat.'/'.$noSurat.'/'.'RSUD-UMPEG'.'/'.date('Y');
        //         break;

        //     default:
        //         $noCuti = '';
        //         break;
        // }

        $id = $_POST['id'];
        $errors = 0;
        $location = url([ADMIN, 'kepegawaian', 'cuti']);
              $query = $this->db('izin_cuti')
                ->where('id', $id)
                ->save([
                'nip' => $_POST['nik'],
                //'jenis_cuti' => $_POST['jenis_cuti'],
                'alasan' => $_POST['alasan'],
                'no_telp' => $_POST['telp'],
                'lama' => $numberDays,
                'sisa_cuti_tahunan' => $sisaCuti,
                'tahun' => $tahun,
                'tgl_buat' => $_POST['tanggal_buat'],
                'tgl_awal' => $_POST['tanggal_awal'],
                'tgl_akhir' => $_POST['tanggal_akhir'],
                'alamat' => $_POST['alamat'],
                'tgl_surat' => date('Y-m-d'),
                //'no_surat' => $noCuti,
                'created_at' => null,
                'updated_at' => date('Y-m-d H:i:s'),
                'status' => $_POST['status'],
                'keterangan' => $_POST['keterangan'],
                ]);
            if ($query) {
                $this->notify('success', 'Data Berhasil Update');
            } else {
                $this->notify('failure', 'Gagal Update');
            }
          redirect($location);
    }

    public function getDelete($id)
    {
            if ($this->db('izin_cuti')->delete($id)) {
                $this->notify('success', 'Data berhasil dihapus.');
            } else {
                $this->notify('failure', 'Gagal dihapus.');
            }
        redirect(url([ADMIN, 'kepegawaian', 'cuti']));
    }

  public function getCetakIzin($id)
  {
    $cuti_pegawai = $this->db('izin_cuti')
    ->select([
        'tgl_buat' => 'izin_cuti.tgl_buat',
        'tgl_awal' => 'izin_cuti.tgl_awal',
        'tgl_akhir'=> 'izin_cuti.tgl_akhir',
        'lama'     => 'izin_cuti.lama',
        'alasan'   => 'izin_cuti.alasan',
        'nama'     => 'pegawai.nama',
        'jbtn'     => 'pegawai.jbtn',
        'bidang'   => 'pegawai.bidang',
        //'username'  => 'mlite_users.username'
         'nip'      => 'pegawai.nik',
    ])

    ->join('pegawai', 'pegawai.nik = izin_cuti.nip')
    //->join('mlite_users', 'mlite_users.fullname = pegawai.nama')
    ->where('izin_cuti.id', $id)
    ->oneArray();

    $tanggal_buat= $cuti_pegawai['tgl_buat'];
    $date = dateIndonesia(date('Y-m-d', strtotime($tanggal_buat)));

    $tanggal_awal= $cuti_pegawai['tgl_awal'];
    $date1 = dateIndonesia(date('Y-m-d', strtotime($tanggal_awal)));

    $tanggal_akhir= $cuti_pegawai['tgl_akhir'];
    $date2 = dateIndonesia(date('Y-m-d', strtotime($tanggal_akhir)));

    $tentukan_hari1 = date('D', strtotime($tanggal_awal));
    $day = array(
        'Sun' => 'Minggu', 
        'Mon' => 'Senin', 
        'Tue' => 'Selasa', 
        'Wed' => 'Rabu', 
        'Thu' => 'Kamis', 
        'Fri' => 'Jumat', 
        'Sat' => 'Sabtu'
    );
    $hari = $day[$tentukan_hari1];

    $tentukan_hari2 = date('D', strtotime($tanggal_akhir));
    $hari2 = $day[$tentukan_hari2];

    $nama2 = $cuti_pegawai['nama'];
    $nip2 = $cuti_pegawai['nip'];   

    $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor(MODULES.'/kepegawaian/template/cetakIzin.docx');
    $templateProcessor->setValues([
         'nama'      => $cuti_pegawai['nama'],
         // 'nip'      => $cuti_pegawai['username'],
          'nip'      => $cuti_pegawai['nip'],
          'jbtn'     => $cuti_pegawai['jbtn'],
          'hari'     => $hari,
          'hari2'    => $hari2,
          'tgl_buat' => $date,
          'tgl_awal' => $date1,
          'tgl_akhir'=> $date2,
          'lama'     => $cuti_pegawai['lama'],
          'alasan'   => $cuti_pegawai['alasan'],
          'bidang'   => $cuti_pegawai['bidang'],
          'nama2'    => $nama2,
          'nip2'     => $nip2

    ]);
     // header("Content-Disposition: attachment; filename=Surat_Izin.docx");
    //   header("Content-type: application/msword");
        $file = 'Surat_Izin.docx';
        header("Content-Description: File Transfer");
        header('Content-Disposition: attachment; filename="' . $file . '"');
        header('Content-Type: application/vnd.openxmlformats-officedocument.wordprocessingml.document');
        header('Content-Transfer-Encoding: binary');
        header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
        header('Expires: 0');
      //$templateProcessor = \PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
      $templateProcessor->saveAs('php://output');
      exit();
    }

    public function getCetakCuti($id)
    {
      $cuti_pegawai = $this->db('izin_cuti')
      ->select([
          'tgl_buat'            => 'izin_cuti.tgl_buat',
          'tgl_awal'            => 'izin_cuti.tgl_awal',
          'tgl_akhir'           => 'izin_cuti.tgl_akhir',
          'lama'                => 'izin_cuti.lama',
          'alasan'              => 'izin_cuti.alasan',
          'sisa_cuti_tahunan'   => 'izin_cuti.sisa_cuti_tahunan',
          'alamat'              => 'izin_cuti.alamat',
          'no_telp'             => 'izin_cuti.no_telp',
          'jenis_cuti'          => 'izin_cuti.jenis_cuti',
          'nama'                => 'pegawai.nama',
          'jbtn'                => 'pegawai.jbtn',
          'nip'                 => 'pegawai.nik',
          'bidang'              => 'pegawai.bidang',
          'ms_kerja'            => 'pegawai.ms_kerja'
         // 'username'            => 'mlite_users.username'
          
      ])
  
      ->join('pegawai', 'pegawai.nik = izin_cuti.nip')
     // ->join('mlite_users', 'mlite_users.fullname = pegawai.nama')
      ->where('izin_cuti.id', $id)
      ->oneArray();
  
      $tanggal_buat = $cuti_pegawai['tgl_buat'];
      $date = dateIndonesia(date('Y-m-d', strtotime($tanggal_buat)));

      $tanggal_awal = $cuti_pegawai['tgl_awal'];
      $date1 = dateIndonesia(date('Y-m-d', strtotime($tanggal_awal)));

      $tanggal_akhir = $cuti_pegawai['tgl_akhir'];
      $date2 = dateIndonesia(date('Y-m-d', strtotime($tanggal_akhir)));
       
        $jns1 = '';
        $jns2 = '';
        $jns3 = '';
        $jns4 = '';
        $jns5 = '';
        $jns6 = '';

        switch ($cuti_pegawai['jenis_cuti']) {
        case '1':
            $jns1 = 'v';
            break;
        case '2':
            $jns2 = 'v';
            break;
        case '3':
            $jns3 = 'v';
            break;
        case '4':
            $jns4 = 'v';
            break;
        case '5':
            $jns5 = 'v';
            break;
        case '6':
            $jns6 = 'v';
            break;

        default:
        $jns1 = '';
        $jns2 = '';
        $jns3 = '';
        $jns4 = '';
        $jns5 = '';
        $jns6 = '';
            break;
    }

      $nama2 = $cuti_pegawai['nama'];
      $nip2 = $cuti_pegawai['nip'];   

      $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor(MODULES.'/kepegawaian/template/cetakCuti.docx');
      $templateProcessor->setValues([
            'nama'              => $cuti_pegawai['nama'],
            'nip'               => $cuti_pegawai['nip'],
            'jbtn'              => $cuti_pegawai['jbtn'],
            'bidang'            => $cuti_pegawai['bidang'],
            'ms_kerja'          => $cuti_pegawai['ms_kerja'],
            'alasan'            => $cuti_pegawai['alasan'],
            'lama'              => $cuti_pegawai['lama'],
            'alamat'            => $cuti_pegawai['alamat'],
            'tgl_buat'          => $date,
            'tgl_awal'          => $date1,
            'tgl_akhir'         => $date2,
            'sisa_cuti_tahunan' => $cuti_pegawai['sisa_cuti_tahunan'],
            'no_telp'           => $cuti_pegawai['no_telp'],
            'nama2'             => $nama2,
            'nip2'              => $nip2,
            'jns1'              => $jns1,
            'jns2'              => $jns2,
            'jns3'              => $jns3,
            'jns4'              => $jns4,
            'jns5'              => $jns5,
            'jns6'              => $jns6
  
      ]);
        header("Content-Disposition: attachment; filename=Surat_Cuti.docx");
    
        $templateProcessor->saveAs('php://output');
        exit();
      }
  

    public function getCSS()
    {
        header('Content-type: text/css');
        echo $this->draw(MODULES.'/kepegawaian/css/admin/kepegawaian.css');
        exit();
    }

    public function getJavascript()
    {
        header('Content-type: text/javascript');
        echo $this->draw(MODULES.'/kepegawaian/js/admin/kepegawaian.js');
        exit();
    }

    public function getJsChart()
    {
        header('Content-type: text/javascript');
        echo $this->draw(MODULES.'/kepegawaian/js/admin/chart.js');
        exit();
    }

    private function _addHeaderFiles()
    {
        // CSS
        $this->core->addCSS(url('assets/css/dataTables.bootstrap.min.css'));

        // JS
        $this->core->addJS(url('assets/jscripts/jquery.dataTables.min.js'), 'footer');
        $this->core->addJS(url('assets/jscripts/dataTables.bootstrap.min.js'), 'footer');

        $this->core->addCSS(url('assets/css/bootstrap-datetimepicker.css'));
        $this->core->addJS(url('assets/jscripts/moment-with-locales.js'));
        $this->core->addJS(url('assets/jscripts/bootstrap-datetimepicker.js'));

        // MODULE SCRIPTS
        $this->core->addCSS(url([ADMIN, 'kepegawaian', 'css']));
        $this->core->addJS(url([ADMIN, 'kepegawaian', 'javascript']), 'footer');
    }

}
