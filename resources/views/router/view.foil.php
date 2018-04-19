<?php
/** @var Foil\Template\Template $t */

$this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= action( 'RouterController@list' )?>">Routers</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li><?= $t->ee( $t->rt->getName() ) ?></li>
<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>
    <li class="pull-right">
        <div class="btn-group btn-group-xs" role="group">
            <a type="button" class="btn btn-default" href="<?= route('router@list' ) ?>" title="list">
                <span class="glyphicon glyphicon-th-list"></span>
            </a>
            <a type="button" class="btn btn-default" href="<?= route ('router@add' ) ?>" title="add">
                <span class="glyphicon glyphicon-plus"></span>
            </a>
            <a type="button" class="btn btn-default" href="<?= route ('router@edit' , [ 'id' => $t->rt->getId() ] ) ?>" title="edit">
                <span class="glyphicon glyphicon-pencil"></span>
            </a>

        </div>
    </li>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            Router Details
        </div>
        <div class="panel-body">
            <div class="col-xs-6">
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

            <div class="col-xs-6">
                <table class="table_view_info">
                    <tr>
                        <td>
                            <b>
                                Software:
                            </b>
                        </td>
                        <td>
                            <?= $t->rt->resolveSoftware() ?>
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
                                <span class="label label-danger"><i class="glyphicon glyphicon-exclamation-sign" title="Last updated more than 1 day ago"></i></span>
                            <?php endif; ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
<?php $this->append() ?>