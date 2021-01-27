<div class="card col-sm-12">
    <div class="card-body">
        <?= Former::open()->method( $t->data['params']['isAdd'] ? 'POST' : 'PUT' )
            ->id( 'form' )
            ->action( $t->data['params']['isAdd'] ? route( $t->feParams->route_prefix . '@store' ) : route($t->feParams->route_prefix . '@update', [ 'id' => $t->data[ 'params'][ 'object']->id ] ) )
            ->customInputWidthClass( 'col-lg-4 col-sm-6' )
            ->customLabelWidthClass( 'col-lg-3 col-sm-4' )
            ->actionButtonsCustomClass( "grey-box")
        ?>

        <?= Former::text( 'name' )
            ->label( 'Name' )
            ->blockHelp( "The name of your VLAN as presented in IXP Manager - e.g. <code>Peering LAN #1</code>" );
        ?>

        <?= Former::number( 'number' )
            ->label( '802.1q Tag' )
            ->blockHelp( "The VLAN number / 802.1q tag for this VLAN. (A number between 1 and 4096 but some switch platforms may have reserved numbers).<br><br>"
                . "<b>NB:</b> While it is technically possible to use the same VLAN across different infrastructures, this is a bad idea in IXP Manager. "
                . 'See <a href="https://github.com/inex/IXP-Manager/issues/517" target="_blank">this GitHub issue as one example for this</a>.'
            );
        ?>

        <?= Former::select( 'infrastructureid' )
            ->label( 'Infrastructure' )
            ->fromQuery( $t->data[ 'params'][ 'infrastructure'], 'name' )
            ->placeholder( 'Choose an infrastructure' )
            ->addClass( 'chzn-select' );
        ?>

        <?= Former::text( 'config_name' )
            ->label( 'Config Name' )
            ->blockHelp( "The name of the VLAN as it should entered in the switch configuration when using automation.<br><br>"
                . "Validation rules limit this to alphanumeric characters and the dash and underscore. You should also<br><br>"
                . "be mindful of any specific limitations on your specific switches.");
        ?>

        <?= Former::checkbox( 'private' )
            ->label( ' ' )
            ->text( 'Private VLAN between a subset of members' )
            ->value( 1 )
            ->inline()
            ->blockHelp( "Check this if this is a private VLAN." );
        ?>

        <?= Former::checkbox( 'peering_matrix' )
            ->label( ' ' )
            ->text( 'Include VLAN in the peering matrix (see help)' )
            ->value( 1 )
            ->inline()
            ->blockHelp( "Selecting this checkbox means that this VLAN will appear on the <a href='http://ixp-master.dev/peering-matrix' target='_blank'>peering matrix</a>. 
                Note that this does not mean that this matrix will be populated. For that, you need to <a href='https://github.com/inex/IXP-Manager/wiki/Peering-Matrix'>configure
                sflow support for this</a>." );
        ?>

        <?= Former::checkbox( 'peering_manager' )
            ->label( ' ' )
            ->text( 'Include VLAN in the peering manager (see help)' )
            ->value( 1 )
            ->inline()
            ->blockHelp( "Selecting this checkbox means that this VLAN will appear on the members' peering manager. 
                Note that this does not mean that it will be populated. For that, you need to <a href='https://github.com/inex/IXP-Manager/wiki/Peering-Matrix'>configure
                sflow support for this</a>." );
        ?>

        <div class="form-group col-sm-8">
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
                                ->rows( 10 )
                                ->blockHelp( "" )
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
            Former::primary_submit( $t->data[ 'params']['isAdd'] ? 'Create' : 'Save Changes' )->class( "mb-2 mb-sm-0" ),
            Former::secondary_link( 'Cancel' )->href( route ($t->feParams->route_prefix . '@list') )->class( "mb-2 mb-sm-0" ),
            Former::success_button( 'Help' )->id( 'help-btn' )->class( "mb-2 mb-sm-0" )
        );
        ?>

        <?= Former::close() ?>

        <div class="alert alert-info tw-mt-8" role="alert">
            When choosing a VLAN / 802.1q tag, note that while it is technically possible to use the same VLAN tag across different infrastructures,
            this is a bad idea in IXP Manager. Some components expect unique tags for peering LANs.
            See <a href="https://github.com/inex/IXP-Manager/issues/517" target="_blank">this GitHub issue as one example for this</a>.
        </div>
    </div>
</div>