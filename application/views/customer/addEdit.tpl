{include file="header.tpl"}

<!-- YAHOO Global Object source file --> 
<script type="text/javascript" src="{$config->web->basepath}/css/yui/build/yahoo/yahoo-min.js" ></script>
<!--CSS file (default YUI Sam Skin) -->
<link rel="stylesheet" type="text/css" href="{$config->web->basepath}/css/yui/build/calendar/assets/skins/sam/calendar.css">
<!-- Dependencies -->
<script type="text/javascript" src="{$config->web->basepath}/css/yui/build/yahoo-dom-event/yahoo-dom-event.js"></script>
<!-- Source file -->
<script type="text/javascript" src="{$config->web->basepath}/css/yui/build/calendar/calendar-min.js"></script>

<script type="text/javascript">
    {literal}
    YAHOO.namespace( "inex.customer.calendar" );
    YAHOO.inex.customer.calendar.launchCal = function () 
    {
        var myCal = new YAHOO.widget.Calendar( "datejoinContainer", {navigator:true} );
        myCal.render(); myCal.hide();
        
        var showCal = function () {
            myCal.show();
        }
            
        YAHOO.util.Event.addListener( "datejoin", "click", showCal );
        
        var getCalDate = function ( type, args ) {
            var dates = args[0];
            var date  = dates[0];
            
            var year  = date[0];
            var month = ( date[1].toString().length == 1 ) ? '0' + date[1] : date[1];
            var day   = ( date[2].toString().length == 1 ) ? '0' + date[2] : date[2];
            
            var dateString = year + "-" + month + "-" + day;
            
            YAHOO.util.Dom.get( 'datejoin' ).value  = dateString;
            
            myCal.hide();
        }
        
        myCal.selectEvent.subscribe( getCalDate );
    }
        
    YAHOO.util.Event.onDOMReady( YAHOO.inex.customer.calendar.launchCal );
    {/literal}
</script>


<div class="content">

{if $isEdit}
    <h2>Customer :: Editing <em>{$cust->name}</em></h2>
{else}
    <h2>Customer :: Add New</h2>
{/if}

{$form}

</div>

{include file="footer.tpl"}

