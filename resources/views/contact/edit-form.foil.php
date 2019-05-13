<div class="card">
    <div class="card-body">

        <?= Former::open()->method( 'POST' )
            ->id( 'form' )
            ->action( route( $t->feParams->route_prefix . '@store' ) )
            ->customInputWidthClass( 'col-sm-6 col-md-6 col-lg-8 col-xl-6' )
            ->customLabelWidthClass( 'col-sm-2 col-md-2 col-lg-3 text-xs-center' )
            ->actionButtonsCustomClass( "grey-box")
        ?>
        <div class="row">
            <div class="col-lg-6">
                <?= Former::text( 'name' )
                    ->label( 'Name' )
                    ->placeholder( 'Firstname Lastname' )
                    ->blockHelp( "The full name of the contact." );
                ?>

                <?= Former::text( 'position' )
                    ->label( 'Position' )
                    ->placeholder( 'Senior Network Engineer' )
                    ->blockHelp( "The contact's job title / position." );
                ?>

                <?php if( Auth::getUser()->isSuperUser() ):?>
                    <?= Former::select( 'custid' )
                        ->id( 'cust' )
                        ->label( 'Customer' )
                        ->placeholder( 'Select a customer' )
                        ->fromQuery( $t->data[ 'params'][ 'custs' ], 'name' )
                        ->addClass( 'chzn-select' )
                        ->blockHelp( "Customer to assign this contact to." );
                    ?>
                <?php endif; ?>

                <?= Former::text( 'email' )
                    ->label( 'Email' )
                    ->placeholder( 'firstname.lastname@example.com' )
                    ->blockHelp( "Email address of the contact." );
                ?>

                <?= Former::text( 'phone' )
                    ->label( 'Phone' )
                    ->placeholder( config( 'ixp_fe.customer.form.placeholders.phone' ) )
                    ->blockHelp( "Office / landline phone number of the contact." );
                ?>

                <?= Former::text( 'mobile' )
                    ->label( 'Mobile' )
                    ->placeholder( config( 'ixp_fe.customer.form.placeholders.phone' ) )
                    ->blockHelp( "Mobile phone / cell number of the contact." );
                ?>
            </div>

            <div class="col-lg-6">

                <div class="form-group row" >
                    <div class="" style="display: contents">
                        <label class="control-label col-lg-3 col-sm-3">&nbsp;Role&nbsp;</label>
                    </div>


                    <?php if( $t->data[ 'params'][ "allGroups" ] && isset( $t->data[ 'params'][ "allGroups" ][ 'ROLE' ] ) ): ?>
                        <div>
                            <?php foreach( $t->data[ 'params'][ "allGroups" ][ 'ROLE' ] as $role ): ?>

                                <?= Former::checkbox( "ROLE_" . $role[ 'id' ] )
                                    ->label( '&nbsp;')
                                    ->text( $role[ 'name' ] )
                                    ->value( 1 )
                                    ->blockHelp( '' )
                                    ->inline()

                                ?>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>

                </div>


                <?php if( !Auth::getUser()->isCustAdmin() ): ?>
                    <div class="form-group">

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
                                    <textarea class="form-control" style="font-family:monospace;" rows="10" id="notes" name="notes"><?= $t->data[ 'params'][ 'notes' ] ?></textarea>
                                </div>
                                <div role="tabpanel" class="tab-pane" id="preview">
                                    <div class="bg-light p-4 well-preview">
                                        Loading...
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>
                <?php endif; ?>

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
                                                        ->inline()

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
        </div>

        <?php
            // need to figure out where the cancel button foes. shouldn't be this hard :-(
            if( session()->get( 'contact_post_store_redirect' ) === 'contact@list' || session()->get( 'contact_post_store_redirect' ) === 'contact@add' ) {
                    $cancel_url = route('contact@list' );
            } else {
                $custid = null;
                if( isset( $t->data[ 'params'][ 'object'] ) && $t->data[ 'params'][ 'object'] instanceof \Entities\Contact ) {
                    $custid = $t->data[ 'params'][ 'object']->getCustomer()->getId();
                } else if( session()->get( 'contact_post_store_redirect_cid', null ) !== null ) {
                    $custid = session()->get( 'contact_post_store_redirect_cid' );
                }

                if( $custid !== null ) {
                    $cancel_url = route( 'customer@overview', [ "id" => $custid,  "tab" => "contacts" ] );
                } else {
                    $cancel_url = route( 'contact@list' );
                }
            }

        ?>

        <?= Former::actions(
            Former::primary_submit( $t->data['params']['isAdd'] ? 'Add' : 'Save Changes' )->class( "mb-2 mb-sm-0" ),
            Former::secondary_link( 'Cancel' )->href( $cancel_url )->class( "mb-2 mb-sm-0" ),
            Former::success_button( 'Help' )->id( 'help-btn' )->class( "mb-2 mb-sm-0" )
        );
        ?>

        <?= Former::hidden( 'id' )
            ->value( $t->data[ 'params'][ 'object'] ? $t->data[ 'params'][ 'object']->getId() : '' )
        ?>

        <?= Former::close() ?>

    </div>
</div>
