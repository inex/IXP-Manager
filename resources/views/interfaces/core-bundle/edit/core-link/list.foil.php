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
                ->action( route( 'core-link@edit-store' ) )
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
                        <td>
                            <?= $nbCl ?>
                        </td>
                        <td>
                            <?= $cl->getCoreInterfaceSideA()->getPhysicalInterface()->getSwitchPort()->getName() ?>
                            <a class="btn btn-sm btn-white" href="<?= route('interfaces/physical/edit/from-core-bundle' , [ 'id' => $cl->getCoreInterfaceSideA()->getPhysicalInterface()->getId(), 'cb' => $t->cb->getId() ] ) ?>">
                                <i class="fa fa-pencil"></i>
                            </a>
                        </td>
                        <td>
                            <?= $cl->getCoreInterfaceSideB()->getPhysicalInterface()->getSwitchPort()->getName() ?>
                            <a class="btn btn-sm btn-white" href="<?= route('interfaces/physical/edit/from-core-bundle' , [ 'id' => $cl->getCoreInterfaceSideB()->getPhysicalInterface()->getId(), 'cb' => $t->cb->getId() ] ) ?>">
                                <i class="fa fa-pencil"></i>
                            </a>
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
                                    ->class( 'subnet-cl form-control subnet' )
                                    ->style( "padding: 0rem 1rem" )
                                ?>
                            </td>
                        <?php endif; ?>
                        <td>
                            <?php if( count( $t->cb->getCoreLinks() ) > 1 ): ?>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a class="btn btn btn-white delete-cl" id="delete-cl-<?= $cl->getId() ?>" href="#" title="Delete">
                                        <i class="fa fa-trash"></i>
                                    </a>
                                </div>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php $nbCl++ ?>
                <?php endforeach; ?>
            </table>

            <?= Former::hidden( 'cb' )
                ->id( 'cb')
                ->value( $t->cb->getId() )
            ?>

            <?=Former::actions(
                Former::primary_submit( 'Save Core links Changes' )->id( 'core-links-submit-btn' )
            );?>

            <?= Former::close() ?>
        </div>
    </div>
</div>