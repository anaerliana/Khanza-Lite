<?php
namespace Plugins\Phpoffice;

use Systems\AdminModule;

class Admin extends AdminModule
{

  public function navigation()
  {
    return [
      'Kelola' => 'manage'
    ];
  }

  public function getManage()
  {
    return $this->draw('manage.html');
  }

  
  public function getCetak()
  {
    $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor(MODULES.'/phpoffice/template.docx');

    $templateProcessor->setValues([
        'nama' => 'Eko Yudhi Prastowo',
        'nip' => '12345678 123456 1 123'
    ]);

    header("Content-Disposition: attachment; filename=templatenya.docx");

    $templateProcessor->saveAs('php://output');
    exit();
  }

}

?>
