{include file="header.tpl" pageTitle="IXP Manager :: Member Dashboard"}

<div class="yui-g" style="margin-bottom: 70px;">

<table class="adminheading" border="0">
	<tr>
		<th class="Meeting">INEX Members' Meetings :: Compose Mail Notification</th>
	</tr>
</table>

{include file="message.tpl"}

<div class="content">

<h3>Composing Email for Members' Meeting of {$meeting.date|strtotime|date_format}</h3>

<p>
The form below allows you to enter some <strong>pre-amble</strong> to the email detailing
the members' meeting. If you are unsure of how this works, enter some text and send the mail
which, by default, only sends it to yourself.
</p>

<p>
When you're happy with the result, you can send it to <code>members@inex.ie</code>.
</p>

<div id="email_dialog">

<form method="post" action="{genUrl controller=meeting action='compose' id=$meeting.id}">

<table border="0">

<tr>
	<td align="right">
	    <strong>From:</strong>&nbsp;&nbsp;
	</td>
	<td>
	    <input id="from" type="text" name="from"
	        value="{if isset( $from )}{$from}{else}{$user.email}{/if}" maxlength="254" size="60"
	    />
	</td>
</tr>

<tr>
    <td align="right">
        <strong>To:</strong>&nbsp;&nbsp;
    </td>
    <td>
        <input id="to" type="text" name="to"
            value="{if isset( $to )}{$to}{else}{$user.email}{/if}" maxlength="254" size="60"
        />
    </td>
</tr>

<tr>
    <td align="right">
        <strong>BCC:</strong>&nbsp;&nbsp;
    </td>
    <td>
        <input id="bcc" type="text" name="bcc"
            value="{if isset( $bcc )}{$bcc}{else}{$user.email}{/if}" maxlength="254" size="60"
        />
    </td>
</tr>

<tr>
    <td align="right">
        <strong>Subject:</strong>&nbsp;&nbsp;
    </td>
    <td>
        <input id="subject" type="text" name="subject"
            value="{if isset( $subject )}{$subject}{else}INEX Members' Meeting - {$meeting.date|strtotime|date_format}{/if}"
            maxlength="254" size="60"
        />
    </td>
</tr>

<tr>
    <td align="center" colspan="2">
        <textarea id="message" name="body" cols="78" rows="10">{if isset( $body )}{$body}{/if}</textarea>
    </td>
</tr>

<tr>
    <td align="right" colspan="2">
        <input type="submit" name="submit" value="Send" />
    </td>
</tr>

</table>

<input type="hidden" name="send" value="1" />

</form>

</div>

<link rel="stylesheet" href="{genUrl}/js/jwysiwyg/jquery.wysiwyg.css" type="text/css" />
<script type="text/javascript" src="{genUrl}/js/jwysiwyg/jquery.wysiwyg.js"></script>

<script type="text/javascript">
    $(function()
    {ldelim}
        $( '#message' ).wysiwyg();
    {rdelim});
</script>


</div>


{include file="footer.tpl"}
