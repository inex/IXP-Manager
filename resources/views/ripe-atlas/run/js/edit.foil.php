<script>

    /**
     * Check or uncheck all the checkboxes
     */
    $( "#select-all"  ).on( 'change', function() {
        $( ".cust-checkbox"   ).prop('checked',     $( "#select-all"  ).is( ":checked" ) );
    });

    /**
     * Check or uncheck all the checkboxes
     */
    $( ".label-cust"  ).on( 'click', function() {
        let checkbox = $( this ).closest( 'td' ).find( '[type=checkbox]' );
        checkbox.prop('checked', !checkbox.is( ":checked" ) );
    });

    /**
     * Show/hide the scheduled at input date
     */
    $( "#scheduled_at"  ).on( 'change', function() {
        $( "#scheduled_at_area"   ).toggleClass( 'collapse',     $( this ).val() == <?= \IXP\Models\AtlasRun::SCHEDULED_AT_DATETIME ?> ? false : true );
    });

    /**
     * Remove some CSS classes added by default by Former for a better display
     */
    $( "#scheduled_date" ).parent().find( '.form-group' ).removeClass().find( '.col-lg-4' ).removeClass();

    $( document ).ready(function() {

        // Hide or show the scheduled date input depending on the user choice
        $( "#scheduled_at_area"   ).toggleClass( 'collapse', $("#scheduled_at" ).val() == <?= \IXP\Models\AtlasRun::SCHEDULED_AT_DATETIME ?> ? false : true );

    });

</script>