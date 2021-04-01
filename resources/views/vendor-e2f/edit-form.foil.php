<div class="card">
    <div class="card-body">
        <?= Former::open()->method( $t->data['params']['isAdd'] ? 'POST' : 'PUT' )
            ->id( 'form' )
            ->action( $t->data['params']['isAdd'] ? route( $t->feParams->route_prefix . '@store' ) : route($t->feParams->route_prefix . '@update', [ 'id' => $t->data[ 'params'][ 'object']->id ] ) )
            ->customInputWidthClass( 'col-lg-3 col-sm-6' )
            ->customLabelWidthClass( 'col-lg-2 col-sm-4' )
            ->actionButtonsCustomClass( "grey-box")
        ?>

        <?= Former::text( 'name' )
            ->label( 'Name' )
            ->blockHelp( "The full name of the vendor (e.g. <code>Cisco Systems</code>." );
        ?>

        <?= Former::text( 'shortname' )
            ->label( 'Shortname' )
            ->blockHelp( "A single work short form version of vendor name (e.g. <code>Cisco</code>." );
        ?>

        <?= Former::text( 'bundle_name' )
            ->label( 'Bundle Name' )
            ->blockHelp( "The bundle name is used for orchestration / automated switch configuration. Some switches / routers use a "
                . "specific bundle name for port channels / LAGs / aggregate ports. On Cisco this would be <code>Port-channel</code> "
                . "for example. If your device has such a naming convention, please add the base name here (i.e. no trailing "
                . "number as this will be added when creating the interface)." );
        ?>

        <?= Former::actions(
            Former::primary_submit( $t->data['params']['isAdd'] ? 'Create' : 'Save Changes' )->class( "mb-2 mb-sm-0" ),
            Former::secondary_link( 'Cancel' )->href( route( $t->feParams->route_prefix . '@list') )->class( "mb-2 mb-sm-0" ),
            Former::success_button( 'Help' )->id( 'help-btn' )->class( "mb-2 mb-sm-0" )
        ); ?>

        <?= Former::close() ?>
    </div>
</div>

