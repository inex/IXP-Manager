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

    <?= Former::select( 'consoleserverid' )
        ->id( 'consoleserverid' )
        ->label( 'Console Server' )
        ->placeholder( 'Select the console server' )
        ->fromQuery( $t->data[ 'params'][ 'consoleservers' ], 'name' )
        ->addClass( 'chzn-select' )
        ->blockHelp( "Select the console server to which this device is connected.<br><br>"
            . "Console servers are added via the <em>Console Servers</em> menu option.");
    ?>

    <?= Former::text( 'port' )
        ->label( 'Port' )
        ->blockHelp( "Enter the port number." );
    ?>

    <?= Former::number( 'speed' )
        ->label( 'Speed' )
        ->blockHelp( "Enter the baud speed - just used for your own informational purposes." );
    ?>

    <?= Former::number( 'parity' )
        ->label( 'parity' )
        ->blockHelp( "Enter the parity - just used for your own informational purposes." );
    ?>


    <?= Former::number( 'stopbits' )
        ->label( 'Stopbits' )
        ->blockHelp( "Enter the number of stop bits - just used for your own informational purposes." );
    ?>

    <?= Former::number( 'flowcontrol' )
        ->label( 'Flow Control' )
        ->blockHelp( "Enter the flowcontrol status - just used for your own informational purposes." );
    ?>

    <?= Former::checkbox( 'autobaud' )
        ->label( '&nbsp;' )
        ->text( 'Autobaud' )
        ->value( 1 )
        ->blockHelp( "Indicate is autobaud is supported - just used for your own informational purposes." );
    ?>

    <div class="col-lg-offset-2 col-sm-offset-2 col-sm-8">

        <ul class="nav nav-tabs">
            <li role="presentation" class="active"><a id="tab-link-body" href="#body">Notes</a></li>
            <li role="presentation"><a  id="tab-link-preview" href="#preview">Preview</a></li>
        </ul>

        <div class="tab-content">
            <div role="tabpanel" class="tab-pane active" id="body">
                <textarea class="form-control" style="font-family:monospace;" rows="20" id="notes" name="notes"><?= $t->data[ 'params'][ 'notes' ] ?></textarea>
            </div>
            <div role="tabpanel" class="tab-pane" id="preview">
                <div id="well-preview" class="well" style="background: rgb(255,255,255);">
                    Loading...
                </div>
            </div>
        </div>

        <br><br>
    </div>

    <?= Former::actions(
        Former::primary_submit( $t->data['params']['isAdd'] ? 'Add' : 'Save Changes' ),
        Former::default_link( 'Cancel' )->href( $t->data[ 'params'][ "cs" ]  ? route ($t->feParams->route_prefix . '@listPort' , [ "id " => $t->data[ 'params'][ "cs" ] ] )  : route ($t->feParams->route_prefix . '@list') ),
        Former::success_button( 'Help' )->id( 'help-btn' )
    );
    ?>

    <?= Former::hidden( 'id' )
        ->value( $t->data[ 'params'][ 'object'] ? $t->data[ 'params'][ 'object']->getId() : '' )
    ?>

    <?= Former::hidden( 'cs' )
        ->value( $t->data[ 'params'][ "cs" ]  ? $t->data[ 'params'][ "cs" ] : "" )
    ?>

    <?= Former::close() ?>

</div>
