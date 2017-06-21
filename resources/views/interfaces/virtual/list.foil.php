<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= route('interfaces/virtual/list') ?>">Virtual Interfaces</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>List</li>
<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>
    <li class="pull-right">
        <div class=" btn-group btn-group-xs" role="group">
            <button type="button" class="btn btn-default dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="glyphicon glyphicon-plus"></i> <span class="caret"></span>
            </button>
            <ul class="dropdown-menu dropdown-menu-right">
                <li>
                    <a id="" href="<?= action( 'Interfaces\VirtualInterfaceController@wizard' ) ?>" >
                        Add Interface Wizard...
                    </a>
                </li>
                <li>
                    <a id="" href="<?= action( 'Interfaces\VirtualInterfaceController@add' ) ?>" >
                        Add Virtual Interface Object Only...
                    </a>
                </li>
            </ul>
        </div>
    </li>
<?php $this->append() ?>

<?php $this->section('content') ?>

    <?= $t->alerts() ?>

    <div id="message-vi"></div>
    <div id="area-vi" class="collapse">
        <table id='table-vi' class="table">
            <thead>
                <tr>
                    <td>
                        Customer
                    </td>
                    <td>
                        Location
                    </td>
                    <td>
                        Switch
                    </td>
                    <td>
                        Port(s)
                    </td>
                    <td>
                        Speed
                    </td>
                    <td>
                        Action
                    </td>
                </tr>
            <thead>
            <tbody>
            <?php foreach( $t->vis as $vi ):
                /** @var Entities\VirtualInterface $vi */ ?>
                <tr>
                    <td>
                        <a href="<?= url( '/customer/overview/id' ).'/' . $vi->getCustomer()->getId() ?>">
                            <?= $t->ee( $vi->getCustomer()->getName() ) ?>
                        </a>
                    </td>
                    <?php if( count( $vi->getPhysicalInterfaces() ) ): ?>
                        <td>
                            <?= $t->ee( $vi->getPhysicalInterfaces()[0]->getSwitchPort()->getSwitcher()->getCabinet()->getLocation()->getName() ) ?>
                        </td>
                        <td>
                            <?= $t->ee( $vi->getPhysicalInterfaces()[0]->getSwitchPort()->getSwitcher()->getName() ) ?>
                        </td>
                        <td>
                            <?php
                                $speed = 0;
                                foreach( $vi->getPhysicalInterfaces() as $pi ) {
                                    echo $t->ee( $pi->getSwitchPort()->getName() ) . "<br>";
                                    $speed += $pi->getSpeed();
                                }
                            ?>
                        </td>
                        <td>
                            <?= $t->scaleBits( $speed*1000*1000, 0 ) ?>
                        </td>
                    <?php else: ?>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td></td>
                    <?php endif; ?>
                    <td>
                        <div class="btn-group btn-group-sm" role="group">
                            <a class="btn btn btn-default" href="<?= action( 'Interfaces\VirtualInterfaceController@view' , ['id' => $vi->getId() ] )?>" title="Preview">
                                <i class="glyphicon glyphicon-eye-open"></i>
                            </a>
                            <a class="btn btn btn-default" href="<?= route( 'interfaces/virtual/edit' , [ 'id' => $vi->getId() ]) ?>" title="Edit">
                                <i class="glyphicon glyphicon-pencil"></i>
                            </a>

                            <a class="btn btn btn-default" id="delete-vi-<?= $vi->getId() ?>" href="" title="Delete Virtual Interface">
                                <i class="glyphicon glyphicon-trash"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endforeach;?>
            <tbody>
        </table>
    </div>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>

    <?= $t->insert( 'interfaces/virtual/js/interface' ); ?>

    <script>
        $(document).ready( function() {
            loadDataTable( 'vi' );
            $('#area-vi').show();
        });

        /**
         * on click even allow to delete a Virtual Interface
         */
        $(document).on('click', "a[id|='delete-vi']" ,function(e){
            e.preventDefault();
            var vi = (this.id).substring(10);
            deletePopup( vi , false, 'vi');
        });

    </script>
<?php $this->append() ?>

