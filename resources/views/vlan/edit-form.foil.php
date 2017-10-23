
<div class="well col-sm-12">
    <?= Former::open()->method( 'POST' )
        ->id( 'form' )
        ->action( action ( $t->controller.'@store' ) )
        ->customWidthClass( 'col-sm-3' )
    ?>

    <?= Former::text( 'name' )
        ->label( 'Name' )
        ->blockHelp( "The name of your VLAN as presented in IXP Manager - e.g. <code>Peering LAN #1</code>" );
    ?>

    <?= Former::text( 'number' )
        ->label( '802.1q Tag' )
        ->blockHelp( "The VLAN number / 802.1q tag for this VLAN. (A number between 1 and 4096 but some switch platforms may have reserved numbers)." );
    ?>

    <?= Former::select( 'infrastructureid' )
        ->label( 'Infrastructure' )
        ->fromQuery( $t->data[ 'params'][ 'infrastructure'], 'name' )
        ->placeholder( 'Choose an infrastructure' )
        ->addClass( 'chzn-select' );
    ?>

    <?= Former::text( 'config_name' )
        ->label( 'Config Name' )
        ->blockHelp( "The name of the VLAN as it should entered in the switch configuration when using automation.<br><br>"
            . "Validation rules limit this to alphanumeric characters and the dash and underscore. You should also<br><br>"
            . "be mindful of any specific limitations on your specific switches.");
    ?>

    <?= Former::checkbox( 'private' )
        ->label( ' ' )
        ->text( 'Private VLAN between a subset of members' )
        ->checked_value( 1 )
        ->unchecked_value( 0 )
        ->blockHelp( "Check this if this is a private VLAN." );
    ?>

    <?= Former::checkbox( 'peering_matrix' )
        ->label( ' ' )
        ->text( 'Include VLAN in the peering matrix (see help)' )
        ->checked_value( 1 )
        ->unchecked_value( 0 )
        ->blockHelp( "Selecting this checkbox means that this VLAN will appear on the <a href='http://ixp-master.dev/peering-matrix' target='_blank'>peering matrix</a>. 
            Note that this does not mean that this matrix will be populated. For that, you need to <a href='https://github.com/inex/IXP-Manager/wiki/Peering-Matrix'>configure
            sflow support for this</a>." );
    ?>

    <?= Former::checkbox( 'peering_manager' )
        ->label( ' ' )
        ->text( 'Include VLAN in the peering manager (see help)' )
        ->checked_value( 1 )
        ->unchecked_value( 0 )
        ->blockHelp( "Selecting this checkbox means that this VLAN will appear on the members' peering manager. 
            Note that this does not mean that it will be populated. For that, you need to <a href='https://github.com/inex/IXP-Manager/wiki/Peering-Matrix'>configure
            sflow support for this</a>." );
    ?>

    <?= Former::textarea( 'notes' )
        ->label( 'Notes' )
        ->rows( 5 )
        ->blockHelp( '' );
    ?>

    <?= Former::actions(
        Former::primary_submit( $t->data[ 'params']['isAdd'] ? 'Add' : 'Save Changes' ),
        Former::default_link( 'Cancel' )->href( action ($t->controller.'@list') ),
        Former::success_button( 'Help' )->id( 'help-btn' )
    );
    ?>

    <?= Former::hidden( 'id' )
        ->value( $t->data[ 'params'][ 'object'] ? $t->data[ 'params'][ 'object']->getId() : '' )
    ?>

    <?= Former::close() ?>

</div>
