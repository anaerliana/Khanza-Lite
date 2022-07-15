<?php

namespace Plugins\Bundles_Hais;

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
      //'Master Pegawai' => 'master',
    ];
  }


  public function getManage($no_rawat)
  {


    $sub_modules = [
      ['name' => 'Bundles Insersi', 'url' => url([ADMIN, 'bundles_hais', 'bundlesinsersi', $no_rawat]), 'icon' => 'pencil', 'desc' => 'Bundles Insersi'],
      ['name' => 'Bundles Maintanance', 'url' => url([ADMIN, 'bundles_hais', 'bundles_maintanance', $no_rawat]), 'icon' => 'pencil-square-o', 'desc' => 'Bundles Maintanance'],
      ['name' => 'Bundles IDO', 'url' => url([ADMIN, 'bundles_hais', 'bundles_ido', $no_rawat]), 'icon' => 'pencil-square', 'desc' => 'Bundles IDO'],
    ];
    return $this->draw('manage.html', ['sub_modules' => $sub_modules]);
  }

  public function anyBundlesInsersi($no_rawat)
  {
    // //js
    // $this->_addHeaderFiles();
    // $row = [];
    // $id = revertNorawat($no_rawat);
    // $pasien = $this->db('reg_periksa')
    //   ->join('pasien', 'pasien.no_rkm_medis=reg_periksa.no_rkm_medis')
    //   ->where('no_rawat', $id)
    //   ->oneArray();

    // $row['no_rkm_medis'] = $pasien['no_rkm_medis'];
    // $row['nm_pasien'] = $pasien['nm_pasien'];
    // $row['no_rawat'] = $pasien['no_rawat'];

    //   $bundles = $this->db('bundles_hais')
    //     ->where('no_rawat', $id)
    //     ->toArray();
    //   foreach($bundles as $bundle){

    //     $row['bundles'] = $bundle;
    //   }
    //   $kamar_inap = $this->db('kamar_inap')
    //     ->where('no_rawat', $id)
    //     ->oneArray();

    //   $row['kd_kamar'] = $kamar_inap['kd_kamar'];
    // return $this->draw('bundles.insersi.html', ['bundles_insersi' => $row ]);
    // // echo $this->draw('bundles.insersi.html', ['bundles_insersi' => $row ]);
    {
      $this->_addHeaderFiles();
      $row = [];
      $bundles_insersi = [];
      $id = revertNorawat($no_rawat);
      
      $pasien = $this->db('reg_periksa')
        ->join('pasien', 'pasien.no_rkm_medis=reg_periksa.no_rkm_medis')
        ->where('no_rawat', $id)
        ->oneArray();
  
      $bundles_insersi['no_rkm_medis'] = $pasien['no_rkm_medis'];
      $bundles_insersi['nm_pasien'] = $pasien['nm_pasien'];
      $bundles_insersi['no_rawat'] = $pasien['no_rawat'];
      $kamar_inap = $this->db('kamar_inap')
        ->where('no_rawat', $id)
        ->oneArray();
      $bundles_insersi['kd_kamar'] = $kamar_inap['kd_kamar'];
      
      $bundles = $this->db('bundles_hais')
        ->where('no_rawat', $id)
        ->toArray(); 
      foreach($bundles as $bundle){
        $row['bundles'] = $bundle;
        
      }
  
      return $this->draw('bundles.insersi.html', ['bundles_insersi_hais' => $bundles_insersi , 'bundles' => $row]);
  
    }
  }

  
  public function postSaveInsersi()
  {
    if(!$this->db('bundles_hais')->where('no_rawat', $_POST['no_rawat'])->where('tanggal', $_POST['tanggal'])->where('kd_kamar', $_POST['kd_kamar'])->oneArray()) {
      $this->db('bundles_hais')->save($_POST);
    } else{
      $this->db('bundles_hais')->where('no_rawat', $_POST['no_rawat'])->where('tanggal', $_POST['tanggal'])->where('kd_kamar', $_POST['kd_kamar'])->save($_POST);
    }
    // echo('Simpan_berhasil');
    $no_rawat = convertNorawat($_POST['no_rawat']);
    echo $no_rawat;
    exit();
  }

  public function anyBundles_Maintanance($no_rawat)
  {
    $this->_addHeaderFiles();
    $row = [];
    $id = revertNorawat($no_rawat);
    $pasien = $this->db('reg_periksa')
      ->join('pasien', 'pasien.no_rkm_medis=reg_periksa.no_rkm_medis')
      ->where('no_rawat', $id)
      ->oneArray();

    $row['no_rkm_medis'] = $pasien['no_rkm_medis'];
    $row['nm_pasien'] = $pasien['nm_pasien'];
    $row['no_rawat'] = $pasien['no_rawat'];

      $bundles = $this->db('bundles_hais')
        ->where('no_rawat', $id)
        ->toArray();
      foreach($bundles as $bundle){

        $row['bundles'] = $bundle;
      }
      $kamar_inap = $this->db('kamar_inap')
        ->where('no_rawat', $id)
        ->oneArray();

      $row['kd_kamar'] = $kamar_inap['kd_kamar'];

    return $this->draw('bundles.maintanance.html', ['bundles_maintanance' => $row]);
  }

  public function postSaveMaintanance()
  {
    if(!$this->db('bundles_hais')->where('no_rawat', $_POST['no_rawat'])->where('tanggal', $_POST['tanggal'])->where('kd_kamar', $_POST['kd_kamar'])->oneArray()) {
      $this->db('bundles_hais')->save($_POST);
    } else{
      $this->db('bundles_hais')->where('no_rawat', $_POST['no_rawat'])->where('tanggal', $_POST['tanggal'])->where('kd_kamar', $_POST['kd_kamar'])->save($_POST);
    }
    // echo('Simpan_berhasil');
    $no_rawat = convertNorawat($_POST['no_rawat']);
    echo $no_rawat;
    exit();
  }


  public function anyBundles_IDO($no_rawat)
  {
    $this->_addHeaderFiles();
      // if(isset($_POST['no_rawat'])) {
      //   $id = $_POST['no_rawat'];
      // } else {
      // }
    $row = [];
    $bundles_ido = [];
    
    $id = revertNorawat($no_rawat);
    $pasien = $this->db('reg_periksa')
      ->join('pasien', 'pasien.no_rkm_medis=reg_periksa.no_rkm_medis')
      ->where('no_rawat', $id)
      ->oneArray();

    
    $bundles_ido['no_rkm_medis'] = $pasien['no_rkm_medis'];
    $bundles_ido['nm_pasien'] = $pasien['nm_pasien'];
    $bundles_ido['no_rawat'] = $pasien['no_rawat'];
    $kamar_inap = $this->db('kamar_inap')
      ->where('no_rawat', $id)
      ->oneArray();
    $bundles_ido['kd_kamar'] = $kamar_inap['kd_kamar'];
        
   
    $bundles = $this->db('bundles_hais')
      ->where('no_rawat', $id)
      ->toArray(); 
    foreach($bundles as $bundle){
      
      $row['bundles'] = $bundle;
      
    }

    return $this->draw('bundles.ido.html', ['bundles_ido_hais' => $bundles_ido , 'bundles' => $bundles]);

  }

  public function postSaveBundles_Ido()
  {
    // $this->_addHeaderFiles(); 
    if(!$this->db('bundles_hais')->where('no_rawat', $_POST['no_rawat'])->where('tanggal', $_POST['tanggal'])->where('kd_kamar', $_POST['kd_kamar'])->oneArray()) {
      $this->db('bundles_hais')->save($_POST);
    } else{
      $this->db('bundles_hais')->where('no_rawat', $_POST['no_rawat'])->where('tanggal', $_POST['tanggal'])->where('kd_kamar', $_POST['kd_kamar'])->save($_POST);
    }
    // echo('Simpan_berhasil');
    $no_rawat = convertNorawat($_POST['no_rawat']);
    echo $no_rawat;
    exit();
  }

  public function postHapusIdo()
  {
    $this->db('bundles_hais')->where('no_rawat', $_POST['no_rawat'])->where('tanggal', $_POST['tanggal'])->where('luka_idopost', $_POST['luka_idopost'])->delete();
    exit();
  }



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
