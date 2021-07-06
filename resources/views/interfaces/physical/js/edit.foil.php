<script>
    let fanoutEnabled = <?= $t->enableFanout ? 'true' : 'false' ?>;

    const cb_fanout         = $('#fanout');
    const div_fanout        = $('#fanout-area');
    const in_fanout_checked = $('#fanout-checked');

    if( fanoutEnabled ) {
        handleFanoutEnabled();
        cb_fanout.on( 'click', handleFanoutEnabled );
    }

    function handleFanoutEnabled() {
        if( cb_fanout.is(":checked") ) {
            div_fanout.slideDown();
            in_fanout_checked.val( 1 );
        } else {
            div_fanout.slideUp();
            in_fanout_checked.val( 0 );
        }
    }

    $(document).ready(function(){
        $( "#notes" ).parent().removeClass().addClass( "col-sm-12" )
    });

</script>

