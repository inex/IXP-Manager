
<div class="meetings">

{foreach from=$entries item=e}

<a name="{$e.id}"></a>
<div class="meeting">

<div class="meeting title">
    <h1>{$e.title} &ndash; {$e.date|date_format:"%A, %B %e, %Y"}</h1>
    <h4>
        In {if $e.venue_url neq ''}<a href="{$e.venue_url}">{$e.venue}</a>{else}{$e.venue}{/if} at {$e.time|date_format:"%H:%M"}
        {if not $simple and $e.venue_url neq ''}
            &nbsp;&nbsp;<a href="{$e.venue_url}" target="_blank">
                <img width="16" height="16" border="0" alt="[LINK]"
                    title="Visit venue site" src="{genUrl}/images/joomla-admin/menu/globe1.png" />
            </a>
        {/if}
    </h4>
</div>

<div class="meeting content">{$e.before_text}</div>

<div class="meetingitem">

<dl>

    {assign var='inOtherContent' value=0}

    {foreach from=$e.MeetingItem key=id item=mi}

    {if $mi.other_content and not $inOtherContent}
        </dl>

        <p>
        Other meeting content also includes:
        </p>

        <dl>

        {assign var='inOtherContent' value=1}
    {/if}

    <dt>

		<div class="meetingitem title">

		    <div class="meetingitem title icons">
		    {if not $simple}
                {if $mi.presentation neq ''}
                    <a href="{genUrl controller='meeting-item' action='get-presentation' id=$mi.id}">
                        <img src="{genUrl}/images/22x22/presentation.png" width="22" height="22"
                                alt="[VIDEO]" class="meetingitem title icons" />
                    </a>
                {/if}
                {if $mi.video_url neq ''}
                    <a href="{$mi.video_url}">
                        <img src="{genUrl}/images/22x22/video.png" width="22" height="22"
                                alt="[VIDEO]" class="meetingitem title icons" />
                    </a>
                {/if}
            {/if}
		    </div>

		    <h1>
		        {$mi.title} &ndash;

		        {if not $simple and $mi.email neq ''}
		            {mailto address=$mi.email encode='javascript' text=$mi.name}
		        {else}
		            {$mi.name}
		        {/if}
		    </h1>

		    <h4>
		        {if $mi.role neq ''}{$mi.role}, {/if}
		        {if $mi.company_url neq ''}
		            <a href="{$mi.company_url}">{$mi.company}</a>
		        {else}
    		        {$mi.company}
    		    {/if}
		    </h4>
		</div>

    </dt>

    <dd>
        {$mi.summary}
    </dd>

    {/foreach}

</dl>

</div>

<div class="meeting content">{$e.after_text}</div>

{if not $simple and $smarty.now < strtotime( $e.date ) and $user.privs eq 1}
    <div class="meeting buttons" id="rsvp_div_{$e.id}">

        {if $user->getPreference( 'meeting.attending.'|cat:$e.id ) eq 'ATTENDING'
                or $user->getPreference( 'meeting.attending.'|cat:$e.id ) eq 'NOT_ATTENDING'}
            <p>
            You have already RSVP'd for this meeting and told us that you will
            {if $user->getPreference( 'meeting.attending.'|cat:$e.id ) eq 'NOT_ATTENDING'}not{/if}
            be attending. Please email
            {mailto text=$config.meeting.rsvp_to_name address=$config.meeting.rsvp_to_email}
            directly to change this.
            </p>
        {else}
            <p>
    	    Please RSVP for this meeting:
	        <button id="rsvp_attending_{$e.id}"     name="Attending" value="Attending" type="button">Attending</button>
	        <button id="rsvp_not_attending_{$e.id}" name="Not Attending" value="Not Attending" type="button">Not Attending</button>
	        </p>
	    {/if}
    </div>

        <script type="text/javascript">
        {literal}

            $( '#rsvp_attending_{/literal}{$e.id}{literal}' ).click( function() {

                $( '#rsvp_div_{/literal}{$e.id}{literal}' ).hide( 400 );
            	$( '#rsvp_div_{/literal}{$e.id}{literal}' ).html('');

                $.getJSON( "{/literal}{genUrl controller='meeting' action='rsvp'}/id/{$e.id}/answer/attend"{literal},
                        function( data ) {
                            switch( data['response'] )
                            {
                                case 1:
                                    alert( "We have recorded your intention to attend this meeting. Thanks!" );
                                    break;
                                case 0:
                                    alert( {/literal}"ERROR: We could no record your intention to attend this meeting. Please email {$config.meeting.rsvp_to_name} directly at {$config.meeting.rsvp_to_email}."{literal} );
                                    break;
                            }
                        }
                );
            });

            $( '#rsvp_not_attending_{/literal}{$e.id}{literal}' ).click( function() {

                $( '#rsvp_div_{/literal}{$e.id}{literal}' ).hide( 400 );
                $( '#rsvp_div_{/literal}{$e.id}{literal}' ).html('');

                $.getJSON( "{/literal}{genUrl controller='meeting' action='rsvp'}/id/{$e.id}/answer/noattend"{literal},
                        function( data ) {
                            switch( data['response'] )
                            {
                                case 1:
                                    alert( "We have recorded your intention to not attend this meeting. We hope you can make the next one." );
                                    break;
                                case 0:
                                    alert( {/literal}"ERROR: We could no record your intention to not attend this meeting. Please email {$config.meeting.rsvp_to_name} directly at {$config.meeting.rsvp_to_email}."{literal} );
                                    break;
                            }
                        }
                );
            });

        {/literal}
        </script>


    </div>
{/if}

{/foreach}

</div>
