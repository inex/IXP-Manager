<script>

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
     * display the arra to add new link to the core bundle
     */
    $( "#add-new-core-link" ).click(  () => { displayCoreLink() } );

    /**
     * Event to delete a core link
     */
    $( ".delete-cl" ).on( 'click', function(e){
        e.preventDefault();
        deleteElement( true , (this.id).substring(10) );
    });


    /**
     * Event to delete the core bundle
     */
    $( "a[id|='cb-delete']" ).on( 'click', function(e){
        e.preventDefault();
        deleteElement( false , null );
    });

    /**
     * Check if all the switch ports have been chosen before submit
     */
    $('#core-link-form').submit(function( e ) {
        $( ".subnet-cl" ).each(function() {
            if( $( this ).val() !== '' ){
                if( !validSubnet( $( this ).val() ) ){
                    $("#message-cl").html("<div class='alert alert-danger' role='alert'> The subnet " + $( this ).val() + " is not valid! </div>");
                    e.preventDefault();
                }
            }
        });
    });

    /**
     * Check if the subnet is valid before submit the core bundle settings form
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
     * Check if all the switch ports have been chosen before submit
     */
    $('#new-core-links-submit-btn').click(function() {
        $("#message-new-cl").html('');

        if( !$( "#sp-a-1" ).val() || !$( "#sp-b-1" ).val() ){
            $( "#message-new-cl" ).append( "<div class='alert alert-danger' role='alert'>You need to select switch ports.</div>" );
            return false;
        }

        // check if the subnet is valid
        if( $( "#cl-subnet-1").val() ) {
            let subnet = $( "#cl-subnet-1").val();
            if( !validSubnet( subnet ) ){
                error = true;
                $( "#message-new-cl" ).append(  "<div class='alert alert-danger' role='alert'>The subnet " + subnet + " is not valid! </div>" );
                return false;
            }
        }
    });


    /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
    ///
    /// Function:
    ///


    /**
     * Function that allow to delete a Core Link or a Core Bundle
     *
     * @param {boolean}     deletecl    Do we need to delete the core link ? If not delete core bundle
     * @param {integer}     clid        The core link Id
     *
     */
    function deleteElement( deletecl , clid ){

        let urlAction, elementName, elementId;

        if( deletecl ){
             urlAction = "<?= route('core-link@delete') ?>";
             elementName = "Core link";
             elementId = clid;
        } else {
             urlAction = "<?= route('core-bundle@delete' ) ?>";
             elementName = "Core Bundle";
             elementId = <?= $t->cb->getId() ?>;
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
                        $('.bootbox.modal').modal('hide');
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
     * event onchange on the switch port dropdowns
     * Set the value of the switch port dropdown into input hidden
     */
    $(document).on('change', ".sp-dd" ,function(e){
        e.preventDefault();
        let sid = ( this.id ).substring( 5 );
        let sside = $( this ).attr( "data-value" );

        $( "#hidden-sp-" + sside + '-' + sid ).val( $("#sp-"+ sside + "-" + sid).val() );

    });

    /**
     * Display the form to add a new core link to the core bundle
     */
    function displayCoreLink(){
        $('#core-links-area').find( 'label' ).removeClass().addClass( 'col-sm-6 col-lg-3' );
        $('#core-links-area').find( '.new-core-link-input' ).parent().removeClass().addClass( 'col-lg-4 col-sm-6' );

        $('#core-links-area').css( 'opacity' , '100' );
        $('#core-links-area').show();
        $('#add-new-core-link').attr( 'disabled', 'disabled' );

    }

</script>