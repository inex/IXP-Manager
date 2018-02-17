<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>


<?php $this->section( 'title' ) ?>

    <?php if( Auth::check() && Auth::user()->isSuperUser() ): ?>
        <a href="<?= route( 'customer@list' )?>"><?= $t->associates ? 'Associate Members' : 'Customers' ?></a> (Member Details Page)
    <?php else: ?>
        <?= $t->associates ? 'Associate' : '' ?> Members
    <?php endif; ?>

<?php $this->append() ?>


<?php $this->section('content') ?>

    <?= $t->alerts() ?>

    <table id='customer-list' class="table" style="display: none;">

        <thead>
            <tr>
                <td>
                    Member
                </td>
                <td>
                    Joined
                </td>

                <?php if( !$t->associates ): ?>

                    <td>
                        ASN
                    </td>

                    <td>
                        Peering Policy
                    </td>

                    <?php if( Auth::check() ): ?>
                        <td>
                            Peering Email
                        </td>
                        <td>
                            NOC Phone
                        </td>
                        <td>
                            NOC Hours
                        </td>
                    <?php endif; ?>

                <?php endif; ?>

                <td>
                    Action
                </td>
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
                            <?php if( $c->getType() == \Entities\Customer::TYPE_ASSOCIATE ): ?>
                                <em>(associate)</em>
                            <?php else: ?>
                                <?php if( $c->getAutsys() ): ?>
                                    <a href="#">
                                        <?=  $t->asNumber( $c->getAutsys() ) ?>
                                    </a>
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
                            <a class="btn btn btn-default" href="<?= route ( 'customer@detail' , [ 'id' => $c->getId() ] )  ?>" title="View">
                                <i class="glyphicon glyphicon-eye-open"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endforeach;?>
        <tbody>
    </table>

<?php $this->append() ?>


<?php $this->section( 'scripts' ) ?>

    <script>
        $(document).ready( function() {
            $( '#customer-list' ).dataTable( { "autoWidth": false, "iDisplayLength": 100 } ).show();
        });
    </script>

<?php $this->append() ?>
