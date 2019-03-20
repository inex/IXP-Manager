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

        <div class="col-sm-12">

            <?= $t->alerts() ?>

            <table id='customer-list' class="table table-hover ixpm-table">

                <thead class="thead-dark">
                    <tr>
                        <th>
                            Member
                        </th>
                        <th class="hidden md:table-cell">
                            Joined
                        </th>

                        <?php if( !$t->associates ): ?>

                            <th class="text-right">
                                ASN
                            </th>

                            <?php if( Auth::check() ): ?>
                                <th class="hidden lg:table-cell">
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

                            <?php if( $c->getPeeringpolicy() != \Entities\Customer::PEERING_POLICY_OPEN ): ?>
                                <span class="hidden lg:inline border p-1 rounded-full float-right text-grey-dark text-uppercase text-xs">
                                    <?= $c->getPeeringpolicy() ?>
                                </span>
                            <?php endif; ?>

                            <?php if( $c->getInManrs() ): ?>
                                <a href="https://www.manrs.org/" target="_blank" class="hover:no-underline">
                                    <span class="hidden md:inline border border-green p-1 rounded-full text-green text-uppercase text-xs mx-3" style="border-color: #38c172 !important;">
                                        MANRS
                                    </span>
                                </a>
                            <?php endif; ?>


                        </td>

                        <td class="font-mono hidden md:table-cell">
                            <?= $c->getDatejoin()->format( 'Y-m-d' ) ?>
                        </td>

                        <?php if( !$t->associates ): ?>

                            <td class="font-mono text-right">
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
                                <td  class="hidden lg:table-cell">
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
                responsive: false,
                "iDisplayLength": 100
            });
        });
    </script>

<?php $this->append() ?>
