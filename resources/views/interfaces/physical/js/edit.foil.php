<script type="module">
    $(document).ready(function() {
        let fanoutEnabled = <?= $t->enableFanout ? 'true' : 'false' ?>;

        const cb_fanout         = $('#fanout');
        const div_fanout        = $('#fanout-area');
        const in_fanout_checked = $('#fanout-checked');

        function handleFanoutEnabled() {
            if( cb_fanout.is(":checked") ) {
                div_fanout.slideDown();
                in_fanout_checked.val( 1 );
            } else {
                div_fanout.slideUp();
                in_fanout_checked.val( 0 );
            }
        }

        if( fanoutEnabled ) {
            handleFanoutEnabled();
            cb_fanout.on( 'click', handleFanoutEnabled );
        }

        $( "#notes" ).parent().removeClass().addClass( "col-sm-12" );
    });
</script>

