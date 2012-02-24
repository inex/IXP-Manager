<!DOCTYPE html>
<html lang="en">

<head>
    <base href="{genUrl}//index.php">

    <meta http-equiv="Content-Type" content="text/html; charset=utf8" />

    <title>{$pageTitle|default:"IXP Manager"}</title>

	{if $config.use_minified_css}
	    <link rel="stylesheet" type="text/css" href="{genUrl}/css/min.bundle.css">
    {else}
        {include file="header-css.tpl"}
	{/if}

	{if $config.use_minified_js}
    	<script type="text/javascript" src="{genUrl}/js/min.bundle.js"></script>
    {else}
        {include file="header-js.tpl"}
	{/if}
	
	<!-- Le HTML5 shim, for IE6-8 support of HTML5 elements -->
    <!--[if lt IE 9]>
      <script src="//html5shim.googlecode.com/svn/trunk/html5.js"></script>
    <![endif]-->
    	
</head>

<body>

