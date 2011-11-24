<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <base href="{genUrl}//index.php">

    <meta http-equiv="Content-Type" content="text/html; charset=utf8" />

    <title>{$pageTitle|default:"IXP Manager"}</title>

	{if $config.use_minified_css}
        <link rel="stylesheet" type="text/css" href="{genUrl}/css/min.100-yui-reset-fonts-grids.css" />
        <link rel="stylesheet" type="text/css" href="{genUrl}/css/min.900-ixp-manager.css"               />
	{else}
        <link rel="stylesheet" type="text/css" href="{genUrl}/css/100-yui-reset-fonts-grids.css" />
        <link rel="stylesheet" type="text/css" href="{genUrl}/css/900-ixp-manager.css"               />
	{/if}
	
</head>

<body>

<div id="doc3">

<div id="hd">

