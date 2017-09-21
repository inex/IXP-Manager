<script>

var fanoutEnabled = <?= $t->enableFanout ? 'true' : 'false' ?>;

$(document).ready( function() {

    $( "#switch-port" ).on( 'change', excludeSp );
    $( "#switch" ).on( 'change', updateSwitchPort );

    if( fanoutEnabled ) {
        checkFanout();
        $('#fanout').on( 'click', checkFanout );
        setSpFanout();
        $( "#switch-port-fanout" ).on( 'change', excludeSp );
        $( "#switch-fanout" ).on( 'change', updateSwitchPort );
    }
});

function checkFanout(){
    if( $( '#fanout' ).prop( 'checked' ) ) {
        $( '#fanout-area' ).slideDown();
        $( "#fanout-checked" ).val( 1 );
    } else {
        $( '#fanout-area' ).slideUp();
        $( "#fanout-checked" ).val( 0 );
    }
}

function setSpFanout(){
    if( $( '#fanout' ).prop( 'checked' ) && $('#switch-fanout').val() != null ) {
        $('#switch-fanout').change();
    }
}


// this function exclude the switch port selected in the switch port fanout dropdown ( and vice versa )
function excludeSp() {
    let type = "";
    let otherType = '-fanout';

    if( $( this ).attr( "id" ).substr( -6 ) === "fanout" ) {
        type = "-fanout";
        otherType = '';
    }

    let selectedValue = $( "#switch-port" + type ).val();
    $( "#switch-port" + otherType +  " option" ).show().each(function( ) {
        if( $( this ).val() === selectedValue ){
            console.log( $( this ).val() );
            console.log( selectedValue );
            $( "#switch-port" + otherType +  " option[value='" + $( this ).val() + "']" ).hide();
        }
    });
    $( "#switch-port" + otherType ).trigger( "change" );
}


function updateSwitchPort() {
    let type, excludeSp, arrayType;

    if( $( this ).attr( "id" ).substr( -6 ) === "fanout" ) {
        type = "-fanout";
        arrayType = [ <?= \Entities\SwitchPort::TYPE_UNSET ?>, <?= \Entities\SwitchPort::TYPE_FANOUT ?> ];
        excludeSp = $( "#switch-port" ).val();
    } else {
        type = "";
        excludeSp = $( "#switch-port-fanout" ).val();
        arrayType = [ <?= \Entities\SwitchPort::TYPE_UNSET ?>,  <?= \Entities\SwitchPort::TYPE_PEERING ?> ];
    }

    $( "#switch-port" + type ).html( "<option value=\"\">Loading please wait</option>\n" ).trigger( "change" );

    switchId = $( "#switch" + type ).val();

    let url = "<?= url( '/api/v4/switch' )?>/" + switchId + "/ports";

    $.ajax( url )
        .done( function( data ) {
            options = "<option value=\"\">Choose a switch port</option>\n";

            $.each( data.switchports, function( key, port ) {
                let spFanoutVal = $( '#sp-fanout' ).val();
                if( ( ( port.pi_id === null && arrayType.indexOf( port.sp_type ) !== -1 ) || ( fanoutEnabled && port.sp_id === spFanoutVal ) ) && excludeSp !== port.sp_id) {
                    options += "<option";
                    if( port.sp_id == spFanoutVal ) {
                        options += ' selected="selected"';
                    }
                    options += " value=\"" + port.sp_id + "\">" + port.sp_name + " (" + port.sp_type_name + ")</option>\n";
                }
            });

            $( "#switch-port" + type ).html( options );
        })
        .fail( function() {
            options = "<option value=\"\">ERROR</option>\n";
            $( "#switch-port" + type ).html( options );
            alert( "Error running ajax query for " + url );
            throw new Error( "Error running ajax query for " + url );
        })
        .always( function() {
            $( "#switch-port" + type ).trigger( "change" );
        });
}


</script>
