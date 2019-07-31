<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>


<?php $this->section( 'page-header-preamble' ) ?>

    <?php if( Auth::check() && Auth::user()->isSuperUser() ): ?>
        <?= $t->associates ? 'Associate Members' : 'Customers' ?>
    <?php else: ?>
        <?= $t->associates ? 'Associate' : '' ?> Members
    <?php endif; ?>

<?php $this->append() ?>


<?php $this->section('content') ?>

    <div class="row">

        <div class="col-12">

            <?= $t->alerts() ?>

            <table id='customer-list' class="table table-hover ixpm-table">

                <thead class="thead-dark">
                    <tr>
                        <th>
                            Member
                        </th>
                        <th class="tw-hidden md:tw-table-cell">
                            Joined
                        </th>

                        <?php if( !$t->associates ): ?>

                            <th class="tw-text-right">
                                ASN
                            </th>

                            <?php if( Auth::check() ): ?>
                                <th class="hidden lg:tw-table-cell">
                                    Peering Email
                                </th>
                            <?php endif; ?>

                        <?php endif; ?>

                    </tr>
                <thead>

                <tbody>

                <?php foreach( $t->custs as $c ):
                    /** @var Entities\Customer $c */
                    ?>

                    <tr>
                        <td>
                            <a href="<?= route ( 'customer@detail' , [ 'id' => $c->getId() ] )  ?>">
                                <?= $t->ee( $c->getName() ) ?>
                            </a>

                            <?php if( !$t->associates ): ?>
                                <?php if( $c->getPeeringpolicy() != \Entities\Customer::PEERING_POLICY_OPEN ): ?>
                                    <span class="tw-hidden lg:tw-inline tw-border tw-p-1 tw-rounded-full tw-float-right tw-text-grey-dark tw-uppercase tw-text-xs">
                                        <?= $c->getPeeringpolicy() ?>
                                    </span>
                                <?php endif; ?>

                                <?php if( $c->getInManrs() ): ?>
                                    <a href="https://www.manrs.org/" target="_blank" class="hover:no-underline">
                                        <span class="tw-hidden md:tw-inline tw-border tw-border-green-500 tw-p-1 tw-rounded-full tw-text-green-500 tw-uppercase tw-text-xs tw-mx-3">
                                            MANRS
                                        </span>
                                    </a>
                                <?php endif; ?>
                            <?php endif; ?>


                        </td>

                        <td class="tw-font-mono tw-hidden md:tw-table-cell">
                            <?= $c->getDatejoin()->format( 'Y-m-d' ) ?>
                        </td>

                        <?php if( !$t->associates ): ?>

                            <td class="tw-font-mono tw-text-right">
                                <?php if( $c->getInPeeringdb() ): ?>
                                    <?php if( Auth::check() ): ?>
                                        <?=  $t->asNumber( $c->getAutsys(), false ) ?>
                                    <?php else: ?>
                                        <a href="https://www.peeringdb.com/asn/<?= $c->getAutsys() ?>" target="_peeringdb">
                                            <?= $c->getAutsys() ?>
                                        </a>
                                    <?php endif; ?>
                                <?php else: ?>
                                    <?= $c->getAutsys() ?>
                                <?php endif; ?>
                            </td>

                            <?php if( Auth::check() ): ?>
                                <td  class="hidden lg:tw-table-cell">
                                    <a href="mailto:<?= $t->ee( $c->getPeeringemail() ) ?>">
                                        <?= $t->ee( $c->getPeeringemail() ) ?>
                                    </a>
                                </td>
                            <?php endif; ?>

                        <?php endif; ?>

                    </tr>
                <?php endforeach;?>
                <tbody>
            </table>

        </div>

    </div>



<?php $this->append() ?>


<?php $this->section( 'scripts' ) ?>

    <script>
        $(document).ready( function() {
            $( '#customer-list' ).show();

            $( '#customer-list' ).dataTable( {
                stateSave: true,
                stateDuration : DATATABLE_STATE_DURATION,
                responsive: false,
                "iDisplayLength": 100
            });
        });
    </script>

<?php $this->append() ?>
