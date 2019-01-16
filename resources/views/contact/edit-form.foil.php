<div class="well">

    <?= Former::open()->method( 'POST' )
        ->id( 'form' )
        ->action( route( $t->feParams->route_prefix . '@store' ) )
        ->customInputWidthClass( 'col-sm-6' )
    ?>
    <div class="col-sm-6">
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

    <div class="col-sm-6">

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
        Former::primary_submit( $t->data['params']['isAdd'] ? 'Add' : 'Save Changes' ),
        Former::default_link( 'Cancel' )->href( $cancel_url ),
        Former::success_button( 'Help' )->id( 'help-btn' )
    );
    ?>

    <?= Former::hidden( 'id' )
        ->value( $t->data[ 'params'][ 'object'] ? $t->data[ 'params'][ 'object']->getId() : '' )
    ?>

    <?= Former::close() ?>

</div>
