<p>
    <em>
        This is public list of INEX Members' Meetings. INEX members should log into the IXP Manager
        where they can download presentations and view recorded presentations that were requested 
        to not be made public as well as access speaker contact details.
    </em>
</p>



{foreach from=$entries item=e}

<h1>{$e.title} &ndash; {$e.date|date_format:"%A, %B %e, %Y"}</h1>

<h4>In {if $e.venue_url neq ''}<a href="{$e.venue_url}">{$e.venue}</a>{else}{$e.venue}{/if} at {$e.time|date_format:"%H:%M"}</h4>

<div>{$e.before_text}</div>

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

		    <h3>
			{$mi.name}: {if $mi.role neq ''}{$mi.role}, {/if}{if $mi.company_url neq ''}<a href="{$mi.company_url}">{$mi.company}</a>{else}{$mi.company}{/if}
	            </h3>
    </dt>

    <dd>
	{$mi.title}

                {if $mi.presentation neq ''}
                    &nbsp;&nbsp;&nbsp;&nbsp;[<a href="{genUrl controller='meeting-item' action='get-presentation' id=$mi.id}">PRES</a>]
                {/if}
                {if $mi.video_url neq ''}
                    &nbsp;&nbsp;&nbsp;&nbsp;[<a href="{$mi.video_url}">VIDEO</a>]
                {/if}
            
                    </div>


        {if $mi.summary neq ''} <em>{$mi.summary}</em>{/if}
    </dd>

    {/foreach}

</dl>


<div>{$e.after_text}</div>

{/foreach}

