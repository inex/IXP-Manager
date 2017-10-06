
<?php
/** @var Foil\Template\Template $t */

$this->layout( 'layouts/ixpv4' );
?>

    <?php $this->section( 'title' ) ?>
        <a href="<?= action($t->controller.'@list') ?>">
            <?=  $t->data[ 'feParams' ]->pagetitle  ?>
        </a>
    <?php $this->append() ?>

    <?php $this->section( 'page-header-postamble' ) ?>
        <li>
            View <?=  $t->data[ 'feParams' ]->pagetitle  ?>
        </li>
    <?php $this->append() ?>



    <?php $this->section( 'page-header-preamble' ) ?>
    <li class="pull-right">
        <div class="btn-group btn-group-xs" role="group">
            <a type="button" class="btn btn-default" href="<?= action($t->controller.'@list') ?>">
                <span class="glyphicon glyphicon-th-list"></span>
            </a>
            <a type="button" class="btn btn-default" href="<?= action($t->controller.'@edit' , [ 'id' => $t->data[ 'data' ][ 'id' ] ]) ?>">
                <span class="glyphicon glyphicon-pencil"></span>
            </a>
            <a type="button" class="btn btn-default" href="<?= action($t->controller.'@add') ?>">
                <span class="glyphicon glyphicon-plus"></span>
            </a>
        </div>
    </li>
    <?php $this->append() ?>

<?php $this->section('content') ?>
    <?= $t->alerts() ?>

<dl>
    <div class="panel panel-default">
        <div class="panel-heading">
            Informations
        </div>
        <div class="panel-body">
            <div class="col-xs-6">
                <table class="table_view_info">
                    <?php if( isset( $t->data[ 'feParams' ]->viewColumns ) ): ?>
                        <?php foreach( $t->data[ 'feParams' ]->viewColumns as $col => $cconf ): ?>
                            <tr>
                            <?php if( !is_array( $cconf ) || !isset( $cconf[ 'display'] ) || $cconf[ 'display'] ): ?>

                                <td>
                                    <?php if( !is_array( $cconf ) ): ?>
                                        <?= $cconf ?>
                                    <?php else: ?>
                                        <?= $cconf[ 'title' ] ?>
                                    <?php endif; ?>
                                </td>



                                <td>

                                    <?php if( !is_array( $cconf ) ): ?>
                                        <?php if( $t->data[ 'data' ][ $col ] == false ): ?>
                                            0
                                        <?php else: ?>
                                            <?= $t->data[ 'data' ][ $col ] ?>
                                        <?php endif; ?>
                                    <?php elseif( isset( $cconf[ 'type' ] ) ): ?>
                                        <?php if( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'HAS_ONE'] ): ?>
                                            <?php  $hasOneId = $cconf['idField'] ?>
                                            <a href="<?= url( $cconf[ 'controller'].'/'.$cconf[ 'action'].'/id/'.$t->data[ 'data' ][ $hasOneId ] ) ?>"> <?= $t->data[ 'data' ][ $col ] ?> </a>
                                        <?php elseif( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'XLATE'] ): ?>
                                            <?php if( isset($cconf[ 'xlator'][ $t->data[ 'data' ][ $col ] ]) ): ?>
                                                <?= $cconf[ 'xlator' ][ $t->data[ 'data' ][ $col] ] ?>
                                            <?php else: ?>
                                                <?= $t->data[ 'data' ][ $col ] ?>
                                            <?php endif; ?>
                                        <?php elseif( $cconf[ 'type'] ==  $t->data[ 'col_types' ][ 'DATETIME'] ): ?>
                                            <?php if( $t->data[ 'data' ][ $col ] ) ?>
                                            <?= date('Y-m-d H:M:S', strtotime($t->data[ $col ] ) ) ?>
                                        <?php elseif( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'DATE'] ): ?>
                                            <?php if ( $t->data[ 'data' ][ $col ] ): ?>
                                                <?= date('Y-m-d', strtotime( $t->data[ $col ] ) ) ?>
                                            <?php endif; ?>
                                        <?php elseif( $cconf[ 'type' ] ==  $t->data[ 'col_types' ][ 'TIME'] ): ?>
                                            <?php if( $t->data[ 'data' ][ $col ] ): ?>
                                                <?= date('H:M:S', strtotime($t->data[ $col ] ) ) ?>
                                            <?php endif; ?>
                                        <?php elseif( $cconf[ 'type' ] ==  $t->data[ 'col_types' ][ 'REPLACE'] ): ?>
                                            <?php if( $t->data[ 'data' ][ $col ] ): ?>
                                                <?= str_replace( '%%COL%%', $t->data[ 'data' ][ $col ] , $cconf[ 'subject' ] ) ?>
                                            <?php endif; ?>
                                        <?php elseif( $cconf[ 'type' ] ==  $t->data[ 'col_types' ][ 'YES_NO'] ): ?>
                                            <?php if( $t->data[ 'data' ][ $col ] ): ?>
                                                YES
                                            <?php else: ?>
                                                NO
                                            <?php endif; ?>
                                        <?php elseif( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'SCRIPT'] ): ?>

                                        <?php endif; ?>
                                    <?php else: ?>
                                            Type?
                                    <?php endif; ?>
                                </td>

                            <?php endif; ?>

                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </table>
            </div>
        </div>
    </div>
</dl>
<?php $this->append() ?>


