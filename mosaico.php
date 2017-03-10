<?php

require_once 'mosaico.civix.php';

/**
 * Implements hook_civicrm_config().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_config
 */
function mosaico_civicrm_config(&$config) {
  _mosaico_civix_civicrm_config($config);
}

/**
 * Implements hook_civicrm_xmlMenu().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_xmlMenu
 */
function mosaico_civicrm_xmlMenu(&$files) {
  _mosaico_civix_civicrm_xmlMenu($files);
}

/**
 * Implements hook_civicrm_install().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_install
 */
function mosaico_civicrm_install() {
  _mosaico_civix_civicrm_install();

  $schema = new CRM_Logging_Schema();
  $schema->fixSchemaDifferences();
}

/**
 * Implements hook_civicrm_postInstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_postInstall
 */
function mosaico_civicrm_postInstall() {
  _mosaico_civix_civicrm_postInstall();
}

/**
 * Implements hook_civicrm_uninstall().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_uninstall
 */
function mosaico_civicrm_uninstall() {
  _mosaico_civix_civicrm_uninstall();

  $schema = new CRM_Logging_Schema();
  $schema->fixSchemaDifferences();
}

/**
 * Implements hook_civicrm_enable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_enable
 */
function mosaico_civicrm_enable() {
  _mosaico_civix_civicrm_enable();
}

/**
 * Implements hook_civicrm_disable().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_disable
 */
function mosaico_civicrm_disable() {
  _mosaico_civix_civicrm_disable();
}

/**
 * Implements hook_civicrm_upgrade().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_upgrade
 */
function mosaico_civicrm_upgrade($op, CRM_Queue_Queue $queue = NULL) {
  return _mosaico_civix_civicrm_upgrade($op, $queue);
}

/**
 * Implements hook_civicrm_managed().
 *
 * Generate a list of entities to create/deactivate/delete when this module
 * is installed, disabled, uninstalled.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_managed
 */
function mosaico_civicrm_managed(&$entities) {
  _mosaico_civix_civicrm_managed($entities);
}

/**
 * Implements hook_civicrm_caseTypes().
 *
 * Generate a list of case-types
 *
 * Note: This hook only runs in CiviCRM 4.4+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function mosaico_civicrm_caseTypes(&$caseTypes) {
  _mosaico_civix_civicrm_caseTypes($caseTypes);
}

/**
 * Implements hook_civicrm_angularModules().
 *
 * Generate a list of Angular modules.
 *
 * Note: This hook only runs in CiviCRM 4.5+. It may
 * use features only available in v4.6+.
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_caseTypes
 */
function mosaico_civicrm_angularModules(&$angularModules) {
  $canRead = Civi::service('civi_api_kernel')->runAuthorize(
    'MosaicoTemplate', 'get', array('version' => 3, 'check_permissions' => 1));
  if (!$canRead) {
    return;
  }
  _mosaico_civix_civicrm_angularModules($angularModules);
}

/**
 * Implements hook_civicrm_alterSettingsFolders().
 *
 * @link http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_alterSettingsFolders
 */
function mosaico_civicrm_alterSettingsFolders(&$metaDataFolders = NULL) {
  _mosaico_civix_civicrm_alterSettingsFolders($metaDataFolders);
}

function mosaico_civicrm_navigationMenu(&$params) {
  _mosaico_civix_insert_navigation_menu($params, 'Mailings', array(
    'label' => ts('Mosaico Templates', array('domain' => 'org.civicrm.styleguide')),
    'name' => 'mosaico_templates',
    'permission' => 'edit message templates',
    'child' => array(),
    'operator' => 'AND',
    'separator' => 0,
    'url' => CRM_Utils_System::url('civicrm/a/', NULL, TRUE, '/mosaico-template'),
  ));

  _mosaico_civix_navigationMenu($params);
}

/**
 * Implements hook_civicrm_alterAPIPermissions().
 *
 * @link https://docs.civicrm.org/dev/en/master/hooks/hook_civicrm_alterAPIPermissions/
 */
function mosaico_civicrm_alterAPIPermissions($entity, $action, &$params, &$permissions) {
  if (empty($permissions['message_template']['get']) || empty($permissions['message_template']['create'])) {
    throw new CRM_Core_Exception("Cannot define Mosaico permissio model. Core permissions for message_template are unavailable.");
  }

  $permissions['mosaico_base_template'] = $permissions['message_template'];
  $permissions['mosaico_template'] = $permissions['message_template'];
  $permissions['mosaico_template']['clone'] = $permissions['message_template']['create'];
}

/**
 * Implements hook_civicrm_check().
 */
function mosaico_civicrm_check(&$messages) {
  //Make sure the ImageMagick library is loaded.
  if (!(extension_loaded('imagick') || class_exists("Imagick"))) {
    $messages[] = new CRM_Utils_Check_Message(
      'mosaico_imagick',
      ts('the ImageMagick library is not installed.  The Email Template Builder extension will not work without it.'),
      ts('ImageMagick not installed'),
      \Psr\Log\LogLevel::CRITICAL
    );
  }
  if (!extension_loaded('fileinfo')) {
    $messages[] = new CRM_Utils_Check_Message('mosaico_fileinfo', ts('May experience mosaico template or thumbnail loading issues (404 errors).'), ts('PHP extension Fileinfo not loaded or enabled'));
  }
  if (CRM_Mailing_Info::workflowEnabled()) {
    $messages[] = new CRM_Utils_Check_Message(
      'mosaico_workflow',
      ts('CiviMail is configured to support advanced workflows. This is currently incompatible with the Mosaico mailer. Navigate to "Administer => CiviMail => CiviMail Component Settings" to disable it.'),
      ts('Advanced CiviMail workflows unsupported'),
      \Psr\Log\LogLevel::CRITICAL
    );
  }
  if (!CRM_Extension_System::singleton()->getMapper()->isActiveModule('shoreditch') && !CRM_Extension_System::singleton()->getMapper()->isActiveModule('bootstrapcivicrm')) {
    $messages[] = new CRM_Utils_Check_Message(
      'mosaico_bootstrap',
      ts('Mosaico uses Bootstrap CSS. Please install the extension "org.civicrm.shoreditch".'),
      ts('Bootstrap required'),
      \Psr\Log\LogLevel::CRITICAL
    );
  }
  if (!CRM_Extension_System::singleton()->getMapper()->isActiveModule('flexmailer')) {
    $messages[] = new CRM_Utils_Check_Message(
      'mosaico_flexmailer',
      ts('Mosaico uses FlexMailer for delivery. Please install the extension "org.civicrm.flexmailer".'),
      ts('FlexMailer required'),
      \Psr\Log\LogLevel::CRITICAL
    );
  }
  if (!empty($mConfig['BASE_URL'])) {
    // detect incorrect image upload url. (Note: Since v4.4.4, CRM_Utils_Check_Security has installed index.html placeholder.)
    $handle = curl_init($mConfig['BASE_URL'] . '/index.html');
    curl_setopt($handle, CURLOPT_RETURNTRANSFER, TRUE);
    $response = curl_exec($handle);
    $httpCode = curl_getinfo($handle, CURLINFO_HTTP_CODE);
    if ($httpCode == 404) {
      $messages[] = new CRM_Utils_Check_Message('mosaico_base_url', ts('BASE_URL seems incorrect - %1. Images when uploaded, may not appear correctly as thumbnails. Make sure "Image Upload URL" is configured correctly with Administer » System Settings » Resouce URLs.', array(1 => $mConfig['BASE_URL'])), ts('Incorrect image upload url'));
    }
  }
  $extDirName = basename(__DIR__);
  if ($extDirName != 'uk.co.vedaconsulting.mosaico') {
    $messages[] = new CRM_Utils_Check_Message(
      'mosaico_extdirname',
      ts("We expect extension directory name to be '%1' instead of '%2'. Images and icons may not load correctly.", array(1 => 'uk.co.vedaconsulting.mosaico', 2 => $extDirName)),
      ts('Installed extension directory name not suitable')
    );
  }

  _mosaico_civicrm_check_dirs($messages);
}

function _mosaico_civicrm_check_dirs(&$messages) {
  $mConfig = CRM_Mosaico_Utils::getConfig();

  // Check if UPLOADS directory exists and create it if it doesn't
  if (!is_dir($mConfig['BASE_DIR'] . $mConfig['UPLOADS_DIR'])) {
    if (!mkdir($mConfig['BASE_DIR'] . $mConfig['UPLOADS_DIR'], 0775, TRUE)) {
      $messages[] = new CRM_Utils_Check_Message('mosaico_uploads_dir', ts('%1 not writable or configured.', array(1 => $mConfig['BASE_DIR'] . $mConfig['UPLOADS_DIR'])), ts('UPLOADS_DIR not writable or configured'));
    }
  }
  elseif (!is_writable($mConfig['BASE_DIR'] . $mConfig['UPLOADS_DIR'])) {
    $messages[] = new CRM_Utils_Check_Message('mosaico_uploads_dir', ts('%1 not writable or configured.', array(1 => $mConfig['BASE_DIR'] . $mConfig['UPLOADS_DIR'])), ts('UPLOADS_DIR not writable or configured'));
  }

  // Check if uploads/STATIC directory exists and create it if it doesn't
  if (!is_dir($mConfig['BASE_DIR'] . $mConfig['STATIC_DIR'])) {
    if (!mkdir($mConfig['BASE_DIR'] . $mConfig['STATIC_DIR'], 0775, TRUE)) {
      $messages[] = new CRM_Utils_Check_Message('mosaico_static_dir', ts('%1 not writable or configured.', array(1 => $mConfig['BASE_DIR'] . $mConfig['STATIC_DIR'])), ts('STATIC_DIR not writable or configured'));
    }
  }
  elseif (!is_writable($mConfig['BASE_DIR'] . $mConfig['STATIC_DIR'])) {
    $messages[] = new CRM_Utils_Check_Message('mosaico_static_dir', ts('%1 not writable or configured.', array(1 => $mConfig['BASE_DIR'] . $mConfig['STATIC_DIR'])), ts('STATIC_DIR not writable or configured'));
  }

  // Check if uploads/THUMBNAILS directory exists and create it if it doesn't
  if (!is_dir($mConfig['BASE_DIR'] . $mConfig['THUMBNAILS_DIR'])) {
    if (!mkdir($mConfig['BASE_DIR'] . $mConfig['THUMBNAILS_DIR'], 0775, TRUE)) {
      $messages[] = new CRM_Utils_Check_Message('mosaico_thumbnails_dir', ts('%1 not writable or configured.', array(1 => $mConfig['BASE_DIR'] . $mConfig['THUMBNAILS_DIR'])), ts('THUMBNAILS_DIR not writable or configured'));
    }
  }
  elseif (!is_writable($mConfig['BASE_DIR'] . $mConfig['THUMBNAILS_DIR'])) {
    $messages[] = new CRM_Utils_Check_Message('mosaico_thumbnails_dir', ts('%1 not writable or configured.', array(1 => $mConfig['BASE_DIR'] . $mConfig['THUMBNAILS_DIR'])), ts('THUMBNAILS_DIR not writable or configured'));
  }
}

/**
 * Convert dyanmic-y image URLs to static-y URLs.
 *
 * This is analogous to alterMailContent, but we only apply to Mosaico mailings.
 *
 * @param $content
 *   This parameter seems a bit confused.
 * @see CRM_Mosaico_MosaicoComposer
 */
function _mosaico_civicrm_alterMailContent(&$content) {

  // Mosaico templates have a few of their own tokens which are named differently from
  // CiviMail tokens. By treating these as aliases, we can get more compatibility between
  // Civi's delivery system and upstream Mosaico templates.
  $tokenAliases = array(
    // '[profile_link]' => 'FIXME',
    '[show_link]' => '{mailing.viewUrl}',
    '[unsubscribe_link]' => '{action.unsubscribeUrl}',
  );
  $content = str_replace(array_keys($tokenAliases), array_values($tokenAliases), $content);

  /**
   * create absolute urls for Mosaico/imagemagick images when sending an email in CiviMail
   * convert string below into just the absolute url with addition of static directory where correctly sized image is stored
   * Mosaico image urls are in this format:
   * img?src=BASE_URL+UPLOADS_URL+imagename+imagemagickparams
   */
  $mosaico_config = CRM_Mosaico_Utils::getConfig();
  $mosaico_image_upload_dir = rawurlencode($mosaico_config['BASE_URL'] . $mosaico_config['UPLOADS_URL']);

  $content = preg_replace_callback(
    "/src=\"h.+img\?src=(" . $mosaico_image_upload_dir . ")(.+)&.*\"/U",
    function($matches){
      return "src=\"" . rawurldecode($matches[1]) . "static/" . rawurldecode($matches[2]) . "\"";
    },
    $content
  );
}

/**
 * Implements hook_civicrm_mailingTemplateTypes().
 *
 * @throws \CRM_Core_Exception
 */
function mosaico_civicrm_mailingTemplateTypes(&$types) {
  $messages = array();
  mosaico_civicrm_check($messages);

  // v4.6 compat
  require_once 'CRM/Mosaico/Utils.php';

  $editorUrl = empty($messages)
    ? CRM_Mosaico_Utils::getLayoutPath()
    : '~/crmMosaico/requirements.html';

  $types[] = array(
    'name' => 'mosaico',
    'editorUrl' => $editorUrl,
    'weight' => -10,
  );
}

/**
 * Implements hook_civicrm_entityTypes().
 */
function mosaico_civicrm_entityTypes(&$entityTypes) {
  $entityTypes[] = array(
    'name' => 'MosaicoTemplate',
    'class' => 'CRM_Mosaico_DAO_MosaicoTemplate',
    'table' => 'civicrm_mosaico_template',
  );
}

/**
 * Implements hook_civicrm_pre().
 */
function mosaico_civicrm_pre($op, $objectName, $id, &$params) {
  if ($objectName === 'Mailing' && $op === 'create') {
    if (isset($params['template_type']) && $params['template_type'] === 'mosaico') {
      $params['header_id'] = NULL;
      $params['footer_id'] = NULL;
    }
  }
}

/**
 * Implements hook_civicrm_container().
 */
function mosaico_civicrm_container(\Symfony\Component\DependencyInjection\ContainerBuilder $container) {
  if (version_compare(\CRM_Utils_System::version(), '4.7.0', '>=')) {
    $container->addResource(new \Symfony\Component\Config\Resource\FileResource(__FILE__));
  }
  require_once 'CRM/Mosaico/Services.php';
  CRM_Mosaico_Services::registerServices($container);
}
