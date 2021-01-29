<div class="card">
    <div class="card-body">
        <?= Former::open()->method(  $t->data['params']['isAdd'] ? 'POST' : 'PUT'  )
            ->id( 'form' )
            ->action( $t->data['params']['isAdd'] ? route( $t->feParams->route_prefix . '@store' ) : route($t->feParams->route_prefix . '@update', [ 'id' => $t->data[ 'params'][ 'object']->id ] ) )
            ->customInputWidthClass( 'col-lg-4 col-sm-6' )
            ->customLabelWidthClass( 'col-lg-2 col-sm-3' )
            ->actionButtonsCustomClass( "grey-box")
        ?>

        <?= Former::text( 'name' )
            ->label( 'Name' )
            ->blockHelp( "The name of the console server. Typically you should use the host part of the hostname." );
        ?>

        <?= Former::text( 'hostname' )
            ->label( 'Hostname' )
            ->blockHelp( "The hostname of the console server. This should be in DNS and should be resolvable." );
        ?>

        <?= Former::select( 'cabinet_id' )
            ->id( 'cabinet' )
            ->label( 'Rack' )
            ->addClass( 'chzn-select' )
            ->placeholder( 'Choose a rack' )
            ->fromQuery( $t->data[ 'params'][ 'cabinets' ], 'name' )
            ->blockHelp( "The rack where the console server is located." );
        ?>

        <?= Former::select( 'vendor_id' )
            ->id( 'vendor' )
            ->placeholder( 'Choose a vendor' )
            ->fromQuery( $t->data[ 'params'][ 'vendors' ], 'name' )
            ->addClass( 'chzn-select' )
            ->label( 'Vendor' )
            ->blockHelp( "If the vendor is not listed here, you can "
                . '<a href="' . route( 'vendor@create' ) . '">add them by clicking here</a>.' );
        ?>

        <?= Former::text( 'model' )
            ->label( 'Model' )
            ->blockHelp( "The model of the console server." );
        ?>

        <?= Former::text( 'serialNumber' )
            ->label( 'Serial Number' )
            ->blockHelp( "The serial number of the console server." );
        ?>

        <?= Former::checkbox( 'active' )
            ->label( '&nbsp;' )
            ->text( 'Active' )
            ->value( 1 )
            ->inline()
            ->blockHelp( "Marking a console inactive will exclude it from, for example, Nagios configuration generation." );
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
            Former::primary_submit( $t->data['params']['isAdd'] ? 'Create' : 'Save Changes' )->id( 'btn-submit' )->class( "mb-2 mb-sm-0"),
            Former::secondary_link( 'Cancel' )->href( route($t->feParams->route_prefix.'@list') )->class( "mb-2 mb-sm-0"),
            Former::success_button( 'Help' )->id( 'help-btn' )->class( "mb-2 mb-sm-0")
        ); ?>

        <?= Former::close() ?>
    </div>
</div>