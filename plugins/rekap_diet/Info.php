<?php
return [
  'name'          =>  'Data Diet Pasien',
  'description'   =>  'Modul Data Diet Pasien',
  'author'        =>  'Basoro.ID',
  'version'       =>  '1.5',
  'compatibility' =>  '2021',
  'icon'          =>  'book',
  'install'       =>  function () use ($core) {

      $core->db()->pdo()->exec("CREATE TABLE IF NOT EXISTS `detail_beri_diet` (
            `no_rawat` varchar(17) NOT NULL AUTO_INCREMENT PRIMARY KEY,
            `kd_kamar` varchar(15) NOT NULL,
            `tanggal` date DEFAULT NULL,
            `waktu` enum('Pagi','Siang','Sore','Malam', Snack) DEFAULT NULL,
            `kd_diet` varchar(3) NOT NULL 
          ) ENGINE=InnoDB DEFAULT CHARSET=latin1;");

        $core->db()->pdo()->exec("ALTER TABLE `detail_beri_diet`
        ADD UNIQUE KEY `no_rawat` (`no_rawat`),
        ADD UNIQUE KEY `kd_kamar` (`kd_kamar`);");

        $core->db()->pdo()->exec("ALTER TABLE `detail_beri_diet`
        ADD CONSTRAINT `reg_periksa` FOREIGN KEY (`no_rawat`) REFERENCES `reg_periksa` (`no_rawat`),
        ADD CONSTRAINT `kamar` FOREIGN KEY (`kd_kamar`) REFERENCES `kamar` (`kd_kamar`),
        ADD CONSTRAINT `diet` FOREIGN KEY (`kd_diet`) REFERENCES `diet` (`kd_diet`);");
        },
        'uninstall'     =>  function() use($core)
        {
        }
      ];