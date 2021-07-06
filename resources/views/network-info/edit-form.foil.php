<div class="card col-sm-12">
    <div class="card-body">
        <?= $t->alerts() ?>
        <?= Former::open()->method( $t->data['params']['isAdd'] ? 'POST' : 'PUT' )
            ->id( 'form' )
            ->action( $t->data['params']['isAdd'] ? route( $t->feParams->route_prefix . '@store' ) : route($t->feParams->route_prefix . '@update', [ 'id' => $t->data[ 'params'][ 'object']->id ] ) )
            ->customInputWidthClass( 'col-lg-4 col-sm-6' )
            ->customLabelWidthClass( 'col-lg-3 col-sm-4' )
            ->actionButtonsCustomClass( "grey-box")
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
            ->fromQuery( \IXP\Models\Router::$PROTOCOLS , 'name' )
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
            Former::primary_submit( $t->data['params']['isAdd'] ? 'Create' : 'Save Changes' )->id( 'btn-submit' )->class( "mb-2 mb-sm-0" ),
            Former::secondary_link( 'Cancel' )->href( route($t->feParams->route_prefix . '@list') )->class( "mb-2 mb-sm-0" ),
            Former::success_button( 'Help' )->id( 'help-btn' )->class( "mb-2 mb-sm-0" )
        ); ?>

        <?= Former::close() ?>
    </div>
</div>