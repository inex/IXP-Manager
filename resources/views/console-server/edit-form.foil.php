<div class="well">

    <?= Former::open()->method( 'POST' )
        ->id( 'form' )
        ->action( route( $t->feParams->route_prefix.'@store' ) )
        ->customInputWidthClass( 'col-sm-3' )
    ?>

    <?= Former::text( 'name' )
        ->label( 'Name' )
        ->blockHelp( "The name of the console server. Typically you should use the host part of the hostname." );
    ?>

    <?= Former::text( 'hostname' )
        ->label( 'Hostname' )
        ->blockHelp( "The hostname of the console server. This should be in DNS and should be resolvable." );
    ?>

    <?= Former::select( 'cabinet' )
        ->id( 'cabinet' )
        ->label( 'Rack' )
        ->addClass( 'chzn-select' )
        ->fromQuery( [ '' => '' ] + $t->data[ 'params'][ 'cabinets' ], 'name' )
        ->blockHelp( "The rack where the console server is located." );
    ?>

    <?= Former::select( 'vendor' )
        ->id( 'vendor' )
        ->fromQuery( [ '' => '' ] + $t->data[ 'params'][ 'vendors' ], 'name' )
        ->addClass( 'chzn-select' )
        ->label( 'Vendor' )
        ->placeholder( '' )
        ->blockHelp( "If the vendor is not listed here, you can "
            . '<a href="' . route( 'vendor@add' ) . '">add them by clicking here</a>.' );
    ?>

    <?= Former::text( 'model' )
        ->label( 'Model' )
        ->blockHelp( "The model of the console server." );
    ?>

    <?= Former::text( 'serial_number' )
        ->label( 'Serial Number' )
        ->blockHelp( "The serial number of the console server." );
    ?>

    <?= Former::checkbox( 'active' )
        ->label( '&nbsp;' )
        ->text( 'Active' )
        ->value( 1 )
        ->blockHelp( "Marking a console inactive will exclude it from, for example, Nagios configuration generation." );
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
        Former::primary_submit( $t->data['params']['isAdd'] ? 'Add' : 'Save Changes' )->id( 'btn-submit' ),
        Former::default_link( 'Cancel' )->href( route($t->feParams->route_prefix.'@list') ),
        Former::success_button( 'Help' )->id( 'help-btn' )
    );
    ?>

    <?= Former::hidden( 'id' )
        ->value( $t->data[ 'params'][ 'object'] ? $t->data[ 'params'][ 'object']->getId() : '' )
    ?>

    <?= Former::close() ?>

</div>