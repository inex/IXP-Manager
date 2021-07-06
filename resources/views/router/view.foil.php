<?php
  /** @var \IXP\Models\Router $rt */
  $this->layout( 'layouts/ixpv4' );
  $rt = $t->rt /** @var $rt \IXP\Models\Router */;
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Routers / <?= $t->ee( $rt->name ) ?>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm" role="group">
        <a target="_blank" class="btn btn-white" href="https://docs.ixpmanager.org/features/routers/">
            Documentation
        </a>
        <a class="btn btn-white" href="<?= route('router@list' ) ?>" title="list">
            <span class="fa fa-th-list"></span>
        </a>
        <a class="btn btn-white" href="<?= route ('router@create' ) ?>" title="add">
            <span class="fa fa-plus"></span>
        </a>
        <a class="btn btn-white" href="<?= route ('router@edit' , [ 'router' => $rt->id ] ) ?>" title="edit">
            <span class="fa fa-pencil"></span>
        </a>
    </div>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
    <div class="row">
        <div class="col-sm-12">
            <div class="card">
                <div class="card-header tw-flex">
                    <div class="mr-auto">
                        Details
                    </div>
                    <?php if( !config( 'ixp_fe.frontend.disabled.logs' ) && method_exists( \IXP\Models\Router::class, 'logSubject') ): ?>
                        <a class="btn-white btn btn-sm" href="<?= route( 'log@list', [ 'model' => 'Router' , 'model_id' => $rt->id ] ) ?>">
                            View logs
                        </a>
                    <?php endif; ?>
                </div>
                <div class="card-body row">
                    <div class="col-lg-6 col-md-12">
                        <table class="table_view_info">
                            <tr>
                                <td>
                                    <b>Handle:</b>
                                </td>
                                <td>
                                    <?= $t->ee( $rt->handle )?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>Vlan:</b>
                                </td>
                                <td>
                                    <a href="<?= route( "vlan@view" , [ "id" => $rt->vlan_id ] )?> ">
                                        <?= $t->ee( $rt->vlan->name )?>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>Protocol:</b>
                                </td>
                                <td>
                                    <?= $rt->protocol()?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>Type:</b>
                                </td>
                                <td>
                                    <?= $rt->type()?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>Name:</b>
                                </td>
                                <td>
                                    <?= $t->ee( $rt->name ) ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>ShortName:</b>
                                </td>
                                <td>
                                    <?= $t->ee( $rt->shortname ) ?>

                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>Router ID:</b>
                                </td>
                                <td>
                                    <?= $rt->router_id ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>Peering IP:</b>
                                </td>
                                <td>
                                    <?= $t->ee( $rt->peering_ip ) ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>ASN:</b>
                                </td>
                                <td>
                                    <?= $rt->asn ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>Configuration Last Updated:</b>
                                </td>
                                <td>
                                    <?= $rt->last_updated ?: '(unknown)' ?>
                                    <?php if( $rt->last_updated && $rt->lastUpdatedGreaterThanSeconds( 86400 ) ): ?>
                                        <span class="badge badge-danger">
                                            <i class="fa fa-exclamation-triangle" title="Last updated more than 1 day ago"></i>
                                        </span>
                                    <?php endif; ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>Created:</b>
                                </td>
                                <td>
                                    <?= $rt->created_at ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>Updated:</b>
                                </td>
                                <td>
                                    <?= $rt->updated_at ?>
                                </td>
                            </tr>
                        </table>
                    </div>
                    <div class="col-lg-6 col-md-12">
                        <table class="table_view_info">
                            <tr>
                                <td>
                                    <b>Software:</b>
                                </td>
                                <td>
                                    <?= $rt->software() ?> <?= $rt->software_version ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>Operating System:</b>
                                </td>
                                <td>
                                    <?= $rt->operating_system ?> <?= $rt->operating_system_version ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>MGMT Host:</b>
                                </td>
                                <td>
                                    <?= $t->ee( $rt->mgmt_host ) ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>API Type:</b>
                                </td>
                                <td>
                                    <?= $rt->resolveApiType() ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>
                                        API:
                                    </b>
                                </td>
                                <td>
                                    <a href="<?= $rt->api ?>">
                                        <?= $t->ee( $rt->api )?>
                                    </a>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>LG Access:</b>
                                </td>
                                <td>
                                    <?= $rt->lgAccess() ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>Quarantine:</b>
                                </td>
                                <td>
                                    <?= $rt->quarantine ? 'Yes' : 'No'  ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>BGP LC:</b>
                                </td>
                                <td>
                                    <?= $rt->bgp_lc ? 'Yes' : 'No' ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>RPKI:</b>
                                </td>
                                <td>
                                    <?= $rt->rpki ? 'Yes' : 'No' ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>RFC1997 Pass Through:</b>
                                </td>
                                <td>
                                    <?= $rt->rfc1997_passthru ? 'Yes' : 'No' ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>Skip MD5:</b>
                                </td>
                                <td>
                                    <?= $rt->skip_md5 ? 'Yes' : 'No' ?>
                                </td>
                            </tr>
                            <tr>
                                <td>
                                    <b>Template:</b>
                                </td>
                                <td>
                                    <code> <?= $t->ee( $rt->template )?> </code>
                                </td>
                            </tr>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
<?php $this->append() ?>