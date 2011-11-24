<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <base href="{genUrl}/index.php">

    <meta http-equiv="Content-Type" content="text/html; charset=utf8" />

    <title>{$pageTitle|default:"IXP Manager"}</title>

    <link rel="stylesheet" type="text/css" href="{genUrl}/css/yui-reset-fonts-grids.css">

    <link rel="stylesheet" type="text/css" href="{genUrl}/css/colorbox.css">
    <link rel="stylesheet" type="text/css" href="{genUrl}/css/smoothness/jquery.css" />
    <link rel="stylesheet" type="text/css" href="{genUrl}/css/demo_table_jui.css" />
    <link rel="stylesheet" type="text/css" href="{genUrl}/css/jquery.contextMenu.css" />
    <link rel="stylesheet" type="text/css" href="{genUrl}/css/ixp-manager.css" />
    <link rel="stylesheet" type="text/css" href="{genUrl}/css/joomla.css" />


    <script type="text/javascript" src="{genUrl}/js/jquery.js"></script>
    <script type="text/javascript" src="{genUrl}/js/jquery.colorbox-min.js"></script>
    <script type="text/javascript" src="{genUrl}/js/jquery-ui.js"></script>
    <script type="text/javascript" src="{genUrl}/js/jquery.dataTables.min.js"></script>
    <script type="text/javascript" src="{genUrl}/js/jquery.contextMenu.js"></script>

    <script type="text/javascript" src="{genUrl}/js/JSCookMenu_mini.js"></script>
    <script type="text/javascript" src="{genUrl}/js/theme.js"></script>

    <script type="text/javascript" src="{genUrl}/js/php.js"></script>

    {if $hasIdentity and $action neq 'my-peering-matrix'}
    <meta http-equiv="refresh" content="{$config.resources.session.remember_me_seconds};url={genUrl controller="auth" action="logout" auto=1}">
    {/if}

</head>

<body>

<div id="doc4">





