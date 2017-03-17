<?php
// This file declares an Angular module which can be autoloaded
// in CiviCRM. See also:
// http://wiki.civicrm.org/confluence/display/CRMDOC/hook_civicrm_angularModules

$result = array (
  'js' => 
  array (
    0 => 'ang/crmMosaico.js',
    1 => 'ang/crmMosaico/*.js',
    2 => 'ang/crmMosaico/*/*.js',
  ),
  'css' => 
  array (
    0 => 'ang/crmMosaico.css',
  ),
  'partials' => 
  array (
    0 => 'ang/crmMosaico',
  ),
  'settings' => 
  array (
    'canDelete' => Civi::service('civi_api_kernel')->runAuthorize('MosaicoTemplate', 'delete', array('version' => 3, 'check_permissions' => 1)),
  ),
);

if (version_compare(CRM_Utils_System::version(), '4.7', '<')) {
  $result['css'][]= 'ang/crmMosaico-4.6.css';
}

return $result;