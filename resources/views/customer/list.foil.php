<?php
/** @var Foil\Template\Template $t */
/** @var $t->active */

$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    <?= ucfirst( config( 'ixp_fe.lang.customer.many' ) ) ?> / List
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>


    <div class="btn-group btn-group-sm" role="group">
        <a id="btn-filter-options" class="btn btn-white" href="<?= route( "customer@list" ) . '?current-only=' . ( $t->showCurrentOnly ? '0' : '1' ) ?>">
            <?= $t->showCurrentOnly ? ( "Show All " . ucfirst( config( 'ixp_fe.lang.customer.many' ) ) ) : ( "Show Current " . ucfirst( config( 'ixp_fe.lang.customer.many' ) ) ) ?>
        </a>

        <div class="btn-group btn-group-sm">

            <button class="btn btn-white dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?= $t->state ? 'State: ' . \Entities\Customer::$CUST_STATUS_TEXT[ $t->state ] : "Limit to state..." ?>
            </button>
            <div class="dropdown-menu">
                <a class="dropdown-item  <?= $t->state ?: "active" ?>" href="<?= route( "customer@list" ) . '?state=0' ?>">
                    All States
                </a>

                <div class="dropdown-divider"></div>

                <?php foreach( \Entities\Customer::$CUST_STATUS_TEXT as $state => $text ): ?>
                    <a class="dropdown-item <?= $t->state != $state ?: "active" ?>" href="<?= route( "customer@list" ) . '?state=' . $state ?>">
                        <?= $text ?>
                    </a>
                <?php endforeach; ?>

            </div>

        </div>

        <div class="btn-group btn-group-sm">

            <button class="btn btn-white dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?= $t->type ? 'Type: ' . \Entities\Customer::$CUST_TYPES_TEXT[ $t->type ] : "Limit to type..." ?>
            </button>

            <ul class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item <?= $t->type ?: "active" ?>" href="<?= route( "customer@list" ) . '?type=0' ?>">
                    All Types
                </a>

                <div class="dropdown-divider"></div>

                <?php foreach( \Entities\Customer::$CUST_TYPES_TEXT as $type => $text ): ?>
                    <a class="dropdown-item <?= $t->type != $type ?: "active" ?>" href="<?= route( "customer@list" ) . '?type=' . $type ?>">
                        <?= $text ?>
                    </a>
                <?php endforeach; ?>

            </ul>
        </div>

        <div class="btn-group btn-group-sm">

            <button class="btn btn-white btn-xs dropdown-toggle" type="button" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <?= $t->tag ? 'Tag: ' . $t->tags[ $t->tag ] : "Limit to tag..." ?>
            </button>

            <ul class="dropdown-menu dropdown-menu-right">
                <a class="dropdown-item <?= $t->tag ?: "active" ?>" href="<?= route( "customer@list") . '?tag=0'  ?>">
                    All Tags
                </a>

                <div class="dropdown-divider"></div>

                <?php foreach( $t->tags as $id => $name ): ?>
                    <a class="dropdown-item <?= $t->tag != $id ?: "active" ?>"href="<?= route( "customer@list" ) . '?tag=' . $id ?>">
                        <?= $name ?>
                    </a>
                <?php endforeach; ?>

            </ul>
        </div>


        <a class="btn btn-white" href="<?= route( 'customer@add' ) ?>">
            <span class="fa fa-plus"></span>
        </a>
    </div>

<?php $this->append() ?>

<?php $this->section('content') ?>

    <?= $t->alerts() ?>

        <table id='customer-list' class="table collapse table-striped no-wrap responsive tw-shadow-md" style="width:100%" >
            <thead class="thead-dark">
                <tr>
                    <th>
                        Name
                    </th>
                    <th>
                        AS
                    </th>
                    <th>
                        Peering Policy
                    </th>
                    <th>
                        Reseller
                    </th>
                    <th>
                        Type
                    </th>
                    <th>
                        Status
                    </th>
                    <th>
                        Joined
                    </th>
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
                            <a class="btn btn-white" href="<?= route( "customer@overview" , [ "id" => $c->getId() ] ) ?>" title="Overview">
                                <i class="fa fa-eye"></i>
                            </a>
                            <a class="btn btn-white" href="<?= route ( "customer@delete-recap", [ "id" => $c->getId() ] )   ?>" title="Delete">
                                <i class="fa fa-trash"></i>
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

            $( '#customer-list' ).show();

            $('#customer-list').DataTable( {
                responsive: true,
                stateSave: true,
                stateDuration : DATATABLE_STATE_DURATION,
                columnDefs: [
                    { responsivePriority: 1, targets: 0 },
                    { responsivePriority: 2, targets: -1 }
                ],
            } );

        });
    </script>
<?php $this->append() ?>