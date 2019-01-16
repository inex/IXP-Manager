
<div class="well col-sm-12">
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
        ->blockHelp( "Check this if this is a private VLAN." );
    ?>

    <?= Former::checkbox( 'peering_matrix' )
        ->label( ' ' )
        ->text( 'Include VLAN in the peering matrix (see help)' )
        ->value( 1 )
        ->blockHelp( "Selecting this checkbox means that this VLAN will appear on the <a href='http://ixp-master.dev/peering-matrix' target='_blank'>peering matrix</a>. 
            Note that this does not mean that this matrix will be populated. For that, you need to <a href='https://github.com/inex/IXP-Manager/wiki/Peering-Matrix'>configure
            sflow support for this</a>." );
    ?>

    <?= Former::checkbox( 'peering_manager' )
        ->label( ' ' )
        ->text( 'Include VLAN in the peering manager (see help)' )
        ->value( 1 )
        ->blockHelp( "Selecting this checkbox means that this VLAN will appear on the members' peering manager. 
            Note that this does not mean that it will be populated. For that, you need to <a href='https://github.com/inex/IXP-Manager/wiki/Peering-Matrix'>configure
            sflow support for this</a>." );
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

                    <textarea class="form-control" style="font-family:monospace;" rows="20" id="notes" name="notes"><?= $t->data['params']['notes'] ?></textarea>
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
        Former::primary_submit( $t->data[ 'params']['isAdd'] ? 'Add' : 'Save Changes' ),
        Former::default_link( 'Cancel' )->href( route ($t->feParams->route_prefix . '@list') ),
        Former::success_button( 'Help' )->id( 'help-btn' )
    );
    ?>

    <?= Former::hidden( 'id' )
        ->value( $t->data[ 'params'][ 'object'] ? $t->data[ 'params'][ 'object']->getId() : null )
    ?>

    <?= Former::close() ?>

</div>
