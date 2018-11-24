<?php
/** @var Foil\Template\Template $t */
/** @var $t->active */

$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= route( 'customer@list' )?>">Customers</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>List <?= $t->summary ?> </li>
<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>

    <li class="pull-right">

        <div class="btn-group btn-group-xs" role="group">

            <a id="btn-filter-options" class="btn btn-default" href="<?= route( "customer@list" ) . '?current-only=' . ( $t->showCurrentOnly ? '0' : '1' ) ?>">
                <?= $t->showCurrentOnly ? "Show All Customers" : "Show Current Customers" ?>
            </a>


            <div class="btn-group">

                <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= $t->state ? 'State: ' . \Entities\Customer::$CUST_STATUS_TEXT[ $t->state ] : "Limit to state..." ?> <span class="caret"></span>
                </button>

                <ul class="dropdown-menu dropdown-menu-right">

                    <li class="<?= $t->state ? "" : "active" ?>">
                        <a href="<?= route( "customer@list" ) . '?state=0' ?>">All States</a>
                    </li>

                    <li role="separator" class="divider"></li>

                    <?php foreach( \Entities\Customer::$CUST_STATUS_TEXT as $state => $text ): ?>

                        <li class="<?= $t->state == $state ? "active" : "" ?>">
                            <a href="<?= route( "customer@list" ) . '?state=' . $state ?>"><?= $text ?></a>
                        </li>

                    <?php endforeach; ?>

                </ul>

            </div>



            <div class="btn-group">

                <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= $t->type ? 'Type: ' . \Entities\Customer::$CUST_TYPES_TEXT[ $t->type ] : "Limit to type..." ?> <span class="caret"></span>
                </button>

                <ul class="dropdown-menu dropdown-menu-right">

                    <li class="<?= $t->type ? "" : "active" ?>">
                        <a href="<?= route( "customer@list" ) . '?type=0' ?>">All Types</a>
                    </li>

                    <li role="separator" class="divider"></li>

                    <?php foreach( \Entities\Customer::$CUST_TYPES_TEXT as $type => $text ): ?>

                        <li class="<?= $t->type == $type ? "active" : "" ?>">
                            <a href="<?= route( "customer@list" ) . '?type=' . $type ?>"><?= $text ?></a>
                        </li>

                    <?php endforeach; ?>

                </ul>
            </div>

            <div class="btn-group">

                <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                    <?= $t->tag ? 'Tag: ' . $t->tags[ $t->tag ] : "Limit to tag..." ?> <span class="caret"></span>
                </button>

                <ul class="dropdown-menu dropdown-menu-right">

                    <li class="<?= $t->tag ? "" : "active" ?>">
                        <a href="<?= route( "customer@list") . '?tag=0'  ?>">All Tags</a>
                    </li>

                    <li role="separator" class="divider"></li>

                    <?php foreach( $t->tags as $id => $name ): ?>

                        <li class="<?= $t->tag == $id ? "active" : "" ?>">
                            <a href="<?= route( "customer@list" ) . '?tag=' . $id ?>"><?= $name ?></a>
                        </li>

                    <?php endforeach; ?>

                </ul>
            </div>


            <a type="button" class="btn btn-default" href="<?= route( 'customer@add' ) ?>">
                <span class="glyphicon glyphicon-plus"></span>
            </a>
        </div>
    </li>
<?php $this->append() ?>

<?php $this->section('content') ?>

<div class="row">

    <div class="col-sm-12">

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
                        <?= $t->insert( 'customer/list-type',   [ 'cust' => $c ] ) ?>
                    </td>
                    <td>
                        <?= $t->insert( 'customer/list-status', [ 'cust' => $c ] ) ?>
                    </td>
                    <td>
                        <?= $c->getDatejoin() != null ? $c->getDatejoin()->format( "Y-m-d" ) : "" ?>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group">
                            <a class="btn btn btn-default" href="<?= route( "customer@overview" , [ "id" => $c->getId() ] ) ?>" title="Overview">
                                <i class="glyphicon glyphicon-eye-open"></i>
                            </a>
                            <a class="btn btn btn-default" href="<?= route ( "customer@delete-recap", [ "id" => $c->getId() ] )   ?>" title="Delete">
                                <i class="glyphicon glyphicon-trash"></i>
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
            $( '#customer-list' ).dataTable( { "autoWidth": false } );

            $( '#customer-list' ).show();
        });
    </script>
<?php $this->append() ?>