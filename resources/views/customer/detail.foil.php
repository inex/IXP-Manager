<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
    $c = $t->c;/** @var \IXP\Models\Customer $c */
    $isSuperUser = Auth::check() && Auth::getUser()->isSuperUser();
?>

<?php $this->section( 'page-header-preamble' ) ?>
    <?php if( Auth::check() && $isSuperUser ): ?>
        <a href="<?= route( $c->typeAssociate() ? 'customer@associates' : 'customer@details' )?>">
            <?= $c->typeAssociate() ? 'Associate ' : '' ?><?= ucfirst( config( 'ixp_fe.lang.customer.many' ) ) ?>
        </a>
    <?php else: ?>
        <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?> Detail
    <?php endif; ?>

    <?php if( Auth::check() && $isSuperUser ): ?>
        /
        <a href="<?= route( 'customer@overview', [ 'cust' => $c->id ] ) ?>">
            <?= $t->ee( $c->name ) ?>
        </a>
    <?php endif; ?>
<?php $this->append() ?>

<?php $this->section('content') ?>
    <div class="row">
        <div class="col-lg-12">
            <div class="tw-bg-gray-100 shadow-sm tw-p-6">
                <div class="row">
                    <div class="<?= $t->logoManagementEnabled() && ( $logo = $c->logo ) ? "col-md-9 col-lg-8" : "col-12" ?>">
                        <h3>
                            <?= $t->ee( $c->getFormattedName() ) ?>
                            <span class="tw-text-sm"><?= $t->insert( 'customer/cust-type', [ 'cust' => $t->c ] ); ?></span>
                        </h3>

                        <p class="tw-mt-2">
                            <a href="<?= $c->corpwww ?>" target="_blank">
                                <?= $t->nakedUrl( $c->corpwww ?? '' ) ?>
                            </a>

                            <span class="tw-text-gray-600">
                                - joined <?= \Carbon\Carbon::instance( $c->datejoin )->format('Y') ?>
                            </span>
                        </p>

                        <?php if( !$t->c->typeAssociate() ): ?>
                            <p class="tw-mt-6">
                                <?php if( $c->in_manrs ): ?>
                                    <a href="https://www.manrs.org/" target="_blank" class="hover:tw-no-underline">
                                        <span class="tw-inline-block tw-border-1 tw-border-green-500 tw-p-1 tw-rounded-full tw-text-green-500 tw-font-semibold tw-uppercase tw-text-sm tw-px-3 tw-py-1 tw-mr-3">
                                            MANRS
                                        </span>
                                    </a>
                                <?php endif; ?>

                                <?php if( $c->peeringpolicy !== \IXP\Models\Customer::PEERING_POLICY_OPEN ): ?>
                                    <span class="tw-inline-block tw-border-1 tw-border-gray-600 tw-p-1 tw-rounded-full tw-text-gray-600 tw-uppercase tw-text-sm tw-px-3 tw-py-1 tw-mr-3">
                                        <?= $c->peeringpolicy ?>
                                    </span>
                                <?php endif; ?>
                            </p>
                        <?php endif; ?>
                    </div>

                    <?php if( $t->logoManagementEnabled() && ( $logo = $c->logo ) ): ?>
                        <div class="col-md-3 col-lg-4 col-12 tw-mt-6 md:tw-mt-0 tw-text-center align-self-center">
                            <img class="img-fluid lg:tw-inline-block tw-align-middle" src="<?= url( 'logos/' . $logo->shardedPath() ) ?>">
                        </div>
                    <?php endif; ?>
                </div>
            </div>

            <?php if( Auth::check() && !$c->typeAssociate() ): ?>
                <div class="row tw-mt-6 tw-mx-4">
                    <div class="col-12 tw-border-1 tw-border-grey-light tw-p-4 tw-text-gray-700 ">
                        <?php if( filter_var( $c->peeringemail, FILTER_VALIDATE_EMAIL ) ): ?>
                            <div class="row">
                                <div class="col-12 col-md-3 tw-text-center md:tw-text-right">
                                    <span class="tw-font-bold tw-mr-4">Peering&nbsp;Email:</span>
                                </div>
                                <div class="col-12 col-md-9 tw-text-center md:tw-text-left">
                                    <a href="mailto:<?= $c->peeringemail ?>">
                                        <?= $c->peeringemail ?>
                                    </a>
                                </div>
                            </div>
                        <?php endif; ?>

                        <div class="row">
                            <div class="col-12 col-md-3 tw-text-center md:tw-text-right">
                                <span class="tw-font-bold  tw-mr-4">NOC&nbsp;Contact:</span>
                            </div>
                            <div class="col-12 col-md-9 tw-text-center md:tw-text-left">
                                <?php if( filter_var( $c->nocemail, FILTER_VALIDATE_EMAIL ) ): ?>
                                    <a href="mailto:<?= $t->ee( $c->nocemail ) ?>">
                                        <?= $t->ee( $c->nocemail ) ?>
                                    </a> /
                                <?php endif; ?>

                                <?= $t->ee( $c->nocphone ) ?> (<?= $t->ee( $c->nochours ) ?>)

                                <?php if( $c->noc24hphone && $c->nocphone !== $c->noc24hphone ): ?>
                                    / <?= $t->ee( $c->noc24hphone ) ?> (24/7)
                                <?php endif; ?>

                                <?php if( filter_var( $c->nocwww, FILTER_VALIDATE_URL ) ): ?>
                                    / <a href="<?= $c->nocwww ?>"><?= $c->nocwww ?></a>
                                <?php endif; ?>
                            </div>
                        </div>

                        <?php if( strlen( $c->peeringmacro ?? "" ) ): ?>
                            <div class="row">
                                <div class="col-12 col-md-3 tw-text-center md:tw-text-right">
                                    <span class="tw-font-bold tw-mr-4">
                                      Peering&nbsp;Macro:
                                    </span>
                                </div>
                                <div class="col-12 col-md-9 tw-text-center md:tw-text-left">
                                    <?=  $t->ee( $c->peeringmacro ) ?>
                                </div>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endif; ?>

            <div class="row tw-mt-6">
                <?php $countVi = 1 ?>
                <?php foreach( $c->virtualInterfaces as $vi ):
                    if( $vi->physicalInterfaces()->connected()->doesntExist()
                        || !$vi->physicalInterfaces()->first()->switchport->typePeering() ) {
                        continue;
                    }
                ?>
                    <div class="col-12 col-md-6 col-xl-4 mt-4">
                        <div class="tw-max-w-sm tw-rounded-sm tw-overflow-hidden tw-shadow-lg">
                            <div class="tw-px-6 tw-py-6">
                                <div class="tw-font-bold tw-text-xl tw-mb-2">
                                    <?php if( $infra = $vi->physicalInterfaces[ 0 ]->switchPort->switcher->infrastructureModel ): ?>
                                        <?= $infra->name ?>
                                    <?php else: ?>
                                      '<em>Unknown Infrastructure</em>'
                                    <?php endif; ?>

                                    <span class="tw-block tw-float-right tw-ml-6 tw-text-lg tw-font-semibold">
                                        <?= $t->scaleBits( $vi->speed() * 1000 * 1000, 0 ) ?>
                                    </span>

                                </div>

                                <?php if( $pi = $vi->physicalInterfaces[ 0 ] ):
                                    /** @var $pi \IXP\Models\PhysicalInterface */?>
                                    <p class="tw-text-grey-dark tw-text-sm">
                                        Location
                                    </p>
                                    <p class="tw-text-grey-darker tw-text-base">
                                        <?= $t->ee( $pi->switchPort->switcher->cabinet->location->name ) ?>
                                    </p>
                                <?php endif; ?>
                                <br>

                                <?php if( $vlis = $vi->vlanInterfaces ): ?>
                                    <?php foreach( $vlis as $vli ): ?>
                                        <?php if( $vli->vlan->private ): ?>
                                            <?php continue; ?>
                                        <?php endif; ?>

                                        <p class="tw-text-grey-dark tw-text-sm">
                                            <?= $vi->numberPublicVlans() > 1 ? $t->ee( $vli->vlan->name ) : 'IP Addresses' ?>
                                        </p>

                                        <p class="tw-text-grey-darker tw-text-base">
                                            <?php if( $vli->ipv6enabled && $vli->ipv6address ): ?>
                                                <?= $vli->ipv6address->address ?>
                                                <br>
                                            <?php endif; ?>
                                            <?php if( $vli->ipv4enabled && $vli->ipv4address ): ?>
                                                <?= $vli->ipv4address->address ?>
                                                <br>
                                            <?php else: ?>
                                                &nbsp;<br>
                                            <?php endif; ?>

                                            <?php if( !$vli->ipv6enabled ): ?>
                                                &nbsp;<br>
                                            <?php endif; ?>
                                        </p>
                                        <br>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>

                            <div class="tw-px-6 tw-pb-8">
                                <?php if( $vlis ): ?>
                                    <?php foreach( $vlis as $vli ): ?>
                                        <?php if( $vli->vlan->private ): ?>
                                            <?php continue; ?>
                                        <?php endif; ?>

                                        <?php if( !in_array( $c->autsys , $t->rsasns, true ) ): ?>
                                            <?php if( $vli->rsclient ): ?>
                                                <span class="tw-inline-block tw-border-1 tw-border-green-500 tw-p-1 tw-rounded-full tw-text-green-dark  tw-font-semibold tw-uppercase tw-text-sm tw-px-3 tw-py-1 tw-mr-2">
                                            <?php else: ?>
                                                <span class="tw-inline-block tw-border-1 tw-border-red-lighter tw-p-1 tw-rounded-full tw-text-red-lighter tw-font-semibold tw-uppercase tw-text-sm tw-px-3 tw-py-1 tw-mr-2">
                                            <?php endif; ?>
                                                Route Server
                                            </span>
                                        <?php endif; ?>

                                        <?php if( $c->autsys !== 112 ): ?>
                                            <?php if( $vli->as112client ): ?>
                                                <span class="tw-inline-block tw-border-1 tw-border-green-500 tw-p-1 tw-rounded-full tw-text-green-dark  tw-font-semibold tw-uppercase tw-text-sm tw-px-3 tw-py-1 tw-mr-2">
                                            <?php else: ?>
                                                <span class="tw-inline-block tw-border-1 tw-border-red-lighter tw-p-1 tw-rounded-full tw-text-red-lighter tw-font-semibold tw-uppercase tw-text-sm tw-px-3 tw-py-1 tw-mr-2">
                                            <?php endif; ?>
                                                AS112
                                            </span>
                                        <?php endif; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php $this->append() ?>