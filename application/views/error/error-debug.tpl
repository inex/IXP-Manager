{include file="header.tpl"}

<h2>An error has occured</h2>
	
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


{include file="footer.tpl"}
