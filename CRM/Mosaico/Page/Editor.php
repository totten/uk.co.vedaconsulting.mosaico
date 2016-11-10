<?php

class CRM_Mosaico_Page_Editor extends CRM_Core_Page {
  const DEFAULT_MODULE_WEIGHT = 200;

  function run() {
    $smarty = CRM_Core_Smarty::singleton();
    $smarty->assign('mosaicoDistUrl', CRM_Mosaico_Utils::getMosaicoDistUrl('relative'));
    $smarty->assign('mosaicoExtUrl', CRM_Core_Resources::singleton()->getUrl('uk.co.vedaconsulting.mosaico'));
    echo $smarty->fetch('CRM/Mosaico/Page/Editor.tpl');
    CRM_Utils_System::civiExit();
  }

}
