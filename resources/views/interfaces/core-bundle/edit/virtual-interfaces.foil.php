<div class="mt-4">
    <h3>
        Virtual Interfaces
    </h3>
    <div class="" id="area-vi">
        <table id="table-virtual-interface" class="table table-bordered">
            <?php foreach( $t->cb->virtualInterfaces() as $side => $vi ) :
                /** @var \IXP\Models\VirtualInterface $vi */ ?>
                <tr>
                    <td>
                        Side <?= $side ?>
                    </td>
                    <td>
                        <?= $t->ee( $vi->name )?>
                    </td>
                    <td>
                        <div class="btn-group btn-group-sm" role="group">
                            <a class="btn btn btn-white" href="<?= route( 'virtual-interface@edit' , [ 'vi' => $vi->id ] )?>" title="Edit">
                                <i class="fa fa-pencil"></i>
                            </a>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </table>
    </div>
</div>