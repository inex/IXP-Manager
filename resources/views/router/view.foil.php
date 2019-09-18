<?php
/** @var Foil\Template\Template $t */

$this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Routers / <?= $t->ee( $t->rt->getName() ) ?>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm" role="group">
        <a target="_blank" class="btn btn-white" href="https://docs.ixpmanager.org/features/routers/">
            Documentation
        </a>
        <a class="btn btn-white" href="<?= route('router@list' ) ?>" title="list">
            <span class="fa fa-th-list"></span>
        </a>
        <a class="btn btn-white" href="<?= route ('router@add' ) ?>" title="add">
            <span class="fa fa-plus"></span>
        </a>
        <a class="btn btn-white" href="<?= route ('router@edit' , [ 'id' => $t->rt->getId() ] ) ?>" title="edit">
            <span class="fa fa-pencil"></span>
        </a>

    </div>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>

<div class="row">

    <div class="col-sm-12">

        <div class="card">
            <div class="card-header">
                Details for Router
            </div>
            <div class="card-body row">
                <div class="col-lg-6 col-md-12">
                    <table class="table_view_info">
                        <tr>
                            <td>
                                <b>
                                    Handle:
                                </b>
                            </td>
                            <td>
                                <?= $t->ee( $t->rt->getHandle() )?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>
                                    Vlan:
                                </b>
                            </td>
                            <td>
                                <a href="<?= route( "vlan@view" , [ "id" => $t->rt->getVlan()->getId() ] )?> ">
                                    <?= $t->ee( $t->rt->getVlan()->getName() )?>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>
                                    Protocol:
                                </b>
                            </td>
                            <td>
                                <?= $t->rt->resolveProtocol()?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>
                                    Type:
                                </b>
                            </td>
                            <td>
                                <?= $t->rt->resolveType()?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>
                                    Name:
                                </b>
                            </td>
                            <td>
                                <?= $t->ee( $t->rt->getName() ) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>
                                    ShortName:
                                </b>
                            </td>
                            <td>
                                <?= $t->ee( $t->rt->getShortName() ) ?>

                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>
                                    Router ID:
                                </b>
                            </td>
                            <td>
                                <?= $t->rt->getRouterId() ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>
                                    Peering IP:
                                </b>
                            </td>
                            <td>
                                <?= $t->ee( $t->rt->getPeeringIp() ) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>
                                    ASN:
                                </b>
                            </td>
                            <td>
                                <?= $t->rt->getAsn() ?>
                            </td>
                        </tr>
                    </table>
                </div>

                <div class="col-lg-6 col-md-12">
                    <table class="table_view_info">
                        <tr>
                            <td>
                                <b>
                                    Software:
                                </b>
                            </td>
                            <td>
                                <?= $t->rt->resolveSoftware() ?> <?= $t->rt->getSoftwareVersion() ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>
                                    Operating System:
                                </b>
                            </td>
                            <td>
                                <?= $t->rt->getOperatingSystem() ?> <?= $t->rt->getOperatingSystemVersion() ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>
                                    MGMT Host:
                                </b>
                            </td>
                            <td>
                                <?= $t->ee( $t->rt->getMgmtHost() ) ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>
                                    API Type:
                                </b>
                            </td>
                            <td>
                                <?= $t->rt->resolveApiType() ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>
                                    API:
                                </b>
                            </td>
                            <td>
                                <a href="<?= $t->rt->getApi()?>">
                                    <?= $t->ee( $t->rt->getApi() )?>
                                </a>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>
                                    LG Access:
                                </b>
                            </td>
                            <td>
                                <?= $t->rt->resolveLgAccess() ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>
                                    Quarantine:
                                </b>
                            </td>
                            <td>
                                <?= $t->rt->getQuarantine() ? 'Yes' : 'No'  ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>
                                    BGP LC:
                                </b>
                            </td>
                            <td>
                                <?= $t->rt->getBgpLc() ? 'Yes' : 'No' ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>
                                    RPKI:
                                </b>
                            </td>
                            <td>
                                <?= $t->rt->getRPKI() ? 'Yes' : 'No' ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>
                                    RFC1997 Pass Through:
                                </b>
                            </td>
                            <td>
                                <?= $t->rt->getRFC1997Passthru() ? 'Yes' : 'No' ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>
                                    Skip MD5:
                                </b>
                            </td>
                            <td>
                                <?= $t->rt->getSkipMd5() ? 'Yes' : 'No' ?>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>
                                    Template:
                                </b>
                            </td>
                            <td>
                                <code> <?= $t->ee( $t->rt->getTemplate() )?> </code>
                            </td>
                        </tr>
                        <tr>
                            <td>
                                <b>
                                    Last Update:
                                </b>
                            </td>
                            <td>
                                <?= $t->rt->getLastUpdated() ? $t->rt->getLastUpdated()->format('Y-m-d H:i:s') : '(unknown)' ?>
                                <?php if( $t->rt->getLastUpdated() && $t->rt->lastUpdatedGreaterThanSeconds( 86400 ) ): ?>
                                    <span class="badge badge-danger">
                                        <i class="fa fa-exclamation-triangle" title="Last updated more than 1 day ago"></i>
                                    </span>
                                <?php endif; ?>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>
        </div>

    </div>

</div>

<?php $this->append() ?>