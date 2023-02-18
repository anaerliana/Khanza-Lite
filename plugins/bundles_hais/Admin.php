<?php

namespace Plugins\Bundles_Hais;

use mysqli;
use Systems\AdminModule;
use Systems\Lib\Fpdf\PDF_MC_Table;

class Admin extends AdminModule
{
  public function navigation()
  {
    return [
      'Kelola' => 'manage',
      'Bundles Insersi' => 'bundles_insersi',
      'Bundles Maintanance' => 'bundles_maintanance',
      'Bundles IDO' => 'bundles_ido',
      // 'Laporan Bundles' => 'laporan_bundles',
    ];
  }


  public function getManage($no_rawat=null)
  {
    if ($no_rawat==null) {
    }
   else {
    $sub_modules = [
      ['name' => 'Bundles Insersi', 'url' => url([ADMIN, 'bundles_hais', 'bundlesinsersi', $no_rawat]), 'icon' => 'pencil', 'desc' => 'Bundles Insersi'],
      ['name' => 'Bundles Maintanance', 'url' => url([ADMIN, 'bundles_hais', 'bundles_maintanance', $no_rawat]), 'icon' => 'pencil-square-o', 'desc' => 'Bundles Maintanance'],
      ['name' => 'Bundles IDO', 'url' => url([ADMIN, 'bundles_hais', 'bundles_ido', $no_rawat]), 'icon' => 'pencil-square', 'desc' => 'Bundles IDO'],
      // ['name' => 'Laporan Bundles', 'url' => url([ADMIN, 'bundles_hais', 'laporan_bundles', $no_rawat]), 'icon' => 'list-alt"', 'desc' => 'Laporan Bundles'],
    ];
    return $this->draw('manage.html', ['sub_modules' => $sub_modules]);
  }
}

//BUNDLES_INSERSI
  public function anyBundlesInsersi($no_rawat)
  {
    $this->_addHeaderFiles();
    $id = revertNorawat($no_rawat);

   
      $no_rkm_medis = $this->core->getRegPeriksaInfo('no_rkm_medis',$id);
      $nama = $this->core->getPasienInfo('nm_pasien',$no_rkm_medis);
      $kd_kamar = $this->core->getKamarInapInfo('kd_kamar',$id);
      if (!$kd_kamar) {
        # code...
        $kd_kamar = 'IGDK';
      }

    $bundles = $this->db('bundles_hais')
      ->where('no_rawat', $id)
      ->toArray();

    return $this->draw('bundles.insersi.html', ['no_rawat' => $id ,'no_rkm_medis' => $no_rkm_medis, 'nama' => $nama ,'kd_kamar' => $kd_kamar, 'bundles' => $bundles]);
  }


  public function postSaveInsersi()
  { 
    if (!$this->db('bundles_hais')->where('no_rawat', $_POST['no_rawat'])->where('tanggal', $_POST['tanggal'])->where('kd_kamar', $_POST['kd_kamar'])->oneArray()) {
      $this->db('bundles_hais')->save($_POST);
    } else {
      $this->db('bundles_hais')->where('no_rawat', $_POST['no_rawat'])->where('tanggal', $_POST['tanggal'])->where('kd_kamar', $_POST['kd_kamar'])->save($_POST);
    }

    $no_rawat = convertNorawat($_POST['no_rawat']);
    echo $no_rawat;
    $this->notify('success', 'Data Bundles Insersi telah disimpan');
    exit();
  }

  public function postHapus_Insersi_Vap()
  {

    $this->db('bundles_hais')
      ->where('id', $_POST['id'])
      ->update([
        'hand_vap' => NULL,
        'tehniksteril_vap' => NULL,
        'apd_vap' => NULL,
        'sedasi_vap' => NULL
      ]);
    $no_rawat = convertNorawat($_POST['no_rawat']);
    echo $no_rawat;
    
    exit();
  }

  public function postHapus_Insersi_Iadp()
  {

    $this->db('bundles_hais')
      ->where('id', $_POST['id'])
      ->update([
        'hand_iadp' => NULL,
        'area_iadp' => NULL,
        'tehniksteril_iadp' => NULL,
        'alcohol_iadp' => NULL,
        'apd_iadp' => NULL
      ]);
    $no_rawat = convertNorawat($_POST['no_rawat']);
    echo $no_rawat;
    exit();
  }

  public function postHapus_Insersi_Vena()
  {

    $this->db('bundles_hais')
      ->where('id', $_POST['id'])
      ->update([
        'hand_vena' => NULL,
        'kaji_vena' => NULL,
        'tehnik_vena' => NULL,
        'petugas_vena' => NULL,
        'desinfeksi_vena' => NULL
      ]);
    $no_rawat = convertNorawat($_POST['no_rawat']);
    echo $no_rawat;
    exit();
  }

  public function postHapus_Insersi_Isk()
  {

    $this->db('bundles_hais')
      ->where('id', $_POST['id'])
      ->update([
        'kaji_isk' => NULL,
        'petugas_isk' => NULL,
        'tangan_isk' => NULL,
        'tehniksteril_isk' => NULL
      ]);
    $no_rawat = convertNorawat($_POST['no_rawat']);
    echo $no_rawat;
    exit();
  }

  //BUNDLES_MAINTANANCE
  public function anyBundles_Maintanance($no_rawat)
  {
    $this->_addHeaderFiles();
    $id = revertNorawat($no_rawat);

    $no_rkm_medis = $this->core->getRegPeriksaInfo('no_rkm_medis',$id);
    $nama = $this->core->getPasienInfo('nm_pasien',$no_rkm_medis);
    $kd_kamar = $this->core->getKamarInapInfo('kd_kamar',$id);
      if (!$kd_kamar) {
        # code...
        $kd_kamar = 'IGDK';
      }

    $bundles = $this->db('bundles_hais')
      ->where('no_rawat', $id)
      ->toArray();

    return $this->draw('bundles.maintanance.html', ['no_rawat' => $id ,'no_rkm_medis' => $no_rkm_medis, 'nama' => $nama ,'kd_kamar' => $kd_kamar, 'bundles' => $bundles]);
  }


  public function postSaveBundles_Maintanance()
  {
    if (!$this->db('bundles_hais')->where('no_rawat', $_POST['no_rawat'])->where('tanggal', $_POST['tanggal'])->where('kd_kamar', $_POST['kd_kamar'])->oneArray()) {
      $this->db('bundles_hais')->save($_POST);
    } else {
      $this->db('bundles_hais')->where('no_rawat', $_POST['no_rawat'])->where('tanggal', $_POST['tanggal'])->where('kd_kamar', $_POST['kd_kamar'])->save($_POST);
    }

    $no_rawat = convertNorawat($_POST['no_rawat']);
    echo $no_rawat;
    $this->notify('success', 'Data Bundles Maintanance telah disimpan');
    exit();
  }
  
     public function postHapus_Main_Vap()
     {
   
       $this->db('bundles_hais')
         ->where('id', $_POST['id'])
         ->update([
           'hand_mainvap' => NULL,
           'oral_mainvap' => NULL,
           'manage_mainvap' => NULL,
           'sedasi_mainvap' => NULL,
           'kepala_mainvap' => NULL
         ]);
       $no_rawat = convertNorawat($_POST['no_rawat']);
       echo $no_rawat;
       exit();
     }

     public function postHapus_Main_Iadp()
     {
   
       $this->db('bundles_hais')
         ->where('id', $_POST['id'])
         ->update([
           'hand_mainiadp' => NULL,
           'desinfeksi_mainiadp' => NULL,
           'perawatan_mainiadp' => NULL,
           'dreasing_mainiadp' => NULL,
           'infus_mainiadp' => NULL
         ]);
       $no_rawat = convertNorawat($_POST['no_rawat']);
       echo $no_rawat;
       exit();
     }

     public function postHapus_Main_Vena()
     {
   
       $this->db('bundles_hais')
         ->where('id', $_POST['id'])
         ->update([
           'hand_mainvena' => NULL,
           'perawatan_mainvena' => NULL,
           'kaji_mainvena' => NULL,
           'administrasi_mainvena' => NULL,
           'edukasi_mainvena' => NULL
         ]);
       $no_rawat = convertNorawat($_POST['no_rawat']);
       echo $no_rawat;
       exit();
     }

     public function postHapus_Main_Isk()
     {
   
       $this->db('bundles_hais')
         ->where('id', $_POST['id'])
         ->update([
           'hand_mainisk' => NULL,
           'kateter_mainisk' => NULL,
           'baglantai_mainisk' => NULL,
           'bagrendah_mainisk' => NULL,
           'posisiselang_mainisk' => NULL,
           'lepas_mainisk' => NULL
         ]);
       $no_rawat = convertNorawat($_POST['no_rawat']);
       echo $no_rawat;
       exit();
     }

//BUNDLES_IDO     
  public function anyBundles_IDO($no_rawat = 0)
  {

    $this->_addHeaderFiles();
    $id = revertNorawat($no_rawat);

    $no_rkm_medis = $this->core->getRegPeriksaInfo('no_rkm_medis',$id);
    $nama = $this->core->getPasienInfo('nm_pasien',$no_rkm_medis);
    $kd_kamar = $this->core->getKamarInapInfo('kd_kamar',$id);
      if (!$kd_kamar) {
        $kd_kamar = 'IGDK';
      }

    $bundles = $this->db('bundles_hais')
      ->where('no_rawat', $id)
      ->toArray();

    return $this->draw('bundles.ido.html', ['no_rawat' => $id ,'no_rkm_medis' => $no_rkm_medis, 'nama' => $nama ,'kd_kamar' => $kd_kamar, 'bundles' => $bundles]);
  }

  public function postSaveBundles_Ido()
  {
    if (!$this->db('bundles_hais')->where('no_rawat', $_POST['no_rawat'])->where('tanggal', $_POST['tanggal'])->where('kd_kamar', $_POST['kd_kamar'])->oneArray()) {
      $this->db('bundles_hais')->save($_POST);
    } else {
      $this->db('bundles_hais')->where('no_rawat', $_POST['no_rawat'])->where('tanggal', $_POST['tanggal'])->where('kd_kamar', $_POST['kd_kamar'])->save($_POST);
    }
    $no_rawat = convertNorawat($_POST['no_rawat']);
    echo $no_rawat;
    $this->notify('success', 'Data Bundles IDO telah disimpan');
    exit();
  }

  public function postHapus_IdoPre()
  {
    $this->db('bundles_hais')
      ->where('id', $_POST['id'])
      ->update([
        'mandi_idopre' => NULL,
        'cukur_idopre' => NULL,
        'guladarah_idopre' => NULL,
        'antibiotik_idopre' => NULL
      ]);
  
    $no_rawat = convertNorawat($_POST['no_rawat']);
    echo $no_rawat;
    exit();
  }

  public function postHapus_IdoIntra()
  {

    $this->db('bundles_hais')
      ->where('id', $_POST['id'])
      ->update([
        'hand_idointra' => NULL,
        'steril_idointra' => NULL,
        'antiseptic_idointra' => NULL,
        'tehnik_idointra' => NULL,
        'mobile_idointra' => NULL,
        'suhu_idointra' => NULL
      ]);
    $no_rawat = convertNorawat($_POST['no_rawat']);
    echo $no_rawat;
    exit();
  }

  public function postHapus_IdoPost()
  {

    $this->db('bundles_hais')
      ->where('id', $_POST['id'])
      ->update([
        'luka_idopost' => NULL,
        'rawat_idopost' => NULL,
        'apd_idopost' => NULL,
        'kaji_idopost' => NULL
      ]);
    $no_rawat = convertNorawat($_POST['no_rawat']);
    echo $no_rawat;
    exit();
  }

  // public function anyLaporan_Bundles($no_rawat)
  // {

  //   $this->_addHeaderFiles();
  //   $id = revertNorawat($no_rawat);
  //   $i = 1;

  //     $rows = $this->db('bundles_hais')
  //     ->where('no_rawat', $id)
  //     ->toArray();

  //   $result = [];
  //   foreach ($rows as $row) {
  //     $row['nomor'] = $i++;
  //    // $jumlah_pasien = mysqli_num_rows($nama_pasien);

  //   $laporan_bundles = $this->db('kamar_inap')
  //     ->select('reg_periksa.no_rawat')
  //     ->select('pasien.nm_pasien')
  //     ->select('reg_periksa.no_rkm_medis')
  //     ->select('kamar_inap.kd_kamar')
  //     ->join('reg_periksa', 'reg_periksa.no_rawat=kamar_inap.no_rawat')
  //     ->join('pasien', 'pasien.no_rkm_medis=reg_periksa.no_rkm_medis')
  //     ->where('kamar_inap.no_rawat', $id)
  //     ->oneArray();

  //   $result[] = $row;
  //   }
  //   return $this->draw('laporan.bundles.html', ['laporan_bundles_hais' => $result]);
  // }

  public function getCSS()
  {
    header('Content-type: text/css');
    echo $this->draw(MODULES . '/bundles_hais/css/admin/bundles_hais.css');
    exit();
  }

  public function getJavascript()
  {
    header('Content-type: text/javascript');
    echo $this->draw(MODULES . '/bundles_hais/js/admin/bundles_hais.js');
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
    $this->core->addCSS(url([ADMIN, 'bundles_hais', 'css']));
    $this->core->addJS(url([ADMIN, 'bundles_hais', 'javascript']), 'footer');
  }
}
