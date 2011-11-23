<div id="sendPeeringRequestDialog" style="display: none">
	<div id="sendPeeringRequestThrobber" style="display: none;">
		<p>
			<img src="{genUrl}/images/throbber-small.gif" /> 
			Sending peering request...
		</p>
	</div>
    <form method="POST" id="sendPeeringRequestForm" action="{genUrl controller=dashboard action='my-peering-matrix-email' send=1}">

        <table border="0">
        <tr>
            <td align="right">
                <strong>From:</strong>&nbsp;&nbsp;
            </td>
            <td>
                <input id="sendPeeringRequestDialog-from" type="text" name="from"
                    value="{$m.Cust.peeringemail}"
                    maxlength="254" size="60"  readonly="1"
                />
            </td>
        </tr>

        <tr>
            <td align="right">
                <strong>To:</strong>&nbsp;&nbsp;
            </td>
            <td>
                <input id="sendPeeringRequestDialog-to" type="text" name="to" value="Loading..." maxlength="254" size="60"  readonly="1" />
            </td>
        </tr>

        <tr>
            <td align="right">
                <strong>BCC:</strong>&nbsp;&nbsp;
            </td>
            <td>
                <input id="sendPeeringRequestDialog-bcc" type="text" name="bcc"
                    value="{$m.Cust.peeringemail}"
                    maxlength="254" size="60" readonly="1"
                />
            </td>
        </tr>

        <tr>
            <td align="right">
                <strong>Subject:</strong>&nbsp;&nbsp;
            </td>
            <td>
                <input id="sendPeeringRequestDialog-subject" type="text" name="subject" value="Loading..." maxlength="254" size="60" />
            </td>
        </tr>

        <tr>
            <td align="center" colspan="2">
                <textarea id="sendPeeringRequestDialog-message" name="message" cols="78" rows="10" class="fixedFont">Loading...</textarea>
            </td>
        </tr>

        </table>

        <input id="sendPeeringRequestDialog-id" type="hidden" name="id" value="" />
    </form>
</div>


