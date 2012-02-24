<!DOCTYPE html>
<html lang="en">
<head>
    <base href="{$pagebase}/index.php">

    <meta http-equiv="Content-Type" content="text/html; charset="utf8" />

    <link href="{genUrl}/css/min.bundle-v1.css" rel="stylesheet" type="text/css" />
</head>

<body>

<center>
<h3>IXP Interface Statistics :: {$customer.name} :: {foreach from=$categories key=cname item=cvalue}{if $category eq $cvalue}{$cname}{/if}{/foreach}</h3>

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
