<?php if( $t->data[ 'params'][ 'isAdd'] ): ?>

    <div class="alert alert-info">
        <h4>Use of this method is discouraged!</h4>

        Switch ports are best added using the <a href="https://github.com/inex/IXP-Manager/wiki/Updating-Switches-and-Ports-via-SNMP" target="_blank">CLI scripts</a> or the View / Edit Ports (with SNMP poll) option from the <a href="<?= route( "switch@list" ) ?>">switch list page</a>. See <a href="https://github.com/inex/IXP-Manager/wiki/Switch-and-Switch-Port-Management">the documentation for more information.</a>
    </div>

<?php endif; ?>

<div class="well col-sm-12">

    <?= Former::open()->method( 'POST' )
        ->id( 'form' )
        ->action( route( $t->feParams->route_prefix.'@store' ) )
        ->customWidthClass( 'col-sm-3' )
    ?>

        <?= Former::select( 'switchid' )
            ->label( 'Cabinet' )
            ->fromQuery( $t->data[ 'params'][ 'switches'], 'name' )
            ->placeholder( 'Choose a Switch' )
            ->addClass( 'chzn-select' )
            ->blockHelp( "" );
        ?>

        <?php if( !$t->data[ 'params'][ 'isAdd'] ): ?>
            <?= Former::text( 'name' )
                ->label( 'Name' )
                ->blockHelp( "" );
            ?>
        <?php endif; ?>

        <?= Former::select( 'type' )
            ->label( 'Type' )
            ->fromQuery( \Entities\SwitchPort::$TYPES )
            ->placeholder( 'Choose a Type' )
            ->addClass( 'chzn-select' )
            ->blockHelp( "" );
        ?>

    <?php if( !$t->data[ 'params'][ 'isAdd'] ): ?>
        <?= Former::checkbox( 'active' )
            ->label( '&nbsp;' )
            ->text( 'Active' )
            ->value( 1 )
            ->check()
            ->blockHelp( "" );
        ?>
    <?php endif; ?>


    <?php if( $t->data[ 'params'][ 'isAdd'] ): ?>

        <?= Former::number( 'numfirst' )
            ->label( 'Number of First Port' )
            ->blockHelp( "" );
        ?>

        <?= Former::number( 'numports' )
            ->label( 'Number of Ports' )
            ->blockHelp( "" );
        ?>

        <?= Former::text( 'prefix' )
            ->label( 'printf Format' )
            ->blockHelp( "" );
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

