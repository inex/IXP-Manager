<div class="card">
    <div class="card-body">
        <?= Former::open()->method( $t->data['params']['isAdd'] ? 'POST' : 'PUT' )
            ->id( 'form' )
            ->action( $t->data['params']['isAdd'] ? route( $t->feParams->route_prefix . '@store' ) : route($t->feParams->route_prefix . '@update', [ 'id' => $t->data[ 'params'][ 'object']->id ] ) )
            ->customInputWidthClass( 'col-lg-4 col-sm-6' )
            ->customLabelWidthClass( 'col-lg-2 col-sm-3' )
            ->actionButtonsCustomClass( "grey-box")
        ?>

        <?= Former::select( 'switchid' )
            ->label( 'Switch' )
            ->fromQuery( $t->data[ 'params'][ 'switches'], 'name' )
            ->placeholder( 'Choose a Switch' )
            ->addClass( 'chzn-select' )
            ->blockHelp( "The switch that this port belongs to." );
        ?>

        <?php if( !$t->data[ 'params'][ 'isAdd'] ): ?>
            <?= Former::text( 'name' )
                ->label( 'Name' )
                ->blockHelp( "The port name." );
            ?>
        <?php endif; ?>

        <?= Former::select( 'type' )
            ->label( 'Type' )
            ->fromQuery( \IXP\Models\SwitchPort::$TYPES )
            ->placeholder( 'Choose a Type' )
            ->addClass( 'chzn-select' )
            ->blockHelp( "The port type." );
        ?>

        <?php if( !$t->data[ 'params'][ 'isAdd'] ): ?>
            <?= Former::checkbox( 'active' )
                ->label( '&nbsp;' )
                ->text( 'Active' )
                ->value( 1 )
                ->check()
                ->inline()
                ->blockHelp( "Is the port active?" );
            ?>
        <?php endif; ?>


        <?php if( $t->data[ 'params'][ 'isAdd'] ): ?>
            <?= Former::number( 'numfirst' )
                ->label( 'Number of First Port' )
                ->blockHelp( "The number of the first port to add. This will be incremented by 1 for <em>Number of Ports</em> below." );
            ?>

            <?= Former::number( 'numports' )
                ->label( 'Number of Ports' )
                ->blockHelp( "The number of ports to be created starting from <em>Number of First Port</em> above." );
            ?>

            <?= Former::text( 'prefix' )
                ->label( 'Name in printf Format' )
                ->blockHelp( "The name of the port using printf format with a <code>%d</code> handle for the port number. For example: <code>Ethernet%d</code>." );
            ?>

            <?= Former::actions(
                Former::primary_button( 'Generate' )->id( "generate-btn" ),
                Former::success_button( 'Help' )->class( 'help-btn' )
            )->class( "bg-light p-4 mt-4 shadow-sm text-center" );
            ?>

            <div class="collapse col-sm-12 row" id="ports-area"></div>

        <?php endif; ?>

        <?= Former::actions(
            Former::primary_submit( $t->data[ 'params'][ 'isAdd'] ? 'Create' : 'Save Changes' )->id( 'btn-submit' )->class( "mb-2 mb-sm-0"),
            Former::secondary_link( 'Cancel' )->href( route( $t->feParams->route_prefix.'@list') )->class( "mb-2 mb-sm-0")
        )
            ->id( "submit-area" )->class(  $t->data[ 'params'][ 'isAdd'] ? "collapse" : '' )->class( "mb-2 mb-sm-0");
        ?>

        <?= Former::close() ?>
    </div>
</div>