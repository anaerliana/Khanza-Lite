<?php

namespace Plugins\Presensi_Iht;

use Systems\AdminModule;
use Systems\Lib\QRCode;

class Admin extends AdminModule
{

    public function navigation()
    {
        return [
            'Kelola' => 'manage',
            // 'Presensi Masuk' => 'presensi',
            'Rekap Presensi' => 'rekap_presensi',
            // 'Jadwal Pegawai' => 'jadwal',
            // 'Jadwal Tambahan' => 'jadwal_tambahan'
        ];
    }

    public function getManage()
    {
        $sub_modules = [
            // ['name' => 'Presensi', 'url' => url([ADMIN, 'presensi', 'presensi']), 'icon' => 'cubes', 'desc' => 'Presensi Pegawai'],
            ['name' => 'Rekap Presensi IHT', 'url' => url([ADMIN, 'presensi_iht', 'rekap_presensi']), 'icon' => 'cubes', 'desc' => 'Rekap Presensi Pegawai'],
            // ['name' => 'Jadwal', 'url' => url([ADMIN, 'presensi', 'jadwal']), 'icon' => 'cubes', 'desc' => 'Jadwal Pegawai'],
            // ['name' => 'Jadwal Tambahan', 'url' => url([ADMIN, 'presensi', 'jadwal_tambahan']), 'icon' => 'cubes', 'desc' => 'Jadwal Tambahan Pegawai'],
        ];
        return $this->draw('manage.html', ['sub_modules' => $sub_modules]);
    }

    public function getRekap_Presensi($page = 1)
    {
        $this->_addHeaderFiles();
        // $qrcode = new Generator;
        $this->core->addCSS(url('https://cdn.datatables.net/buttons/2.2.3/css/buttons.dataTables.min.css'));
        $this->core->addJS(url('https://cdn.datatables.net/buttons/2.2.3/js/dataTables.buttons.min.js'), 'footer');
        $this->core->addJS(url('https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js'), 'footer');
        $this->core->addJS(url('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js'), 'footer');
        $this->core->addJS(url('https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js'), 'footer');
        $this->core->addJS(url('https://cdn.datatables.net/buttons/1.3.1/js/buttons.html5.min.js'), 'footer');
        $perpage = '10';
        $phrase = '';
        if (isset($_GET['s']))
            $phrase = $_GET['s'];

        $tgl_kunjungan = date('Y-m-d');

        if (isset($_GET['awal'])) {
            $tgl_kunjungan = $_GET['awal'];
        }

        // list

        $rows = $this->db('presensi_iht')
            ->join('pegawai', 'pegawai.nik = presensi_iht.nip_')
            // ->where('tanggal', $tgl_kunjungan)
            ->toArray();

        $src = '';
        $this->assign['list'] = [];
        if (count($rows)) {
            foreach ($rows as $row) {
                $row = htmlspecialchars_array($row);


                $im = url().'/systems/lib/Barcode.php?codetype=codabar&size=40&text='.$row['nik'];
                $type = pathinfo($im, PATHINFO_EXTENSION);
                $data = file_get_contents($im);

                $imgData = base64_encode($data);
                $src = 'data:image/'.$type.';base64,'.$imgData;

                $this->assign['list'][] = $row;
            }
        }

        return $this->draw('rekap_presensi.html', ['rekap' => $this->assign,'src' => $src]);
    }

    public function getPengaturan_Api()
    {
        $this->_addHeaderFiles();
        $this->assign['title'] = 'Pengaturan Presensi API';

        $this->assign['presensi'] = htmlspecialchars_array($this->settings('presensi'));
        return $this->draw('settings.html', ['settings' => $this->assign]);
    }

    public function postSaveSettings()
    {
        foreach ($_POST['presensi'] as $key => $val) {
            $this->settings('presensi', $key, $val);
        }
        $this->notify('success', 'Pengaturan telah disimpan');
        redirect(url([ADMIN, 'presensi', 'pengaturan_api']));
    }

    public function getValidasi_Presensi($page = 1)
    {
        $this->_addHeaderFiles();

        $perpage = '10';
        $phrase = '';
        if (isset($_GET['s']))
            $phrase = $_GET['s'];

        // $tgl_kunjungan = '2021-01';
        $tgl_kunjungan_akhir = date('Y-m-d');

        if (isset($_GET['awal'])) {
            $tgl_kunjungan = $_GET['awal'];
        }
        if (isset($_GET['akhir'])) {
            $tgl_kunjungan_akhir = $_GET['akhir'];
        }

        $ruang = '';
        if (isset($_GET['ruang'])) {
            $ruang = $_GET['ruang'];
        }

        $username = $this->core->getUserInfo('username', null, true);

        // pagination
        $totalRecords = $this->db('rekap_presensi')
            ->join('pegawai', 'pegawai.id = rekap_presensi.id')
            ->like('jam_datang', '%' . $tgl_kunjungan . '%')
            ->like('bidang', '%' . $ruang . '%')
            ->like('nama', '%' . $phrase . '%')
            ->group('rekap_presensi.id')
            ->asc('jam_datang')
            ->toArray();

        $pagination = new \Systems\Lib\Pagination($page, count($totalRecords), 10, url([ADMIN, 'presensi', 'validasi_presensi', '%d?awal=' . $tgl_kunjungan . '&akhir=' . $tgl_kunjungan_akhir . '&ruang=' . $ruang . '&s=' . $phrase]));
        $this->assign['pagination'] = $pagination->nav('pagination', '5');
        $this->assign['totalRecords'] = $totalRecords;

        // list
        $offset = $pagination->offset();

        $rows = $this->db('rekap_presensi')
            ->select([
                'nama' => 'pegawai.nama',
                'departemen' => 'pegawai.departemen',
                'jbtn' => 'pegawai.jbtn',
                'bidang' => 'pegawai.bidang',
                'id' => 'rekap_presensi.id',
            ])
            ->join('pegawai', 'pegawai.id = rekap_presensi.id')
            ->like('bidang', '%' . $ruang . '%')
            ->like('bidang', '%' . $ruang . '%')
            ->like('nama', '%' . $phrase . '%')
            ->group('rekap_presensi.id')
            ->asc('jam_datang')
            ->offset($offset)
            ->limit($perpage)
            ->toArray();
        $this->assign['list'] = [];
        if (count($rows)) {
            foreach ($rows as $row) {
                $row = htmlspecialchars_array($row);
                $row['editURL'] = url([ADMIN, 'presensi', 'rekappresensibyid', $row['id']]);
                $this->assign['list'][] = $row;
            }
        }
        $this->assign['bidang'] = $this->db('bidang')->toArray();
        $this->assign['title'] = 'Validasi Presensi';

        return $this->draw('validasi.html', ['rekap' => $this->assign]);
    }

    public function getJavascript()
    {
        header('Content-type: text/javascript');
        echo $this->draw(MODULES . '/presensi_iht/js/admin/app.js');
        exit();
    }

    private function _addHeaderFiles()
    {

        $this->core->addCSS(url('assets/css/dataTables.bootstrap.min.css'));

        // CSS
        $this->core->addCSS(url('assets/css/jquery-ui.css'));
        $this->core->addCSS(url('assets/jscripts/lightbox/lightbox.min.css'));
        $this->core->addCSS(url('assets/css/bootstrap-datetimepicker.css'));

        $this->core->addJS(url('assets/jscripts/jquery.dataTables.min.js'), 'footer');
        $this->core->addJS(url('assets/jscripts/dataTables.bootstrap.min.js'), 'footer');
        // JS
        $this->core->addJS(url('assets/jscripts/jquery-ui.js'), 'footer');
        $this->core->addJS(url('assets/jscripts/lightbox/lightbox.min.js'), 'footer');
        $this->core->addJS(url('assets/jscripts/moment-with-locales.js'));
        $this->core->addJS(url('assets/jscripts/bootstrap-datetimepicker.js'));

        // MODULE SCRIPTS
        $this->core->addJS(url([ADMIN, 'presensi_iht', 'javascript']), 'footer');
    }
}
