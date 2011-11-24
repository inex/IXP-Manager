<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01//EN" "http://www.w3.org/TR/html4/strict.dtd">
<html xmlns="http://www.w3.org/1999/xhtml" xml:lang="en" lang="en">
<head>
    <base href="{$pagebase}/index.php">

    <meta http-equiv="Content-Type" content="text/html; charset="utf8" />

    <link rel="stylesheet" type="text/css" href="{genUrl}/css/yui-reset-fonts-grids.css">
    <link href="{genUrl}/css/ixp-manager.css"         rel="stylesheet" type="text/css" />
</head>

<body>

<center>
<h2>IXP Interface Statistics :: {$customer.name} :: {foreach from=$categories key=cname item=cvalue}{if $category eq $cvalue}{$cname}{/if}{/foreach}</h2>

{foreach from=$periods key=pname item=pvalue}

<h3>{$pname} Graph</h3>

<p align="center">
    {genMrtgGraphBox
            shortname=$customer->shortname
            category=$category
            monitorindex=$monitorindex
            period=$pvalue
            values=$stats.$pvalue
    }
</p>


{/foreach}

</center>

</body>
</html>
