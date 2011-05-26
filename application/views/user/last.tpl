{include file="header.tpl" pageTitle="IXP Manager :: Member Dashboard"}

<div class="yui-g">

<div id="content">

<table class="adminheading" border="0">
<tr>
    <th class="User">
        Users :: Last Logins
    </th>
</tr>
</table>

<center>
<div id="lastContainer">
    <table id="lastTable">
        <thead>
        <tr>
            <th>Username</th>
            <th>Customer</th>
            <th>Last Login</th>
        </tr>
        </thead>

        <tbody>

        {foreach from=$last item=l}
        <tr>
            <td>{$l.u_username}</td>
            <td>{$l.c_shortname}</td>
            <td>{$l.up_value|date_format:"%Y-%m-%d %H:%M:%S"}</td>
        </tr>
        {/foreach}
        </tbody>
    </table>
</div>
</center>

<script type="text/javascript">
{literal}
var lastDataSource = new YAHOO.util.DataSource( YAHOO.util.Dom.get( "lastTable" ) );
lastDataSource.responseType = YAHOO.util.DataSource.TYPE_HTMLTABLE;

lastDataSource.responseSchema = {
    fields: [
        {key:'Username'},
        {key:'Shortname'},
        {key:'Last Login At'}
    ]
};

var lastColumnDefs = [
        {key:'Username'},
        {key:'Shortname'},
        {key:'Last Login At'}
];

var lastDataTable = new YAHOO.widget.DataTable( "lastContainer", lastColumnDefs, lastDataSource );
{/literal}
</script>

</div>
</div>

{include file="footer.tpl"}
