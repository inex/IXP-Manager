<div class="card tw-my-6">
    <div class="card-body">
        <h3>
            Virtual Interfaces
        </h3>

        <p>
            <em>
                To fully understand the role of virtual interfaces, please refer to the documentation. Virtual interfaces are used to group the physical
                interface(s) that represent the link between the individual core links below and a physical switch port. Some elements that are
                configured on each individual virtual interface must be matched on the other side also. Be very aware of this if you are
                provisioning switches from IXP Manager. Otherwise much of this is just informational.
            </em>
        </p>

        <div class="" id="area-vi">
            <table id="table-virtual-interface" class="table table-bordered">
                <?php foreach( $t->cb->virtualInterfaces() as $side => $vi ) :
                    /** @var \IXP\Models\VirtualInterface $vi */ ?>
                    <tr>
                        <td>
                            Side <?= strtoupper( $side ) ?>
                        </td>
                        <td>
                            <?= $t->ee( $vi->bundleName() )?>
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
</div>