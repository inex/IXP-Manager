<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
    $userCheck = Auth::check();
?>

<?php $this->section( 'page-header-preamble' ) ?>
    <?php if( Auth::check() && Auth::getUser()->isSuperUser() ): ?>
        <?= $t->associates ? ( 'Associate ' . ucfirst( config( 'ixp_fe.lang.customer.many' ) ) )  : ucfirst( config( 'ixp_fe.lang.customer.many' ) ) ?>
    <?php else: ?>
        <?= $t->associates ? 'Associate' : '' ?> <?= ucfirst( config( 'ixp_fe.lang.customer.many' ) ) ?>
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
                            <?= ucfirst( config( 'ixp_fe.lang.customer.one' ) ) ?>
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
                        /** @var \IXP\Models\Customer $c */
                        ?>
                        <tr>
                            <td>
                                <a href="<?= route ( 'customer@detail' , [ 'cust' => $c->id ] )  ?>">
                                    <?= $t->ee( $c->name ) ?>
                                </a>

                                <?php if( !$t->associates ): ?>
                                    <?php if( $c->peeringpolicy !== \IXP\Models\Customer::PEERING_POLICY_OPEN ): ?>
                                        <span class="tw-hidden lg:tw-inline tw-border-1 tw-p-1 tw-rounded-full tw-float-right tw-text-grey-dark tw-uppercase tw-text-xs">
                                            <?= $c->peeringpolicy ?>
                                        </span>
                                    <?php endif; ?>

                                    <?php if( $c->in_manrs ): ?>
                                        <a href="https://www.manrs.org/" target="_blank" class="hover:no-underline">
                                            <span class="tw-hidden md:tw-inline tw-border-1 tw-border-green-500 tw-p-1 tw-rounded-full tw-text-green-500 tw-uppercase tw-text-xs tw-mx-3">
                                                MANRS
                                            </span>
                                        </a>
                                    <?php endif; ?>
                                <?php endif; ?>
                            </td>

                            <td class="tw-font-mono tw-hidden md:tw-table-cell">
                                <?= \Carbon\Carbon::instance( $c->datejoin )->format( 'Y-m-d' ) ?>
                            </td>

                            <?php if( !$t->associates ): ?>
                                <td class="tw-font-mono tw-text-right">
                                    <?php if( $c->in_peeringdb ): ?>
                                        <?php if( Auth::check() ): ?>
                                            <?=  $t->asNumber( $c->autsys, false ) ?>
                                        <?php else: ?>
                                            <a href="https://www.peeringdb.com/asn/<?= $c->autsys ?>" target="_peeringdb">
                                                <?= $c->autsys ?>
                                            </a>
                                        <?php endif; ?>
                                    <?php else: ?>
                                        <?= $c->autsys ?>
                                    <?php endif; ?>
                                </td>

                                <?php if( Auth::check() ): ?>
                                    <td  class="hidden lg:tw-table-cell">
                                        <a href="mailto:<?= $t->ee( $c->peeringemail ) ?>">
                                            <?= $t->ee( $c->peeringemail ) ?>
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
            $( '#customer-list' ).dataTable( {
                stateSave: true,
                stateDuration : DATATABLE_STATE_DURATION,
                responsive: false,
                "iDisplayLength": 100
            }).show();
        });
    </script>
<?php $this->append() ?>