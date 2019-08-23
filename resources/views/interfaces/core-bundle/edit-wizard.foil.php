<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'headers' ) ?>
    <style>
        .checkbox input[type=checkbox]{
            margin-left: 0px;
        }

        .col-lg-offset-2{
            margin-left: 0px;
        }

        .checkbox{
            text-align: center;
        }

        #table-core-link tr td{
            vertical-align: middle;
        }
    </style>
<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>
    Core Bundles / Edit
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>


    <div class="btn-group btn-group-sm" role="group">
        <a class="btn btn-white" href="<?= route( 'core-bundle@list' )?>" title="list">
            <span class="fa fa-th-list"></span>
        </a>
        <button type="button" class="btn btn-white dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            <i class="fa fa-plus"></i> <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <a class="dropdown-item" href="<?= route( 'core-bundle@add-wizard' )?>" >
                Add Core Bundle Wizard...
            </a>

        </ul>
    </div>


<?php $this->append() ?>

<?php $this->section('content') ?>

    <div class="row">

        <div class="col-sm-12">

            <?= $t->alerts() ?>

            <div id="message-cb"></div>

            <div class="card">
                <div class="card-body">
                    <?= Former::open()->method( 'POST' )
                        ->id( 'core-bundle-form' )
                        ->action( route ( 'core-bundle@edit-store' ) )
                        ->customInputWidthClass( 'col-lg-8 col-md-6 col-sm-6' )
                        ->customLabelWidthClass( 'col-lg-4 col-md-3 col-sm-4' )
                        ->actionButtonsCustomClass( "grey-box")
                    ?>

                        <h3>
                            General Core Bundle Settings :
                        </h3>
                        <hr>
                        <div class="col-lg-6 col-sm-12">

                            <?= Former::select( 'customer' )
                                ->label( 'Customer' )
                                ->fromQuery( $t->customers, 'name' )
                                ->placeholder( 'Choose a customer' )
                                ->addClass( 'chzn-select' )
                                ->blockHelp( '' );
                            ?>

                            <?= Former::text( 'description' )
                                ->label( 'Description' )
                                ->placeholder( 'Description' )
                                ->blockHelp( 'help text' );
                            ?>

                            <?= Former::text( 'graph-title' )
                                ->label( 'Graph Title' )
                                ->placeholder( 'Graph Title' )
                                ->blockHelp( 'help text' );
                            ?>

                            <?= Former::select( 'type' )
                                ->label( 'Type<sup>*</sup>' )
                                ->fromQuery( Entities\CoreBundle::$TYPES , 'name' )
                                ->placeholder( 'Choose Core Bundle type' )
                                ->addClass( 'chzn-select' )
                                ->blockHelp( '' )
                                ->value( Entities\CoreBundle::TYPE_ECMP )
                                ->disabled( true );
                            ?>

                        </div>

                        <div class="col-lg-6 col-sm-12">

                            <?php if( $t->cb->isL2LAG() ): ?>
                                <?= Former::checkbox( 'stp' )
                                    ->id('stp')
                                    ->label( 'STP' )
                                    ->value( 1 )
                                    ->inline()
                                    ->blockHelp( "" );
                                ?>
                            <?php endif; ?>

                            <?= Former::number( 'cost' )
                                ->label( 'Cost' )
                                ->placeholder( '10' )
                                ->min( 0 )
                                ->blockHelp( 'help text' );
                            ?>

                            <?= Former::number( 'preference' )
                                ->label( 'Preference' )
                                ->placeholder( '10' )
                                ->min( 0 )
                                ->blockHelp( 'help text' );
                            ?>

                            <?= Former::checkbox( 'enabled' )
                                ->id( 'enabled' )
                                ->label( 'Enabled' )
                                ->value( 1 )
                                ->blockHelp( "" );
                            ?>

                            <?php if( $t->cb->isL3LAG() ): ?>

                                <?= Former::checkbox( 'bfd' )
                                    ->label( 'BFD' )
                                    ->value( 1 )
                                    ->inline()
                                    ->blockHelp( "" );
                                ?>

                                <?= Former::text( 'subnet' )
                                    ->label( 'SubNet' )
                                    ->placeholder( '192.0.2.0/30' )
                                    ->blockHelp( "" );
                                ?>
                            <?php endif; ?>

                            <?= Former::hidden( 'type' )
                                ->id( 'type')
                                ->value( $t->cb->getType() )
                                ->blockHelp( "" );
                            ?>

                            <?= Former::hidden( 'cb' )
                                ->id( 'cb')
                                ->value( $t->cb->getId() )
                            ?>

                        </div>

                        <?=Former::actions(
                            Former::primary_submit( 'Save Changes' )->id( 'core-bundle-submit-btn' ),
                            Former::secondary_link( 'Cancel' )->href( route( 'core-bundle@list' ) ),
                            Former::success_button( 'Help' )->id( 'help-btn' )
                        )?>


                    <?= Former::close() ?>

                </div>
            </div>


            <div class="mt-4">

                <h3>
                    Virtual Interfaces
                </h3>

                <div class="" id="area-vi">

                    <table id="table-virtual-interface" class="table table-bordered">

                        <?php foreach( $t->cb->getVirtualInterfaces() as $side => $vi ) :
                            /** @var Entities\VirtualInterface $vi */ ?>

                            <tr>
                                <td>
                                    Side <?= $side ?>
                                </td>
                                <td>
                                    <?= $t->ee( $vi->getName() )?>
                                </td>
                                <td>
                                    <div class="btn-group btn-group-sm" role="group">
                                        <a class="btn btn btn-white" href="<?= route( 'interfaces/virtual/edit' , [ 'id' => $vi->getId() ] )?>" title="Edit">
                                            <i class="fa fa-pencil"></i>
                                        </a>
                                    </div>
                                </td>
                            </tr>

                        <?php endforeach; ?>

                    </table>

                </div>

            </div>


            <div class="card">

                <div class="card-body">

                    <div id="message-cl"></div>

                    <h3>
                        Core Links
                        <?php if( $t->cb->sameSwitchForEachPIFromCL( true ) && $t->cb->sameSwitchForEachPIFromCL( false ) ): ?>
                            <button style="float: right; margin-right: 20px" id="add-new-core-link" type="button" class=" btn-sm btn btn-white" href="#" title="Add Core link">
                                <span class="fa fa-plus"></span>
                            </button>
                        <?php endif;?>
                    </h3>
                    <div id="area-cl">

                        <?= Former::open()->method( 'POST' )
                            ->id( 'core-link-form' )
                            ->action( route( 'core-link@store', [ 'id' => $t->cb->getId() ] ) )
                            ->customInputWidthClass( 'col-sm-10' )
                            ->actionButtonsCustomClass( "grey-box")
                        ?>

                            <table id="" class="table table-bordered">

                                <tr class="active">
                                    <td>
                                        Switch A :
                                        <?php if( $t->cb->sameSwitchForEachPIFromCL( true ) ): ?>
                                            <?= $t->cb->getSwitchSideX( true )->getName() ?>
                                            <input type="hidden" value="<?= $t->cb->getSwitchSideX( true )->getId() ?>" id="switch-a">
                                        <?php else: ?>
                                            <span class="badge badge-warning">Multiple</span>
                                        <?php endif;?>
                                    </td>
                                    <td>
                                        Switch B :
                                        <?php if( $t->cb->sameSwitchForEachPIFromCL( false ) ): ?>
                                            <?= $t->cb->getSwitchSideX( false )->getName() ?>
                                            <input type="hidden" value="<?= $t->cb->getSwitchSideX( false )->getId() ?>" id="switch-b">
                                        <?php else: ?>
                                            <span class="badge badge-warning">Multiple</span>
                                        <?php endif;?>
                                    </td>
                                </tr>

                            </table>

                            <table id="table-core-link" class="table table-bordered table-striped table-responsive-ixp-no-header" width="100%">

                                <thead class="thead-dark">
                                    <tr>
                                        <th>
                                            Number
                                        </th>
                                        <th>
                                            Switch Port A
                                        </th>
                                        <th>
                                            Switch Port B
                                        </th>
                                        <th>
                                            Enabled
                                        </th>
                                        <?php if( $t->cb->isECMP () ): ?>
                                            <th>
                                                BFD
                                            </th>
                                            <th>
                                                Subnet
                                            </th>
                                        <?php endif; ?>
                                        <th>
                                            Action
                                        </th>
                                    </tr>
                                </thead>
                                <?php $nbCl = 1 ?>
                                <?php foreach( $t->cb->getCoreLinks() as $cl ) :
                                    /** @var Entities\CoreLink $cl */ ?>
                                    <tr>
                                        <td style="vertical-align: middle">
                                            <?= $nbCl ?>
                                        </td>
                                        <td>
                                            <?= $cl->getCoreInterfaceSideA()->getPhysicalInterface()->getSwitchPort()->getName() ?>
                                            <a class="btn btn-sm btn-white" href="<?= route('interfaces/physical/edit/from-core-bundle' , [ 'id' => $cl->getCoreInterfaceSideA()->getPhysicalInterface()->getId(), 'cb' => $t->cb->getId() ] ) ?>"><i class="fa fa-pencil"></i></a>
                                        </td>
                                        <td>
                                            <?= $cl->getCoreInterfaceSideB()->getPhysicalInterface()->getSwitchPort()->getName() ?>
                                            <a class="btn btn-sm btn-white" href="<?= route('interfaces/physical/edit/from-core-bundle' , [ 'id' => $cl->getCoreInterfaceSideB()->getPhysicalInterface()->getId(), 'cb' => $t->cb->getId() ] ) ?>"><i class="fa fa-pencil"></i></a>
                                        </td>
                                        <td>
                                            <?= Former::checkbox( 'enabled-'.$cl->getId() )
                                                ->label( '' )
                                                ->value( 1 )
                                                ->inline()
                                                ->check( $cl->getEnabled() ? true : false )
                                            ?>
                                        </td>
                                        <?php if( $t->cb->isECMP () ): ?>
                                            <td>
                                                <?= Former::checkbox( 'bfd-'.$cl->getId() )
                                                    ->label( '' )
                                                    ->value( 1 )
                                                    ->inline()
                                                    ->check( $cl->getBFD() ? true : false )
                                                ?>

                                            </td>
                                            <td>
                                                <?= Former::text( 'subnet-'.$cl->getId() )
                                                    ->label( '' )
                                                    ->placeholder( '192.0.2.0/30' )
                                                    ->value( $t->ee( $cl->getIPv4Subnet() ) )
                                                    ->class( 'subnet-cl form-control' )
                                                ?>
                                            </td>
                                        <?php endif; ?>
                                        <td>
                                            <?php if( count( $t->cb->getCoreLinks() ) > 1 ): ?>
                                                <div class="btn-group btn-group-sm" role="group">
                                                    <a class="btn btn btn-white" id="delete-cl-<?=  $cl->getId() ?>" href="#" title="Delete">
                                                        <i class="fa fa-trash"></i>
                                                    </a>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php $nbCl++ ?>
                                <?php endforeach; ?>
                            </table>

                        <?=Former::actions(
                            Former::primary_submit( 'Save Changes' )->id( 'core-links-submit-btn' )
                        );?>

                        <?= Former::close() ?>
                    </div>
                </div>
            </div>


            <!-- If a new Core link is added it will appear here  -->
            <div id="core-links-area" class="mb-4" style="display: none;" >

                <?= Former::horizontal_open()->method( 'POST' )
                    ->id( 'core-link-form' )
                    ->action( route( "core-link@add" ) )
                    ->customInputWidthClass( 'col-sm-6' )
                    ->actionButtonsCustomClass( "grey-box")
                ?>
                    <div id="core-links"></div>

                    <?= Former::hidden( 'nb-core-links' )
                        ->id( 'nb-core-links')
                        ->value( 0 )
                    ?>

                    <?= Former::hidden( 'core-bundle' )
                        ->id( 'core-bundle')
                        ->value( $t->cb->getId() )
                    ?>

                    <?=Former::actions(
                        Former::primary_submit( 'Add new core link' )->id( 'new-core-links-submit-btn' )
                    )->class('text-center');?>

                <?= Former::close() ?>
            </div>

            <br/>

            <!-- Delete Core Bundle area -->

            <div class="alert alert-danger mt-4" role="alert">
                <div class="d-flex align-items-center">
                    <div class="text-center">
                        <i class="fa fa-exclamation-triangle fa-2x"></i>
                    </div>
                    <div class="col-sm-12 d-flex">
                        <div class="mr-auto">
                            <b>If you are sure you want to delete this Core Bundle:</b>
                        </div>
                        <div class="my-auto">
                            <a id="cb-delete-<?= $t->cb->getId() ?>" class="btn btn btn-danger" href="#" title="Delete">
                                Delete
                            </a>
                        </div>
                    </div>
                </div>
            </div>

        </div>

    </div>


<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'interfaces/core-bundle/js/edit-wizard' ); ?>
    <?= $t->insert( 'interfaces/common/js/cb-functions' ); ?>
<?php $this->append() ?>