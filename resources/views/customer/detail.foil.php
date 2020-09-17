<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );

    // convenience for IDE autocompletion
    /** @var Entities\Customer $c */
    $c = $t->c;

    // list of route server asns
    $rsasns = d2r( 'Router' )->getAllPeeringASNs( \Entities\Router::TYPE_ROUTE_SERVER );
?>


<?php $this->section( 'page-header-preamble' ) ?>
    <?php if( Auth::check() && Auth::user()->isSuperUser() ): ?>
        <a href="<?= route( $c->isTypeAssociate() ? 'customer@associates' : 'customer@details' )?>"><?= $c->isTypeAssociate() ? 'Associate ' : '' ?><?= ucfirst( config( 'ixp_fe.lang.customer.many' ) ) ?></a>
    <?php else: ?>
        <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?> Detail
    <?php endif; ?>

    <?php if( Auth::check() && Auth::user()->isSuperUser() ): ?>
        /
        <a href="<?= route( 'customer@overview', [ 'id' => $c->getId() ] ) ?>">
            <?= $t->ee( $c->getName() ) ?>
        </a>
    <?php endif; ?>
<?php $this->append() ?>



<?php $this->section('content') ?>

<div class="row">

    <div class="col-lg-12">


        <div class="tw-bg-gray-100 shadow-sm tw-p-6">

            <div class="row">
                <div class="<?= $t->logoManagementEnabled() && ( $logo = $c->getLogo() ? "col-md-9 col-lg-8" : "col-12" ?>">

                    <h3>
                        <?= $t->ee( $c->getFormattedName() ) ?>
                        <span class="tw-text-sm"><?= $t->insert( 'customer/cust-type', [ 'cust' => $t->c ] ); ?></span>
                    </h3>

                    <p class="tw-mt-2">
                        <a href="<?= $t->c->getCorpwww() ?>" target="_blank"><?= $t->nakedUrl( $t->c->getCorpwww() ?? '' ) ?></a>

                        <span class="tw-text-gray-600">
                            - joined <?= $c->getDatejoin()->format('Y') ?>
                        </span>
                    </p>

                    <?php if( !$t->c->isTypeAssociate() ): ?>
                        <p class="tw-mt-6">

                            <?php if( $c->getInManrs() ): ?>
                                <a href="https://www.manrs.org/" target="_blank" class="hover:tw-no-underline">
                                        <span class="tw-inline-block tw-border tw-border-green-500 tw-p-1 tw-rounded-full tw-text-green-500 tw-font-semibold tw-uppercase tw-text-sm tw-px-3 tw-py-1 tw-mr-3">
                                            MANRS
                                        </span>
                                </a>
                            <?php endif; ?>

                            <?php if( $c->getPeeringpolicy() != \Entities\Customer::PEERING_POLICY_OPEN ): ?>
                                <span class="tw-inline-block tw-border tw-border-gray-600 tw-p-1 tw-rounded-full tw-text-gray-600 tw-uppercase tw-text-sm tw-px-3 tw-py-1 tw-mr-3">
                                    <?= $c->getPeeringpolicy() ?>
                                </span>
                            <?php endif; ?>

                        </p>
                    <?php endif; ?>
                </div>

                <?php if( $t->logoManagementEnabled() && ( $logo = $c->getLogo() ) ): ?>

                    <div class="col-md-3 col-lg-4 col-12 tw-mt-6 md:tw-mt-0 tw-text-center align-self-center">
                        <img class="img-fluid lg:tw-inline-block tw-align-middle" src="<?= url( 'logos/'.$logo->getShardedPath() ) ?>">
                    </div>

                <?php endif; ?>
            </div>
        </div>


        <?php if( Auth::check() && !$t->c->isTypeAssociate() ): ?>

            <div class="row tw-mt-6 tw-mx-4">

                <div class="col-12 tw-border tw-border-grey-light tw-p-4 tw-text-gray-700 ">


                    <?php if( filter_var( $c->getPeeringemail(), FILTER_VALIDATE_EMAIL ) ): ?>
                        <div class="row">
                            <div class="col-12 col-md-3 tw-text-center md:tw-text-right">
                                <span class="tw-font-bold tw-mr-4">Peering&nbsp;Email:</span>
                            </div>
                            <div class="col-12 col-md-9 tw-text-center md:tw-text-left">
                                <a href="mailto:<?= $c->getPeeringemail() ?>"><?= $c->getPeeringemail() ?></a>
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="row">
                        <div class="col-12 col-md-3 tw-text-center md:tw-text-right">
                            <span class="tw-font-bold  tw-mr-4">NOC&nbsp;Contact:</span>
                        </div>
                        <div class="col-12 col-md-9 tw-text-center md:tw-text-left">
                            <?php if( filter_var( $c->getNocemail(), FILTER_VALIDATE_EMAIL ) ): ?>
                                <a href="mailto:<?= $c->getNocemail() ?>"><?= $c->getNocemail() ?></a> /
                            <?php endif; ?>

                            <?= $t->ee( $c->getNocphone() ) ?> (<?= $t->ee( $c->getNochours() ) ?>)

                            <?php if( $c->getNoc24hphone() && $c->getNocphone() != $c->getNoc24hphone() ): ?>
                                / <?= $t->ee( $c->getNoc24hphone() ) ?> (24/7)
                            <?php endif; ?>

                            <?php if( filter_var( $c->getNocwww(), FILTER_VALIDATE_URL ) ): ?>
                                / <a href="<?= $c->getNocwww() ?>"><?= $c->getNocwww() ?></a>
                            <?php endif; ?>
                        </div>
                    </div>

                    <?php if( strlen( $c->getPeeringmacro() ?? "" ) ): ?>
                        <div class="row">
                            <div class="col-12 col-md-3 tw-text-center md:tw-text-right">
                                <span class="tw-font-bold tw-mr-4">Peering&nbsp;Macro:</span>
                            </div>
                            <div class="col-12 col-md-9 tw-text-center md:tw-text-left">
                                <?=  $t->ee( $c->getPeeringmacro() ) ?>
                            </div>
                        </div>
                    <?php endif; ?>


                </div>
            </div>
        <?php endif; ?>






        <div class="row tw-mt-6">

            <?php $countVi = 1 ?>
            <?php foreach( $c->getVirtualInterfaces() as $vi ):

                if( !$vi->isConnected() || !$vi->isPeeringPort() ) {
                    continue;
                }
            ?>

            <div class="col-12 col-md-6 col-xl-4 mt-4">

                <div class="tw-max-w-sm tw-rounded tw-overflow-hidden tw-shadow-lg">
                    <div class="tw-px-6 tw-py-6">

                        <div class="tw-font-bold tw-text-xl tw-mb-2">
                            <?= $vi->getInfrastructure() ? $vi->getInfrastructure()->getName() : '<em>Unknown Infrastructure</em>' ?>

                            <span class="tw-block tw-float-right tw-ml-6 tw-text-lg tw-font-semibold">
                                <?= $t->scaleBits( $vi->speed() * 1000 * 1000, 0 ) ?>
                            </span>

                        </div>

                        <?php if( $vi->getPhysicalInterfaces() ):
                            $pi = $vi->getPhysicalInterfaces()[0]; ?>

                            <p class="tw-text-grey-dark tw-text-sm">
                                Location
                            </p>
                            <p class="tw-text-grey-darker tw-text-base">
                                <?= $t->ee( $pi->getSwitchPort()->getSwitcher()->getCabinet()->getLocation()->getName() ) ?>
                            </p>
                        <?php endif; ?>

                        <br>

                        <?php if( $vi->getVlanInterfaces() ): ?>

                            <?php foreach( $vi->getVlanInterfaces() as $vli ): ?>

                                <?php if( $vli->getVlan()->getPrivate() ): ?>
                                    <?php continue; ?>
                                <?php endif; ?>

                                <p class="tw-text-grey-dark tw-text-sm">
                                    <?= $vi->numberOfPublicVlans() > 1 ? $t->ee( $vli->getVlan()->getName() ) : 'IP Addresses' ?>
                                </p>

                                <p class="tw-text-grey-darker tw-text-base">

                                    <?php if( $vli->getIpv6enabled() and $vli->getIpv6address() ): ?>
                                        <?= $vli->getIPv6Address()->getAddress() ?>
                                        <br>
                                    <?php endif; ?>
                                    <?php if( $vli->getIpv4enabled() and $vli->getIpv4address() ): ?>
                                        <?= $vli->getIPv4Address()->getAddress() ?>
                                        <br>
                                    <?php else: ?>
                                        &nbsp;<br>
                                    <?php endif; ?>

                                    <?php if( !$vli->getIpv6enabled() ): ?>
                                        &nbsp;<br>
                                    <?php endif; ?>

                                </p>
                                <br>

                            <?php endforeach; ?>
                        <?php endif; ?>

                    </div>
                    <div class="tw-px-6 tw-pb-8">

                        <?php if( $vi->getVlanInterfaces() ): ?>

                            <?php foreach( $vi->getVlanInterfaces() as $vli ): ?>

                                <?php if( $vli->getVlan()->getPrivate() ): ?>
                                    <?php continue; ?>
                                <?php endif; ?>


                                <?php if( !in_array( $t->c->getAutsys(), $rsasns ) ): ?>
                                    <?php if( $vli->getRsclient() ): ?>
                                        <span class="tw-inline-block tw-border tw-border-green-500       tw-p-1 tw-rounded-full tw-text-green-dark  tw-font-semibold tw-uppercase tw-text-sm tw-px-3 tw-py-1 tw-mr-2">
                                    <?php else: ?>
                                        <span class="tw-inline-block tw-border tw-border-red-lighter     tw-p-1 tw-rounded-full tw-text-red-lighter tw-font-semibold tw-uppercase tw-text-sm tw-px-3 tw-py-1 tw-mr-2">
                                    <?php endif; ?>
                                        Route Server
                                    </span>
                                <?php endif; ?>

                                <?php if( $t->c->getAutsys() !== 112 ): ?>
                                    <?php if( $vli->getAs112client() ): ?>
                                        <span class="tw-inline-block tw-border tw-border-green-500       tw-p-1 tw-rounded-full tw-text-green-dark  tw-font-semibold tw-uppercase tw-text-sm tw-px-3 tw-py-1 tw-mr-2">
                                    <?php else: ?>
                                        <span class="tw-inline-block tw-border tw-border-red-lighter     tw-p-1 tw-rounded-full tw-text-red-lighter tw-font-semibold tw-uppercase tw-text-sm tw-px-3 tw-py-1 tw-mr-2">
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



