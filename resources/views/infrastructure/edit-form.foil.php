<div class="card col-sm-12">
    <div class="card-body">
        <?= Former::open()->method( $t->data['params']['isAdd'] ? 'POST' : 'PUT' )
            ->id( 'form' )
            ->action( $t->data['params']['isAdd'] ? route( $t->feParams->route_prefix . '@store' ) : route($t->feParams->route_prefix . '@update', [ 'id' => $t->data[ 'params'][ 'object']->id ] ) )
            ->customInputWidthClass( 'col-lg-4 col-md-5 col-sm-5' )
            ->customLabelWidthClass( 'col-lg-2 col-sm-3' )
            ->actionButtonsCustomClass( "grey-box")
        ?>

        <?= Former::text( 'name' )
            ->label( 'Name' )
            ->blockHelp( "The name of this infrastructure. Displayed in a number of places. Examples at INEX are: INEX LAN1, INEX LAN2, INEX Cork." );
        ?>

        <?= Former::text( 'shortname' )
            ->label( 'Shortname' )
            ->blockHelp( "A lowercase single word to represent the infrastructure." );
        ?>

        <?= Former::select( 'country' )
            ->label( 'Country' )
            ->fromQuery( $t->data[ 'params'][ 'countries' ], 'name', 'iso_3166_2' )
            ->placeholder( 'Choose a country' )
            ->addClass( 'chzn-select' )
            ->blockHelp( 'The country is shown in the <a
                href="https://docs.ixpmanager.org/features/ixf-export/">IX-F Member
                Export</a> for the IXP. For IXPs that span more than one country, this
                should represent the head office / administrative location. Specific
                city and country information can be added to the facility / location
                entries which in turn will list the switch in the correct city / country
                in the export.' );
        ?>

        <?= Former::checkbox( 'isPrimary' )
            ->label( '&nbsp;' )
            ->text( 'Primary Infrastructure' )
            ->value( 1 )
            ->inline()
            ->blockHelp( "Only one infrastructure can be primary. Setting this will unset this on all other infrastructures. Usually used to "
                . "signify an infrastructure where <em>everyone</em> connects such as a primary peering LAN." );
        ?>

        <?= Former::select( 'ixf_ix_id' )
            ->id( 'ixf_ix_id' )
            ->label( 'IX-F DB IX ID' )
            ->placeholder( 'Please wait, loading...' )
            ->blockHelp( "Identify your IXP from the <a href=\"http://ml.ix-f.net/\">IX Federation's database</a>. If it does not exist there, "
                . "<a href=\"https://www.euro-ix.net/\">contact the euro-ix secretariat</a>.<br><br>Note the local copy of this list is "
                . "cached for two hours. Use 'artisan cache:clear' to reset it.");
        ?>

        <?= Former::select( 'peeringdb_ix_id' )
            ->id( 'peeringdb_ix_id' )
            ->label( 'Peering DB IX ID' )
            ->placeholder( 'Please wait, loading...' )
            ->blockHelp( "Identify your IXP from <a href=\"https://www.peeringdb.com/\">PeeringDB</a>. If it does not exist there, "
                . "then you should add it yourself through their web interface.<br><br>Note the local copy of this list is "
                . "cached for two hours. Use 'artisan cache:clear' to reset it.");
        ?>

        <div class="form-group row">
            <div class="col-sm-8">
                <div class="card">
                    <div class="card-header">
                        <ul class="nav nav-tabs card-header-tabs">
                            <li role="presentation" class="nav-item">
                                <a class="tab-link-body-note nav-link active" href="#body1">Public Notes</a>
                            </li>
                            <li role="presentation" class="nav-item">
                                <a class="tab-link-preview-note nav-link " href="#preview1">Preview</a>
                            </li>
                        </ul>
                    </div>
                    <div class="tab-content card-body">
                        <div role="tabpanel" class="tab-pane show active" id="body1">
                            <?= Former::textarea( 'notes' )
                                ->id( 'notes' )
                                ->class( 'notes' )
                                ->label( '' )
                                ->rows( 10 )
                                ->blockHelp( "These notes are visible (but not editable) to the member. You can use markdown here." )
                            ?>
                        </div>
                        <div role="tabpanel" class="tab-pane" id="preview1">
                            <div class="bg-light p-4 well-preview">
                                Loading...
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?= Former::actions(
            Former::primary_submit( $t->data['params']['isAdd'] ? 'Create' : 'Save Changes' )->id( 'btn-submit' )->disabled( true )->class( "mb-2 mb-sm-0" ),
            Former::secondary_link( 'Cancel' )->href( route($t->feParams->route_prefix . '@list') )->class( "mb-2 mb-sm-0" ),
            Former::success_button( 'Help' )->id( 'help-btn' )->class( "mb-2 mb-sm-0" )
        );
        ?>

        <?= Former::close() ?>
    </div>
</div>