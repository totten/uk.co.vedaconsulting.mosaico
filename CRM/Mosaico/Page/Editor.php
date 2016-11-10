<?php

class CRM_Mosaico_Page_Editor extends CRM_Core_Page {
  const DEFAULT_MODULE_WEIGHT = 200;

  function run() {
    $mailTokens = civicrm_api3('Mailing', 'gettokens', array(
      'entity' => array('contact', 'mailing'),
      'sequential' => 1,
    ));
    $smarty = CRM_Core_Smarty::singleton();
    $smarty->assign('mosaicoDistUrl', CRM_Mosaico_Utils::getMosaicoDistUrl('relative'));
    $smarty->assign('mosaicoExtUrl', CRM_Core_Resources::singleton()->getUrl('uk.co.vedaconsulting.mosaico'));
    $smarty->assign_by_ref('civicrmTokens', json_encode(array(
      'tokens' => $mailTokens['values'],
      'hotlist' => array('{contact.contact_id}', '{contact.display_name}', '{contact.first_name}', '{contact.last_name}'),
      'msgTplURL' => CRM_Utils_System::url('civicrm/admin/messageTemplates', 'reset=1&activeTab=mosaico'),
    )));
    echo $smarty->fetch('CRM/Mosaico/Page/Editor.tpl');
    CRM_Utils_System::civiExit();
  }

}
