<script>

    //////////////////////////////////////////////////////////////////////////////////////
    // we'll need these handles to html elements in a few places:

    const btn_new_core_link              = $( '#add-new-core-link'  );
    const div_message_new_cl             = $( '#message-new-cl'     );
    const div_core_link_area             = $( '#core-links-area'    );

    //////////////////////////////////////////////////////////////////////////////////////
    // action bindings:

    $( document ).ready( function() {
        $('.table-responsive-ixp-no-header').show();

        $('.table-responsive-ixp-no-header').DataTable( {
            stateSave: true,
            stateDuration : DATATABLE_STATE_DURATION,
            responsive: true,
            ordering: false,
            searching: false,
            paging:   false,
            info:   false,
            columnDefs: [
                { responsivePriority: 1, targets: 0 },
                { responsivePriority: 2, targets: -1 }
            ],
        } );

        $( ".subnet-cl" ).parent().removeClass().addClass( "col-sm-8" );

    });

    //////////////////////////////////////////////////////////////////////////////////////
    // action bindings:

    /**
     * check if the subnet is valid
     */
    $( ".subnet" ).blur( function() { checkSubnet( this ) } );

    /**
     * display the area to add new link to the core bundle
     */
    btn_new_core_link.click(  () => { displayCoreLink() } );

    /**
     * Event to delete a core link
     */
    $( ".delete-cl" ).on( 'click', function( e ) {
        e.preventDefault();
        deleteElement( true , ( this.id ).substring(10) );
    });


    /**
     * Event to delete the core bundle
     */
    $( "a[id|='cb-delete']" ).on( 'click', function( e ) {
        e.preventDefault();
        deleteElement( false , ( this.id ).substring( 10 ) );
    });

    /**
     * Check if all the switch ports have been chosen before submitting the Core links form
     */
    $( '#core-link-form' ).submit( function( e ) {
        $( ".subnet-cl" ).each(function() {
            if( $( this ).val() !== '' ) {
                if( !validSubnet( $( this ).val() ) ) {
                    $( "#message-cl" ).html( "<div class='alert alert-danger' role='alert'> The subnet " + $( this ).val() + " is not valid! </div>" );
                    e.preventDefault();
                }
            }
        });
    });

    /**
     * Check if the subnet is valid before submitting the Core Bundle settings form
     */
    $('#core-bundle-form').submit(function( e ) {
        if( $( "#subnet" ).val() !== '' ){
            if( !validSubnet( $( "#subnet" ).val() ) ){
                $("#message-cb").html("<div class='alert alert-danger' role='alert'> The subnet " + $( this ).val() + " is not valid! </div>");
                e.preventDefault();
                $("html, body").animate({ scrollTop: 0 }, "slow");
                return false;
            }
        }
    });


    /**
     * Check if all the switch ports have been chosen before submitting the form to add a new Core Link
     */
    $('#new-core-links-submit-btn').click(function() {
        div_message_new_cl.html('');

        // check if the switch ports side A/B are selected
        if( !$( "#sp-a-1" ).val() || !$( "#sp-b-1" ).val() ){
            div_message_new_cl.append( "<div class='alert alert-danger' role='alert'>You need to select switch ports.</div>" );
            return false;
        }

        // check if the subnet is valid
        if( $( "#cl-subnet-1").val() ) {
            let subnet = $( "#cl-subnet-1").val();
            if( !validSubnet( subnet ) ){
                div_message_new_cl.append(  "<div class='alert alert-danger' role='alert'>The subnet " + subnet + " is not valid! </div>" );
                return false;
            }
        }
    });

    /**
     * event Onchange on the switch port dropdowns of the Core Link form
     * Set the value of the switch port dropdown into hidden inputs
     */
    $( document ).on( 'change', ".sp-dd" ,function( e ) {
        e.preventDefault();
        let sid     = ( this.id ).substring( 5 );
        let sside    = $( this ).attr( "data-value" );

        $( "#hidden-sp-" + sside + '-' + sid ).val( $( "#sp-"+ sside + "-" + sid ).val() );
    });


    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///
    /// Function:
    ///


    /**
     * Function that allow to delete a Core Link or a Core Bundle
     *
     * @param {boolean}     deletecl        Do we need to delete the core link ? If not delete core bundle
     * @param {integer}     elementId       The ID of the element to delete
     *
     */
    function deleteElement( deletecl , elementId ){

        let urlAction, elementName;

        if( deletecl ){
             urlAction = "<?= route('core-link@delete') ?>";
             elementName = "Core link";
        } else {
             urlAction = "<?= route('core-bundle@delete' ) ?>";
             elementName = "Core Bundle";
        }


        let html = `<form id="form-delete" method="POST" action="${urlAction}">
                        <div>Do you really want to delete this ${elementName}?</div>
                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                        <input type="hidden" name="id" value="${elementId}">
                    </form>`;

        bootbox.dialog({
            title: `Delete ${elementName}`,
            message: html ,
            buttons: {
                cancel: {
                    label: 'Close',
                    className: 'btn-secondary',
                    callback: function () {
                        $( '.bootbox.modal' ).modal( 'hide' );
                        return false;
                    }
                },
                submit: {
                    label: 'Delete',
                    className: 'btn-danger',
                    callback: function () {
                        $( '#form-delete' ).submit();
                    }
                }
            }
        });
    }

    /**
     * Display the form to add a new core link to the core bundle
     * Add and remove some CSS class for a better display
     */
    function displayCoreLink() {
        div_core_link_area.find( 'label' ).removeClass().addClass( 'col-sm-6 col-lg-3' );
        div_core_link_area.find( '.new-core-link-input' ).parent().removeClass().addClass( 'col-lg-4 col-sm-6' );

        div_core_link_area.css( 'opacity' , '100' );
        div_core_link_area.show();
        btn_new_core_link.attr( 'disabled', 'disabled' );

    }

</script>