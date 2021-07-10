<script>

    //////////////////////////////////////////////////////////////////////////////////////
    // we'll need these handles to html elements in a few places:

    const btn_new_core_link              = $( '#btn-create-cl'   );
    const div_message_new_cl             = $( '#message-new-cl'  );
    const div_core_link_area             = $( '#core-links-area' );
    const table                          = $( '.table-responsive-ixp-no-header' );
    const subnet_input                   = $( "#subnet" );

    //////////////////////////////////////////////////////////////////////////////////////
    // action bindings:

    $( document ).ready( function() {
        table.dataTable( {
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
        } ).show();

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
    $( ".btn-delete-cl" ).click( function( e ) {
        e.preventDefault();
        deleteElement( true , this.href );
    });

    /**
     * Event to delete the core bundle
     */
    $( '.btn-delete-cb' ).click( function( e ) {
        e.preventDefault();
        deleteElement( false , this.href );
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
        if( subnet_input.val() !== '' ){
            if( !validSubnet( subnet_input.val() ) ){
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
        if( $( "#cl-subnet-1" ).val() ) {
            let subnet = $( "#cl-subnet-1" ).val();
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
    $( '.sp-dd' ).change( function( e ) {
        e.preventDefault();
        let sid      = $( this ).attr( 'data-id')
        let sside    = $( this ).attr( "data-value" );

        $( "#hidden-sp-" + sside + '-' + sid ).val( $( "#sp-" + sside + "-" + sid ).val() );
    });


    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///
    /// Function:
    ///

    /**
     * Function that allow to delete a Core Link or a Core Bundle
     *
     * @param {boolean}     deletecl        Do we need to delete the core link ? If not delete core bundle
     * @param {string}     url              Url to delete te object
     *
     */
    function deleteElement( deletecl , url ) {
        let elementName = deletecl ? 'core link' : 'core bundle'

        let html = `<form id="form-delete" method="POST" action="${url}">
                        <div>Do you really want to delete this ${elementName}? </div>
                        <input type="hidden" name="_token" value="<?= csrf_token() ?>">
                        <input type="hidden" name="_method" value="delete" />
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

        $("#sp-a-1 option:nth-child(2)").attr('selected','selected').trigger( 'change.select2' );
        $("#sp-b-1 option:nth-child(2)").attr('selected','selected').trigger( 'change.select2' );

        $( "#hidden-sp-a-1" ).val( $( "#sp-a-1" ).val() );
        $( "#hidden-sp-b-1" ).val( $( "#sp-b-1" ).val() );
    }

</script>