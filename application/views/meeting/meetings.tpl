{include file="header.tpl"}

{if $user.privs eq 3}
    <ul class="breadcrumb">
        <li>
            <a href="{genUrl}">Home</a> <span class="divider">/</span>
        </li>
        <li>
            <a href="{genUrl controller='meeting' action='list'}">Meetings</a> <span class="divider">/</span>
        </li>
        <li class="active">
            Member View
        </li>
    </ul>
{else}
    <div class="page-content">
    
        <div class="page-header">
            <h1>Meetings</h1>
        </div>
{/if}

{include file="message.tpl"}

<div class="meetings_index">
    <p>
        <form name="meeting_jumpto" class="form">
            <strong>Jump to:</strong>&nbsp;
        
            <select
                name="meetings_index"
                onChange="window.location.href=meeting_jumpto.meetings_index.options[selectedIndex].value">
            >
        
                <option></option>
                {foreach from=$entries item=e}
                    <option value="#{$e.id}">{$e.date|date_format:"%A, %B %e, %Y"}</option>
                {/foreach}
        
            </select>
        </form>
    </p>
</div>


{include file='meeting/core.tpl'}


{include file="footer.tpl"}
