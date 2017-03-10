<?php

//this may not be required as it doesn't appear to be used anywhere?
//require_once 'packages/premailer/premailer.php';

/**
 * Class CRM_Mosaico_Utils
 */
class CRM_Mosaico_Utils {

  /**
   * Get a list of layout options.
   *
   * @return array
   *   Array (string $machineName => string $label).
   */
  public static function getLayoutOptions() {
    return array(
      'auto' => 'Automatically select a layout',
      'bootstrap-single' => 'Single Page (Bootstrap CSS)',
      'bootstrap-wizard' => 'Wizard (Bootstrap CSS)',
    );
  }

  /**
   * Get the path to the Mosaico layout file.
   *
   * @return string
   *   Ex: `~/crmMosaico/EditMailingCtrl/mosaico.html`
   * @see getLayoutOptions()
   */
  public static function getLayoutPath() {
    $layout = CRM_Core_BAO_Setting::getItem('Mosaico Preferences', 'mosaico_layout');
    $prefix = '~/crmMosaico/EditMailingCtrl';

    switch ($layout) {
      case '':
      case 'auto':
      case 'bootstrap-single':
        return "$prefix/mosaico.html";

      case 'bootstrap-wizard':
        return "$prefix/mosaico-wizard.html";

      default:
        throw new \RuntimeException("Failed to determine path for Mosaico layout ($layout)");
    }
  }

  /**
   * Determine the URL of the (upstream) Mosaico libraries.
   *
   * @param string $preferFormat
   *   'absolute' or 'relative'.
   * @param string|NULL $file
   *   The file within the Mosaico library.
   * @return string
   *   Ex: "https://example.com/sites/all/modules/civicrm/tools/extension/uk.co.vedaconsulting.mosaico/packages/mosaico/dist".
   */
  public static function getMosaicoDistUrl($preferFormat, $file = NULL) {
    $key = "distUrl";
    if (!isset(Civi::$statics[__CLASS__][$key])) {
      Civi::$statics[__CLASS__][$key] = CRM_Core_Resources::singleton()->getUrl('uk.co.vedaconsulting.mosaico', 'packages/mosaico/dist');
    }
    return self::filterAbsoluteRelative($preferFormat, Civi::$statics[__CLASS__][$key] . ($file ? "/$file" : ''));
  }

  /**
   * Determine the URL of the Mosaico templates folder.
   *
   * @param string $preferFormat
   *   'absolute' or 'relative'.
   * @param string|NULL $file
   *   The file within the template library.
   * @return string
   *   Ex: "https://example.com/sites/all/modules/civicrm/tools/extension/uk.co.vedaconsulting.mosaico/packages/mosaico/templates".
   */
  public static function getTemplatesUrl($preferFormat, $file = NULL) {
    $key = "templatesUrl";
    if (!isset(Civi::$statics[__CLASS__][$key])) {
      Civi::$statics[__CLASS__][$key] = CRM_Core_Resources::singleton()->getUrl('uk.co.vedaconsulting.mosaico', 'packages/mosaico/templates');
    }
    return self::filterAbsoluteRelative($preferFormat, Civi::$statics[__CLASS__][$key] . ($file ? "/$file" : ''));
  }

  /**
   * @param string $preferFormat
   *   'absolute' or 'relative'.
   * @param string $url
   * @return string
   */
  private static function filterAbsoluteRelative($preferFormat, $url) {
    if ($preferFormat === 'absolute' && !preg_match('/^https?:/', $url)) {
      $url = (\CRM_Utils_System::isSSL() ? 'https://' : 'http://') . $_SERVER['HTTP_HOST'] . $url;
    }
    return $url;
  }

  public static function getUrlMimeType($url) {
    $buffer = file_get_contents($url);
    $finfo = new finfo(FILEINFO_MIME_TYPE);
    return $finfo->buffer($buffer);
  }

  public static function getConfig() {
    static $mConfig = array();

    if (empty($mConfig)) {
      $civiConfig = CRM_Core_Config::singleton();

      $mConfig = array(
        /* base url for image folders */
        'BASE_URL' => $civiConfig->imageUploadURL,

        /* local file system base path to where image directories are located */
        'BASE_DIR' => $civiConfig->imageUploadDir,

        /* url to the static images folder (relative to BASE_URL) */
        'UPLOADS_URL' => "images/uploads/",

        /* local file system path to the static images folder (relative to BASE_DIR) */
        'UPLOADS_DIR' => "images/uploads/",

        /* url to the static images folder (relative to BASE_URL) */
        'STATIC_URL' => "images/uploads/static/",

        /* local file system path to the static images folder (relative to BASE_DIR) */
        'STATIC_DIR' => "images/uploads/static/",

        /* url to the thumbnail images folder (relative to'BASE_URL'*/
        'THUMBNAILS_URL' => "images/uploads/thumbnails/",

        /* local file system path to the thumbnail images folder (relative to BASE_DIR) */
        'THUMBNAILS_DIR' => "images/uploads/thumbnails/",

        /* width and height of generated thumbnails */
        'THUMBNAIL_WIDTH' => 90,
        'THUMBNAIL_HEIGHT' => 90,
      );
    }

    return $mConfig;
  }


  /**
   * handler for upload requests
   */
  public static function processUpload() {
    $config = self::getConfig();

    global $http_return_code;

    $messages = array();
    _mosaico_civicrm_check_dirs($messages);
    if (!empty($messages)) {
      CRM_Core_Error::debug_log_message('Mosaico uploader failed. Check system status for directory errors.');
      $http_return_code = 500;
      return;
    }

    $files = array();

    if ($_SERVER["REQUEST_METHOD"] == "GET") {
      $dir = scandir($config['BASE_DIR'] . $config['UPLOADS_DIR']);

      foreach ($dir as $file_name) {
        $file_path = $config['BASE_DIR'] . $config['UPLOADS_DIR'] . $file_name;

        if (is_file($file_path)) {
          $size = filesize($file_path);

          $file = array(
            "name" => $file_name,
            "url" => $config['BASE_URL'] . $config['UPLOADS_DIR'] . $file_name,
            "size" => $size,
          );

          if (file_exists($config['BASE_DIR'] . $config['THUMBNAILS_DIR'] . $file_name)) {
            $file["thumbnailUrl"] = $config['BASE_URL'] . $config['THUMBNAILS_URL'] . $file_name;
          }

          $files[] = $file;
        }
      }
    }
    elseif (!empty($_FILES)) {
      foreach ($_FILES["files"]["error"] as $key => $error) {
        if ($error == UPLOAD_ERR_OK) {
          $tmp_name = $_FILES["files"]["tmp_name"][$key];

          $file_name = $_FILES["files"]["name"][$key];
          //issue - https://github.com/veda-consulting/uk.co.vedaconsulting.mosaico/issues/28
          //Change file name to unique by adding hash so every time uploading same image it will create new image name
          $file_name = CRM_Utils_File::makeFileName($file_name);

          $file_path = $config['BASE_DIR'] . $config['UPLOADS_DIR'] . $file_name;

          if (move_uploaded_file($tmp_name, $file_path) === TRUE) {
            $size = filesize($file_path);

            $image = new Imagick($file_path);

            $image->resizeImage($config['THUMBNAIL_WIDTH'], $config['THUMBNAIL_HEIGHT'], Imagick::FILTER_LANCZOS, 1.0, TRUE);
            // $image->writeImage( $config['BASE_DIR'] . $config[ THUMBNAILS_DIR ] . $file_name );
            if ($f = fopen($config['BASE_DIR'] . $config['THUMBNAILS_DIR'] . $file_name, "w")) {
              $image->writeImageFile($f);
            }
            $image->destroy();

            $file = array(
              "name" => $file_name,
              "url" => $config['BASE_URL'] . $config['UPLOADS_DIR'] . $file_name,
              "size" => $size,
              "thumbnailUrl" => $config['BASE_URL'] . $config['THUMBNAILS_URL'] . $file_name,
            );

            $files[] = $file;
          }
          else {
            $http_return_code = 500;
            return;
          }
        }
        else {
          $http_return_code = 400;
          return;
        }
      }
    }

    header("Content-Type: application/json; charset=utf-8");
    header("Connection: close");

    echo json_encode(array("files" => $files));
    CRM_Utils_System::civiExit();
  }

  /**
   * handler for img requests
   */
  public static function processImg() {
    if ($_SERVER["REQUEST_METHOD"] == "GET") {
      $method = $_GET["method"];

      $params = explode(",", $_GET["params"]);

      $width = (int) $params[0];
      $height = (int) $params[1];

      if ($method == "placeholder") {
        $image = new Imagick();

        $image->newImage($width, $height, "#707070");
        $image->setImageFormat("png");

        $x = 0;
        $y = 0;
        $size = 40;

        $draw = new ImagickDraw();

        while ($y < $height) {
          $draw->setFillColor("#808080");

          $points = array(
            array("x" => $x, "y" => $y),
            array("x" => $x + $size, "y" => $y),
            array("x" => $x + $size * 2, "y" => $y + $size),
            array("x" => $x + $size * 2, "y" => $y + $size * 2),
          );

          $draw->polygon($points);

          $points = array(
            array("x" => $x, "y" => $y + $size),
            array("x" => $x + $size, "y" => $y + $size * 2),
            array("x" => $x, "y" => $y + $size * 2),
          );

          $draw->polygon($points);

          $x += $size * 2;

          if ($x > $width) {
            $x = 0;
            $y += $size * 2;
          }
        }

        $draw->setFillColor("#B0B0B0");
        $draw->setFontSize($width / 5);
        $draw->setFontWeight(800);
        $draw->setGravity(Imagick::GRAVITY_CENTER);
        $draw->annotation(0, 0, $width . " x " . $height);

        $image->drawImage($draw);

        header("Content-type: image/png");

        echo $image;
      }
      else {
        $file_name = $_GET["src"];

        $path_parts = pathinfo($file_name);

        switch ($path_parts["extension"]) {
          case "png":
            $mime_type = "image/png";
            break;

          case "gif":
            $mime_type = "image/gif";
            break;

          default:
            $mime_type = "image/jpeg";
            break;
        }

        $file_name = $path_parts["basename"];

        $image = self::resizeImage($file_name, $method, $width, $height);

        $expiry_time = 2592000;  //30days (60sec * 60min * 24hours * 30days)
        header("Pragma: cache");
        header("Cache-Control: max-age=" . $expiry_time . ", public");
        header('Expires: ' . gmdate('D, d M Y H:i:s', time() + $expiry_time) . ' GMT');
        header("Content-type:" . $mime_type);

        echo $image;
      }
    }
    CRM_Utils_System::civiExit();
  }

  /**
   * handler for dl requests
   */
  public static function processDl() {
    $config = self::getConfig();
    global $http_return_code;

    /* run this puppy through premailer */
    // DS: not sure why we need premailer as it always sends out mobile (inline) layout.
    // Lets disable it till we figure out why we need it.
    //$premailer = Premailer::html( $_POST[ "html" ], true, "hpricot", $config['BASE_URL'] );
    //$html = $premailer[ "html" ];
    $html = $_POST["html"];

    /* create static versions of resized images */

    $matches = array();

    $num_full_pattern_matches = preg_match_all('#<img.*?src="([^"]*?\/[^/]*\.[^"]+)#i',
      $html, $matches);

    for ($i = 0; $i < $num_full_pattern_matches; $i++) {
      if (preg_match('#/img/(\?|&amp;)src=#i', $matches[1][$i])) {
        $src_matches = array();

        if (preg_match('#/img/(\?|&amp;)src=(.*)&amp;method=(.*)&amp;params=(.*)#i',
            $matches[1][$i], $src_matches) !== FALSE
        ) {
          $file_name = urldecode($src_matches[2]);
          $file_name = substr($file_name,
            strlen($config['BASE_URL'] . $config['UPLOADS_DIR']));

          $method = urldecode($src_matches[3]);

          $params = urldecode($src_matches[4]);
          $params = explode(",", $params);
          $width = (int) $params[0];
          $height = (int) $params[1];

          $static_file_name = $method . "_" . $width . "x" . $height . "_" . $file_name;

          $html = str_ireplace($matches[1][$i],
            $config['BASE_URL'] . $config['STATIC_URL'] . rawurlencode($static_file_name),
            $html);//Changed to rawurlencode because space gets into + in the image file name if it has space

          // resize and save static version of image
          self::resizeImage($file_name, $method, $width, $height);

        }
      }
    }
    if (defined('CIVICRM_MAIL_SMARTY') && CIVICRM_MAIL_SMARTY == 1) {
      // keep head section in literal to avoid smarty errors. Specially when CIVICRM_MAIL_SMARTY is turned on.
      $html = str_ireplace(array('<head>', '</head>'),
        array('{literal}<head>', '</head>{/literal}'), $html);
    }
    else {
      if (defined('CIVICRM_MAIL_SMARTY') && CIVICRM_MAIL_SMARTY == 0) {
        // get rid of any injected literal tags to avoid them appearing in emails
        $html = str_ireplace(array('{literal}<head>', '</head>{/literal}'),
          array('<head>', '</head>'), $html);
      }
    }

    /* perform the requested action */

    switch (CRM_Utils_Type::escape($_POST['action'], 'String')) {
      case "download": {
        // download
        header("Content-Type: application/force-download");
        header("Content-Disposition: attachment; filename=\"" . $_POST["filename"] . "\"");
        header("Content-Length: " . strlen($html));

        echo $html;
        break;
      }

      case "save": {
        $result = array();
        $msgTplId = NULL;
        $hashKey = CRM_Utils_Type::escape($_POST['key'], 'String');
        if (!$hashKey) {
          CRM_Core_Session::setStatus(ts('Mosaico hask key not found...'));
          return FALSE;
        }
        $mosTpl = new CRM_Mosaico_DAO_MessageTemplate();
        $mosTpl->hash_key = $hashKey;
        if ($mosTpl->find(TRUE)) {
          $msgTplId = $mosTpl->msg_tpl_id;
        }

        $name = "Mosaico Template " . date('d-m-Y H:i:s');
        if (CRM_Utils_Type::escape($_POST['name'], 'String')) {
          $name = $_POST['name'];
        }

        // save to message templates
        $messageTemplate = array(
          //'msg_text' => $formValues['text_message'],
          'msg_html' => $html,
          'is_active' => TRUE,
        );
        $messageTemplate['msg_title'] = $messageTemplate['msg_subject'];
        if ($msgTplId) {
          $messageTemplate['id'] = $msgTplId;
        }

        $messageTemplate['msg_title'] = $messageTemplate['msg_subject'] = $name;

        $msgTpl = CRM_Core_BAO_MessageTemplate::add($messageTemplate);
        $mosaicoTemplate = array(
          //'msg_text' => $formValues['text_message'],
          'msg_tpl_id' => $msgTpl->id,
          'hash_key' => $hashKey,
          'name' => $name,
          'html' => $_POST['html'],
          'metadata' => $_POST['metadata'],
          'template' => $_POST['template'],
        );
        $mosTpl = new CRM_Mosaico_DAO_MessageTemplate();
        $mosTpl->msg_tpl_id = $msgTpl->id;
        $mosTpl->hash_key = $hashKey;
        $mosTpl->find(TRUE);
        $mosTpl->copyValues($mosaicoTemplate);
        $mosTpl->save();
        if ($mosTpl->id) {
          $result['id'] = $mosTpl->id;
        }
        CRM_Utils_JSON::output($result);
        break;
      }

      case "email": {
        $result = array();
        if (!CRM_Utils_Rule::email($_POST['rcpt'])) {
          CRM_Core_Session::setStatus('Recipient Email address not found');
          return FALSE;
        }
        $to = $_POST['rcpt'];
        $subject = CRM_Utils_Type::escape($_POST['subject'], 'String');
        list($domainEmailName, $domainEmailAddress) = CRM_Core_BAO_Domain::getNameAndEmail();
        $mailParams = array(
          //'groupName' => 'Activity Email Sender',
          'from' => $domainEmailAddress, //FIXME: use configured from address
          'toName' => 'Test Recipient',
          'toEmail' => $to,
          'subject' => $subject,
          //'text' => $text_message,
          'html' => $html,
        );

        $sent = FALSE;
        if (CRM_Utils_Mail::send($mailParams)) {
          $result['sent'] = TRUE;
          CRM_Utils_JSON::output($result);
        }
        else {
          CRM_Utils_JSON::output($result);
          return FALSE;
        }

        break;
      }
    }
    CRM_Utils_System::civiExit();
  }

  /**
   * function to resize images using resize or cover methods
   */
  public static function resizeImage($file_name, $method, $width, $height) {
    $config = self::getConfig();

    if (file_exists($config['BASE_DIR'] . $config['STATIC_DIR'] . $file_name)) {
      //use existing file
      $image = new Imagick($config['BASE_DIR'] . $config['STATIC_DIR'] . $file_name);

    }
    else {

      $image = new Imagick($config['BASE_DIR'] . $config['UPLOADS_DIR'] . $file_name);

      if ($method == "resize") {
        // We get 0 for height variable from mosaico
        // In order to use last parameter(best fit), this will make right scale, as true in 'resizeImage' menthod, we can't have 0 for height
        // hence retreiving height from image
        // more details about best fit http://php.net/manual/en/imagick.resizeimage.php
        $image->resizeImage($width, $image->getImageHeight(), Imagick::FILTER_LANCZOS, 1.0, TRUE);
      }
      else {
        // assert: $method == "cover"
        $image_geometry = $image->getImageGeometry();

        $width_ratio = $image_geometry["width"] / $width;
        $height_ratio = $image_geometry["height"] / $height;

        $resize_width = $width;
        $resize_height = $height;

        if ($width_ratio > $height_ratio) {
          $resize_width = 0;
        }
        else {
          $resize_height = 0;
        }

        $image->resizeImage($resize_width, $resize_height,
          Imagick::FILTER_LANCZOS, 1.0);

        $image_geometry = $image->getImageGeometry();

        $x = ($image_geometry["width"] - $width) / 2;
        $y = ($image_geometry["height"] - $height) / 2;

        $image->cropImage($width, $height, $x, $y);
      }
      //save image for next time so don't need to resize each time
      if ($f = fopen($config['BASE_DIR'] . $config['STATIC_DIR'] . $file_name, "w")) {
        $image->writeImageFile($f);
      }

    }

    return $image;
  }

  /**
   * function to get mosaico msg template id from mosaico msg template id
   *
   * @param $msgTplId
   * @return int
   *   The $mosaicoTplId
   */
  public static function getMosaicoMsgTplIdFromMsgTplId($msgTplId) {
    $query = "SELECT id FROM civicrm_mosaico_msg_template WHERE msg_tpl_id = %1";
    $queryParams = array(1 => array($msgTplId, 'Int'));
    return CRM_Core_DAO::singleValueQuery($query, $queryParams);

  }

  /**
   * function to get mosaico msg template detilas
   */
  public static function getMosaicoMsgTemplate($mosaicoTemplateId) {
    $tableName = CRM_Mosaico_DAO_MessageTemplate::getTableName();
    $getSQL = "SELECT hash_key, html, metadata, template FROM {$tableName} WHERE id = %1";
    $getSQLParams = array(1 => array($mosaicoTemplateId, 'Int'));
    $dao = CRM_Core_DAO::executeQuery($getSQL, $getSQLParams);
    $mosaicoTemplate = array();
    while ($dao->fetch()) {
      $mosaicoTemplate = array(
        'hash_key' => $dao->hash_key,
        'html' => $dao->html,
        'metadata' => $dao->metadata,
        'template' => $dao->template,
      );

    }
    return $mosaicoTemplate;
  }

}
