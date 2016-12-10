<?php

/**
 * Adjust metadata for "get" action.
 *
 * @param array $spec
 *   List of fields.
 */
function _civicrm_api3_mosaico_base_template_get_spec(&$spec) {
  $spec['name'] = array(
    'name' => 'name',
    'type' => CRM_Utils_Type::T_STRING,
    'title' => ts('Name'),
    'description' => '',
    'maxlength' => 127,
  );
  $spec['title'] = array(
    'name' => 'title',
    'type' => CRM_Utils_Type::T_STRING,
    'title' => ts('Title'),
    'description' => '',
    'maxlength' => 127,
  );
  $spec['thumbnail'] = array(
    'name' => 'thumbnail',
    'type' => CRM_Utils_Type::T_STRING,
    'title' => ts('Thumbnail URL'),
    'description' => '',
    'maxlength' => 255,
  );
  $spec['path'] = array(
    'name' => 'path',
    'type' => CRM_Utils_Type::T_STRING,
    'title' => ts('Relative path to the template file'),
    'description' => '',
    'maxlength' => 255,
  );
}

/**
 * MosaicoBaseTemplate.get API
 *
 * @param array $params
 * @return array API result descriptor
 * @throws API_Exception
 */
function civicrm_api3_mosaico_base_template_get($params) {
  return _civicrm_api3_basic_array_get('CxnApp', $params, CRM_Mosaico_BAO_MosaicoTemplate::findBaseTemplates(), 'name',
    array('name', 'title', 'type', 'thumbnail', 'path'));

}
