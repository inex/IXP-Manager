<div class="well">

    <?= Former::open()->method( 'POST' )
        ->id( 'form' )
        ->action( route( $t->feParams->route_prefix . '@store' ) )
        ->customWidthClass( 'col-sm-3' )
    ?>

    <?= Former::text( 'description' )
        ->label( 'Description' )
        ->blockHelp( "Description of the device that this console port connects to. Usually a switch hostname." );
    ?>

    <?= Former::select( 'custid' )
        ->id( 'cust' )
        ->label( 'Customer' )
        ->placeholder( 'Select a customer' )
        ->fromQuery( $t->data[ 'params'][ 'custs' ], 'name' )
        ->addClass( 'chzn-select' )
        ->blockHelp( "This field exists as you may co-locate some equipment for your customers. Ordinarily, just pick your IXP customer." );
    ?>

    <?= Former::text( 'port' )
        ->label( 'Port' )
        ->blockHelp( "Enter the port number." );
    ?>

    <div id="autobaud-section" style="<?= $t->data['params']['object'] && $t->data['params']['object']->getAutobaud() ? 'display: none;' : '' ?>">

        <?= Former::select( 'speed' )
            ->label( 'Speed' )
            ->placeholder( "Choose speed")
            ->options(   Entities\ConsoleServerConnection::$SPEED )
            ->addClass( 'chzn-select' )
            ->blockHelp( 'Enter the baud speed - used for your own informational purposes but could also be used for automated console server provisioning.' );
        ?>

        <?= Former::select( 'parity' )
            ->label( 'Parity' )
            ->placeholder( "Choose parity")
            ->options(   Entities\ConsoleServerConnection::$PARITY )
            ->addClass( 'chzn-select' )
            ->blockHelp( 'Enter the parity - used for your own informational purposes but could also be used for automated console server provisioning.' );
        ?>


        <?= Former::select( 'stopbits' )
            ->label( 'Stopbits' )
            ->placeholder( "Choose stop bits")
            ->options(   Entities\ConsoleServerConnection::$STOP_BITS )
            ->addClass( 'chzn-select' )
            ->blockHelp( 'Enter the number of stop bits - used for your own informational purposes but could also be used for automated console server provisioning.' );
        ?>


        <?= Former::select( 'flowcontrol' )
            ->label( 'Flow Control' )
            ->placeholder( "Choose flow control")
            ->options(   Entities\ConsoleServerConnection::$FLOW_CONTROL )
            ->addClass( 'chzn-select' )
            ->blockHelp( 'Enter the flowcontrol status - used for your own informational purposes but could also be used for automated console server provisioning.' );
        ?>

    </div>


    <?= Former::checkbox( 'autobaud' )
        ->label( '&nbsp;' )
        ->text( 'Autobaud' )
        ->value( 1 )
        ->blockHelp( "Indicate is autobaud is supported - used for your own informational purposes but could also be used for automated console server provisioning." );
    ?>

    <div class="form-group">

        <label for="notes" class="control-label col-lg-2 col-sm-4">Notes</label>
        <div class="col-sm-8">

            <ul class="nav nav-tabs">
                <li role="presentation" class="active">
                    <a class="tab-link-body-note" href="#body">Notes</a>
                </li>
                <li role="presentation">
                    <a class="tab-link-preview-note" href="#preview">Preview</a>
                </li>
            </ul>

            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="body">

                    <textarea class="form-control" style="font-family:monospace;" rows="20" id="notes" name="notes"><?= $t->data[ 'params'][ 'notes' ] ?></textarea>
                </div>
                <div role="tabpanel" class="tab-pane" id="preview">
                    <div class="well well-preview" style="background: rgb(255,255,255);">
                        Loading...
                    </div>
                </div>
            </div>

            <br><br>
        </div>

    </div>


    <?= Former::actions(
        Former::primary_submit( $t->data['params']['isAdd'] ? 'Add' : 'Save Changes' ),
        Former::default_link( 'Cancel' )->href( isset( $t->data[ 'params'][ "cs" ] ) ? route ($t->feParams->route_prefix . '@listPort' , [ "id " => $t->data[ 'params'][ "cs" ] ] )  : route ($t->feParams->route_prefix . '@list') ),
        Former::success_button( 'Help' )->id( 'help-btn' )
    );
    ?>

    <?= Former::hidden( 'id' )
        ->value( $t->data[ 'params'][ 'object'] ? $t->data[ 'params'][ 'object']->getId() : '' )
    ?>

    <?= Former::hidden( 'cs' )
        ->value( isset( $t->data[ 'params'][ "cs" ] ) ? $t->data[ 'params'][ "cs" ] : "" )
    ?>

    <?= Former::close() ?>

</div>
