<script>


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///
/// we'll need these handles to html elements in a few places:
///

const btn_advanced   = $( '#advanced-options' );

const cb_lag_framing = $( '#lag_framing' );

const div_advanced   = $( "#advanced-area" );
const div_fastlacp   = $( "#fastlacp-area" );


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///
/// action bindings:
///

// display or hide the advanced area
btn_advanced.click( () => { div_advanced.slideToggle() } );

// display or hide the fastlapc area
cb_lag_framing.change( () => { cb_lag_framing.is(":checked") ? div_fastlacp.slideDown() : div_fastlacp.slideUp() } );

/**
 * on click even allow to delete a Sflow receiver
 */
$("a[id|='delete-pi']").on('click', function(e){
    e.preventDefault();
    let piid = (this.id).substring(10);
    deletePopup( piid, <?= $t->vi->getId() ?> , 'pi' );
});

/**
 * on click even allow to delete a VLI
 */
$("a[id|='delete-vli']").on( 'click', function(e) {
    e.preventDefault();
    let vliid = (this.id).substring(11);
    deletePopup( vliid, <?= $t->vi->getId() ?>, 'vli' );
});

/**
 * on click even allow to delete a Sflow receiver
 */
$( "a[id|='duplicate-vli']" ).on( 'click', function(e) {
    e.preventDefault();
    let vliid = (this.id).substring(14);
    duplicateVliPopup( vliid, <?= $t->vi->getId() ?> );
});

/**
 * on click even allow to delete a Sflow receiver
 */
$( "a[id|='delete-sflr']" ).on( 'click', function(e) {
    e.preventDefault();
    let sflrid = (this.id).substring(12);
    deletePopup( sflrid, <?= $t->vi->getId() ?>, 'sflr' );
});

/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
///
/// Initial states
///

if ( $( '#name' ).val() != '' || $( '#description' ).val() != '' || ( $( '#channel-group' ).val() != '' && $( '#channel-group' ).val() != '0' ) || ( $( '#mtu' ).val() != '' && $( '#mtu' ).val() != '0' ) ) {
    div_advanced.show();
}


/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
/////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////




/**
 * function to show the duplicate vli popup
 */
function duplicateVliPopup( id, viid ){

    let html = `
            <p>
            Duplicating a VLAN interface allows you to copy IP addresses and other settings to a new VLAN
            interface on another VLAN. Typical use cases for this is creating a quarantine VLAN interface
            on an 802.1q tagged port for example.
            </p>
            <p>
            Duplicating can also be used to copy all values to a new VLAN with needing to remember / create the
            matching IP addresses. In this case, copy and then delete the older VLAN interface.
            </p>
            <p>
                <b>
                While IXP Manager will let you create multiple VLAN interfaces on an untagged port, it is
                only a convenience for the above but may have unexpected consequences in production!
                </b>
            </p>
            <p>
            Please select the VLAN: <select id="duplicateTo" class="chzn-select">
                <option></option>
            <?php foreach( $t->vls as $id => $vl): ?>
                <option value="<?= $id ?>"><?= $vl ?></option>
            <?php endforeach; ?>
            </select>
            </p>
        `;


    dialog = bootbox.dialog({
        message: html,
        title: "Duplicate the VLAN Interface",
        buttons: {
            cancel: {
                label: '<i class="fa fa-times"></i> Close',
                callback: function () {
                    $('.bootbox.modal').modal('hide');
                    return false;
                }
            },
            confirm: {
                label: '<i class="glyphicon glyphicon-ok"></i> Duplicate',
                callback: function () {
                    let duplicateTo = $( "#duplicateTo" );
                    if( duplicateTo.val() ) {
                        window.location.href = "<?= url( 'interfaces/vlan/duplicate' ) ?>/"+ id + "/to/" + duplicateTo.val();
                    }
                }
            }
        }
    });

    dialog.init( function(){
        $("#duplicateTo").select2({
            width: "50%", // need to override the changed default
            placeholder: "Select a VLAN..."
        });
    });
}

</script>
