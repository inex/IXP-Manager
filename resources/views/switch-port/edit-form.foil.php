
<div class="well col-sm-12">

    <?= Former::open()->method( 'POST' )
        ->id( 'form' )
        ->action( route( $t->feParams->route_prefix.'@store' ) )
        ->customWidthClass( 'col-sm-3' )
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
            ->fromQuery( \Entities\SwitchPort::$TYPES )
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
            Former::default_link( 'Generate' )->id( "generate-btn" ),
            Former::success_button( 'Help' )->id( 'help-btn' )
        );
        ?>

        <div class="collapse col-sm-8" id="ports-area"></div>

        <div style="clear: both"></div>
        <br>
        <br>

    <?php endif; ?>

    <?= Former::actions(
        Former::primary_submit( $t->data[ 'params'][ 'isAdd'] ? 'Add' : 'Save Changes' )->id( 'btn-submit' ),
        Former::default_link( 'Cancel' )->href( route( $t->feParams->route_prefix.'@list') ),
        Former::success_button( 'Help' )->id( 'help-btn' ) )
        ->id( "submit-area" )->class( $t->data[ 'params'][ 'isAdd'] ? "collapse" : '' );
    ?>

    <div style="clear: both"></div>

    <?= Former::hidden( 'id' )
        ->value( $t->data[ 'params'][ 'object'] ? $t->data[ 'params'][ 'object']->getId() : '' )
    ?>

    <?= Former::hidden( 'isAdd' )
        ->value( $t->data[ 'params'][ 'isAdd'] ? 1 : 0 )
    ?>

    <?= Former::close() ?>

</div>

