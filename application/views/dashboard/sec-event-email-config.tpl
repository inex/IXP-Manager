{tmplinclude file="header.tpl" pageTitle="IXP Manager :: SEC Event Notification Config"}

<div class="yui-g">

<div id="content">

<table class="adminheading" border="0">
<tr>
    <th class="Statistics">
        Member Configuration :: SEC Event Notifications
    </th>
</tr>
</table>

{tmplinclude file="message.tpl"}

<div id='ajaxMessage'></div>


<h2>About SEC Events</h2>

<p>
At INEX, we use the <a href="http://simple-evcorr.sourceforge.net/">SEC - simple event correlator</a> to
monitor various logs and feed information that we feel is important into a processor within the IXP Manager.
The processor parses the entry and then correlates that information with our IXP database to match it
to switch ports and INEX members.
</p>

<p>
We now want to make that information available to our members through email alerts for the follow types
of information. We are enabling all these by default as we feel they are important and should be acted on.
Please feel free to disable these email alerts by unchecking the boxes below.
</p>

<p>
Note that in all cases, the notification email is sent to your NOC email address. As such,
notifications are not user account specific but apply to the overall member account.
</p>


<form method="post" action="{genUrl controller="dashboard" action="sec-event-email-config"}">

<h3>BGP MD5 Authentication</h3>

<p>
This alert is issued when our monitoring systems record a bad or missing BGP MD5 authentication
with our route collector. As it is a requirement of INEX that all members peer and exchange
routes with the route collector, we'd appreciate it if you could address these alerts at your
earliest convenience.
</p>

<p>
&nbsp;&nbsp;<input type="checkbox" name="BGP_AUTH" value="1" {if $events.BGP_AUTH}checked{/if} /> Enable notifications for missing or bad
BGP MD5 authentication with the route collector.
</p>



<h3>Member Port Up/Down Notification</h3>

<p>
This alert is issued when our monitoring systems find that one of your switch ports has
gone up or down. As a result of a port down notification, your INEX link may be no longer
passing traffic and you should address it immediately.
</p>

<p>
&nbsp;&nbsp;<input type="checkbox" name="PORT_UPDOWN" value="1" {if $events.PORT_UPDOWN}checked{/if} /> Enable notifications for port up/down events.
</p>



<h3>Port Security Violation</h3>

<p>
To ensure the stability of our peering LANs, it is a strict requirement at INEX that our members
only present one MAC address per port. You will receive one mail notification per port per day on
any day that we register a security violation and we would kindly ask that you address this issue
at your earliest convenience.
</p>

<p>
&nbsp;&nbsp;<input type="checkbox" name="SECURITY_VIOLATION" value="1" {if $events.SECURITY_VIOLATION}checked{/if} /> Enable notifications for port security violations.
</p>

<p align="right">
<br />
<input type="hidden" name="update" value="1" />
<input type="submit" name="submit" value="Update Settings" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
</p>

</form>

</div>
</div>

{tmplinclude file="footer.tpl"}

