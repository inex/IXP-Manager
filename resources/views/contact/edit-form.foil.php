<div class="well">

    <?= Former::open()->method( 'POST' )
        ->id( 'form' )
        ->action( route( $t->feParams->route_prefix . '@store' ) )
        ->customWidthClass( 'col-sm-6' )
    ?>
    <div class="col-sm-6">
        <?= Former::text( 'name' )
            ->label( 'Name' )
            ->blockHelp( "" );
        ?>

        <?= Former::text( 'position' )
            ->label( 'Position' )
            ->blockHelp( "" );
        ?>

        <?php if( Auth::getUser()->isSuperUser() ):?>
            <?= Former::select( 'custid' )
                ->id( 'cust' )
                ->label( 'Customer' )
                ->placeholder( 'Select a customer' )
                ->fromQuery( $t->data[ 'params'][ 'custs' ], 'name' )
                ->addClass( 'chzn-select' )
                ->blockHelp( "" );
            ?>
        <?php endif; ?>

        <?= Former::text( 'email' )
            ->label( 'Email' )
            ->blockHelp( "" );
        ?>

        <?= Former::text( 'phone' )
            ->label( 'Phone' )
            ->blockHelp( "" );
        ?>

        <?= Former::text( 'mobile' )
            ->label( 'Mobile' )
            ->blockHelp( "" );
        ?>
    </div>

    <div class="col-sm-6">
        <?= Former::checkbox( 'facilityaccess' )
            ->label('&nbsp;')
            ->text( 'Multicast Enabled' )
            ->value( 1 )
            ->blockHelp( '' )
            ->check()
        ?>

        <?= Former::checkbox( 'mayauthorize' )
            ->label('&nbsp;')
            ->text( 'May Authorize' )
            ->value( 1 )
            ->blockHelp( '' )
            ->check()
        ?>

            <div class="form-group" style="display: inline; display: inline-flex">
                <label for="" class="control-label col-lg-2 col-sm-4">&nbsp;Role&nbsp;</label>
                <?php if( $t->data[ 'params'][ "allGroups" ] && isset( $t->data[ 'params'][ "allGroups" ][ 'ROLE' ] ) ): ?>

                    <?php foreach( $t->data[ 'params'][ "allGroups" ][ 'ROLE' ] as $role ): ?>
                        <?= Former::checkbox( "ROLE_" . $role[ 'id' ] )
                            ->label( '&nbsp;')
                            ->text( $role[ 'name' ] )
                            ->value( 1 )
                            ->blockHelp( '' )
                            ->class( "inline" )

                        ?>
                    <?php endforeach; ?>

                <?php endif; ?>

            </div>



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

                        <textarea class="form-control" style="font-family:monospace;" rows="10" id="notes" name="notes"><?= $t->data[ 'params'][ 'notes' ] ?></textarea>
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

        <?php if( Auth::getUser()->isSuperUser() ): ?>

            <?php if( $t->data[ 'params'][ "allGroups" ] && count( $t->data[ 'params'][ "allGroups" ] ) > 1 || ( count( $t->data[ 'params'][ "allGroups" ] ) == 1 && !isset( $t->data[ 'params'][ "allGroups" ]['ROLE'] ) )): ?>

                <div class="form-group" style="display: inline; display: inline-flex">

                    <label for="mayauthorize" class="control-label col-lg-2 col-sm-4">&nbsp;Groups&nbsp;</label>

                    <table class="table table-no-border">

                        <?php foreach( $t->data[ 'params'][ "allGroups" ] as $gname => $gvalue ): ?>

                            <?php if( $gname != "ROLE" && config('contact_group.types.' . $gname ) ): ?>
                                <tr>
                                    <td>
                                        <label for="mayauthorize" class="control-label col-lg-2 col-sm-4">&nbsp;<?= $gname ?>&nbsp;</label>
                                    </td>

                                    <?php foreach( $gvalue as $ggroup ): ?>

                                        <td>

                                            <?= Former::checkbox( $gname . "_" . $ggroup[ 'id' ] )
                                                ->label('&nbsp;')
                                                ->text( $ggroup[ 'name' ] )
                                                ->value( 1 )
                                                ->blockHelp( '' )
                                                ->class( 'inline' )
                                            ?>

                                        </td>

                                    <?php endforeach; ?>

                                </tr>
                            <?php endif; ?>

                        <?php endforeach; ?>

                    </table>
                </div>
            <?php endif; ?>

        <?php endif; ?>

    </div>

<div style="clear: both"></div>


    <?= Former::actions(
        Former::primary_submit( $t->data['params']['isAdd'] ? 'Add' : 'Save Changes' ),
        Former::default_link( 'Cancel' )->href( $t->data[ 'params'][ "from" ] == "contact@list" ? route($t->feParams->route_prefix . '@list' ) : route('customer@overview', [ "id" => $t->data[ 'params'][ 'object']->getCustomer()->getId() ,  "tab" => "contacts" ] ) ),
        Former::success_button( 'Help' )->id( 'help-btn' )
    );
    ?>

    <?= Former::hidden( 'id' )
        ->value( $t->data[ 'params'][ 'object'] ? $t->data[ 'params'][ 'object']->getId() : '' )
    ?>

    <?= Former::hidden( 'from' )
        ->value( $t->data[ 'params'][ "from" ] )
    ?>

    <?= Former::close() ?>

</div>
