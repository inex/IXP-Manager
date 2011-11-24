<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <base href="{genUrl}/index.php">

    <meta http-equiv="Content-Type" content="text/html; charset=utf8" />

    <title>{$pageTitle|default:"IXP Manager"}</title>

    <link rel="stylesheet" type="text/css" href="{genUrl}/css/yui/build/reset-fonts-grids/reset-fonts-grids.css">

    <link rel="stylesheet" type="text/css" href="{genUrl}/css/yui/build/assets/skins/sam/skin.css">
    <link rel="stylesheet" type="text/css" href="{genUrl}/css/yui/build/assets/skins/sam/button.css">
    <link rel="stylesheet" type="text/css" href="{genUrl}/css/yui/build/container/assets/skins/sam/container.css">
    <link rel="stylesheet" type="text/css" href="{genUrl}/css/yui/build/tabview/assets/skins/sam/tabview.css">

    <link rel="stylesheet" type="text/css" href="{genUrl}/css/colorbox.css">

    <link rel="stylesheet" type="text/css" href="{genUrl}/css/smoothness/jquery.css" />

    <link href="{genUrl}/css/demo_table_jui.css"      rel="stylesheet" type="text/css" />
    <link href="{genUrl}/css/jquery.contextMenu.css"      rel="stylesheet" type="text/css" />

    <link href="{genUrl}/css/ixp-manager.css"         rel="stylesheet" type="text/css" />
    <link href="{genUrl}/css/joomla.css"              rel="stylesheet" type="text/css" />


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

<body class="yui-skin-sam">

<div id="doc4">





