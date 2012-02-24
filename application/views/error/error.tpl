{include file="header.tpl"}

<h2>An error has occured</h2>
	
{include file="message.tpl"}

{if not isset( $message )}
    <p>
    We apologise but an unexpected server error has occured.
    </p>
{/if}

<p>
Please mail our support team with a description of what you were doing when the error
occured and we will rectify it as soon as possible. Our support team can be reached via
{mailto address=$config.identity.email encode="javascript" subject="Unexpected web error report"}.
</p>


{include file="footer.tpl"}