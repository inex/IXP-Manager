<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">

<head>
    <base href="{genUrl}//index.php">

    <meta http-equiv="Content-Type" content="text/html; charset=utf8" />

    <title>{$pageTitle|default:"IXP Manager"}</title>



    <link rel="stylesheet" type="text/css" href="{genUrl}/css/yui/build/reset-fonts-grids/reset-fonts-grids.css">

    <link rel="stylesheet" type="text/css" href="{genUrl}/css/yui/build/assets/skins/sam/skin.css">
    <link rel="stylesheet" type="text/css" href="{genUrl}/css/yui/build/assets/skins/sam/button.css">
    <link rel="stylesheet" type="text/css" href="{genUrl}/css/yui/build/autocomplete/assets/skins/sam/autocomplete.css" />
    <link rel="stylesheet" type="text/css" href="{genUrl}/css/yui/build/paginator/assets/skins/sam/paginator.css" />
    <link rel="stylesheet" type="text/css" href="{genUrl}/css/yui/build/datatable/assets/skins/sam/datatable.css">
    <link rel="stylesheet" type="text/css" href="{genUrl}/css/yui/build/menu/assets/skins/sam/menu.css">
    <link rel="stylesheet" type="text/css" href="{genUrl}/css/yui/build/container/assets/skins/sam/container.css">
    <link rel="stylesheet" type="text/css" href="{genUrl}/css/yui/build/tabview/assets/skins/sam/tabview.css">

    <link rel="stylesheet" type="text/css" href="{genUrl}/css/colorbox.css">

    <link rel="stylesheet" type="text/css" href="{genUrl}/css/smoothness/jquery-ui-1.7.2.custom.css" />

    <link href="{genUrl}/css/ixp-manager.css"         rel="stylesheet" type="text/css" />
    <link href="{genUrl}/css/joomla.css"              rel="stylesheet" type="text/css" />


    <script type="text/javascript" src="{genUrl}/js/jquery/jquery.js"></script>
    <script type="text/javascript" src="{genUrl}/js/jquery/jquery.colorbox-min.js"></script>
    <script type="text/javascript" src="{genUrl}/js/jquery-ui.js"></script>


    <!-- Dependencies -->
    <script type="text/javascript" src="{genUrl}/css/yui/build/yahoo-dom-event/yahoo-dom-event.js"></script>
    <script type="text/javascript" src="{genUrl}/css/yui/build/element/element-min.js"></script>
    <script type="text/javascript" src="{genUrl}/css/yui/build/datasource/datasource-min.js"></script>
    <script type="text/javascript" src="{genUrl}/css/yui/build/container/container_core-min.js"></script>
    <script type="text/javascript" src="{genUrl}/css/yui/build/animation/animation-min.js"></script>

    <!-- OPTIONAL: JSON Utility (for DataSource) -->
    <script type="text/javascript" src="{genUrl}/css/yui/build/json/json-min.js"></script>

    <!-- OPTIONAL: Connection Manager (enables XHR for DataSource) -->
    <script type="text/javascript" src="{genUrl}/css/yui/build/connection/connection-min.js"></script>

    <!-- OPTIONAL: Get Utility (enables dynamic script nodes for DataSource) -->
    <script type="text/javascript" src="{genUrl}/css/yui/build/get/get-min.js"></script>
    <script type="text/javascript" src="{genUrl}/css/yui/build/autocomplete/autocomplete-min.js"></script>
    <script type="text/javascript" src="{genUrl}/css/yui/build/paginator/paginator-min.js"></script>
    <!-- OPTIONAL: Drag Drop (enables resizeable or reorderable columns) -->
    <script type="text/javascript" src="{genUrl}/css/yui/build/dragdrop/dragdrop-min.js"></script>

    <!-- OPTIONAL: Calendar (enables calendar editors) -->
    <script type="text/javascript" src="{genUrl}/css/yui/build/calendar/calendar-min.js"></script>

    <!-- Source files -->
    <script type="text/javascript" src="{genUrl}/css/yui/build/datatable/datatable-min.js"></script>
    <script type="text/javascript" src="{genUrl}/css/yui/build/menu/menu-min.js"></script>
    <script type="text/javascript" src="{genUrl}/css/yui/build/button/button-min.js"></script>
    <script type="text/javascript" src="{genUrl}/css/yui/build/container/container-min.js"></script>
    <script type="text/javascript" src="{genUrl}/css/yui/build/editor/editor-min.js"></script>
    <script type="text/javascript" src="{genUrl}/css/yui/build/editor/simpleeditor-min.js"></script>
    <script type="text/javascript" src="{genUrl}/css/yui/build/tabview/tabview-min.js"></script>

    <script type="text/javascript" src="{genUrl}/js/JSCookMenu_mini.js"></script>
    <script type="text/javascript" src="{genUrl}/js/theme.js"></script>

    <script type="text/javascript" src="{genUrl}/js/php.js"></script>

    {if $hasIdentity and $action neq 'my-peering-matrix'}
    <meta http-equiv="refresh" content="{$config.resources.session.remember_me_seconds};url={genUrl controller="auth" action="logout" auto=1}">
    {/if}

</head>

<body class="yui-skin-sam">

<div id="doc4">



<div id="hd">

<div id="wrapper">

    <div id="header">
            <div id="headertext">
            <strong>INEX :: IXP Manager</strong>
            </div>
        </div>
    </div>


</div>


{include file="menu.tpl"}

