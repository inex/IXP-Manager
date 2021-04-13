<div class="card col-sm-12">
    <div class="card-body">
        <?= Former::open()->method( $t->data[ 'params'][ 'preAddForm'] ? 'GET' : 'POST' )
            ->id( 'form' )
            ->action( route( $t->data[ 'params'][ 'preAddForm'] ? $t->feParams->route_prefix . '@add-step-2' : $t->feParams->route_prefix . '@store' ) )
            ->customInputWidthClass( 'col-lg-4 col-md-5 col-sm-5' )
            ->customLabelWidthClass( 'col-lg-2 col-sm-3' )
            ->actionButtonsCustomClass( "grey-box")
        ?>

        <?= Former::select( 'vlan_id' )
            ->label( 'Vlan' )
            ->fromQuery( $t->data[ 'params'][ 'vlans'], 'name' )
            ->placeholder( 'Choose a vlan' )
            ->addClass( 'chzn-select' )
            ->disabled( !$t->data[ 'params'][ 'preAddForm'] );
        ?>

        <?= Former::select( 'protocol' )
            ->label( 'Protocol' )
            ->fromQuery( \IXP\Models\Router::$PROTOCOLS )
            ->placeholder( 'Choose a protocol' )
            ->addClass( 'chzn-select' )
            ->disabled( !$t->data[ 'params'][ 'preAddForm'] );
        ?>

        <?= Former::select( 'scheduled_at' )
            ->label( 'Scheduled' )
            ->options( \IXP\Models\AtlasRun::$SCHEDULED_AT )
            ->placeholder( 'Choose when to run' )
            ->addClass( 'chzn-select' )
            ->disabled( !$t->data[ 'params'][ 'preAddForm'] );
        ?>

        <div id="scheduled_at_area" class="collapse">
            <?= Former::date( 'scheduled_date' )
                ->label( ' ' )
                ->placeholder( '' )
                ->type( "date" )
                ->append( Former::date( 'scheduled_time' )->label( '' )->type( "time" )->class( 'ml-2' )->disabled( !$t->data[ 'params'][ 'preAddForm'] ) )
                ->blockHelp( "On what date/time the ATLAS run action will start" )
                ->disabled( !$t->data[ 'params'][ 'preAddForm'] );
            ?>
        </div>


        <?php if( !$t->data[ 'params'][ 'preAddForm'] ): ?>
            <?= Former::hidden( 'vlan_id' )
                ->value( $t->data[ 'params' ][ 'vlanid' ] )
            ?>

            <?= Former::hidden( 'protocol' )
                ->value( $t->data[ 'params' ][ 'protocol' ] )
            ?>

            <?= Former::hidden( 'scheduled_at' )
                ->value( $t->data[ 'params' ][ 'scheduled_at' ] )
            ?>

            <?= Former::hidden( 'scheduled_date' )
                ->value( $t->data[ 'params' ][ 'scheduled_date' ] )
            ?>

            <?= Former::hidden( 'scheduled_time' )
                ->value( $t->data[ 'params' ][ 'scheduled_time' ] )
            ?>

            <table id="list-port" class="table">
                <thead class="thead-dark border-0">
                    <tr>
                        <th class="d-flex border-bottom-0" colspan="3">
                            <div class="tw-mr-2 tw-mt-1">
                                <input type="checkbox" name="select-all" id="select-all" value="" <?= old( 'selected_custs' ) && count( old( 'selected_custs' ) ) == count( $t->data[ 'params'][ 'custs'] ) ? "checked" : "" ?>/>
                            </div>
                            <div class="mt-1 label-cust">
                                <?= ucfirst( config( "ixp_fe.lang.customer.many" ) )?>
                            </div>
                        </th>
                        <th class="border-bottom-0" colspan="2"></th>
                    </tr>
                </thead>
                <tbody>
                    <?php if( count( $t->data[ 'params'][ 'custs'] ) ): ?>
                        <?php $count = 1; ?>
                        <tr>
                            <?php foreach( $t->data[ 'params'][ 'custs'] as $c ): ?>
                                <td>
                                    <input type="checkbox" <?= !old( 'selected_custs' ) && $count <= 10 ? "checked" : "" ?> <?= old( 'selected_custs' ) && in_array( $c[ 'id' ], old( 'selected_custs' ) ) ? "checked" : "" ?> class="cust-checkbox mr-2" name="selected_custs[<?= $c[ "id" ] ?>]" id="custs-<?= $c[ "id" ] ?>" value="<?= $c[ "id" ] ?>" />

                                    <span class="label-cust cursor-pointer">
                                            <?= $c[ "name"] ?>
                                        </span>
                                </td>
                                <?php if( $count++ % 3 === 0 ) {
                                    echo "</tr><tr>";
                                } ?>
                            <?php endforeach;
                            if( !$count % 3 === 0 ) {
                                while( $count % 3 === 0 ) {
                                    echo '<td></td>';
                                    $count++;
                                }
                                echo "</tr>";
                            }
                            ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" class="text-center">
                                No matching records found
                            </td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        <?php endif; ?>

        <?= Former::actions(
            Former::primary_submit( $t->data['params']['isAdd'] ? 'Create' : 'Save Changes' )->class( "mb-2 mb-sm-0" ),
            Former::secondary_link( 'Cancel' )->href( route($t->data[ 'params'][ 'preAddForm'] ? $t->feParams->route_prefix . '@list' : $t->feParams->route_prefix . '@create' ) )->class( "mb-2 mb-sm-0" ),
            Former::success_button( 'Help' )->id( 'help-btn' )->class( "mb-2 mb-sm-0" )
        );
        ?>

        <?= Former::close() ?>
    </div>
</div>