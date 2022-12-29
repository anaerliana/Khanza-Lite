<?php

namespace Plugins\Triase_Igd;

use Systems\AdminModule;
use Systems\Lib\Fpdf\PDF_MC_Table;

class Admin extends AdminModule
{

    public function navigation()
    {
        return [
            'Kelola' => 'manage',
        ];
    }

    public function getManage()
    {
        $sub_modules = [
            ['name' => 'Triase', 'url' => url([ADMIN, 'triase_igd', 'triase']), 'icon' => 'cubes', 'desc' => 'Triase IGD'],
        ];
        return $this->draw('manage.html', ['sub_modules' => $sub_modules]);
    }

    public function getTriase($page = 1)
    {
        $this->_addHeaderFiles();
        $rows = $this->db('reg_periksa')->join('pasien', 'reg_periksa.no_rkm_medis=pasien.no_rkm_medis')->where('kd_poli', 'IGDK')->where('tgl_registrasi', date('Y-m-d'))->where('stts','Belum')->toArray();
        $this->assign['list'] = [];
        foreach ($rows as $row) {
            $row['norawatconv'] = convertNorawat($row['no_rawat']);
            $triage = $this->db('triase_igd')->where('no_rawat',$row['no_rawat'])->where('status',1)->oneArray();
            $row['warna'] = $triage['warna_triase'];
            $row['bed'] = $triage['bed'];
            $this->assign['list'][] = $row;
        }
        return $this->draw('index.html', ['triase' => $this->assign]);
    }

    public function getPilih_Triase($id_input)
    {
        $id_input = revertNorawat($id_input);
        $this->tpl->set('id_input', $id_input);
        $cek = $this->db('reg_periksa')->join('pasien','reg_periksa.no_rkm_medis = pasien.no_rkm_medis')->where('no_rawat',$id_input)->oneArray();
        $this->tpl->set('nama', $cek['nm_pasien']);
        echo $this->draw('triase_modal.html');
        exit();
    }

    public function postInputTriase()
    {
        $cekTriage = $this->db('triase_igd')->where('no_rawat',$_POST['no_rawat'])->where('status',1)->oneArray();
        if ($cekTriage) {
            $updateTriage = $this->db('triase_igd')->where('id',$cekTriage['id'])->save(['status' => 0]);
        }
        $save = $this->db('triase_igd')->save([
            'no_rawat' => $_POST['no_rawat'],
            'warna_triase' => $_POST['triase'],
            'bed' => $_POST['bed'],
            'status' => 1,
        ]);
        $check = $this->db('triase_igd')->lastInsertId();
        if ($save == $check) {
            $location = url([ADMIN, 'triase_igd', 'triase']);
            redirect($location);
        }
        // exit();
    }

    public function getJavascript()
    {
        header('Content-type: text/javascript');
        echo $this->draw(MODULES . '/triase_igd/js/admin/app.js');
        exit();
    }

    private function _addHeaderFiles()
    {
        $this->core->addCSS(url('assets/css/dataTables.bootstrap.min.css'));
        $this->core->addCSS(url('assets/jscripts/lightbox/lightbox.min.css'));
        $this->core->addCSS(url('assets/css/bootstrap-datetimepicker.css'));

        $this->core->addJS(url('assets/jscripts/jquery.dataTables.min.js'));
        $this->core->addJS(url('assets/jscripts/dataTables.bootstrap.min.js'));
        $this->core->addJS(url('assets/jscripts/lightbox/lightbox.min.js'));
        $this->core->addJS(url('assets/jscripts/moment-with-locales.js'));
        $this->core->addJS(url('assets/jscripts/bootstrap-datetimepicker.js'));

        // MODULE SCRIPTS
        $this->core->addJS(url([ADMIN, 'triase_igd', 'javascript']), 'footer');
    }
}
