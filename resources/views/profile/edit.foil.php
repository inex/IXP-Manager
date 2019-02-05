<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' )
?>


<?php $this->section( 'page-header-preamble' ) ?>
    My Profile
<?php $this->append() ?>


<?php $this->section('content') ?>


    <div class="row">
        <div class="col-md-12">
            <?= $t->alerts() ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-6">
            <h3>
                Change Your Password
            </h3>
            <hr>

            <div class="alert alert-info mt-4" role="alert">
                <div class="d-flex align-items-center">
                    <div class="text-center">
                        <i class="fa fa-info-circle fa-2x"></i>
                    </div>
                    <div class="col-sm-12">
                        Passwords are stored in IXP Manager using <a href="https://en.wikipedia.org/wiki/Bcrypt">bcrypt</a>.
                    </div>
                </div>
            </div>


            <?= Former::open()
                ->method( 'post' )
                ->id( "password" )
                ->action( route ( "profile@update-password" ) )
                ->customInputWidthClass( 'col-sm-8' )
                ->rules([
                    'current_password'   => 'required|max:255',
                    'new_password'       => 'required|max:255',
                    'confirm_password'   => 'required|max:255|same:new_password',
                ])
            ?>

            <?= Former::password( 'current_password' )
                ->label( 'Current Password' )
            ?>

            <?= Former::password( 'new_password' )
                ->label( 'New Password' )
            ?>

            <?= Former::password( 'confirm_password' )
                ->label( 'Confirm Password' )
            ?>

            <?= Former::actions(
                Former::primary_submit( 'Update Password' )
            )->class( "bg-light p-4 mt-4 shadow-sm text-center" );
            ?>

            <?= Former::close() ?>

        </div>

        <div class="col-md-6">
            <h3>
                Update Your Profile
            </h3>
            <hr>
            <?= Former::open()
                ->populate( $t->profileDetails )
                ->method( 'post' )
                ->id( "infos" )
                ->action( route ( "profile@update-profile" ) )
                ->customInputWidthClass( 'col-sm-8' )
            ?>

            <?= Former::text( 'username' )
                ->label( 'Username' )
            ?>

            <?= Former::text( 'name' )
                ->label( 'Name' )
                ->placeholder( 'Firstname Lastname' )
            ?>

            <?= Former::text( 'email' )
                ->label( 'Email' )
            ?>

            <?= Former::text( 'authorisedMobile' )
                ->label( 'Mobile' )
                ->placeholder( config( 'ixp_fe.customer.form.placeholders.phone' ) )
            ?>

            <?= Former::password( 'actual_password' )
                ->label( 'Current Password' )
            ?>

            <?= Former::actions(
                Former::primary_submit( 'Update Profile' )
            )->class( "bg-light p-4 mt-4 shadow-sm text-center" );
            ?>

            <?= Former::close() ?>
        </div>

    </div>

    <div class="row mt-4">

        <?php if( Auth::getUser()->isSuperUser() ): ?>

            <div class="col-md-6">
                <h3>
                    Customer Notes
                </h3>
                <hr>
                <?= Former::open()
                    ->populate( $t->customerNotesNotificationOption )
                    ->method( 'post' )
                    ->id( "note" )
                    ->action( route ( "profile@update-notification-preference" ) )
                    ->customInputWidthClass( 'col-sm-10' );
                ?>

                <?=  Former::radios('')
                    ->radios([
                        '&nbsp;&nbsp;&nbsp;Disable all email notifications'                           => [ 'name' => 'notify', 'id' => 'notify-none',     'value' => 'none'    ],
                        '&nbsp;&nbsp;&nbsp;Email me on changes to only watched customers and notes'   => [ 'name' => 'notify', 'id' => 'notify-watched',  'value' => 'watched' ],
                        '&nbsp;&nbsp;&nbsp;Email me on any change to any customer note'               => [ 'name' => 'notify', 'id' => 'id="notify-all"', 'value' => 'all'     ],
                    ])
                    ->name( 'notify' )
                    ->label( '' )
                    ->setValue( "all" );
                ?>

                <?= Former::actions(
                    Former::primary_submit( 'Set Notification Preference' )
                )->class( "bg-light p-4 mt-4 shadow-sm text-center" );
                ?>

                <?= Former::close() ?>
            </div>

        <?php endif; ?>

        <?php if( $t->mailingListsEnabled ): ?>

            <div class="col-md-6">
                <h3>
                    Your Mailing List Subscriptions
                </h3>
                <hr>
                <p>
                    <?= config( "identity.orgname" ) ?> operates the below mailing lists to help us interact with our
                    members and for our members to interact with each other.
                </p>
                <p>
                    The below are your subscriptions for <strong><?= Auth::getUser()->getEmail() ?></strong>.
                </p>

                <?= Former::open()
                    ->populate( $t->mailingListSubscriptions )
                    ->method( 'post' )
                    ->id( "mailing" )
                    ->action( route ( "profile@update-mailing-lists" ) )
                    ->customInputWidthClass( 'col-sm-10' );
                ?>

                <?php foreach( config( "mailinglists.lists") as $name => $ml ): ?>

                    <?php if( !Auth::getUser()->getCustomer()->isTypeAssociate() || ( isset( $ml[ "associates"] ) && $ml[ "associates" ] ) ): ?>

                        <?= Former::checkbox( 'ml_'. $name )
                            ->label( '' )
                            ->value(1)
                            ->inline()
                            ->check( $t->mailingListSubscriptions[ $name ] ? true : false )
                            ->text( "<strong>".$ml[ "name"] ."</strong> - " . $ml[ "desc" ]
                                . "(" . ( ( $ml[ "email" ] ) ? "<a href='mailto:" .$ml[ "email" ]. " '>" . $ml[ "email" ] . "</a> - " : '' )
                                . "<a target='_blank' href='" . $ml[ "archive" ] ."'>archives</a>)"
                            )
                        ?>

                    <?php endif; ?>

                <?php endforeach; ?>

                <?= Former::actions(
                        Former::primary_submit( 'Update Subscriptions' )
                )->class( "bg-light p-4 mt-4 shadow-sm text-center" );
                ?>

                <?= Former::close() ?>
            </div>

        <?php endif; ?>

    </div>



<?php $this->append() ?>