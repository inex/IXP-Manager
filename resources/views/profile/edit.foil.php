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
        <div class="col-lg-6 col-md-12 mb-4">
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
                ->customInputWidthClass( 'col-xl-6 col-lg-8 col-sm-6' )
                ->customLabelWidthClass( 'col-sm-4' )
                ->actionButtonsCustomClass( "grey-box")
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
            );
            ?>

            <?= Former::close() ?>

        </div>

        <div class="col-lg-6 col-md-12 mb-4">
            <h3>
                Update Your Profile
            </h3>
            <hr>
            <?= Former::open()
                ->populate( $t->profileDetails )
                ->method( 'post' )
                ->id( "infos" )
                ->action( route ( "profile@update-profile" ) )
                ->customInputWidthClass( 'col-xl-6 col-lg-8 col-sm-6' )
                ->customLabelWidthClass( 'col-sm-4' )
                ->actionButtonsCustomClass( "grey-box");
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
            );
            ?>

            <?= Former::close() ?>
        </div>

    </div>

    <div class="row mt-4">

        <div class="col-lg-6 col-md-12">
            <h3>
                Two Factor Authentication
            </h3>
            <hr>

            <p>
                <b>IXP Manager</b> supports two factor authentication (2FA) which strengthens access security by requiring two methods
                (also referred to as factors) to verify your identity. Two factor authentication protects against phishing, social
                engineering and password brute force attacks and secures your logins from attackers exploiting weak or stolen credentials.
            </p>

            <?php if( !Auth::getUser()->getPasswordSecurity() || !Auth::getUser()->getPasswordSecurity()->isGoogle2faEnable() ): ?>
                <p>
                    To enable it, begin by entering your password below and follow the instructions.
                </p>
            <?php endif ?>

            <?= Former::open()
                ->method( 'post' )
                ->id( "2fa-form" )
                ->action( Auth::getUser()->getPasswordSecurity() && Auth::getUser()->getPasswordSecurity()->isGoogle2faEnable() ? "#" : route ( "2fa@check-password" ) )
                ->customInputWidthClass( 'col-xl-6 col-lg-8 col-sm-6' )
                ->customLabelWidthClass( 'col-sm-4' )
                ->actionButtonsCustomClass( "grey-box");
            ?>

            <?= Former::password( 'pass' )
                ->label( 'Password' )
                ->required( true )
            ?>

            <?php if( Auth::getUser()->getPasswordSecurity() && Auth::getUser()->getPasswordSecurity()->isGoogle2faEnable() ): ?>

                <?php if( Auth::getUser()->isSuperUser() && config( "google2fa.superuser_required" ) ): ?>

                    <?= Former::actions(
                        Former::primary_submit( 'Reset 2FA' )->id( "btn-2fa-reset" ),
                        Former::primary_submit( 'Get 2FA QRcode' )->id( "btn-2fa-enable" )
                    );
                    ?>

                <?php else: ?>

                    <?= Former::actions(
                            Former::danger_submit( 'Disable 2FA' )->id( "btn-2fa-delete" ),
                            Former::secondary_submit( 'Get 2FA QRcode' )->id( "btn-2fa-enable" )
                        );
                    ?>

                <?php endif; ?>


                <?= Former::hidden( 'id' )
                    ->value( Auth::getUser()->getPasswordSecurity()->getId() )
                ?>
            <?php else: ?>
                <?= Former::actions(
                    Former::primary_submit( 'Enable 2FA' )->id( "btn-2fa-enable" )
                );
                ?>

            <?php endif; ?>


            <?= Former::close() ?>
        </div>

        <?php if( Auth::getUser()->isSuperUser() ): ?>

            <div class="col-lg-6 col-md-12">
                <h3>
                    <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?> Notes
                </h3>
                <hr>
                <?= Former::open()
                    ->populate( $t->customerNotesNotificationOption )
                    ->method( 'post' )
                    ->id( "note" )
                    ->action( route ( "profile@update-notification-preference" ) )
                    ->customInputWidthClass( 'col-sm-10' )
                    ->actionButtonsCustomClass( "grey-box");
                ?>

                <?=  Former::radios('')
                    ->radios([
                        '&nbsp;&nbsp;&nbsp;Disable all email notifications'                           => [ 'name' => 'notify', 'id' => 'notify-none',     'value' => 'none'    ],
                        '&nbsp;&nbsp;&nbsp;Email me on changes to only watched ' . config( 'ixp_fe.lang.customer.many' ) . ' and notes'   => [ 'name' => 'notify', 'id' => 'notify-watched',  'value' => 'watched' ],
                        '&nbsp;&nbsp;&nbsp;Email me on any change to any ' . config( 'ixp_fe.lang.customer.one' ) . ' note'               => [ 'name' => 'notify', 'id' => 'id="notify-all"', 'value' => 'all'     ],
                    ])
                    ->name( 'notify' )
                    ->label( '' )
                    ->setValue( "all" );
                ?>

                <?= Former::actions(
                    Former::primary_submit( 'Set Notification Preference' )
                );
                ?>

                <?= Former::close() ?>
            </div>

        <?php endif; ?>

        <?php if( $t->mailingListsEnabled ): ?>

            <div class="col-lg-6 col-md-12">
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
                    ->actionButtonsCustomClass( "grey-box")
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
                );
                ?>

                <?= Former::close() ?>
            </div>

        <?php endif; ?>

    </div>



<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?php if( Auth::getUser()->getPasswordSecurity() && Auth::getUser()->getPasswordSecurity()->isGoogle2faEnable() ): ?>
        <?= $t->insert( 'profile/js/edit' ); ?>
    <?php endif; ?>
<?php $this->append() ?>
