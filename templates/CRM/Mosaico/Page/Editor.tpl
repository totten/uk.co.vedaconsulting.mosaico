<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Strict//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" lang="{$config->lcMessages|truncate:2:"":true}" xml:lang="{$config->lcMessages|truncate:2:"":true}">

<head>
  <title>CiviCRM Mosaico</title>
  <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>

  {foreach from=$scriptUrls item=scriptUrl}
  <script type="text/javascript" src="{$scriptUrl|htmlspecialchars}">
  </script>
  {/foreach}
  {foreach from=$styleUrls item=styleUrl}
  <link href="{$styleUrl|htmlspecialchars}" rel="stylesheet" type="text/css"/>
  {/foreach}

  {literal}
  <script type="text/javascript">
    function addCustomButton() {
      if ($('#page .rightButtons').is(':visible')) {
        $("#page .rightButtons").append('<a href="' + {/literal}{$msgTplURL|json}{literal} + '" class="ui-button">Done</a>');
      } else {
        console.log('timeout 50');
        setTimeout(addCustomButton, 50);
      }
    }

    $(function() {
      if (!Mosaico.isCompatible()) {
        alert('Update your browser!');
        return;
      }

      var plugins;
      // A basic plugin that expose the "viewModel" object as a global variable.
      // plugins = [function(vm) {window.viewModel = vm;}];
      var ok = Mosaico.init({/literal}{$mosaicoConfig}{literal}, plugins);
      if (!ok) {
        console.log("Missing initialization hash, redirecting to main entrypoint");
      }
      else {
        addCustomButton();
      }
    });

  </script>
  {/literal}
</head>

<body class="mo-standalone">
</body>

</html>
