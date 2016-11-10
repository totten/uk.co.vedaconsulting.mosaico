<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$config->lcMessages|truncate:2:"":true}" xml:lang="{$config->lcMessages|truncate:2:"":true}">

<head>
  <title>CiviCRM Mosaico</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

  <script type="text/javascript" src="{$mosaicoDistUrl}/vendor/knockout.js">
  </script>
  <script type="text/javascript" src="{$mosaicoDistUrl}/vendor/jquery.min.js">
  </script>
  <script type="text/javascript" src="{$mosaicoDistUrl}/vendor/jquery-ui.min.js">
  </script>
  <script type="text/javascript" src="{$mosaicoDistUrl}/vendor/jquery.ui.touch-punch.min.js">
  </script>
  <script type="text/javascript" src="{$mosaicoDistUrl}/vendor/load-image.all.min.js">
  </script>
  <script type="text/javascript" src="{$mosaicoDistUrl}/vendor/canvas-to-blob.min.js">
  </script>
  <script type="text/javascript" src="{$mosaicoDistUrl}/vendor/jquery.iframe-transport.js">
  </script>
  <script type="text/javascript" src="{$mosaicoDistUrl}/vendor/jquery.fileupload.js">
  </script>
  <script type="text/javascript" src="{$mosaicoDistUrl}/vendor/jquery.fileupload-process.js">
  </script>
  <script type="text/javascript" src="{$mosaicoDistUrl}/vendor/jquery.fileupload-image.js">
  </script>
  <script type="text/javascript" src="{$mosaicoDistUrl}/vendor/jquery.fileupload-validate.js">
  </script>
  <script type="text/javascript" src="{$mosaicoDistUrl}/vendor/knockout-jqueryui.min.js">
  </script>
  <script type="text/javascript" src="{$mosaicoDistUrl}/vendor/tinymce.min.js">
  </script>
  <script type="text/javascript" src="{$mosaicoDistUrl}/mosaico.min.js?v=0.15">
  </script>
  <script type="text/javascript" src="{$mosaicoExtUrl}/js/editor.js">
  </script>

  <link href="{$mosaicoDistUrl}/mosaico-material.min.css?v=0.10" rel="stylesheet" type="text/css"/>
  <link href="{$mosaicoDistUrl}/vendor/notoregular/stylesheet.css" rel="stylesheet" type="text/css"/>

  {capture assign=msgTplURL}{crmURL p='civicrm/admin/messageTemplates' q="reset=1&activeTab=mosaico"}{/capture}
  {literal}
  <script type="text/javascript">
    $(function() {
      addCustomButton();
    });
    function addCustomButton() {
      var msgTplURL = "{/literal}{$msgTplURL}{literal}";
      if ($('#page .rightButtons').is(':visible')) {
        $("#page .rightButtons").append('<a href="' + msgTplURL + '" class="ui-button">Done</a>');
      } else {
        console.log('timeout 50');
        setTimeout(addCustomButton, 50);
      }
    }
  </script>
  {/literal}
</head>

<body class="mo-standalone">
</body>

</html>
