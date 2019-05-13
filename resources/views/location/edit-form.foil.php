<div class="card col-sm-12">
    <div class="card-body">

        <?= Former::open()->method( 'POST' )
            ->id( 'form' )
            ->action( route( $t->feParams->route_prefix . '@store' ) )
            ->customInputWidthClass( 'col-lg-4 col-md-5 col-sm-5' )
            ->customLabelWidthClass( 'col-lg-2 col-sm-3' )
            ->actionButtonsCustomClass( "grey-box")
        ?>

        <?= Former::text( 'name' )
            ->label( 'Name' )
            ->blockHelp( "The name of the data centre / facility / point of presence (PoP)." );
        ?>

        <?= Former::text( 'shortname' )
            ->label( 'Shortname' )
            ->blockHelp( "A short name, ideally less than 10 characters, that can be substituted for the full name above where space is contrained." );
        ?>

        <?= Former::text( 'tag' )
            ->label( 'Tag' )
            ->blockHelp( "Typically a lower case, 3-4 letter identifier. For example, INEX uses tags as part of our switch hostname to identify its facility." );
        ?>

        <?= Former::select( 'pdb_facility_id' )
            ->id( 'pdb_facility_id' )
            ->label( 'PeeringDB Facility' )
            ->placeholder( 'Please wait, loading...' )
            ->blockHelp( "If listed, identify the facility from PeeringDB's facility list. If it is not listed here and you believe it should be, "
                . "then please contact PeeringDB directly. Note also that IXP Manager caches this data for a number of hours - so an "
                . "<code>artisan cache:clear</code> is required if you get your facility listed and IXP Manager still does not have it." );
        ?>


        <?= Former::textarea( 'address' )
            ->label( 'Address' )
            ->rows( 5 )
            ->style( 'width:100%' )
            ->blockHelp( '' );
        ?>

        <div class="row mt-4">
            <div class="col-lg-6 col-md-12">
                <div class="card">
                    <div class="card-body former-input-col-sm-6 former-label-col-sm-6">
                        <h3>
                            NOC Details
                        </h3>
                        <hr>
                        <?= Former::text( 'nocphone' )
                            ->label( 'Phone' )
                            ->blockHelp( "" );
                        ?>

                        <?= Former::text( 'nocfax' )
                            ->label( 'Fax' )
                            ->blockHelp( "" );
                        ?>

                        <?= Former::text( 'nocemail' )
                            ->label( 'E-mail' )
                            ->blockHelp( "" );
                        ?>
                    </div>
                </div>
            </div>


            <div class="col-lg-6 col-md-12 mt-4 mt-sm-4 mt-lg-0">
                <div class="card">
                    <div class="card-body former-input-col-sm-6 former-label-col-sm-6">
                        <h3>
                            Office Details
                        </h3>
                        <hr>

                        <?= Former::text( 'officephone' )
                            ->label( 'Phone' )
                            ->blockHelp( "" );
                        ?>

                        <?= Former::text( 'officefax' )
                            ->label( 'Fax' )
                            ->blockHelp( "" );
                        ?>

                        <?= Former::text( 'officeemail' )
                            ->label( 'E-mail' )
                            ->blockHelp( "" );
                        ?>
                    </div>
                </div>
            </div>
        </div>


        <div class="form-group">
            <div class="col-lg-offset-2 col-sm-8">
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
                            <textarea class="form-control" style="font-family:monospace;" rows="20" id="notes" name="notes"><?= $t->data['params']['notes'] ?></textarea>
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
            Former::primary_submit( $t->data['params']['isAdd'] ? 'Add' : 'Save Changes' )->id( 'btn-submit' )->disabled( true )->class( "mb-2 mb-sm-0" ),
            Former::secondary_link( 'Cancel' )->href( route( $t->feParams->route_prefix . '@list') )->class( "mb-2 mb-sm-0" ),
            Former::success_button( 'Help' )->id( 'help-btn' )->class( "mb-2 mb-sm-0" )
        );
        ?>

        <?= Former::hidden( 'id' )
            ->value( $t->data[ 'params'][ 'object'] ? $t->data[ 'params'][ 'object']->getId() : '' )
        ?>

        <?= Former::close() ?>

    </div>
</div>
