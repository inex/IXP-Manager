
<div class="card col-sm-12">
    <div class="card-body">
        <?= Former::open()->method( 'POST' )
            ->id( 'form' )
            ->action( route ( $t->feParams->route_prefix . '@store' ) )
            ->customInputWidthClass( 'col-sm-3' )
        ?>

        <?= Former::text( 'name' )
            ->label( 'Name' )
            ->blockHelp( "The name of your VLAN as presented in IXP Manager - e.g. <code>Peering LAN #1</code>" );
        ?>

        <?= Former::number( 'number' )
            ->label( '802.1q Tag' )
            ->blockHelp( "The VLAN number / 802.1q tag for this VLAN. (A number between 1 and 4096 but some switch platforms may have reserved numbers)." );
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

        <div class="form-group">
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
            Former::primary_submit( $t->data[ 'params']['isAdd'] ? 'Add' : 'Save Changes' ),
            Former::secondary_link( 'Cancel' )->href( route ($t->feParams->route_prefix . '@list') ),
            Former::success_button( 'Help' )->id( 'help-btn' )
        )->class( "bg-light p-4 mt-4 shadow-sm text-center" );
        ?>

        <?= Former::hidden( 'id' )
            ->value( $t->data[ 'params'][ 'object'] ? $t->data[ 'params'][ 'object']->getId() : null )
        ?>

        <?= Former::close() ?>

    </div>
</div>
