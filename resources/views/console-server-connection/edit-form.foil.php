<div class="card">
    <div class="card-body">
        <?= Former::open()->method(  $t->data['params']['isAdd'] ? 'POST' : 'PUT'  )
            ->id( 'form' )
            ->action( $t->data['params']['isAdd'] ? route( $t->feParams->route_prefix . '@store' ) : route($t->feParams->route_prefix . '@update', [ 'id' => $t->data[ 'params'][ 'object']->id ] ) )
            ->customInputWidthClass( 'col-lg-4 col-sm-6' )
            ->customLabelWidthClass( 'col-lg-2 col-sm-3' )
            ->actionButtonsCustomClass( "grey-box")
        ?>

        <?= Former::text( 'description' )
            ->label( 'Description' )
            ->blockHelp( "Description of the device that this console port connects to. Usually a switch hostname." );
        ?>

        <?= Former::select( 'console_server_id' )
            ->id( 'Server' )
            ->label( 'Console Server' )
            ->placeholder( 'Select a console server' )
            ->fromQuery( $t->data[ 'params'][ 'servers' ], 'name' )
            ->addClass( 'chzn-select' )
            ->blockHelp( "Choose the console server." );
        ?>

        <?= Former::select( 'custid' )
            ->id( 'cust' )
            ->label( ucfirst( config( 'ixp_fe.lang.customer.one' ) ) )
            ->placeholder( 'Select a ' . config( 'ixp_fe.lang.customer.one' ) )
            ->fromQuery( $t->data[ 'params'][ 'custs' ], 'name' )
            ->addClass( 'chzn-select' )
            ->blockHelp( 'This field exists as you may co-locate some equipment for your ' . config( 'ixp_fe.lang.customer.one' )  . '. Ordinarily, just pick your IXP ' . config( 'ixp_fe.lang.customer.one' ) . '.' );
        ?>

        <?= Former::text( 'port' )
            ->label( 'Port' )
            ->blockHelp( "Enter the port number." );
        ?>

        <div id="autobaud-section" style="<?= $t->data['params']['object'] && $t->data['params']['object']->autobaud ? 'display: none;' : '' ?>">

            <?= Former::select( 'speed' )
                ->label( 'Speed' )
                ->placeholder( "Choose speed")
                ->options(   \IXP\Models\ConsoleServerConnection::$SPEED )
                ->addClass( 'chzn-select' )
                ->blockHelp( 'Enter the baud rate - used for your own informational purposes but could also be used for automated console server provisioning.' );
            ?>

            <?= Former::select( 'parity' )
                ->label( 'Parity' )
                ->placeholder( "Choose parity")
                ->options(   \IXP\Models\ConsoleServerConnection::$PARITY )
                ->addClass( 'chzn-select' )
                ->blockHelp( 'Enter the parity - used for your own informational purposes but could also be used for automated console server provisioning.' );
            ?>

            <?= Former::select( 'stopbits' )
                ->label( 'Stopbits' )
                ->placeholder( "Choose stop bits")
                ->options(   \IXP\Models\ConsoleServerConnection::$STOP_BITS )
                ->addClass( 'chzn-select' )
                ->blockHelp( 'Enter the number of stop bits - used for your own informational purposes but could also be used for automated console server provisioning.' );
            ?>

            <?= Former::select( 'flowcontrol' )
                ->label( 'Flow Control' )
                ->placeholder( "Choose flow control")
                ->options(   \IXP\Models\ConsoleServerConnection::$FLOW_CONTROL )
                ->addClass( 'chzn-select' )
                ->blockHelp( 'Enter the flowcontrol status - used for your own informational purposes but could also be used for automated console server provisioning.' );
            ?>

        </div>


        <?= Former::checkbox( 'autobaud' )
            ->label( '&nbsp;' )
            ->text( 'Autobaud' )
            ->value( 1 )
            ->inline()
            ->blockHelp( "Indicate whether autobaud is supported - used for your own informational purposes but could also be used for automated console server provisioning." );
        ?>

        <div class="form-group col-lg-8 col-sm-12">
            <div class="col-lg-offset-2 col-sm-offset-2">
                <div class="card mt-4">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs">
                            <li role="presentation" class="nav-item">
                                <a class="tab-link-body-note nav-link active" href="#body">Notes</a>
                            </li>
                            <li role="presentation" class="nav-item">
                                <a class="tab-link-preview-note nav-link" href="#preview">Preview</a>
                            </li>
                        </ul>
                    </div>

                    <div class="tab-content card-body">
                        <div role="tabpanel" class="tab-pane show active" id="body">
                            <?= Former::textarea( 'notes' )
                                ->id( 'notes' )
                                ->label( '' )
                                ->rows( 15 )
                            ?>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="preview">
                            <div class="bg-light p-4 well-preview">
                                Loading...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?= Former::actions(
            Former::primary_submit( $t->data['params']['isAdd'] ? 'Create' : 'Save Changes' )->class( "mb-2 mb-sm-0"),
            Former::secondary_link( 'Cancel' )->href( isset( $t->data[ 'params'][ "cs" ] ) ? route ($t->feParams->route_prefix . '@listPort' , [ "cs" => $t->data[ 'params'][ "cs" ] ] )  : route ($t->feParams->route_prefix . '@list') )->class( "mb-2 mb-sm-0"),
            Former::success_button( 'Help' )->id( 'help-btn' )->class( "mb-2 mb-sm-0")
        );
        ?>

        <?= Former::hidden( 'cs' )
            ->value( $t->data[ 'params' ][ "cs" ] ?? "" )
        ?>

        <?= Former::close() ?>
    </div>
</div>
