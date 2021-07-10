<div class="card">
    <div class="card-body">
        <div id="message-cl"></div>
        <h3>
            Core Links

            <?php if( $t->cb->sameSwitchForEachPIFromCL( true ) && $t->cb->sameSwitchForEachPIFromCL( false ) ): ?>
                <button id='btn-create-cl' type='button' class='btn-sm btn btn-white tw-float-right' href="#" title="Add Core link">
                    <span class="fa fa-plus"></span>
                </button>
            <?php endif;?>
        </h3>


        <div id="area-cl">
            <?= Former::open()->method( 'PUT' )
                ->id( 'core-link-form' )
                ->action( route( 'core-link@update', [ 'cb' => $t->cb->id ] ) )
                ->customInputWidthClass( 'col-sm-10' )
                ->actionButtonsCustomClass( "grey-box")
            ?>

            <table id="" class="table table-bordered tw-mt-6">
                <tr class="active">
                    <td>
                        <strong>Switch A:</strong>
                        <?php if( $t->cb->sameSwitchForEachPIFromCL( true ) ): ?>
                            <span class="tw-font-mono"><?= $t->cb->switchSideX( true )->name ?></span>
                            <input type="hidden" value="<?= $t->cb->switchSideX( true )->id ?>" id="switch-a">
                        <?php else: ?>
                            <span class="badge badge-warning">Multiple</span>
                        <?php endif;?>
                    </td>
                    <td>
                        <strong>Switch B:</strong>
                        <?php if( $t->cb->sameSwitchForEachPIFromCL( false ) ): ?>
                            <span class="tw-font-mono"><?= $t->cb->switchSideX( false )->name ?></span>
                            <input type="hidden" value="<?= $t->cb->switchSideX( false )->id ?>" id="switch-b">
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
                        <?php if( $t->cb->typeECMP() ): ?>
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
                <?php foreach( $t->cb->coreLinks as $cl ) :
                    /** @var \IXP\Models\CoreLink $cl */ ?>
                    <tr >
                        <td class="align-middle">
                            <?= $nbCl ?>
                        </td>
                        <td class="align-middle">
                            <?= $cl->coreInterfaceSideA->physicalInterface->switchPort->name ?>
                            <a class="btn btn-sm btn-white" href="<?= route('physical-interface@edit-from-core-bundle' , [ 'pi' => $cl->coreInterfaceSideA->physicalInterface->id, 'cb' => $t->cb->id ] ) ?>">
                                <i class="fa fa-pencil"></i>
                            </a>
                        </td>
                        <td class="align-middle">
                            <?= $cl->coreInterfaceSideB->physicalInterface->switchPort->name ?>
                            <a class="btn btn-sm btn-white" href="<?= route('physical-interface@edit-from-core-bundle' , [ 'pi' => $cl->coreInterfaceSideB->physicalInterface->id, 'cb' => $t->cb->id ] ) ?>">
                                <i class="fa fa-pencil"></i>
                            </a>
                        </td>
                        <td class="align-middle">
                            <?= Former::checkbox( 'enabled-' . $cl->id )
                                ->label( '' )
                                ->value( 1 )
                                ->inline()
                                ->class( "mx-auto" )
                                ->check( $cl->enabled ? true : false )
                            ?>
                        </td>
                        <?php if( $t->cb->typeECMP() ): ?>
                            <td class="align-middle">
                                <?= Former::checkbox( 'bfd-' . $cl->id )
                                    ->label( '' )
                                    ->value( 1 )
                                    ->inline()
                                    ->class( "mx-auto" )
                                    ->check( $cl->bfd ? true : false )
                                ?>

                            </td>
                            <td class="align-middle">
                                <?= Former::text( 'subnet-' . $cl->id )
                                    ->label( '' )
                                    ->placeholder( '192.0.2.0/30' )
                                    ->value( $t->ee( $cl->ipv4_subnet ) )
                                    ->class( 'subnet-cl form-control subnet align-middle' )
                                    ->style( "padding: 0rem 1rem" )
                                ?>
                            </td>
                        <?php endif; ?>
                        <td class="align-middle">
                            <?php if( !config( 'ixp_fe.frontend.disabled.logs' ) && method_exists( \IXP\Models\CoreLink::class, 'logSubject') ): ?>
                                <a class="btn btn-white btn-sm" href="<?= route( 'log@list', [ 'model' => 'CoreLink' , 'model_id' => $cl->id ] ) ?>">
                                    View logs
                                </a>
                            <?php endif; ?>
                            <?php if( $t->cb->coreLinks()->count() > 1 ): ?>
                                <div class="btn-group btn-group-sm" role="group">
                                    <a class="btn btn btn-white btn-delete-cl" id="btn-delete-cl-<?= $cl->id ?>" href="<?= route( 'core-link@delete', [ 'cb' => $t->cb->id , 'cl' => $cl->id ] ) ?>" title="Delete">
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
                ->value( $t->cb->id )
            ?>

            <?=Former::actions(
                Former::primary_submit( 'Save Core links Changes' )->id( 'core-links-submit-btn' )
            );?>

            <?= Former::close() ?>
        </div>
    </div>
</div>