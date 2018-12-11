<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>
    <a id="d2f-list-a" href="<?= route($t->feParams->route_prefix.'@list') ?>">
        <?=  $t->feParams->pagetitle  ?>
    </a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>
        View <?=  $t->feParams->titleSingular  ?>
    </li>
<?php $this->append() ?>



<?php $this->section( 'page-header-preamble' ) ?>
    <li class="pull-right">
        <div class="btn-group btn-group-xs" role="group">
            <a type="button" class="btn btn-default" href="<?= route($t->feParams->route_prefix.'@list') ?>">
                <span class="glyphicon glyphicon-th-list"></span>
            </a>
            <?php if( !isset( $t->feParams->readonly ) || !$t->feParams->readonly ): ?>
                <a type="button" class="btn btn-default" href="<?= route($t->feParams->route_prefix.'@edit' , [ 'id' => $t->data[ 'item' ][ 'id' ] ]) ?>">
                    <span class="glyphicon glyphicon-pencil"></span>
                </a>
                <a type="button" class="btn btn-default" href="<?= route($t->feParams->route_prefix.'@add') ?>">
                    <span class="glyphicon glyphicon-plus"></span>
                </a>
            <?php endif; ?>
        </div>
    </li>

<?php $this->append() ?>

<?php $this->section('content') ?>
    <div class="row">

        <div class="col-sm-12">

            <?= $t->alerts() ?>

            <?= $t->data[ 'view' ]['viewPreamble'] ? $t->insert( $t->data[ 'view' ]['viewPreamble'] ) : '' ?>

            <div class="panel panel-default">
                <div class="panel-heading">
                    Details for <?=  $t->feParams->titleSingular  ?> <?= !isset( $t->data[ 'item' ]['id'] ) ?: '(DB ID: ' . $t->data[ 'item' ]['id'] . ')' ?>
                </div>

                <div class="panel-body">

                    <table class="table_view_info">
                        <?php if( $t->data[ 'view' ]['viewRowOverride'] ): ?>

                            <?= $t->insert( $t->data[ 'view' ]['viewRowOverride'], [ 'row' => $t->data ] ) ?>

                        <?php else: ?>

                            <?php if( isset( $t->feParams->viewColumns ) ): ?>

                                <?php foreach( $t->feParams->viewColumns as $col => $cconf ): ?>

                                    <?php if( !is_array( $cconf ) || !isset( $cconf[ 'display'] ) || $cconf[ 'display'] ): ?>

                                        <?php if( !isset( $cconf[ 'hideIfFieldTrue'] ) || !$t->data[ 'item' ][ $cconf[ 'hideIfFieldTrue'] ] ): ?>

                                            <tr>

                                                <th>
                                                    <?php if( !is_array( $cconf ) ): ?>
                                                        <?= $cconf ?>
                                                    <?php else: ?>
                                                        <?= $cconf[ 'title' ] ?>
                                                    <?php endif; ?>
                                                </th>


                                                <td>

                                                    <?php if( !is_array( $cconf ) ): ?>

                                                        <?php if( $t->data[ 'item' ][ $col ] ): ?>
                                                            <?= $t->ee( $t->data[ 'item' ][ $col ] ) ?>
                                                        <?php endif; ?>

                                                    <?php elseif( isset( $cconf[ 'type' ] ) ): ?>

                                                        <?php if( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'HAS_ONE'] ): ?>
                                                            <?php $nameIdParam = '' ; ?>
                                                            <?php if( isset( $cconf['nameIdParam'] ) ): ?>
                                                                <?php $nameIdParam = $cconf['nameIdParam'].'/'; ?>
                                                            <?php endif; ?>
                                                            <a href="<?= url( $cconf[ 'controller'] . '/' . $cconf[ 'action'] . '/'.$nameIdParam . $t->data[ 'item' ][ $cconf['idField'] ] ) ?>">
                                                                <?= $t->ee( $t->data[ 'item' ][ $col ] ) ?>
                                                            </a>

                                                        <?php elseif( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'XLATE'] ): ?>

                                                            <?php if( isset($cconf[ 'xlator'][ $t->data[ 'item' ][ $col ] ]) ): ?>
                                                                <?= $cconf[ 'xlator' ][ $t->data[ 'item' ][ $col] ] ?>
                                                            <?php else: ?>
                                                                <?= $t->ee( $t->data[ 'item' ][ $col ] ) ?>
                                                            <?php endif; ?>

                                                        <?php elseif( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'DATETIME'] ): ?>

                                                        <?php if(  $t->data[ 'item' ][ $col ] ): ?>
                                                            <?= $t->data[ 'item' ][ $col ]->format( 'Y-m-d H:i:s' )  ?>

                                                        <?php endif; ?>


                                                        <?php elseif( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'DATE'] ): ?>

                                                            <?php if(  $t->data[ 'item' ][ $col ] ): ?>
                                                                <?= $t->data[ 'item' ][ $col ]->format( 'Y-m-d' )  ?>

                                                            <?php endif; ?>


                                                        <?php elseif( $cconf[ 'type' ] ==  $t->data[ 'col_types' ][ 'TIME'] ): ?>

                                                            <?php if(  $t->data[ 'item' ][ $col ] ): ?>
                                                                <?= $t->data[ 'item' ][ $col ]->format( 'H:i:s' )  ?>

                                                            <?php endif; ?>


                                                        <?php elseif( $cconf[ 'type' ] ==  $t->data[ 'col_types' ][ 'REPLACE'] ): ?>

                                                            <?php if( $t->data[ 'item' ][ $col ] ): ?>
                                                                <?= str_replace( '%%COL%%', $t->ee( $t->data[ 'item' ][ $col ] ) , $cconf[ 'subject' ] ) ?>
                                                            <?php endif; ?>

                                                        <?php elseif( $cconf[ 'type' ] ==  $t->data[ 'col_types' ][ 'YES_NO'] ): ?>

                                                            <?= $t->data[ 'item' ][ $col ] ? 'Yes' : 'No' ?>

                                                        <?php elseif( $cconf[ 'type' ] ==  $t->data[ 'col_types' ][ 'YES_NO_NULL'] ): ?>

                                                            <?= $t->data[ 'item' ][ $col ] === null ? 'Unknown' : ( $t->data[ 'item' ][ $col ] ? 'Yes' : 'No' ) ?>

                                                        <?php elseif( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'SCRIPT'] ): ?>

                                                            <?= $t->insert( $cconf['script'], [ 'row' => $t->data['item'], 'col' => $col ] ) ?>

                                                        <?php elseif( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'PARSDOWN'] ): ?>

                                                            <?= @parsedown( $t->data[ 'item' ][ $col ] )?>

                                                        <?php elseif( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'CONST'] ): ?>

                                                            <?php if( isset( $cconf[ 'const' ][ $t->data[ 'item' ][ $col ] ] ) ): ?>

                                                                <?= $cconf[ 'const' ][ $t->data[ 'item' ][ $col ] ]  ?>

                                                            <?php endif; ?>

                                                        <?php else: ?>

                                                            Type?

                                                        <?php endif; ?>

                                                    <?php else: ?>

                                                        <?= $t->ee( $t->data[ 'item' ][ $col ] ) ?>

                                                    <?php endif; ?>
                                                </td>

                                            </tr>

                                        <?php endif; ?>

                                    <?php endif; ?>

                                <?php endforeach; ?>

                            <?php endif; ?>
                        <?php endif; ?>

                    </table>

                </div>

            </div>

            <?= $t->data[ 'view' ]['viewPostamble'] ? $t->insert( $t->data[ 'view' ]['viewPostamble'] ) : '' ?>

        </div>

    </div>


<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>

    <?= $t->data[ 'view' ][ 'viewScript' ] ? $t->insert( $t->data[ 'view' ][ 'viewScript' ] ) : '' ?>

<?php $this->append() ?>



