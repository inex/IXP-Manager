{if isset( $frontend.columns.viewPanelTitle )}
    {assign var='title' value=$frontend.columns.viewPanelTitle}
    {assign var='title' value=$object.$title}
{else}
    {assign var='title' value=$frontend.pageTitle}
{/if}

{if $perspective neq 'panel'}
    {tmplinclude file="header.tpl"}

    <table class="adminheading" border="0">
    <tr>
        <th class="{$frontend.name}">
            {$frontend.pageTitle}
            {if isset( $frontend.columns.viewPanelTitle )}
                :: {$title}
            {/if}
        </th>
    </tr>
    </table>

    <div class="content">
{else}
    <div class="hd" id="viewPanelHeader">{$title}</div>
    <div class="bd" id="viewPanelBody">
{/if}


<table class="viewObject">

{foreach from=$frontend.columns.viewPanelRows item=title}
<tr>
    <td class="title">
        {$frontend.columns.$title.label}
    </td>
    <td class="value">
        {if isset( $frontend.columns.$title.type )}
            {if $frontend.columns.$title.type eq 'hasOne'}
                {assign var='model' value=$frontend.columns.$title.model}
                {assign var='field' value=$frontend.columns.$title.field}
                {$object->$model->$field}
            {elseif $frontend.columns.$title.type eq 'l2HasOne'}
                {assign var='l1model' value=$frontend.columns.$title.l1model}
                {assign var='l2model' value=$frontend.columns.$title.l2model}
                {assign var='field'   value=$frontend.columns.$title.field}
                {$object->$l1model->$l2model->$field}
            {elseif $frontend.columns.$title.type eq 'xlate'}
                {assign var='index' value=$object->$title}
                {$frontend.columns.$title.xlator.$index}
            {/if}
        {else}
            {$object->$title}
        {/if}
    </td>
</tr>
{/foreach}

<tr>
    <td></td>
    <td>
        <table border="0">
        <tr>
            <td>
                <form action="{genUrl controller=$frontend.controller action='edit' id=$object->id}" method="post">
                    <input type="submit" name="submit" class="button" value="edit" />
                </form>
            </td>
            <td>
                <form action="{genUrl controller=$frontend.controller action='delete' id=$object->id}" method="post">
                    <input type="submit" name="submit" class="button" value="delete" 
                        onClick="return confirm( 'Are you sure you want to delete this tuple?' );"
                    />
                </form>
            </td>
        </tr>
        </table>
    </td>
</tr>

</table>

{if $perspective neq 'panel'}
    </div>

    {tmplinclude file="footer.tpl"}
{else}
    </div>
    <div class="ft" id="viewPanelFooter">INEX IXP Manager</div>
    <span class="container-close" id="view-container-close" href="{$smarty.server.HTTP_REFERER}#">Close</span>
{/if}
