<?php
/** @var Foil\Template\Template $t */
/** @var $t->active */

$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= route( 'customer@list' )?>">Customers</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>List</li>
<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>
    <li class="pull-right">
        <div class="btn-group btn-group-xs" role="group">
            <a id="btn-filter-options" class="btn btn-default" href="<?= route( "customer@listByCurrentCust" , [ "currentCust" => $t->currentCust ? 0 : 1 ] ) ?>">
                <?php if( $t->currentCust ): ?> Show All Customers <?php else: ?>Show Current Customers<?php endif;?>
            </a>

            <div class="btn-group">
                <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?php if( $t->status ): ?> <?= \Entities\Customer::$CUST_STATUS_TEXT[ $t->status ] ?> <?php else: ?>Limit to status...<?php endif;?> <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li <?php if( !$t->status ): ?> class="active" <?php endif; ?>>
                        <a href="<?= route( "customer@listByStatus" , [ "status" => 0 ] ) ?>">All Status</a>
                    </li>
                    <li role="separator" class="divider"></li>
                    <li <?php if( $t->status == \Entities\Customer::STATUS_NORMAL ):        ?> class="active" <?php endif; ?>   >
                        <a href="<?= route( "customer@listByStatus" , [ "status" => \Entities\Customer::STATUS_NORMAL ]         ) ?>">Normal</a>
                    </li>
                    <li <?php if( $t->status == \Entities\Customer::STATUS_NOTCONNECTED ):  ?> class="active" <?php endif; ?>    >
                        <a href="<?= route( "customer@listByStatus" , [ "status" => \Entities\Customer::STATUS_NOTCONNECTED ]   ) ?>">Not Connected</a>
                    </li>
                    <li <?php if( $t->status == \Entities\Customer::STATUS_SUSPENDED ):     ?> class="active" <?php endif; ?>   >
                        <a href="<?= route( "customer@listByStatus" , [ "status" => \Entities\Customer::STATUS_SUSPENDED ]      ) ?>">Suspended</a>
                    </li>
                </ul>
            </div>

            <!-- Small button group -->
            <div class="btn-group">
                <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?php if( $t->type ): ?> <?= \Entities\Customer::$CUST_TYPES_TEXT[ $t->type ] ?> <?php else: ?>Limit to type...<?php endif;?> <span class="caret"></span>
                </button>
                <ul class="dropdown-menu">
                    <li <?php if( !$t->type ): ?> class="active" <?php endif; ?> >
                        <a id="type-0" href="<?= route( "customer@listByType" , [ "type" => 0 ] ) ?>">All types</a>
                    </li>
                    <li role="separator" class="divider"></li>
                    <li <?php if( $t->type == \Entities\Customer::TYPE_FULL ):          ?> class="active" <?php endif; ?> >
                        <a id="type-4" href="<?= route( "customer@listByType" , [ "type" => \Entities\Customer::TYPE_FULL ]         ) ?>">Full</a>
                    </li>
                    <li <?php if( $t->type == \Entities\Customer::TYPE_ASSOCIATE ):     ?> class="active" <?php endif; ?> >
                        <a id="type-6" href="<?= route( "customer@listByType" , [ "type" => \Entities\Customer::TYPE_ASSOCIATE ]    ) ?>">Associated</a>
                    </li>
                    <li <?php if( $t->type == \Entities\Customer::TYPE_INTERNAL ):      ?> class="active" <?php endif; ?> >
                        <a id="type-6" href="<?= route( "customer@listByType" , [ "type" => \Entities\Customer::TYPE_INTERNAL ]     ) ?>">Internal</a>
                    </li>
                    <li <?php if( $t->type == \Entities\Customer::TYPE_PROBONO ):       ?> class="active" <?php endif; ?> >
                        <a id="type-6" href="<?= route( "customer@listByType" , [ "type" => \Entities\Customer::TYPE_PROBONO ]      ) ?>">Pro-bono</a>
                    </li>
                    <li <?php if( $t->type == \Entities\Customer::TYPE_ROUTESERVER ):   ?> class="active" <?php endif; ?> >
                        <a id="type-6" href="<?= route( "customer@listByType" , [ "type" => \Entities\Customer::TYPE_ROUTESERVER ]  ) ?>">Routeserver</a>
                    </li>
                </ul>
            </div>


            <a type="button" class="btn btn-default" href="<?= route( 'customer@add' ) ?>">
                <span class="glyphicon glyphicon-plus"></span>
            </a>
        </div>
    </li>
<?php $this->append() ?>

<?php $this->section('content') ?>

    <?= $t->alerts() ?>
    <table id='customer-list' class="table collapse" >
        <thead>
        <tr>
            <td>
                Name
            </td>
            <td>
                AS
            </td>
            <td>
                ShortName
            </td>
            <td>
                Peering Policy
            </td>
            <td>
                Reseller
            </td>
            <td>
                Type
            </td>
            <td>
                Status
            </td>
            <td>
                Joined
            </td>
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
                    <a href="<?= route( "customer@overview" , [ "id" => $c->getId() ] ) ?>">
                        <?= $t->ee( $c->getName() ) ?>
                    </a>

                </td>
                <td>
                    <?php if( $c->getAutsys() ): ?>
                        <a href="#">
                            <?=  $t->asNumber( $c->getAutsys() ) ?>
                        </a>
                    <?php endif; ?>

                </td>
                <td>
                    <a href="<?= route( "customer@overview" , [ "id" => $c->getId() ] ) ?>">
                        <?= $t->ee( $c->getShortname() ) ?>
                    </a>
                </td>
                <td>
                    <?= $t->ee( $c->getPeeringpolicy() ) ?>
                </td>
                <td>
                    <?= $c->getReseller() ? "Yes" : "No" ?>
                </td>
                <td>
                    <?= $t->insert( 'customer/list-type',   [ 'cust' => $c , 'resellerMode' => $t->resellerMode ] ) ?>
                </td>
                <td>
                    <?= $t->insert( 'customer/list-status', [ 'cust' => $c ] ) ?>
                </td>
                <td>
                    <?= $c->getDatejoin() != null ? $c->getDatejoin()->format( "Y-m-d" ) : "" ?>
                </td>
                <td>
                    <div class="btn-group btn-group-sm" role="group">
                        <a class="btn btn btn-default" href="<?= route( "customer@overview" , [ "id" => $c->getId() ] ) ?>" title="View">
                            <i class="glyphicon glyphicon-eye-open"></i>
                        </a>
                        <a class="btn btn btn-default" href="<?= route ( "customer@deleteRecap", [ "id" => $c->getId() ] )   ?>" title="View">
                            <i class="glyphicon glyphicon-trash"></i>
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
            $( '#customer-list' ).dataTable( { "autoWidth": false } );

            $( '#customer-list' ).show();
        });
    </script>
<?php $this->append() ?>