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
                // $row['editURL'] = url([ADMIN, 'profil', 'biodata', $row['id']]);
                // $row['viewURL'] = url([ADMIN, 'kepegawaian', 'view', $row['id']]);
                $this->assign['list'][] = $row;
            }
        }
        $this->assign['listCuti'] = array('1' => 'Cuti Tahunan', '2'=>'Cuti Besar', '3'=>'Cuti Sakit', '4'=>'Cuti Melahirkan', '5'=>'Cuti Karena Alasan Penting', '6'=>'Cuti Di Luar Tanggungan Negara', '7'=>'Izin');
        $this->assign['getStatus'] = isset($_GET['status']);
        $this->assign['printURL'] = url([ADMIN, 'kepegawaian', 'print']);
        // $this->assign['cetakURL'] = url([ADMIN, 'kepegawaian', 'cetak']);

        return $this->draw('index_cuti.html', ['cuti' => $this->assign]);
    }

    public function getSetStatus($id)
  {

    $pegawai = $this->db('pegawai')->select('nama')->where('nik', $id)->oneArray();

    $cuti = $this->db('izin_cuti')->where('id', $id)->oneArray();
    $set_status = $this->db('izin_cuti')->where('id', $id)->asc('id')->toArray();
    $this->tpl->set('pegawai', $pegawai);
    $this->tpl->set('cuti', $cuti);
    $this->tpl->set('set_status', $set_status);
    echo $this->tpl->draw(MODULES . '/kepegawaian/view/admin/setstatus.html', true);
    exit();
  }

    public function postStatusSave()
    {
        $id = $_POST['id'];
        $errors = 0;
        $location = url([ADMIN, 'kepegawaian', 'cuti']);
        $no_surat = $_POST['no_surat'];
        $status = $_POST['status'];
        $keterangan = $_POST['keterangan'];
              $query = $this->db('izin_cuti')
                ->where('id', $id)
                ->save([
                'no_surat' => $no_surat,
                'status' => $status,
                'keterangan' =>  $keterangan,
                'updated_at' =>  date('Y-m-d H:i:s')
                ]);
            if ($query) {
                $this->notify('success', 'Status Berhasil Disimpan');
            } else {
                $this->notify('failure', 'Status Gagal Disimpan');
            }
          redirect($location);
         echo $status;
    }

    public function postStatusDel()
  {
    $this->db('izin_cuti')
      ->where('id', $_POST['id'])
      ->update([
        'status' => NULL,
        'updated_at' =>  date('Y-m-d H:i:s')
      ]);
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
