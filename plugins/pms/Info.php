<?php

    return [
        'name'          =>  'Project Manager System',
        'description'   =>  'Data Sistem Pengelolaan Kegiatan KhanzaLITE',
        'author'        =>  'Muhammad Nahziannor',
        'version'       =>  '1.0',
        'compatibility' =>  '2022',
        'icon'          =>  'bolt',
        'install'       =>  function () use ($core) {

         $core->db()->pdo()->exec("CREATE TABLE IF NOT EXISTS `mlite_pms` (
            `id` int(15) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `nomor_kegiatan` varchar(15) NOT NULL,
            `order_by` varchar(50) NOT NULL,
            `finish_by` varchar(50) NOT NULL,
            `nama_kegiatan` varchar(50) NOT NULL,
            `detail_kegiatan` varchar(50) NOT NULL,
            `tanggal_mulai` varchar(50) NOT NULL,
            `tanggal_selesai` varchar(50) NOT NULL,
            `jam_mulai` varchar(50) NOT NULL,
            `jam_selesai` varchar(50) NOT NULL,
            `status` varchar(30) NOT NULL 
          ) ENGINE=InnoDB DEFAULT CHARSET=utf8;");
        },
        'uninstall'     =>  function() use($core)
        {
        }
    ];
