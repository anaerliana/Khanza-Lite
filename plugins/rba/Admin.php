<?php

namespace Plugins\Rba;

use Systems\AdminModule;
use Systems\Lib\Fpdf\PDF_MC_Table;

class Admin extends AdminModule
{   
      public function navigation()
    {
        return [
            'Kelola'   => 'manage',
        ];
    }

    public function getManage()
    {
        $sub_modules = [
            ['name' => 'Data Permintaan RBA', 'url' => url([ADMIN, 'rba', 'permintaan_rba']), 'icon' => 'plus-square', 'desc' => 'Permintaan Rencana Bisnis dan Anggaran'],
            ['name' => 'Riwayat RBA', 'url' => url([ADMIN, 'rba', 'riwayat_rba']), 'icon' => 'history', 'desc' => 'Riwayat Rencana Bisnis dan Anggaran'],
          ];
        return $this->draw('manage.html', ['sub_modules' => $sub_modules]);
    }

     public function getPermintaan_RBA()
    {
      $this->_addHeaderFiles();
      $date = date('Y-m-d');
      $rows = $this->db('permintaan_rba')
        ->join('rekening', 'rekening.kd_rek = permintaan_rba.kd_rek')
        ->where('permintaan_rba.tgl_permintaan', $date)
        ->toArray();

        $this->assign['list'] = [];
        if (count($rows)) {
            foreach ($rows as $row) {
                $row = htmlspecialchars_array($row);

                $kd_bangsal = $row['kd_bangsal'];
                $bangsal = $this->db('bangsal')->where('kd_bangsal', $kd_bangsal)->oneArray();

                $row['nm_bangsal'] = $bangsal['nm_bangsal'];

                $this->assign['list'][] = $row;
            }
        }

      return $this->draw('permintaan_rba.html',[
        'permintaan_rba' => $this->assign,
        'nomor' => $this->setNoOrderRba()]);
    }

     public function postPermintaan_RBA()
    {
      $this->_addHeaderFiles();

      if (isset($_POST['submit'])) {
          $date1 = $_POST['periode_rawat_jalan'];
          $date2 = $_POST['periode_rawat_jalan_akhir'];

          if (!empty($date1) && !empty($date2)) {
                $sql = "SELECT * FROM permintaan_rba, rekening
                 WHERE rekening.kd_rek=permintaan_rba.kd_rek
                 AND permintaan_rba.tgl_permintaan BETWEEN '$date1' AND '$date2'";

          $stmt = $this->db()->pdo()->prepare($sql);
          $stmt->execute();
          $rows = $stmt->fetchAll();

          $this->assign['list'] = [];
            foreach ($rows as $row) {
               $kd_bangsal = $row['kd_bangsal'];
               $bangsal = $this->db('bangsal')->where('kd_bangsal', $kd_bangsal)->oneArray();

               $row['nm_bangsal'] = $bangsal['nm_bangsal'];
            
            $this->assign['list'][] = $row;
            }
          } else {
            $this->getPermintaan_RBA();
          }
      }

      return $this->draw('permintaan_rba.html', [
          'permintaan_rba' => $this->assign,
          'nomor' => $this->setNoOrderRba()
      ]);
    }
    
    public function postSavePermintaan()
    {
        if ($_POST['simpan']) {
            $total = floatval($_POST['total']);
            $anggaran = floatval($_POST['saldo_berjalan']);

            if ($total > $anggaran) {
                $this->notify('danger', 'Maaf, total melebihi anggaran.');
            } else {
                $username = $this->core->getUserInfo('fullname', null, true);
                $saved = $this->db('permintaan_rba')->save([
                    'noorder' => $_POST['noorder'],
                    'tgl_permintaan' => $_POST['tgl_permintaan'],
                    'jam_permintaan' => $_POST['jam_permintaan'],
                    'kd_rek' => $_POST['kode_rek2'],
                    'kd_bangsal' => $_POST['kd_bangsal'],
                    'nama_brng' => $_POST['nama_brng'],
                    'jlh_brng' => $_POST['jumlah'],
                    'harga_brng' => $_POST['harga'],
                    'total_brng' => $_POST['total'],
                    'status' => 'Belum',
                    'username' => $username
                ]);

                if ($saved) {
                    $this->notify('success', 'Data Permintaan RBA telah disimpan');
                } else {
                    $this->notify('danger', 'Gagal menyimpan data Permintaan RBA');
                }
            }
        } elseif ($_POST['update']) {
            $noorder = $_POST['noorder'];
            $updated = $this->db('permintaan_rba')
                ->where('noorder', $noorder)
                ->save($_POST);

            if ($updated) {
                $this->notify('success', 'Permintaan RBA telah diubah');
            } else {
                $this->notify('danger', 'Gagal mengubah data Permintaan RBA');
            }
        }

        redirect(url([ADMIN, 'rba', 'permintaan_rba']));
    }

// private function isTotalExceedBudget()
// {
//     $total = floatval($_POST['total']);
//     $anggaran = floatval($_POST['saldo_berjalan']);
//     return $total > $anggaran;
// }

    public function postBelanjaList()
    {

      if(isset($_POST["query"])){
        $output = '';
        $key = "%".$_POST["query"]."%";
        $rows = $this->db('rekening')
         ->select([
                    'kode_rek' => 'rekening.kd_rek',
                    'nama_rek' => 'rekening.nm_rek',
                    'saldo_awal' => 'rekeningtahun.saldo_awal',
                    'saldo_berjalan' => 'rekeningtahun.saldo_berjalan'
                ])
        ->join('rekeningtahun', 'rekening.kd_rek = rekeningtahun.kd_rek')
        ->join('subrekening', 'rekening.kd_rek = subrekening.kd_rek2')
        ->where('subrekening.kd_rek', '52')
        ->where('rekening.level', '1')
        ->where('rekening.balance', 'K')
        ->toArray();
        $output = '';
        if(count($rows)){
          foreach ($rows as $row) {
            $output .= '<li class="list-group-item link-class">'.$row["kode_rek"].': '.$row["nama_rek"].'</li>';
          }
        }
        echo $output;
      }
      exit();
    }

        public function postSubList()
    {
        if(isset($_POST["query"], $_POST["kode_rek"])) {
            $output = '';
            $key = "%" . $_POST["query"] . "%";
            $rek = $_POST["kode_rek"];

            $rows = $this->db('rekening')
                ->select([
                    'kode_rek2' => 'rekening.kd_rek',
                    'nama_rek2' => 'rekening.nm_rek',
                    'saldo_awal' => 'rekeningtahun.saldo_awal',
                    'saldo_berjalan' => 'rekeningtahun.saldo_berjalan'
                ])
                ->join('rekeningtahun', 'rekening.kd_rek = rekeningtahun.kd_rek')
                ->join('subrekening', 'rekening.kd_rek = subrekening.kd_rek2')
                ->where('subrekening.kd_rek', $rek)
                ->where('rekening.level', '1')
                ->where('rekening.balance', 'K')
                ->toArray();

            if (count($rows)) {
                foreach ($rows as $row) {
                    $output .= '<li class="list-group-item link-class">'.$row["kode_rek2"].': '.$row["nama_rek2"].'</li>';
                }
            }
            echo $output;
        }
        exit();
    }

    public function postAnggaran()
    {
        if (isset($_POST["kode_rek2"])) {
            $kode_rek2 = $_POST["kode_rek2"];
            $anggaran = $this->db('rekening')
                ->select(['saldo_berjalan' => 'rekeningtahun.saldo_berjalan'])
                ->join('rekeningtahun', 'rekening.kd_rek = rekeningtahun.kd_rek')
                ->where('rekening.kd_rek', $kode_rek2)
                ->where('rekening.level', '1')
                ->where('rekening.balance', 'K')
                ->oneArray(); 

            echo $anggaran['saldo_berjalan'];
        }
        exit();
    }

    public function setNoOrderRba()
    {
        $date = date('Y-m-d');
        $last_no_order = $this->db()->pdo()->prepare("SELECT ifnull(MAX(CONVERT(RIGHT(noorder,4),signed)),0) FROM permintaan_rba WHERE tgl_permintaan = '$date'");
        $last_no_order->execute();
        $last_no_order = $last_no_order->fetch();
        if(empty($last_no_order[0])) {
          $last_no_order[0] = '0000';
        }
        $next_no_order = sprintf('%04s', ($last_no_order[0] + 1));
        $next_no_order = 'PA'.date('Ymd').''.$next_no_order;

        return $next_no_order;
    }

      public function postRuangList()
  {

    if (isset($_POST["query"])) {
      $output = '';
      $key = "%" . $_POST["query"] . "%";
      $rows = $this->db('bangsal')->like('nm_bangsal', $key)->where('status', '1')->limit(10)->toArray();
      $output = '';
      if (count($rows)) {
        foreach ($rows as $row) {
          $output .= '<li class="list-group-item link-class">' . $row["kd_bangsal"] . ': ' . $row["nm_bangsal"] . '</li>';
        }
      }
      echo $output;
    }

    exit();
  }


      public function getPergeseran_RBA()
    {
      $this->_addHeaderFiles();
      $rows = $this->db('rekeningtahun')
         ->select([
                    'kode_rek' => 'rekening.kd_rek',
                    'nama_rek' => 'rekening.nm_rek',
                    'saldo_awal' => 'rekeningtahun.saldo_awal',
                    'saldo_berjalan' => 'rekeningtahun.saldo_berjalan'
                ])
        ->join('rekening', 'rekening.kd_rek = rekeningtahun.kd_rek')
        ->where('rekening.level', '0')
        ->where('rekening.balance', 'K')
        ->like('rekening.kd_rek', '5%')
        ->toArray();

        $this->assign['list'] = [];
        if (count($rows)) {
            foreach ($rows as $row) {
                $row = htmlspecialchars_array($row);
                $a = $row['saldo_awal'];
                $b = $row['saldo_berjalan'];
                $selisih = $b - $a;
                $row['selisih'] = $selisih;

                $subrekening = $rows = $this->db('rekening')
                ->select([
                    'kode_rek2' => 'rekening.kd_rek',
                    'nama_rek2' => 'rekening.nm_rek',
                    'saldo_awal' => 'rekeningtahun.saldo_awal',
                    'saldo_berjalan' => 'rekeningtahun.saldo_berjalan'
                ])
                ->join('rekeningtahun', 'rekening.kd_rek = rekeningtahun.kd_rek')
                ->join('subrekening', 'rekening.kd_rek = subrekening.kd_rek2')
                ->where('subrekening.kd_rek', '5')
                ->toArray();

                 $row['sub'] = $subrekening;

                

                $this->assign['list'][] = $row;
            }
        }

      return $this->draw('rba.html',['pergeseran_rba' => $this->assign]);
    }

      public function postRekList()
    {

      if(isset($_POST["query"])){
        $output = '';
        $key = "%".$_POST["query"]."%";
        $rows = $this->db('rekening')->like('nm_rek', $key)->limit(10)->toArray();
        $output = '';
        if(count($rows)){
          foreach ($rows as $row) {
            $output .= '<li class="list-group-item link-class">'.$row["kd_rek"].': '.$row["nm_rek"].'</li>';
          }
        }
        echo $output;
      }

      exit();

    }

       public function postRekList2()
    {

      if(isset($_POST["query"])){
        $output = '';
        $key = "%".$_POST["query"]."%";
        $rows = $this->db('rekening')->like('nm_rek', $key)->limit(10)->toArray();
        $output = '';
        if(count($rows)){
          foreach ($rows as $row) {
            $output .= '<li class="list-group-item link-class">'.$row["kd_rek"].': '.$row["nm_rek"].'</li>';
          }
        }
        echo $output;
      }

      exit();

    }


      public function getRiwayat_RBA()
    {
      $this->_addHeaderFiles();
      $date = date('Y-m-d');

      $rows = $this->db('rba_perlap')->where('tgl_pergeseran', $date)->toArray();

        $this->assign['list'] = [];
        if (count($rows)) {
            foreach ($rows as $row) {
                $row = htmlspecialchars_array($row);
                
                $this->assign['list'][] = $row;
            }
        }

      return $this->draw('riwayat_rba.html',['riwayat' => $this->assign]);
    }

    public function postRiwayat_Rba()
    {
      $this->_addHeaderFiles();

      if (isset($_POST['submit'])) {

        $date1 = $_POST['periode_rawat_jalan'];
        $date2 = $_POST['periode_rawat_jalan_akhir'];

        if (!empty($date1) && !empty($date2)) {
          $sql = "SELECT * FROM rba_perlap
          WHERE tgl_pergeseran BETWEEN '$date1' AND '$date2'
          ORDER BY tgl_pergeseran DESC";

          $stmt = $this->db()->pdo()->prepare($sql);
          $stmt->execute();
          $rows = $stmt->fetchAll();

          $this->assign['list'] = [];
          foreach ($rows as $row) {

            $this->assign['list'][] = $row;
          }
        } else {
          $this->getRiwayat_RBA();
        }
      }
      return $this->draw('riwayat_rba.html', ['riwayat' => $this->assign]);
    }

    public function getCSS()
    {
      header('Content-type: text/css');
      echo $this->draw(MODULES . '/rba/css/admin/rba.css');
      exit();
    }

    public function getJavascript()
    {
        header('Content-type: text/javascript');
        echo $this->draw(MODULES . '/rba/js/admin/rba.js');
        exit();
    }

    private function _addHeaderFiles()
    {
        // CSS
        $this->core->addCSS(url('assets/css/jquery-ui.css'));
        $this->core->addCSS(url('assets/jscripts/lightbox/lightbox.min.css'));
        $this->core->addCSS(url('assets/css/bootstrap-datetimepicker.css'));
        $this->core->addCSS(url('assets/css/dataTables.bootstrap.min.css'));

        // JS
        $this->core->addJS(url('assets/jscripts/jquery.dataTables.min.js'), 'footer');
        $this->core->addJS(url('assets/jscripts/dataTables.bootstrap.min.js'), 'footer');
        $this->core->addJS(url('assets/jscripts/jquery-ui.js'), 'footer');
        $this->core->addJS(url('assets/jscripts/lightbox/lightbox.min.js'), 'footer');
        $this->core->addJS(url('assets/jscripts/moment-with-locales.js'));
        $this->core->addJS(url('assets/jscripts/bootstrap-datetimepicker.js'));

        // MODULE SCRIPTS
        $this->core->addCSS(url([ADMIN, 'rba', 'css']));
        $this->core->addJS(url([ADMIN, 'rba', 'javascript']), 'footer');
    }
}