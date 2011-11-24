<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <base href="{genUrl}/index.php">

    <meta http-equiv="Content-Type" content="text/html; charset=utf8" />

    <title>{$pageTitle|default:"IXP Manager"}</title>

	{if $config.use_minified_css}
	    <link rel="stylesheet" type="text/css" href="{genUrl}/css/min.bundle.css">
    {else}
        <link rel="stylesheet" type="text/css" href="{genUrl}/css/100-yui-reset-fonts-grids.css">
        <link rel="stylesheet" type="text/css" href="{genUrl}/css/200-jquery-ui-1.8.16.custom.css" />
        <link rel="stylesheet" type="text/css" href="{genUrl}/css/210-colorbox.css">
        <link rel="stylesheet" type="text/css" href="{genUrl}/css/220-demo_table_jui.css" />
        <link rel="stylesheet" type="text/css" href="{genUrl}/css/230-jquery.contextMenu.css" />
        <link rel="stylesheet" type="text/css" href="{genUrl}/css/400-joomla.css" />
        <link rel="stylesheet" type="text/css" href="{genUrl}/css/900-ixp-manager.css" />
	{/if}

	{if $config.use_minified_js}
    	<script type="text/javascript" src="{genUrl}/js/min.bundle.js"></script>
    {else}
        <script type="text/javascript" src="{genUrl}/js/200-jquery-1.7.js"></script>
        <script type="text/javascript" src="{genUrl}/js/210-jquery.colorbox.js"></script>
        <script type="text/javascript" src="{genUrl}/js/220-jquery-ui-1.8.16.custom.js"></script>
        <script type="text/javascript" src="{genUrl}/js/230-jquery.dataTables.js"></script>
        <script type="text/javascript" src="{genUrl}/js/240-jquery.contextMenu.js"></script>
        <script type="text/javascript" src="{genUrl}/js/400-JSCookMenu_mini.js"></script>
        <script type="text/javascript" src="{genUrl}/js/410-theme.js"></script>
        <script type="text/javascript" src="{genUrl}/js/700-php.js"></script>
	{/if}
	
    {if $hasIdentity and $action neq 'my-peering-matrix'}
    	<meta http-equiv="refresh" content="{$config.resources.session.remember_me_seconds};url={genUrl controller="auth" action="logout" auto=1}">
    {/if}

</head>

<body>

<div id="doc3">





