<div id="sendSMSDialog">
    <div class="hd">Send Password via SMS</div>
    <div class="bd">
        <form method="POST" action="{genUrl controller=user action='send-sms'}">

            <table border="0">
            <tr>
                <td align="right">
                    <strong>To:</strong>&nbsp;&nbsp;
                </td>
                <td>
                    <input id="sendSMSDialog-to" type="text" name="to" value="{$object->authorisedMobile}" maxlength="30" size="15" />
                </td>
            </tr>

            <tr>
                <td colspan="2">
                    <textarea id="sendSMSDialog-message" name="message" rows="3" cols="40"></textarea>
                </td>
            </tr>

            <tr>
                <td align="right">
                    <strong>Size:</strong>&nbsp;&nbsp;
                </td>
                <td>
                    <span id="sendSMSDialog-count"></span>
                </td>
            </tr>

            </table>

            <input id="sendSMSDialog-id" type="hidden" name="id" value="" />
        </form>
    </div>
</div>

<script>
    {literal}
    var countCharacters = function( e )
    {
        document.getElementById( "sendSMSDialog-count" ).innerHTML = 
            document.getElementById( "sendSMSDialog-message" ).value.length;
    }

    YAHOO.util.Event.addListener( document.getElementById( "sendSMSDialog-message" ), "keypress", countCharacters ); 
    {/literal}
</script>


<div id="sendEmailDialog">
    <div class="hd">Send Login Details via Email</div>
    <div class="bd">
        <form method="POST" action="{genUrl controller=user action='send-email'}">

            <table border="0">
            <tr>
                <td align="right">
                    <strong>To:</strong>&nbsp;&nbsp;
                </td>
                <td>
                    <input id="sendEmailDialog-to" type="text" name="to" value="" maxlength="254" size="60" />
                </td>
            </tr>

            <tr>
                <td align="center" colspan="2">
                    <textarea id="sendEmailDialog-message" name="message" cols="60" rows="10"></textarea>
                </td>
            </tr>

            </table>

            <input id="sendEmailDialog-id" type="hidden" name="id" value="" />
        </form>
    </div>
</div>

<script>
    {literal}
    var emailEditor = new YAHOO.widget.SimpleEditor( 'sendEmailDialog-message', {
        height: '300px',
        width: '500px',
        handleSubmit: true,
        dompath: true //Turns on the bar at the bottom
    });
    emailEditor.render();
    {/literal}
</script>

