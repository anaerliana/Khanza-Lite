<?php
    namespace Plugins\Laporan_Bundles;

    use Systems\AdminModule;

    class Admin extends AdminModule
    {

        public function navigation()
        {
            return [
                'Manage' => 'manage',
                'Laporan Bundles' => 'laporanbundles',
            ];
        }

        public function getManage()
        {
          $sub_modules = [
            ['name' => 'Laporan Bundles', 'url' => url([ADMIN, 'laporan_bundles', 'laporanbundles']), 'icon' => 'list-alt', 'desc' => 'Laporan Bundles Hais'],
          ];
          return $this->draw('manage.html', ['sub_modules' => $sub_modules]);
        }

        public function getLaporanBundles()
        {
          //   $this->_addHeaderFiles();
          //   $id = revertNorawat($no_rawat);
           $i = 1;
          $rows = $this->db('bundles_hais')
          
              // ->where('tanggal', $id)
              ->toArray();
              $result = [];
                foreach ($rows as $row) {
                  $row['nomor'] = $i++;
                  
              // $row['editURL'] = url([ADMIN, 'users', 'edit', $row['id']]);
              // $row['delURL']  = url([ADMIN, 'users', 'delete', $row['id']]);
              $result[] = $row;
          }
          $this->core->addCSS(url('assets/css/dataTables.bootstrap.min.css'));
          $this->core->addCSS(url([ADMIN, 'laporan_bundles', 'css']));
          $this->core->addJS(url('assets/jscripts/jquery.dataTables.min.js'));
          $this->core->addJS(url('assets/jscripts/dataTables.bootstrap.min.js'));
          return $this->draw('lapbundles.html', [ 'lapbundles' => $result]);
       }

        public function getCSS()
        {
            header('Content-type: text/css');
            echo $this->draw(MODULES.'/laporan_bundles/css/admin/laporan_bundles.css');
            exit();
        }

    }

    // public function getJavascript()
    //     {
    //         header('Content-type: text/javascript');
    //         echo $this->draw(MODULES.'/laporan_bundles/js/admin/laporan_bundles.js');
    //         exit();
    //     }

    //     private function _addHeaderFiles()
    //     {
    //         // CSS
    //         $this->core->addCSS(url('assets/css/dataTables.bootstrap.min.css'));
    //         $this->core->addJS(url('assets/jscripts/jquery.dataTables.min.js'));
    //         $this->core->addJS(url('assets/jscripts/dataTables.bootstrap.min.js'));
    //         $this->core->addCSS(url('assets/css/bootstrap-datetimepicker.css'));
    //         $this->core->addJS(url('assets/jscripts/moment-with-locales.js'));
    //         $this->core->addJS(url('assets/jscripts/bootstrap-datetimepicker.js'));

    //         // MODULE SCRIPTS
    //         $this->core->addCSS(url([ADMIN, 'laporan_bundles', 'css']));
    //         $this->core->addJS(url([ADMIN, 'laporan_bundles', 'javascript']), 'footer');
    //     }