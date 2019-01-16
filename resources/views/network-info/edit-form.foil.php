<div class="well col-sm-12">
    <?= $t->alerts() ?>
    <?= Former::open()->method( 'POST' )
        ->id( 'form' )
        ->action( route( $t->feParams->route_prefix . '@store' ) )
        ->customInputWidthClass( 'col-sm-3' )
    ?>

    <?= Former::select( 'vlanid' )
        ->label( 'Vlan' )
        ->fromQuery( $t->data[ 'params'][ 'vlans'], 'name' )
        ->placeholder( 'Choose a vlan' )
        ->addClass( 'chzn-select' )
        ->blockHelp( "" );
    ?>

    <?= Former::select( 'protocol' )
        ->label( 'Protocol' )
        ->fromQuery( \Entities\Router::$PROTOCOLS , 'name' )
        ->placeholder( 'Choose a protocol' )
        ->addClass( 'chzn-select' )
        ->blockHelp( "" );
    ?>

    <?= Former::text( 'network' )
        ->label( 'Network Address' )
        ->placeholder( '192.0.2.0 / 2001:db8:12::' )
        ->blockHelp( "" );
    ?>

    <?= Former::number( 'masklen' )
        ->label( 'Network Mask Length' )
        ->placeholder( 'e.g. 24 for ipv4, 64 for ipv6' )
        ->blockHelp( "" );
    ?>

    <?= Former::actions(
        Former::primary_submit( $t->data['params']['isAdd'] ? 'Add' : 'Save Changes' )->id( 'btn-submit' ),
        Former::default_link( 'Cancel' )->href( route($t->feParams->route_prefix . '@list') ),
        Former::success_button( 'Help' )->id( 'help-btn' )
    );
    ?>

    <?= Former::hidden( 'id' )
        ->value( $t->data[ 'params'][ 'object'] ? $t->data[ 'params'][ 'object']->getId() : '' )
    ?>

    <?= Former::close() ?>

</div>

