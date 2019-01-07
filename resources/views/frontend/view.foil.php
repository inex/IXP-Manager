<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>
    <?php if( Auth::getUser()->isSuperUser() ): ?>
        <a id="d2f-list-a" href="<?= route($t->feParams->route_prefix.'@list') ?>">
    <?php endif; ?>

    <?=  $t->feParams->pagetitle  ?>

    <?php if( Auth::getUser()->isSuperUser() ): ?>
        </a>
    <?php endif; ?>
<?php $this->append() ?>




<?php $this->section( 'page-header-postamble' ) ?>
    <?php if( Auth::getUser()->isSuperUser() ): ?>
        <li>
            View <?=  $t->feParams->titleSingular  ?>
        </li>
    <?php else:?>
        <h3 style="display:inline;color: #999999">
            View <?=  $t->feParams->titleSingular  ?>
        </h3>
    <?php endif; ?>
<?php $this->append() ?>





<?php $this->section( 'page-header-preamble' ) ?>
    <li class="pull-right" style=<?= Auth::getUser()->isSuperUser() ? "margin-top: 10px" : "" ?>>
        <div class="btn-group btn-group-xs" role="group">

            <?php if( isset( $t->feParams->documentation ) && $t->feParams->documentation ): ?>
                <a type="button" target="_blank" class="btn btn-default" href="<?= $t->feParams->documentation ?>">Documentation</a>
            <?php endif; ?>

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

                                                            <?= $t->data[ 'item' ][ $col ] ? "<label class='label label-success'>Yes</label>" : "<label class='label label-danger'>No</label>" ?>

                                                        <?php elseif( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'INVERSE_YES_NO'] ): ?>

                                                            <?= !$t->data[ 'item' ][ $col ] ? "<label class='label label-success'>Yes</label>" : "<label class='label label-danger'>No</label>" ?>

                                                        <?php elseif( $cconf[ 'type' ] ==  $t->data[ 'col_types' ][ 'YES_NO_NULL'] ): ?>

                                                            <?= $t->data[ 'item' ][ $col ] === null ? 'Unknown' : ( $t->data[ 'item' ][ $col ] ? "<label class='label label-success'>Yes</label>" : "<label class='label label-danger'>No</label>" ) ?>

                                                        <?php elseif( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'SCRIPT'] ): ?>

                                                            <?= $t->insert( $cconf['script'], [ 'row' => $t->data['item'], 'col' => $col ] ) ?>

                                                        <?php elseif( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'PARSDOWN'] ): ?>

                                                            <?= @parsedown( $t->data[ 'item' ][ $col ] )?>

                                                        <?php elseif( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'CONST'] ): ?>

                                                            <?php if( isset( $cconf[ 'const' ][ $t->data[ 'item' ][ $col ] ] ) ): ?>

                                                                <?= $cconf[ 'const' ][ $t->data[ 'item' ][ $col ] ]  ?>

                                                            <?php endif; ?>

                                                        <?php elseif( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'LABEL'] ): ?>

                                                            <?php if( isset( $t->data[ 'item' ][ $col ] ) ): ?>


                                                                <?php if( isset( $cconf[ 'explode'] ) ): ?>

                                                                    <?php if( strpos( $t->data[ 'item' ][ $col ], $cconf[ 'explode' ][ 'delimiter' ] ) !== false ): ?>

                                                                        <?php $exploded = explode( $cconf[ 'explode' ][ 'delimiter' ] , $t->data[ 'item' ][ $col ] ); ?>

                                                                        <?php foreach( $exploded as $explode ): ?>

                                                                            <span class="label label-success"><?= $t->ee( $explode ) ?> </span><?= $cconf[ 'explode' ][ 'replace' ] ?>

                                                                        <?php endforeach; ?>

                                                                    <?php else: ?>

                                                                        <span class="label label-success"><?= $t->ee( $t->data[ 'item' ][ $col ] ) ?></span>

                                                                    <?php endif;?>

                                                                <?php elseif( isset( $cconf[ 'array'] )  ): ?>

                                                                    <?php foreach( $t->data[ 'item' ][ $col ] as $item ): ?>

                                                                        <span class="label label-success"><?= $t->ee( $item ) ?> </span><?= $cconf[ 'array' ][ 'replace' ] ?>

                                                                    <?php endforeach; ?>

                                                                <?php else: ?>

                                                                    <span class="label label-success"><?= $t->ee( $t->data[ 'item' ][ $col ] ) ?></span>

                                                                <?php endif; ?>

                                                            <?php endif; ?>

                                                        <?php elseif( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'ARRAY'] ): ?>

                                                            <?= $cconf[ 'source' ][ $t->data[ 'item' ][ $col] ] ?>

                                                        <?php elseif( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'INTEGER'] ): ?>

                                                            <?=  (int)$t->data[ 'item' ][ $col] ?>

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



