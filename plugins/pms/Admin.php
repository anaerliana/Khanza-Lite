<?php
    namespace Plugins\Pms;

    use Systems\AdminModule;

    class Admin extends AdminModule
    {

        public function navigation()
        {
            return [
                'manage' => 'manage',
            ];
       }
       public function getManage()
       {
        $this->_addHeaderFiles();
        $pms = $this->db('mlite_pms')->toArray();
        //var_dump($pms);
        return $this->draw('manage.html', ['pms' => $pms]);
       }

       public function getAdd()
       {
        $this->_addHeaderFiles();
        return $this->draw('form.html');
       }

       public function postMlitePmsSave()
    {
      $id = $_POST['id'];
      $errors = 0;
      
      $_POST['tanggal_mulai'] ="";
      $_POST['tanggal_selesai'] ="";
      $_POST['jam_mulai'] ="";
      $_POST['jam_selesai'] ="";
      $_POST['order_by'] ="";
      $_POST['finish_by'] ="";

      if (!$id) {
        $location = url([ADMIN,'pms','manage']);
        $_POST['order_by'] = $this->core->getUserInfo('username', null, true);
        $_POST['tanggal_mulai'] = date('Y-m-d');
        $_POST['jam_mulai'] = date('H:i:s');



      } else {
        $location = url([ADMIN,'pms','manage']);
        $_POST['finish_by'] = $this->core->getUserInfo('username', null, true);
        $_POST['tanggal_selesai'] = date('Y-m-d');
        $_POST['jam_selesai'] = date('H:i:s');
      }

      
      if ($_POST['nama_kegiatan'] == "") {
            $this->notify('failure', 'Isian kosong');
            redirect($location, $_POST);
        }
         

      if (!$errors) {
        unset($_POST['save']);
        if (!$id) {
          $query = $this->db('mlite_pms')->save([
            'nomor_kegiatan' => $_POST['nomor_kegiatan'],
            'order_by' => $_POST['order_by'],
            'finish_by' => $_POST['finish_by'],
            'nama_kegiatan' => $_POST['nama_kegiatan'],
            'detail_kegiatan' => $_POST['detail_kegiatan'],
            'tanggal_mulai' => $_POST['tanggal_mulai'],
            'tanggal_selesai' => $_POST['tanggal_selesai'],
            'jam_mulai' => $_POST['jam_mulai'],
            'jam_selesai' => $_POST['jam_selesai'],
            'status' => $_POST['status'],
          ]);
        } else {
          $query = $this->db('mlite_pms')->where('id',$id)->save([
            'nomor_kegiatan' => $_POST['nomor_kegiatan'],
            'order_by' => $_POST['order_by'],
            'finish_by' => $_POST['finish_by'],
            'nama_kegiatan' => $_POST['nama_kegiatan'],
            'detail_kegiatan' => $_POST['detail_kegiatan'],
            'tanggal_mulai' => $_POST['tanggal_mulai'],
            'tanggal_selesai' => $_POST['tanggal_selesai'],
            'jam_mulai' => $_POST['jam_mulai'],
            'jam_selesai' => $_POST['jam_selesai'],
            'status' => $_POST['status'],
          ]);
        }
        if ($query) {
          $this->notify('success','Berhasil Simpan');
        } else {
          $this->notify('failure','Gagal Simpan');
        }
        redirect($location);
        echo json_encode($query);
      }
      redirect($location, $_POST);
    }

    public function postNomorKegiatan()
    {
        $pms = $this->db('mlite_pms')->select(['nomor_kegiatan' => 'ifnull(MAX(CONVERT(RIGHT(nomor_kegiatan,6),signed)),0)'])->where('tanggal_mulai', date('Y-m-d'))->oneArray();
        if(empty($pms['nomor_kegiatan'])) {
            $pms['nomor_kegiatan'] = '000000';  
        }
        $pms = sprintf('%06s', ($pms['nomor_kegiatan'] + 1));
        $pms = date('Ymd').$pms;
        echo $pms;
        exit();
    }

    public function getCetakSurat($nomor_kegiatan)
    {
        $kegiatan = $this->db('mlite_pms')->where('nomor_kegiatan', $nomor_kegiatan)->oneArray();
        return $this->draw('cetaksurat.html', ['kegiatan' => $kegiatan]);
    }

    public function getView($nomor_kegiatan)
    {
        $kegiatan = $this->db('mlite_pms')->where('nomor_kegiatan', $nomor_kegiatan)->oneArray();
        return $this->draw('view.html', ['kegiatan' => $kegiatan]);
    }


    public function getCetak ()
  {
    $kegiatan = $this->db('mlite_pms')->toArray();
    return $this->draw('cetak.html', ['kegiatan' => $kegiatan]);
  }

    public function getCSS()
          {
              header('Content-type: text/css');
              echo $this->draw(MODULES.'/pms/css/admin/pms.css');
              exit();
          }

    public function getEdit($nomor_kegiatan)
    {
        $kegiatan = $this->db('mlite_pms')->where('nomor_kegiatan', $nomor_kegiatan)->oneArray();
        return $this->draw('editform.html', ['kegiatan' => $kegiatan]);
    }
    public function getDelete($nomor_kegiatan)
    {
        $kegiatan = $this->db('mlite_pms')->where('nomor_kegiatan', $nomor_kegiatan)->delete();
        $location = url([ADMIN,'pms','manage']);
        redirect($location);

    }

    public function getJavascript()
    {
        header('Content-type: text/javascript');
        echo $this->draw(MODULES.'/pms/js/admin/pms.js');
        exit();
    }

       private function _addHeaderFiles()
    {
        $this->core->addCSS(url('assets/css/dataTables.bootstrap.min.css'));
        $this->core->addJS(url('assets/jscripts/jquery.dataTables.min.js'));
        $this->core->addJS(url('assets/jscripts/dataTables.bootstrap.min.js'));
        $this->core->addCSS(url('assets/css/bootstrap-datetimepicker.css'));
        $this->core->addJS(url('assets/jscripts/moment-with-locales.js'));
        $this->core->addJS(url('assets/jscripts/bootstrap-datetimepicker.js'));
        $this->core->addJS(url([ADMIN, 'pms', 'javascript']), 'footer');
    }
   }

?>

