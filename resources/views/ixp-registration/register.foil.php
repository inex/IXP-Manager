<?php
/** @var Foil\Template\Template $t */

use IXP\Models\Customer;
use IXP\Models\IrrdbConfig;

$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= route( 'customer@list' )?>">
        <?= ucfirst( config( 'ixp_fe.lang.customer.many' ) ) ?>
    </a>
<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>
Register on IXP Manager Community
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm ml-auto" role="group">
    </div>
<?php $this->append() ?>

<?php $this->section('content') ?>
    <div class="row">
        <div class="col-lg-12">
            <?= Former::open()
                    ->action(route("ixp-registration@register-submit"))
                    ->method( 'POST' )
                ->id( "form" )
                ->customInputWidthClass( 'col-sm-6' )
                ->customLabelWidthClass( 'col-sm-3' )
                ->actionButtonsCustomClass( "grey-box")
            ?>

            <div id="instructions-alert" class="alert alert-info collapse">
                This page will assist you with registering your IXP Manager infrastructures on the <a target="_blank" href="https://www.ixpmanager.org/community/user-list">IXP Manager community page.</a>
                <br /><br />
                Each infrastructure is registered separately, and will link to your PeeringDB and/or IX-F profile, if available.
            </div>

            <div class="mt-4 row">
                <div class="col-lg-12 mb-4 mb-sm-0">
                    <?= $t->alerts() ?>

                    <h3>
                        Organization Details
                    </h3>
                    <hr class="tw-mb-6">


                    <?= Former::text( 'website' )
                            ->label( "Your IXP's Homepage" )
                            ->required()
                            ->value( config('identity.corporate_url') )
                            ->blockHelp( "This is the homepage / website for your IXP, not the location of IXP Manager. Please enter as a complete URL such as: http://www.example.com" );
                    ?>

                    <?= Former::text( 'ixpmurl' )
                            ->label( "IXP Manager URL" )
                            ->value( config('identity.url') )
                            ->blockHelp( "If your installation of IXP Manager is publicly accessible,
                                               then provide the base URL here. We will use this to routinely pull
                                               the version you are running and other stats via the IX-F Member Export
                                               to build up a global usage profile for IXP Manager and a public stats page.
                                               (For INEX, this would be https://www.inex.ie/ixp/)." );
                    ?>

                    <?= Former::text( 'since' )
                            ->label( "What year did you start using IXP Manager?" )
                            ->blockHelp( "When did you first install and start using IXP Manager (year)" );
                    ?>

                    <?= Former::text( 'submitted_by_name' )
                            ->label( "What is your own name?" )
                            ->blockHelp( "The name or role of the person completing this form. This will not be used
                                                                            for any purpose other that contacting you in relation to this submission." );
                    ?>

                    <?= Former::text( 'submitted_by_email' )
                            ->label( "Your contact email?" )
                            ->blockHelp( "A contact email for the person completing this form. This will not be used
                                                                            for any purpose other that contacting you in relation to this submission." );
                    ?>

                    <?= Former::checkbox( 'submitted_by_ml' )
                            ->label( '&nbsp;' )
                            ->text( 'Sign up to the IXP Manager Announcements mailing list?' )
                            ->value( 1 )
                            ->blockHelp( "We have an <a href='https://www.inex.ie/mailman/listinfo/ixpmanager-announce' target='_blank'>IXP Manager announcements mailing list</a> (~1-2 emails / month). Would you like us to subscribe you to that?" );

                    ?>
                </div>



                <?php foreach ($t->infrastructures as $infrastructure): ?>
                <div class="col-lg-12 mt-4 mb-sm-0">
                    <div class="row tw-flex tw-items-center tw-justify-between">
                        <h4>
                            Infrastructure: <?= $t->ee($infrastructure->name) ?>
                        </h4>

                        <!-- Right-aligned action icons -->
                        <div class="tw-flex tw-items-center tw-gap-2">
                            <?php if ($infrastructure->peeringdb_ix_id != null): ?>
                                <a target="_blank" class="tw-inline-block tw-border-1 tw-border-red-lighter tw-p-1 tw-rounded-full tw-text-red-lighter tw-font-semibold tw-uppercase tw-text-sm tw-px-3 tw-py-1 tw-mr-2" href="https://www.peeringdb.com/ix/<?= $infrastructure->peeringdb_ix_id ?>">PDB #<?= $infrastructure->peeringdb_ix_id ?></a>
                            <?php endif; ?>
                            <?php if ($infrastructure->ixf_ix_id != null): ?>
                                <a target="_blank" class="tw-inline-block tw-border-1 tw-border-red-lighter tw-p-1 tw-rounded-full tw-text-red-lighter tw-font-semibold tw-uppercase tw-text-sm tw-px-3 tw-py-1 tw-mr-2" href="https://ixpdb.euro-ix.net/en/explore/ixp/<?= $infrastructure->ixf_ix_id ?>">IX-F #<?= $infrastructure->ixf_ix_id ?></a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <hr class="tw-mb-6">

                    <?= Former::text( 'infrastructure[' . $infrastructure->id . '][fullname]' )
                            ->label( 'Full name of infrastructure <sup>*</sup>' )
                            ->placeholder( "Some City Internet Exchange" )
                            ->blockHelp( "For example: 'Some City Internet Exchange'. or ''" );
                    ?>

                    <?= Former::text( 'infrastructure[' . $infrastructure->id . '][shortname]' )
                            ->label( 'Short / abbreviated name <sup>*</sup>' )
                            ->placeholder( "SCIX" )
                            ->blockHelp( "The short name of your IXP as it would typically be known or used. For example: 'SCIX'." );
                    ?>

                    <?= Former::text( 'infrastructure[' . $infrastructure->id . '][city]' )
                            ->label( 'City / State / County / Geographic Region' )
                            ->blockHelp( "If your IXP serves a specific city / state / county / geographic region in a country, enter it here." );
                    ?>

                    <?= Former::select( 'infrastructure[' . $infrastructure->id . '][country]' )
                            ->label( 'Country <sup>*</sup>' )
                            ->fromQuery( $t->countries, 'name', 'iso_3166_2' )
                            ->placeholder( 'Choose a country' )
                            ->blockHelp( 'The country (or primary country or country where your IXP is headquartered).' )
                            ->addClass( 'chzn-select' );
                    ?>

                    <?= Former::text( 'infrastructure[' . $infrastructure->id . '][gpsx]' )
                            ->label( "Longitude" )
                            ->blockHelp( "Your GPS longitude (use floating point number for distance east / west of the meridian).
                                        If you leave this blank, we will find a suitable GPS location for the city where your IX is
                                        located." );
                    ?>

                    <?= Former::text( 'infrastructure[' . $infrastructure->id . '][gpsy]' )
                            ->label( "Latitude" )
                            ->blockHelp( "Your GPS latitude (use floating point number for distance north / south of the equator).
                                        If you leave this blank, we will find a suitable GPS location for the city where your IX is
                                        located." );
                    ?>

                    <input type="hidden" name="infrastructure[<?= $infrastructure->id ?>][peeringdbid]" value="<?= $infrastructure->peeringdb_ix_id ?? "" ?>" />
                    <input type="hidden" name="infrastructure[<?= $infrastructure->id ?>][ixfid]" value="<?= $infrastructure->ixf_ix_id ?? "" ?>" />

                    <?= Former::checkbox( 'infrastructure[' . $infrastructure->id . '][register]' )
                            ->label( '&nbsp;' )
                            ->text( 'Register this infrastructure' )
                            ->value( 1 )
                            ->check()
                            ->blockHelp( "Include this infrastructure in your registration" );
                    ?>
                </div>
                <?php endforeach; ?>

            </div>

            <div class="flow-root"></div>

            <br/>

            <?= Former::actions( Former::primary_submit( 'Submit' )->class( "mb-2 mb-sm-0" ),
                Former::success_button( 'Help' )->id( 'help-btn' )->class( "mb-2 mb-sm-0" )
            ); ?>

            <?= Former::close() ?>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
<?php $this->append() ?>