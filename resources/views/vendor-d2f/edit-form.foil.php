<div class="well col-sm-12">

    <?= Former::open()->method( 'POST' )
        ->id( 'form' )
        ->action( action( $t->controller.'@store' ) )
        ->customWidthClass( 'col-sm-3' )
    ?>

    <?= Former::text( 'name' )
        ->label( 'Name' )
        ->blockHelp( "The full name of the vendor (e.g. <code>Cisco Systems</code>." );
    ?>

    <?= Former::text( 'shortname' )
        ->label( 'Shortname' )
        ->blockHelp( "A single work short form version of vendor name (e.g. <code>Cisco</code>." );
    ?>

    <!-- DEPRECATED: Nagios name is deprecated and no longer used - will be removed in a future version -->
    <?= Former::hidden( 'nagios_name' ) ?>

    <?= Former::text( 'bundle_name' )
        ->label( 'Bundle Name' )
        ->blockHelp( "The bundle name is used for orchastration / automated switch configuration. Some switches / routers use a "
            . "specific bundle name for port channels / LAGs / aggregate ports. On Cisco this would be <code>Port-channel</code> "
            . "for example. If your device has such a naming convention, please add the base name here (i.e. no trailing "
            . "number as this will be added when creating the interface)." );
    ?>

    <?= Former::actions(
        Former::primary_submit( 'Save Changes' ),
        Former::default_link( 'Cancel' )->href( action ($t->controller.'@list') ),
        Former::success_button( 'Help' )->id( 'help-btn' )
    );
    ?>

    <?= Former::hidden( 'id' )
        ->value( $t->params[ 'object'] ? $t->params[ 'object']->getId() : '' )
    ?>

    <?= Former::close() ?>

</div>

