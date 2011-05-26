{include file="header.tpl"}

<div class="content">

<h1>An error has occured</h1>
	
{include file="message.tpl"}

<dl>
    <dt>File</dt>
    <dd>{$errorException->getFile()}</dd>
    
    <dt>Line</dt>
    <dd>{$errorException->getLine()}</dd>
    
    <dt>Message</dt>
    <dd>{$errorException->getMessage()}</dd>
    
    <dt>Code</dt>
    <dd>{$errorException->getCode()}</dd>
</dl>

<h3>Trace</h3>

<pre>
{$errorException->getTraceAsString()}
</pre>

</div>

{include file="footer.tpl"}