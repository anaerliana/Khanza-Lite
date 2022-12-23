<?php

namespace Plugins\Triase_Igd;

use Systems\SiteModule;

class Site extends SiteModule
{

    public function routes()
    {
        $this->route('triase_igd/display', 'getDisplay');
    }

    public function getDisplay()
    {
        $title = 'Display Antrian Poliklinik';
        $logo  = $this->settings->get('settings.logo');
        $display = $this->_getDisplay();

        $_username = $this->core->getUserInfo('fullname', null, true);
        $__username = $this->core->getUserInfo('username');
        if ($this->core->getUserInfo('username') !== '') {
            $__username = 'Tamu';
        }
        $tanggal       = getDayIndonesia(date('Y-m-d')) . ', ' . dateIndonesia(date('Y-m-d'));
        $username      = !empty($_username) ? $_username : $__username;

        $content = $this->draw('display.html', [
            'title' => $title,
            'logo' => $logo,
            'powered' => 'Powered by <a href="https://basoro.org/">KhanzaLITE</a>',
            'username' => $username,
            'tanggal' => $tanggal,
            'running_text' => $this->settings->get('anjungan.text_poli'),
            'display' => $display
        ]);

        $assign = [
            'title' => $this->settings->get('settings.nama_instansi'),
            'desc' => $this->settings->get('settings.alamat'),
            'content' => $content
        ];

        $this->setTemplate("canvas.html");

        $this->tpl->set('page', ['title' => $assign['title'], 'desc' => $assign['desc'], 'content' => $assign['content']]);
    }

    public function _getDisplay()
    {
        $rows = $this->db('reg_periksa')->join('pasien', 'reg_periksa.no_rkm_medis=pasien.no_rkm_medis')->where('kd_poli', 'IGDK')->where('tgl_registrasi','2021-11-22')->where('stts','Belum')->toArray();
        $result = [];
        foreach ($rows as $row) {
            $triage = $this->db('triase_igd')->where('no_rawat', $row['no_rawat'])->where('status', 1)->oneArray();
            $row['warna'] = $triage['warna_triase'];
            $row['bed'] = $triage['bed'];
            $row['nm_pasien'] = substr($row['nm_pasien'],0,4).'***';
            $result[] = $row;
        }
        return $result;
    }
}
