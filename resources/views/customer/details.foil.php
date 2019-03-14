<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>


<?php $this->section( 'page-header-preamble' ) ?>

    <?php if( Auth::check() && Auth::user()->isSuperUser() ): ?>
        <?= $t->associates ? 'Associate Members' : 'Customers' ?> / (Member Details Page)
    <?php else: ?>
        <?= $t->associates ? 'Associate' : '' ?> Members
    <?php endif; ?>

<?php $this->append() ?>


<?php $this->section('content') ?>

    <div class="row">

        <div class="col-sm-12">

            <?= $t->alerts() ?>

            <table id='customer-list' class="table collapse table-striped" style="width: 100%">

                <thead class="thead-dark">
                    <tr>
                        <th>
                            Member
                        </th>
                        <th>
                            Joined
                        </th>

                        <?php if( !$t->associates ): ?>

                            <th>
                                ASN
                            </th>

                            <th>
                                Peering Policy
                            </th>

                            <?php if( Auth::check() ): ?>
                                <th>
                                    Peering Email
                                </th>
                                <th>
                                    NOC Phone
                                </th>
                                <th>
                                    NOC Hours
                                </th>
                            <?php endif; ?>

                        <?php endif; ?>

                        <th>
                            Action
                        </th>
                    </tr>
                <thead>

                <tbody>

                <?php foreach( $t->custs as $c ):
                    /** @var Entities\Customer $c */
                    ?>

                    <tr>
                        <td>
                            <a href="<?= $c->getCorpwww() ?>">
                                <?= $t->ee( $c->getName() ) ?>
                            </a>
                        </td>

                        <td>
                            <?= $c->getDatejoin()->format( 'Y-m-d' ) ?>
                        </td>

                        <?php if( !$t->associates ): ?>

                            <td>
                                <?php if( $c->isTypeAssociate() ): ?>
                                    <em>(associate)</em>
                                <?php else: ?>
                                    <?php if( $c->getAutsys() ): ?>
                                        <?=  $t->asNumber( $c->getAutsys() ) ?>
                                    <?php endif; ?>
                                <?php endif; ?>

                            </td>

                            <td>
                                <?= ucfirst( $c->getPeeringpolicy() ) ?>
                            </td>

                            <?php if( Auth::check() ): ?>
                                <td>
                                    <?= $t->ee( $c->getPeeringemail() ) ?>
                                </td>
                                <td>
                                    <?= $c->getNocphone() ?>
                                </td>
                                <td>
                                    <?= $c->getNochours() ?>
                                </td>
                            <?php endif; ?>

                        <?php endif; ?>

                        <td>
                            <div class="btn-group btn-group-sm" role="group">
                                <a class="btn btn btn-outline-secondary" href="<?= route ( 'customer@detail' , [ 'id' => $c->getId() ] )  ?>" title="View">
                                    <i class="fa fa-eye"></i>
                                </a>
                            </div>
                        </td>
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
                responsive: true,
                "iDisplayLength": 100
            });
        });
    </script>

<?php $this->append() ?>
