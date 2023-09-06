<?php
    namespace Plugins\Aset;

    use Systems\AdminModule;

    class Admin extends AdminModule
    {

        public function navigation()
        {
            return [
                'Manage' => 'manage',
                'Data Barang' => 'barang',
                'Inventaris Aset' => 'inventarisaset',
                'Penerimaan Aset' => 'penerimaanaset',
                'Penyerahan Aset' => 'penyerahanbarang',
            ];
        }

        public function getManage()
        {
          $sub_modules = [
            ['name' => 'Data Barang', 'url' => url([ADMIN, 'aset', 'barang']), 'icon' => 'cubes', 'desc' => 'Data barang - barang'],
            ['name' => 'Inventaris Aset', 'url' => url([ADMIN, 'aset', 'inventarisaset']), 'icon' => 'cubes', 'desc' => 'Data inventarisasi aset'],
            ['name' => 'Penerimaan Aset', 'url' => url([ADMIN, 'aset', 'penerimaanaset']), 'icon' => 'cubes', 'desc' => 'Penerimaan aset baru'],
            ['name' => 'Penyerahan Aset', 'url' => url([ADMIN, 'aset', 'penyerahanbarang']), 'icon' => 'cubes', 'desc' => 'Penyerahan aset yang tersedia'],
          ];
          return $this->draw('manage.html', ['sub_modules' => $sub_modules]);
        }

        public function getBarang()
        {
          $this->_addHeaderFiles();
          $this->assign['barang'] = $this->db('inventaris_barang')->toArray();
          return $this->draw('barang.html',['barang' => $this->assign]);
        }

        public function getInventarisAset()
        {
          $this->_addHeaderFiles();
          $this->assign['inventaris'] = $this->db('inventaris')->join('inventaris_barang','inventaris.kode_barang = inventaris_barang.kode_barang')->toArray();
          return $this->draw('inventarisaset.html',['inventaris' => $this->assign]);
        }

        public function getPenerimaanAset()
        {
          $this->_addHeaderFiles();
          return $this->draw('penerimaanaset.html');
        }

        public function getPenyerahanBarang()
        {
          $this->assign['pinjam'] = [];
          $pinjam = $this->db('inventaris_peminjaman')->toArray();
          foreach ($pinjam as $value) {
            $value = htmlspecialchars_array($value);
            $value['barang_nama'] = $this->db('inventaris')->join('inventaris_barang','inventaris.kode_barang = inventaris_barang.kode_barang')->where('no_inventaris',$value['no_inventaris'])->oneArray();
            $value['peminjam_nama'] = $this->db('pegawai')->select(['nama' => 'pegawai.nama'])->where('nik',$value['nip'])->oneArray();
            $this->assign['pinjam'][] = $value;
          }
          return $this->draw('penyerahanbarang.html',['penyerahan' => $this->assign]);
        }

        public function postPenerimaanAsetSave()
        {
          $simpanAset = [
            'id' => 'id',
            'no_faktur' => $_POST['faktur'],
            'tgl_faktur' => $_POST['tgl_faktur'],
            'kode_barang' => 'kodebarang',
            'jumlah' => $_POST['jumlah'],
            'satuan' => $_POST['satuan'],
            'harga' => $_POST['harga']
          ];
          echo json_encode($simpanAset);
        }

        private function _addHeaderFiles()
        {
            $this->core->addCSS(url('assets/css/dataTables.bootstrap.min.css'));
            $this->core->addJS(url('assets/jscripts/jquery.dataTables.min.js'));
            $this->core->addJS(url('assets/jscripts/dataTables.bootstrap.min.js'));
            $this->core->addJS(url('assets/jscripts/lightbox/lightbox.min.js'));
            $this->core->addCSS(url('assets/jscripts/lightbox/lightbox.min.css'));
            $this->core->addCSS(url('assets/css/bootstrap-datetimepicker.css'));
            $this->core->addJS(url('assets/jscripts/moment-with-locales.js'));
            $this->core->addJS(url('assets/jscripts/bootstrap-datetimepicker.js'));
            $this->core->addJS(url([ADMIN, 'data_sirs', 'javascript']), 'footer');
        }
    }

?>
