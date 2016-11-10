<?php

class CRM_Mosaico_Page_Editor extends CRM_Core_Page {
  const DEFAULT_MODULE_WEIGHT = 200;

  public function run() {
    $smarty = CRM_Core_Smarty::singleton();
    $smarty->assign('mosaicoDistUrl', CRM_Mosaico_Utils::getMosaicoDistUrl('relative'));
    $smarty->assign('mosaicoExtUrl', CRM_Core_Resources::singleton()->getUrl('uk.co.vedaconsulting.mosaico'));
    $smarty->assign('msgTplURL', CRM_Utils_System::url('civicrm/admin/messageTemplates', 'reset=1&activeTab=mosaico'));
    $smarty->assign_by_ref('mosaicoConfig', json_encode($this->createMosaicoConfig()));
    echo $smarty->fetch('CRM/Mosaico/Page/Editor.tpl');
    CRM_Utils_System::civiExit();
  }

  /**
   * Generate the configuration options for `Mosaico.init()`.
   *
   * @return array
   */
  private function createMosaicoConfig() {
    $mailTokens = civicrm_api3('Mailing', 'gettokens', array(
      'entity' => array('contact', 'mailing'),
      'sequential' => 1,
    ));

    return array(
      'imgProcessorBackend' => $this->getUrl('civicrm/mosaico/img', NULL, TRUE),
      'emailProcessorBackend' => $this->getUrl('civicrm/mosaico/dl', NULL, FALSE),
      'titleToken' => "MOSAICO Responsive Email Designer",
      'tinymceConfig' => array(
        'civicrmtoken' => array(
          'tokens' => $mailTokens['values'],
          'hotlist' => array(
            '{contact.contact_id}',
            '{contact.display_name}',
            '{contact.first_name}',
            '{contact.last_name}',
          ),
        ),
      ),
      'fileuploadConfig' => array(
        'url' => $this->getUrl('civicrm/mosaico/upload', NULL, FALSE),
        // messages??
      ),
    );
  }

  private function getUrl($path, $query, $frontend) {
    // This function shouldn't really exist, but it's tiring to set `$htmlize`
    // to false every.single.time we need a URL.
    return CRM_Utils_System::url($path, $query, FALSE, NULL, FALSE, $frontend);
  }

}
