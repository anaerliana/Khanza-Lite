<?php

namespace Plugins\Laporan_Bundles;

use Plugins\Master\Src\Kamar;
use Systems\AdminModule;

class Admin extends AdminModule
{

  public function navigation()
  {
    return [
      'Manage' => 'manage',
      'Bulanan Bundles HAIs' => 'laporanbundles',
      'Bundles Per Kamar/Bangsal' => 'bundleskamar',
     
    ];
  }

  public function getManage()
  {
    $sub_modules = [
      ['name' => 'Bulanan Bundles', 'url' => url([ADMIN, 'laporan_bundles', 'laporanbundles']), 'icon' => 'list-alt', 'desc' => 'Laporan Bundles HAIs'],
      ['name' => 'Bundles Per Bangsal', 'url' => url([ADMIN, 'laporan_bundles', 'bundleskamar']), 'icon' => 'list-alt', 'desc' => 'Bundles Per Kamar/Bangsal'],
    ];
    return $this->draw('manage.html', ['sub_modules' => $sub_modules]);
  }

  public function getLaporanBundles()
  {

    $this->_addHeaderFiles();
    $this->core->addCSS(url('https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css'));
    $this->core->addJS(url('https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js'), 'footer');
    $this->core->addJS(url('https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js'), 'footer');
    $this->core->addJS(url('https://cdn.datatables.net/buttons/1.3.1/js/buttons.html5.min.js'), 'footer');
    $this->core->addJS(url('https://cdn.datatables.net/plug-ins/1.12.1/api/sum().js'), 'footer');
    $this->core->addJS(url('https://cdn.datatables.net/datetime/1.1.2/js/dataTables.dateTime.min.js'), 'footer');

    $rows =  $this->db('bundles_hais')
      ->group('bundles_hais.tanggal')
      ->toArray();

    $this->assign['list'] = [];
    foreach ($rows as $row) {
      $row['jumlah'] = $this->db('bundles_hais')
        ->select([
          'jml_no_rawat' => 'count(bundles_hais.no_rawat)',

          'jml_hand_vap' => 'sum(bundles_hais.hand_vap)',
          'jml_tehniksteril_vap' => 'sum(bundles_hais.tehniksteril_vap)',
          'jml_apd_vap'  => 'sum(bundles_hais.apd_vap)',
          'jml_sedasi_vap' => 'sum(bundles_hais.sedasi_vap)',

          'jml_hand_iadp' => 'sum(bundles_hais.hand_iadp)',
          'jml_area_iadp' => 'sum(bundles_hais.area_iadp)',
          'jml_tehniksteril_iadp' => 'sum(bundles_hais.tehniksteril_iadp)',
          'jml_alcohol_iadp'  => 'sum(bundles_hais.alcohol_iadp)',
          'jml_apd_iadp' => 'sum(bundles_hais.apd_iadp)',

          'jml_hand_vena' => 'sum(bundles_hais.hand_vena)',
          'jml_kaji_vena'  => 'sum(bundles_hais.kaji_vena)',
          'jml_tehnik_vena'  => 'sum(bundles_hais.tehnik_vena)',
          'jml_petugas_vena' => 'sum(bundles_hais.petugas_vena)',
          'jml_desinfeksi_vena' => 'sum(bundles_hais.desinfeksi_vena)',

          'jml_kaji_isk' => 'sum(bundles_hais.kaji_isk)',
          'jml_petugas_isk'  => 'sum(bundles_hais.petugas_isk)',
          'jml_tangan_isk' => 'sum(bundles_hais.tangan_isk)',
          'jml_tehniksteril_isk' => 'sum(bundles_hais.tehniksteril_isk)',

          'jml_hand_mainvap'  => 'sum(bundles_hais.hand_mainvap)',
          'jml_oral_mainvap' => 'sum(bundles_hais.oral_mainvap)',
          'jml_manage_mainvap' => 'sum(bundles_hais.manage_mainvap)',
          'jml_sedasi_mainvap'   => 'sum(bundles_hais.sedasi_mainvap)',
          'jml_kepala_mainvap'  => 'sum(bundles_hais.kepala_mainvap)',

          'jml_hand_mainiadp' => 'sum(bundles_hais.hand_mainiadp)',
          'jml_desinfeksi_mainiadp'  => 'sum(bundles_hais.desinfeksi_mainiadp)',
          'jml_perawatan_mainiadp' => 'sum(bundles_hais.perawatan_mainiadp)',
          'jml_dreasing_mainiadp'  => 'sum(bundles_hais.dreasing_mainiadp)',
          'jml_infus_mainiadp'  => 'sum(bundles_hais.infus_mainiadp)',

          'jml_hand_mainvena'  => 'sum(bundles_hais.hand_mainvena)',
          'jml_perawatan_mainvena' => 'sum(bundles_hais.perawatan_mainvena)',
          'jml_kaji_mainvena' => 'sum(bundles_hais.kaji_mainvena)',
          'jml_administrasi_mainvena' => 'sum(bundles_hais.administrasi_mainvena)',
          'jml_edukasi_mainvena' => 'sum(bundles_hais.edukasi_mainvena)',

          'jml_hand_mainisk' => 'sum(bundles_hais.hand_mainisk)',
          'jml_kateter_mainisk' => 'sum(bundles_hais.kateter_mainisk)',
          'jml_baglantai_mainisk' => 'sum(bundles_hais.baglantai_mainisk)',
          'jml_bagrendah_mainisk' => 'sum(bundles_hais.bagrendah_mainisk)',
          'jml_posisiselang_mainisk'  => 'sum(bundles_hais.posisiselang_mainisk)',
          'jml_lepas_mainisk'   => 'sum(bundles_hais.lepas_mainisk)',

          'jml_mandi_idopre'  => 'sum(bundles_hais.mandi_idopre)',
          'jml_cukur_idopre' => 'sum(bundles_hais.cukur_idopre)',
          'jml_guladarah_idopre' => 'sum(bundles_hais.guladarah_idopre)',
          'jml_antibiotik_idopre'  => 'sum(bundles_hais.antibiotik_idopre)',

          'jml_hand_idointra' => 'sum(bundles_hais.hand_idointra)',
          'jml_steril_idointra' => 'sum(bundles_hais.steril_idointra)',
          'jml_antiseptic_idointra' => 'sum(bundles_hais.antiseptic_idointra)',
          'jml_tehnik_idointra'   => 'sum(bundles_hais.tehnik_idointra)',
          'jml_mobile_idointra' => 'sum(bundles_hais.mobile_idointra)',
          'jml_suhu_idointra'  => 'sum(bundles_hais.suhu_idointra)',

          'jml_luka_idopost'   => 'sum(bundles_hais.luka_idopost)',
          'jml_rawat_idopost'  => 'sum(bundles_hais.rawat_idopost)',
          'jml_apd_idopost' => 'sum(bundles_hais.apd_idopost)',
          'jml_kaji_idopost' => 'sum(bundles_hais.kaji_idopost)',
          'tanggal' => 'bundles_hais.tanggal'

        ])
        ->join('kamar', 'kamar.kd_kamar=bundles_hais.kd_kamar')
        ->join('bangsal', 'bangsal.kd_bangsal=kamar.kd_bangsal')
        ->where('bundles_hais.tanggal', $row['tanggal'])
        ->toArray();
      $this->assign['list'][] = $row;
    }
    return $this->draw('lapbundles.html', ['lapbundles' => $this->assign]);
  }

  public function getBundlesKamar()
  {
    $this->_addHeaderFiles();

    $this->core->addCSS(url('https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css'));
    $this->core->addJS(url('https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js'), 'footer');
    $this->core->addJS(url('https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js'), 'footer');
    $this->core->addJS(url('https://cdn.datatables.net/buttons/1.3.1/js/buttons.html5.min.js'), 'footer');
    $this->core->addJS(url('https://cdn.datatables.net/plug-ins/1.12.1/api/sum().js'), 'footer');

    $rows =  $this->db('bangsal')
      ->select([
        'nm_bangsal'     => 'bangsal.nm_bangsal',
        'kd_bangsal'     => 'bangsal.kd_bangsal'
      ])
      ->where('bangsal.status', '1')
      ->toArray();

    $this->assign['list'] = [];
    foreach ($rows as $row) {
      $row['jumlah'] = $this->db('bundles_hais')
        ->select([
          'jml_no_rawat' => 'count(bundles_hais.no_rawat)',

          'jml_hand_vap' => 'sum(bundles_hais.hand_vap)',
          'jml_tehniksteril_vap' => 'sum(bundles_hais.tehniksteril_vap)',
          'jml_apd_vap'  => 'sum(bundles_hais.apd_vap)',
          'jml_sedasi_vap' => 'sum(bundles_hais.sedasi_vap)',

          'jml_hand_iadp' => 'sum(bundles_hais.hand_iadp)',
          'jml_area_iadp' => 'sum(bundles_hais.area_iadp)',
          'jml_tehniksteril_iadp' => 'sum(bundles_hais.tehniksteril_iadp)',
          'jml_alcohol_iadp'  => 'sum(bundles_hais.alcohol_iadp)',
          'jml_apd_iadp' => 'sum(bundles_hais.apd_iadp)',

          'jml_hand_vena' => 'sum(bundles_hais.hand_vena)',
          'jml_kaji_vena'  => 'sum(bundles_hais.kaji_vena)',
          'jml_tehnik_vena'  => 'sum(bundles_hais.tehnik_vena)',
          'jml_petugas_vena' => 'sum(bundles_hais.petugas_vena)',
          'jml_desinfeksi_vena' => 'sum(bundles_hais.desinfeksi_vena)',

          'jml_kaji_isk' => 'sum(bundles_hais.kaji_isk)',
          'jml_petugas_isk'  => 'sum(bundles_hais.petugas_isk)',
          'jml_tangan_isk' => 'sum(bundles_hais.tangan_isk)',
          'jml_tehniksteril_isk' => 'sum(bundles_hais.tehniksteril_isk)',

          'jml_hand_mainvap'  => 'sum(bundles_hais.hand_mainvap)',
          'jml_oral_mainvap' => 'sum(bundles_hais.oral_mainvap)',
          'jml_manage_mainvap' => 'sum(bundles_hais.manage_mainvap)',
          'jml_sedasi_mainvap'   => 'sum(bundles_hais.sedasi_mainvap)',
          'jml_kepala_mainvap'  => 'sum(bundles_hais.kepala_mainvap)',

          'jml_hand_mainiadp' => 'sum(bundles_hais.hand_mainiadp)',
          'jml_desinfeksi_mainiadp'  => 'sum(bundles_hais.desinfeksi_mainiadp)',
          'jml_perawatan_mainiadp' => 'sum(bundles_hais.perawatan_mainiadp)',
          'jml_dreasing_mainiadp'  => 'sum(bundles_hais.dreasing_mainiadp)',
          'jml_infus_mainiadp'  => 'sum(bundles_hais.infus_mainiadp)',

          'jml_hand_mainvena'  => 'sum(bundles_hais.hand_mainvena)',
          'jml_perawatan_mainvena' => 'sum(bundles_hais.perawatan_mainvena)',
          'jml_kaji_mainvena' => 'sum(bundles_hais.kaji_mainvena)',
          'jml_administrasi_mainvena' => 'sum(bundles_hais.administrasi_mainvena)',
          'jml_edukasi_mainvena' => 'sum(bundles_hais.edukasi_mainvena)',

          'jml_hand_mainisk' => 'sum(bundles_hais.hand_mainisk)',
          'jml_kateter_mainisk' => 'sum(bundles_hais.kateter_mainisk)',
          'jml_baglantai_mainisk' => 'sum(bundles_hais.baglantai_mainisk)',
          'jml_bagrendah_mainisk' => 'sum(bundles_hais.bagrendah_mainisk)',
          'jml_posisiselang_mainisk'  => 'sum(bundles_hais.posisiselang_mainisk)',
          'jml_lepas_mainisk'   => 'sum(bundles_hais.lepas_mainisk)',

          'jml_mandi_idopre'  => 'sum(bundles_hais.mandi_idopre)',
          'jml_cukur_idopre' => 'sum(bundles_hais.cukur_idopre)',
          'jml_guladarah_idopre' => 'sum(bundles_hais.guladarah_idopre)',
          'jml_antibiotik_idopre'  => 'sum(bundles_hais.antibiotik_idopre)',

          'jml_hand_idointra' => 'sum(bundles_hais.hand_idointra)',
          'jml_steril_idointra' => 'sum(bundles_hais.steril_idointra)',
          'jml_antiseptic_idointra' => 'sum(bundles_hais.antiseptic_idointra)',
          'jml_tehnik_idointra'   => 'sum(bundles_hais.tehnik_idointra)',
          'jml_mobile_idointra' => 'sum(bundles_hais.mobile_idointra)',
          'jml_suhu_idointra'  => 'sum(bundles_hais.suhu_idointra)',

          'jml_luka_idopost'   => 'sum(bundles_hais.luka_idopost)',
          'jml_rawat_idopost'  => 'sum(bundles_hais.rawat_idopost)',
          'jml_apd_idopost' => 'sum(bundles_hais.apd_idopost)',
          'jml_kaji_idopost' => 'sum(bundles_hais.kaji_idopost)'

        ])
        ->join('kamar', 'kamar.kd_kamar=bundles_hais.kd_kamar')
        ->join('bangsal', 'bangsal.kd_bangsal=kamar.kd_bangsal')
        ->where('bangsal.kd_bangsal', $row['kd_bangsal'])
        ->toArray();

      $this->assign['list'][] = $row;
    }

    $this->core->addCSS(url('assets/css/dataTables.bootstrap.min.css'));
    $this->core->addCSS(url([ADMIN, 'laporan_bundles', 'css']));
    $this->core->addJS(url('assets/jscripts/jquery.dataTables.min.js'));
    $this->core->addJS(url('assets/jscripts/dataTables.bootstrap.min.js'));
    return $this->draw('lapbundles_kamar.html', ['bundleskamar' => $this->assign]);
  }

  public function postLaporanBundles()
  {
    $this->_addHeaderFiles();
    $this->core->addCSS(url('https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css'));
    $this->core->addJS(url('https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js'), 'footer');
    $this->core->addJS(url('https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js'), 'footer');
    $this->core->addJS(url('https://cdn.datatables.net/buttons/1.3.1/js/buttons.html5.min.js'), 'footer');
    $this->core->addJS(url('https://cdn.datatables.net/plug-ins/1.12.1/api/sum().js'), 'footer');
    $this->core->addJS(url('https://cdn.datatables.net/datetime/1.1.2/js/dataTables.dateTime.min.js'), 'footer');


    if (isset($_POST['submit'])) {

      $date1 = $_POST['date1'];
      $date2 = $_POST['date2'];

      if (!empty($date1) && !empty($date2)) {
        $sql = "SELECT COUNT(no_rawat) as jml_no_rawat, bundles_hais.tanggal, bangsal.nm_bangsal 
            FROM bundles_hais, kamar, bangsal 
            WHERE bundles_hais.kd_kamar=kamar.kd_kamar 
            AND bangsal.kd_bangsal=kamar.kd_bangsal
            AND bundles_hais.tanggal BETWEEN '$date1' AND '$date2' 
            GROUP BY bundles_hais.tanggal";

        $stmt = $this->db()->pdo()->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        $this->assign['list'] = [];
        foreach ($rows as $row) {
          $row['jumlah'] = $this->db('bundles_hais')
            ->select([
              'jml_no_rawat' => 'count(bundles_hais.no_rawat)',

              'jml_hand_vap' => 'sum(bundles_hais.hand_vap)',
              'jml_tehniksteril_vap' => 'sum(bundles_hais.tehniksteril_vap)',
              'jml_apd_vap'  => 'sum(bundles_hais.apd_vap)',
              'jml_sedasi_vap' => 'sum(bundles_hais.sedasi_vap)',

              'jml_hand_iadp' => 'sum(bundles_hais.hand_iadp)',
              'jml_area_iadp' => 'sum(bundles_hais.area_iadp)',
              'jml_tehniksteril_iadp' => 'sum(bundles_hais.tehniksteril_iadp)',
              'jml_alcohol_iadp'  => 'sum(bundles_hais.alcohol_iadp)',
              'jml_apd_iadp' => 'sum(bundles_hais.apd_iadp)',

              'jml_hand_vena' => 'sum(bundles_hais.hand_vena)',
              'jml_kaji_vena'  => 'sum(bundles_hais.kaji_vena)',
              'jml_tehnik_vena'  => 'sum(bundles_hais.tehnik_vena)',
              'jml_petugas_vena' => 'sum(bundles_hais.petugas_vena)',
              'jml_desinfeksi_vena' => 'sum(bundles_hais.desinfeksi_vena)',

              'jml_kaji_isk' => 'sum(bundles_hais.kaji_isk)',
              'jml_petugas_isk'  => 'sum(bundles_hais.petugas_isk)',
              'jml_tangan_isk' => 'sum(bundles_hais.tangan_isk)',
              'jml_tehniksteril_isk' => 'sum(bundles_hais.tehniksteril_isk)',

              'jml_hand_mainvap'  => 'sum(bundles_hais.hand_mainvap)',
              'jml_oral_mainvap' => 'sum(bundles_hais.oral_mainvap)',
              'jml_manage_mainvap' => 'sum(bundles_hais.manage_mainvap)',
              'jml_sedasi_mainvap'   => 'sum(bundles_hais.sedasi_mainvap)',
              'jml_kepala_mainvap'  => 'sum(bundles_hais.kepala_mainvap)',

              'jml_hand_mainiadp' => 'sum(bundles_hais.hand_mainiadp)',
              'jml_desinfeksi_mainiadp'  => 'sum(bundles_hais.desinfeksi_mainiadp)',
              'jml_perawatan_mainiadp' => 'sum(bundles_hais.perawatan_mainiadp)',
              'jml_dreasing_mainiadp'  => 'sum(bundles_hais.dreasing_mainiadp)',
              'jml_infus_mainiadp'  => 'sum(bundles_hais.infus_mainiadp)',

              'jml_hand_mainvena'  => 'sum(bundles_hais.hand_mainvena)',
              'jml_perawatan_mainvena' => 'sum(bundles_hais.perawatan_mainvena)',
              'jml_kaji_mainvena' => 'sum(bundles_hais.kaji_mainvena)',
              'jml_administrasi_mainvena' => 'sum(bundles_hais.administrasi_mainvena)',
              'jml_edukasi_mainvena' => 'sum(bundles_hais.edukasi_mainvena)',

              'jml_hand_mainisk' => 'sum(bundles_hais.hand_mainisk)',
              'jml_kateter_mainisk' => 'sum(bundles_hais.kateter_mainisk)',
              'jml_baglantai_mainisk' => 'sum(bundles_hais.baglantai_mainisk)',
              'jml_bagrendah_mainisk' => 'sum(bundles_hais.bagrendah_mainisk)',
              'jml_posisiselang_mainisk'  => 'sum(bundles_hais.posisiselang_mainisk)',
              'jml_lepas_mainisk'   => 'sum(bundles_hais.lepas_mainisk)',

              'jml_mandi_idopre'  => 'sum(bundles_hais.mandi_idopre)',
              'jml_cukur_idopre' => 'sum(bundles_hais.cukur_idopre)',
              'jml_guladarah_idopre' => 'sum(bundles_hais.guladarah_idopre)',
              'jml_antibiotik_idopre'  => 'sum(bundles_hais.antibiotik_idopre)',

              'jml_hand_idointra' => 'sum(bundles_hais.hand_idointra)',
              'jml_steril_idointra' => 'sum(bundles_hais.steril_idointra)',
              'jml_antiseptic_idointra' => 'sum(bundles_hais.antiseptic_idointra)',
              'jml_tehnik_idointra'   => 'sum(bundles_hais.tehnik_idointra)',
              'jml_mobile_idointra' => 'sum(bundles_hais.mobile_idointra)',
              'jml_suhu_idointra'  => 'sum(bundles_hais.suhu_idointra)',

              'jml_luka_idopost'   => 'sum(bundles_hais.luka_idopost)',
              'jml_rawat_idopost'  => 'sum(bundles_hais.rawat_idopost)',
              'jml_apd_idopost' => 'sum(bundles_hais.apd_idopost)',
              'jml_kaji_idopost' => 'sum(bundles_hais.kaji_idopost)',
              'tanggal' => 'bundles_hais.tanggal'

            ])
            ->join('kamar', 'kamar.kd_kamar=bundles_hais.kd_kamar')
            ->join('bangsal', 'bangsal.kd_bangsal=kamar.kd_bangsal')
            ->where('bundles_hais.tanggal', $row['tanggal'])
            ->toArray();
          $this->assign['list'][] = $row;
        }
      } else {
        $this->getLaporanBundles();
      }
    }

    $this->core->addCSS(url('assets/css/dataTables.bootstrap.min.css'));
    $this->core->addCSS(url([ADMIN, 'laporan_bundles', 'css']));
    $this->core->addJS(url('assets/jscripts/jquery.dataTables.min.js'));
    $this->core->addJS(url('assets/jscripts/dataTables.bootstrap.min.js'));
    return $this->draw('lapbundles.html', ['lapbundles' => $this->assign]);
  }

  public function postBundlesKamar()
  {
    $this->_addHeaderFiles();
    $this->core->addCSS(url('https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css'));
    $this->core->addJS(url('https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js'), 'footer');
    $this->core->addJS(url('https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js'), 'footer');
    $this->core->addJS(url('https://cdn.datatables.net/buttons/1.3.1/js/buttons.html5.min.js'), 'footer');
    $this->core->addJS(url('https://cdn.datatables.net/plug-ins/1.12.1/api/sum().js'), 'footer');
    $this->core->addJS(url('https://cdn.datatables.net/datetime/1.1.2/js/dataTables.dateTime.min.js'), 'footer');


    if (isset($_POST['submit'])) {

      $date1 = $_POST['date1'];
      $date2 = $_POST['date2'];

      if (!empty($date1) && !empty($date2)) {
        $sql = "SELECT COUNT(no_rawat) as jml_no_rawat, bundles_hais.tanggal, bangsal.nm_bangsal 
            FROM bundles_hais, kamar, bangsal 
            WHERE bundles_hais.kd_kamar=kamar.kd_kamar 
            AND bangsal.kd_bangsal=kamar.kd_bangsal
            AND bundles_hais.tanggal BETWEEN '$date1' AND '$date2' 
            GROUP BY bundles_hais.tanggal";

        $stmt = $this->db()->pdo()->prepare($sql);
        $stmt->execute();
        $rows = $stmt->fetchAll();

        $this->assign['list'] = [];
        foreach ($rows as $row) {
          $row['jumlah'] = $this->db('bundles_hais')
            ->select([
              'jml_no_rawat' => 'count(bundles_hais.no_rawat)',

              'jml_hand_vap' => 'sum(bundles_hais.hand_vap)',
              'jml_tehniksteril_vap' => 'sum(bundles_hais.tehniksteril_vap)',
              'jml_apd_vap'  => 'sum(bundles_hais.apd_vap)',
              'jml_sedasi_vap' => 'sum(bundles_hais.sedasi_vap)',

              'jml_hand_iadp' => 'sum(bundles_hais.hand_iadp)',
              'jml_area_iadp' => 'sum(bundles_hais.area_iadp)',
              'jml_tehniksteril_iadp' => 'sum(bundles_hais.tehniksteril_iadp)',
              'jml_alcohol_iadp'  => 'sum(bundles_hais.alcohol_iadp)',
              'jml_apd_iadp' => 'sum(bundles_hais.apd_iadp)',

              'jml_hand_vena' => 'sum(bundles_hais.hand_vena)',
              'jml_kaji_vena'  => 'sum(bundles_hais.kaji_vena)',
              'jml_tehnik_vena'  => 'sum(bundles_hais.tehnik_vena)',
              'jml_petugas_vena' => 'sum(bundles_hais.petugas_vena)',
              'jml_desinfeksi_vena' => 'sum(bundles_hais.desinfeksi_vena)',

              'jml_kaji_isk' => 'sum(bundles_hais.kaji_isk)',
              'jml_petugas_isk'  => 'sum(bundles_hais.petugas_isk)',
              'jml_tangan_isk' => 'sum(bundles_hais.tangan_isk)',
              'jml_tehniksteril_isk' => 'sum(bundles_hais.tehniksteril_isk)',

              'jml_hand_mainvap'  => 'sum(bundles_hais.hand_mainvap)',
              'jml_oral_mainvap' => 'sum(bundles_hais.oral_mainvap)',
              'jml_manage_mainvap' => 'sum(bundles_hais.manage_mainvap)',
              'jml_sedasi_mainvap'   => 'sum(bundles_hais.sedasi_mainvap)',
              'jml_kepala_mainvap'  => 'sum(bundles_hais.kepala_mainvap)',

              'jml_hand_mainiadp' => 'sum(bundles_hais.hand_mainiadp)',
              'jml_desinfeksi_mainiadp'  => 'sum(bundles_hais.desinfeksi_mainiadp)',
              'jml_perawatan_mainiadp' => 'sum(bundles_hais.perawatan_mainiadp)',
              'jml_dreasing_mainiadp'  => 'sum(bundles_hais.dreasing_mainiadp)',
              'jml_infus_mainiadp'  => 'sum(bundles_hais.infus_mainiadp)',

              'jml_hand_mainvena'  => 'sum(bundles_hais.hand_mainvena)',
              'jml_perawatan_mainvena' => 'sum(bundles_hais.perawatan_mainvena)',
              'jml_kaji_mainvena' => 'sum(bundles_hais.kaji_mainvena)',
              'jml_administrasi_mainvena' => 'sum(bundles_hais.administrasi_mainvena)',
              'jml_edukasi_mainvena' => 'sum(bundles_hais.edukasi_mainvena)',

              'jml_hand_mainisk' => 'sum(bundles_hais.hand_mainisk)',
              'jml_kateter_mainisk' => 'sum(bundles_hais.kateter_mainisk)',
              'jml_baglantai_mainisk' => 'sum(bundles_hais.baglantai_mainisk)',
              'jml_bagrendah_mainisk' => 'sum(bundles_hais.bagrendah_mainisk)',
              'jml_posisiselang_mainisk'  => 'sum(bundles_hais.posisiselang_mainisk)',
              'jml_lepas_mainisk'   => 'sum(bundles_hais.lepas_mainisk)',

              'jml_mandi_idopre'  => 'sum(bundles_hais.mandi_idopre)',
              'jml_cukur_idopre' => 'sum(bundles_hais.cukur_idopre)',
              'jml_guladarah_idopre' => 'sum(bundles_hais.guladarah_idopre)',
              'jml_antibiotik_idopre'  => 'sum(bundles_hais.antibiotik_idopre)',

              'jml_hand_idointra' => 'sum(bundles_hais.hand_idointra)',
              'jml_steril_idointra' => 'sum(bundles_hais.steril_idointra)',
              'jml_antiseptic_idointra' => 'sum(bundles_hais.antiseptic_idointra)',
              'jml_tehnik_idointra'   => 'sum(bundles_hais.tehnik_idointra)',
              'jml_mobile_idointra' => 'sum(bundles_hais.mobile_idointra)',
              'jml_suhu_idointra'  => 'sum(bundles_hais.suhu_idointra)',

              'jml_luka_idopost'   => 'sum(bundles_hais.luka_idopost)',
              'jml_rawat_idopost'  => 'sum(bundles_hais.rawat_idopost)',
              'jml_apd_idopost' => 'sum(bundles_hais.apd_idopost)',
              'jml_kaji_idopost' => 'sum(bundles_hais.kaji_idopost)',
              'tanggal' => 'bundles_hais.tanggal'

            ])
            ->join('kamar', 'kamar.kd_kamar=bundles_hais.kd_kamar')
            ->join('bangsal', 'bangsal.kd_bangsal=kamar.kd_bangsal')
            ->where('bundles_hais.tanggal', $row['tanggal'])
            ->toArray();
          $this->assign['list'][] = $row;
        }
      } else {
        $this->getBundlesKamar();
      }
    }

    $this->core->addCSS(url('assets/css/dataTables.bootstrap.min.css'));
    $this->core->addCSS(url([ADMIN, 'laporan_bundles', 'css']));
    $this->core->addJS(url('assets/jscripts/jquery.dataTables.min.js'));
    $this->core->addJS(url('assets/jscripts/dataTables.bootstrap.min.js'));
    return $this->draw('lapbundles_kamar.html', ['bundleskamar' => $this->assign]);
  }

  public function getCSS()
  {
    header('Content-type: text/css');
    echo $this->draw(MODULES . '/laporan_bundles/css/admin/laporan_bundles.css');
    exit();
  }

  public function getJavascript()
  {
    header('Content-type: text/javascript');
    echo $this->draw(MODULES . '/laporan_bundles/js/admin/laporan_bundles.js');
    exit();
  }

  private function _addHeaderFiles()
  {
    // CSS
    $this->core->addCSS(url('assets/css/dataTables.bootstrap.min.css'));
    // $this->core->addCSS(url('assets/css/bootstrap-datetimepicker.css'));
    $this->core->addCSS(url('assets/css/jquery-ui.css'));
    $this->core->addCSS(url('assets/jscripts/lightbox/lightbox.min.css'));
    $this->core->addCSS(url('assets/css/bootstrap-datetimepicker.css'));


    // JS
    $this->core->addJS(url('assets/jscripts/jquery.dataTables.min.js'), 'footer');
    $this->core->addJS(url('assets/jscripts/dataTables.bootstrap.min.js'), 'footer');

    $this->core->addJS(url('assets/jscripts/jquery-ui.js'), 'footer');
    $this->core->addJS(url('assets/jscripts/lightbox/lightbox.min.js'), 'footer');
    $this->core->addJS(url('assets/jscripts/moment-with-locales.js'));
    $this->core->addJS(url('assets/jscripts/bootstrap-datetimepicker.js'));

    // MODULE SCRIPTS
    $this->core->addCSS(url([ADMIN, 'laporan_bundles', 'css']));
    $this->core->addJS(url([ADMIN, 'laporan_bundles', 'javascript']), 'footer');
  }
}
