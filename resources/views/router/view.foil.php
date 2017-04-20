<?php
/** @var Foil\Template\Template $t */

$this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= url( 'router/list' )?>">Router</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>Router - <?= $t->rt->getname() ?></li>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
    <div class="panel panel-default">
        <div class="panel-heading">
            Informations
        </div>
        <div class="panel-body">
            <div class="col-xs-6">
                <table class="table_view_info">
                    <tr>
                        <td>
                            <b>
                                Handle :
                            </b>
                        </td>
                        <td>
                            <?= $t->rt->getHandle() ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                Vlan :
                            </b>
                        </td>
                        <td>
                            <a href="<?= url( '/vlan/view/id/' ).'/'.$t->rt->getVlan()->getId()?> ">
                                <?= $t->rt->getVlan()->getName()?>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                Protocol :
                            </b>
                        </td>
                        <td>
                            <?= $t->rt->resolveProtocol() ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                Type :
                            </b>
                        </td>
                        <td>
                            <?= $t->rt->resolveType()?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                Name :
                            </b>
                        </td>
                        <td>
                            <?= $t->rt->getName() ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                ShortName :
                            </b>
                        </td>
                        <td>
                            <?= $t->rt->getShortName() ?>

                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                Router ID :
                            </b>
                        </td>
                        <td>
                            <?= $t->rt->getRouterId() ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                Peering IP :
                            </b>
                        </td>
                        <td>
                            <?= $t->rt->getPeeringIp() ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                ASN :
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
                                Software :
                            </b>
                        </td>
                        <td>
                            <?= $t->rt->resolveSoftware() ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                MGMT Host :
                            </b>
                        </td>
                        <td>
                            <?= $t->rt->getMgmtHost()?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                API Type :
                            </b>
                        </td>
                        <td>
                            <?= $t->rt->resolveApiType() ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                API :
                            </b>
                        </td>
                        <td>
                            <a href="<?= $t->rt->getApi()?>">
                                <?= $t->rt->getApi()?>
                            </a>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                LG Access :
                            </b>
                        </td>
                        <td>
                            <?= $t->rt->resolveLgAccess() ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                Quarantine :
                            </b>
                        </td>
                        <td>
                            <?= $t->rt->getQuarantine()  ?>
                        </td>
                    </tr>
                    <tr>
                        <td>
                            <b>
                                BGP LC :
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
                                Template :
                            </b>
                        </td>
                        <td>
                            <?= $t->rt->getTemplate() ?>
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section('scripts') ?>
    <script>
        $(document).ready(function() {


        });
    </script>
<?php $this->append() ?>