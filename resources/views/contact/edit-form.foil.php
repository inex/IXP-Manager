<?php
    $privs          = Auth::getUser()->privs();
    $isSuperUser    = $privs === \IXP\Models\User::AUTH_SUPERUSER;
    $isCustAdmin    = $privs === \IXP\Models\User::AUTH_CUSTADMIN;
?>

<div class="card">
    <div class="card-body">
        <?= Former::open()->method( $t->data['params']['isAdd'] ? 'POST' : 'PUT'  )
            ->id( 'form' )
            ->action( $t->data['params']['isAdd'] ? route( $t->feParams->route_prefix . '@store' ) : route($t->feParams->route_prefix . '@update', [ 'id' => $t->data[ 'params'][ 'object']->id ] ) )
            ->customInputWidthClass( 'col-sm-6 col-md-6 col-lg-8 col-xl-6' )
            ->customLabelWidthClass( 'col-sm-2 col-md-2 col-lg-3 text-xs-center' )
            ->actionButtonsCustomClass( "grey-box")
        ?>
        <?php
            $checkedRoles = [];
            if( old( 'roles' ) !== null ) {
                $checkedRoles = array_values( old( 'roles' ) );
            } else {
                $checkedRoles = array_keys( $t->data[ 'params' ][ 'groupsForContact' ] );
            }
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

                <?php if( $isSuperUser ):?>
                    <?= Former::select( 'custid' )
                        ->id( 'cust' )
                        ->label( ucfirst( config( 'ixp_fe.lang.customer.one' ) ) )
                        ->placeholder( 'Select a ' . config( 'ixp_fe.lang.customer.one' ) )
                        ->fromQuery( $t->data[ 'params'][ 'custs' ], 'name' )
                        ->addClass( 'chzn-select' )
                        ->blockHelp( ucfirst( config( 'ixp_fe.lang.customer.one' ) )  . ' to assign this contact to.' );
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
                    <?php if( $t->data[ 'params'][ "allGroups" ] && isset( $t->data[ 'params'][ "allGroups" ][ 'ROLE' ] ) ): ?>
                        <div class="collapse">
                            <label class="control-label col-lg-3 col-sm-3">&nbsp;
                                Role&nbsp;
                            </label>
                        </div>
                        <div>
                            <?php foreach( $t->data[ 'params'][ "allGroups" ][ 'ROLE' ] as $role ): ?>
                                <div class="form-group row">
                                    <div class="col-sm-6 col-md-6 col-lg-8 col-xl-6">
                                        <div class="form-check form-check-inline">
                                            <input id='role_<?= $role[ 'id' ] ?>' type='checkbox' name='roles[]' <?= in_array( $role[ 'id' ], $checkedRoles, false ) ? 'checked' : '' ?> value='<?= $role[ 'id' ] ?>'>
                                            <label for="role_<?= $role[ 'id' ] ?>" class="form-check-label">
                                                <?= $role[ 'name' ] ?>
                                            </label>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>

                <?php if( !$isCustAdmin ): ?>
                    <div class="form-group col-sm-12">
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
                <?php endif; ?>

                <?php if( $isSuperUser ): ?>
                    <?php if( ( $t->data[ 'params' ][ "allGroups" ] && count( $t->data[ 'params' ][ "allGroups" ] ) > 1 ) || ( count( $t->data[ 'params'][ "allGroups" ] ) == 1 && !isset( $t->data[ 'params'][ "allGroups" ]['ROLE'] ) ) ): ?>
                        <div class="form-group">
                            <label for="mayauthorize" class="control-label col-lg-2 col-sm-4">&nbsp;Groups&nbsp;</label>
                            <table class="table table-no-border">
                                <?php foreach( $t->data[ 'params'][ "allGroups" ] as $gname => $gvalue ): ?>
                                    <?php if( $gname !== "ROLE" && config('contact_group.types.' . $gname ) ): ?>
                                        <tr>
                                            <td>
                                                <label for="mayauthorize" class="control-label col-lg-2 col-sm-4" style="display: grid">&nbsp;<?= $gname ?>&nbsp;</label>
                                            </td>

                                            <?php foreach( $gvalue as $ggroup ): ?>
                                                <td>
                                                    <div class="form-group row">
                                                        <div class="col-sm-6 col-md-6 col-lg-8 col-xl-6">
                                                            <div class="form-check form-check-inline">
                                                                <input id='role_<?= $ggroup[ 'id' ] ?>' type='checkbox' <?= in_array( $ggroup[ 'id' ], $checkedRoles, false ) ? 'checked' : '' ?> name='roles[]' value='<?= $ggroup[ 'id' ] ?>'>
                                                                <label for="role_<?= $ggroup[ 'id' ] ?>" class="form-check-label">
                                                                    <?= $ggroup[ 'name' ] ?>
                                                                </label>
                                                            </div>
                                                        </div>
                                                    </div>
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
                $redirect = session()->get( 'contact_post_store_redirect' );

                if( $redirect === 'contact@list' || $redirect === 'contact@add' ) {
                        $cancel_url = route('contact@list' );
                } else {
                    $custid = null;
                    if( isset( $t->data[ 'params'][ 'object'] ) && $t->data[ 'params'][ 'object'] instanceof \IXP\Models\ContactGroup ) {
                        $custid = $t->data[ 'params'][ 'object']->custid;
                    } else if( session()->get( 'contact_post_store_redirect_cid', null ) !== null ) {
                        $custid = session()->get( 'contact_post_store_redirect_cid' );
                    }

                    if( $custid !== null ) {
                        $cancel_url = route( 'customer@overview', [ 'cust' => $custid,  "tab" => "contacts" ] );
                    } else {
                        $cancel_url = route( 'contact@list' );
                    }
                }
            ?>

            <?= Former::actions(
                Former::primary_submit( $t->data['params']['isAdd'] ? 'Create' : 'Save Changes' )->class( "mb-2 mb-sm-0" ),
                Former::secondary_link( 'Cancel' )->href( $cancel_url )->class( "mb-2 mb-sm-0" ),
                Former::success_button( 'Help' )->id( 'help-btn' )->class( "mb-2 mb-sm-0" )
            ); ?>
        <?= Former::close() ?>
    </div>
</div>