<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
<?=  $t->feParams->pagetitle  ?>
    /
    View <?=  $t->feParams->titleSingular  ?>
<?php $this->append() ?>


<?php $this->section( 'page-header-postamble' ) ?>
    <div class="btn-group btn-group-sm" role="group">
        <?php if( isset( $t->feParams->documentation ) && $t->feParams->documentation ): ?>
            <a  target="_blank" class="btn btn-white" href="<?= $t->feParams->documentation ?>">
                Documentation
            </a>
        <?php endif; ?>

        <a class="btn btn-white" href="<?= route($t->feParams->route_prefix.'@list') ?>">
            <span class="fa fa-th-list"></span>
        </a>

        <?php if( !isset( $t->feParams->readonly ) || !$t->feParams->readonly ): ?>
            <?php if( !isset( $t->feParams->disableEdit ) || !$t->feParams->disableEdit ): ?>
                <a class="btn btn-white" href="<?= route($t->feParams->route_prefix.'@edit' , [ 'id' => $t->data[ 'item' ][ 'id' ] ]) ?>">
                    <span class="fa fa-pencil"></span>
                </a>
            <?php endif; ?>
            <a class="btn btn-white" href="<?= route($t->feParams->route_prefix.'@create') ?>">
                <span class="fa fa-plus"></span>
            </a>
        <?php endif; ?>
    </div>
<?php $this->append() ?>

<?php $this->section('content') ?>
    <div class="row">
        <div class="col-lg-12">
            <?= $t->alerts() ?>

            <?= $t->data[ 'view' ]['viewPreamble'] ? $t->insert( $t->data[ 'view' ]['viewPreamble'] ) : '' ?>

            <div class="card">
                <div class="card-header">
                    Details for <?=  $t->feParams->titleSingular  ?> <?= !isset( $t->data[ 'item' ]['id'] ) ?: '(DB ID: ' . $t->data[ 'item' ]['id'] . ')' ?>
                    <?php if( !config( 'ixp_fe.frontend.disabled.logs' ) && method_exists( $t->feParams->model, 'logSubject' ) && Auth::check() && Auth::user()->isSuperUser() ): ?>
                        <a class="btn btn-white btn-sm float-right" href="<?= route( 'log@list', [ 'model' => explode( "IXP\\Models\\", $t->feParams->model )[1], 'model_id' => $t->data[ 'item' ]['id'] ] ) ?>">
                            View logs
                        </a>
                    <?php endif; ?>
                </div>
                <div class="card-body">
                    <table class="table_view_info">
                        <?php if( $t->data[ 'view' ]['viewRowOverride'] ): ?>
                            <?= $t->insert( $t->data[ 'view' ]['viewRowOverride'], [ 'row' => $t->data ] ) ?>
                        <?php else: ?>
                            <?php if( isset( $t->feParams->viewColumns ) ): ?>
                                <?php foreach( $t->feParams->viewColumns as $col => $cconf ): ?>
                                    <?php if( !is_array( $cconf ) || !isset( $cconf[ 'display'] ) || $cconf[ 'display'] ): ?>
                                        <?php if( !isset( $cconf[ 'hideIfFieldTrue'] ) || !$t->data[ 'item' ][ $cconf[ 'hideIfFieldTrue'] ] ): ?>
                                            <tr>
                                                <th style="width:20%">
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

                                                        <?php if( $cconf[ 'type'] === $t->data[ 'col_types' ][ 'HAS_ONE'] ): ?>
                                                            <?php $nameIdParam = '' ; ?>
                                                            <?php if( isset( $cconf['nameIdParam'] ) ): ?>
                                                                <?php $nameIdParam = $cconf['nameIdParam'].'/'; ?>
                                                            <?php endif; ?>
                                                            <a href="<?= url( $cconf[ 'controller'] . '/' . $cconf[ 'action'] . '/'.$nameIdParam . $t->data[ 'item' ][ $cconf['idField'] ] ) ?>">
                                                                <?= $t->ee( $t->data[ 'item' ][ $col ] ) ?>
                                                            </a>

                                                        <?php elseif( $cconf[ 'type'] === $t->data[ 'col_types' ][ 'XLATE'] ): ?>

                                                            <?php if( isset($cconf[ 'xlator'][ $t->data[ 'item' ][ $col ] ]) ): ?>
                                                                <?= $cconf[ 'xlator' ][ $t->data[ 'item' ][ $col] ] ?>
                                                            <?php else: ?>
                                                                <?= $t->ee( $t->data[ 'item' ][ $col ] ) ?>
                                                            <?php endif; ?>

                                                        <?php elseif( $cconf[ 'type'] === $t->data[ 'col_types' ][ 'DATETIME'] ): ?>

                                                            <?php if(  $t->data[ 'item' ][ $col ] ): ?>
                                                                <?= Carbon\Carbon::create( $t->data[ 'item' ][ $col ] )->format( 'Y-m-d H:i:s' ) ?>
                                                            <?php endif; ?>

                                                        <?php elseif( $cconf[ 'type'] === $t->data[ 'col_types' ][ 'DATE'] ): ?>

                                                            <?php if( $t->data[ 'item' ][ $col ] instanceof \DateTime ): ?>
                                                                <?= $t->data[ 'item' ][ $col ]->format( 'Y-m-d' )  ?>
                                                            <?php elseif( $t->data[ 'item' ][ $col ] ): ?>
                                                                <?= date('Y-m-d', strtotime( $t->data[ 'item' ][ $col ] ) ) ?>
                                                            <?php endif; ?>

                                                        <?php elseif( $cconf[ 'type' ] ===  $t->data[ 'col_types' ][ 'TIME'] ): ?>

                                                            <?php if(  $t->data[ 'item' ][ $col ] ): ?>
                                                                <?= $t->data[ 'item' ][ $col ]->format( 'H:i:s' )  ?>

                                                            <?php endif; ?>

                                                        <?php elseif( $cconf[ 'type' ] ===  $t->data[ 'col_types' ][ 'REPLACE'] ): ?>

                                                            <?php if( $t->data[ 'item' ][ $col ] ): ?>
                                                                <?= str_replace( '%%COL%%', $t->ee( $t->data[ 'item' ][ $col ] ) , $cconf[ 'subject' ] ) ?>
                                                            <?php endif; ?>

                                                        <?php elseif( $cconf[ 'type' ] ===  $t->data[ 'col_types' ][ 'YES_NO'] ): ?>

                                                            <?= $t->data[ 'item' ][ $col ] ? "<label class='label label-success'>Yes</label>" : "<label class='label label-danger'>No</label>" ?>

                                                        <?php elseif( $cconf[ 'type'] === $t->data[ 'col_types' ][ 'INVERSE_YES_NO'] ): ?>

                                                            <?= !$t->data[ 'item' ][ $col ] ? "<label class='label label-success'>Yes</label>" : "<label class='label label-danger'>No</label>" ?>

                                                        <?php elseif( $cconf[ 'type' ] ===  $t->data[ 'col_types' ][ 'YES_NO_NULL'] ): ?>

                                                            <?= $t->data[ 'item' ][ $col ] === null ? 'Unknown' : ( $t->data[ 'item' ][ $col ] ? "<label class='label label-success'>Yes</label>" : "<label class='label label-danger'>No</label>" ) ?>

                                                        <?php elseif( $cconf[ 'type'] === $t->data[ 'col_types' ][ 'SCRIPT'] ): ?>

                                                            <?= $t->insert( $cconf['script'], [ 'row' => $t->data['item'], 'col' => $col ] ) ?>

                                                        <?php elseif( $cconf[ 'type'] === $t->data[ 'col_types' ][ 'PARSDOWN'] ): ?>

                                                            <?= @parsedown( $t->data[ 'item' ][ $col ] )?>

                                                        <?php elseif( $cconf[ 'type'] === $t->data[ 'col_types' ][ 'CONST'] ): ?>

                                                            <?php if( isset( $cconf[ 'const' ][ $t->data[ 'item' ][ $col ] ] ) ): ?>

                                                                <?= $cconf[ 'const' ][ $t->data[ 'item' ][ $col ] ]  ?>

                                                            <?php endif; ?>

                                                        <?php elseif( $cconf[ 'type'] === $t->data[ 'col_types' ][ 'LABEL'] ): ?>

                                                            <?php if( isset( $t->data[ 'item' ][ $col ] ) ): ?>


                                                                <?php if( isset( $cconf[ 'explode'] ) ): ?>

                                                                    <?php if( strpos( $t->data[ 'item' ][ $col ], $cconf[ 'explode' ][ 'delimiter' ] ) !== false ): ?>

                                                                        <?php $exploded = explode( $cconf[ 'explode' ][ 'delimiter' ] , $t->data[ 'item' ][ $col ] ); ?>

                                                                        <?php foreach( $exploded as $explode ): ?>

                                                                            <span class="badge badge-success"><?= $t->ee( $explode ) ?> </span><?= $cconf[ 'explode' ][ 'replace' ] ?>

                                                                        <?php endforeach; ?>

                                                                    <?php else: ?>

                                                                        <span class="badge badge-success"><?= $t->ee( $t->data[ 'item' ][ $col ] ) ?></span>

                                                                    <?php endif;?>

                                                                <?php elseif( isset( $cconf[ 'array'] )  ): ?>

                                                                    <?php foreach( $t->data[ 'item' ][ $col ] as $item ): ?>

                                                                        <?php if( isset( $cconf[ 'array' ][ 'index' ] ) ): ?>
                                                                            <span class="badge badge-success"><?= $t->ee( $item[ $cconf[ 'array' ][ 'index' ] ] ) ?> </span><?= $cconf[ 'array' ][ 'replace' ] ?>

                                                                        <?php elseif( isset( $cconf[ 'array' ][ 'array_index' ] ) ): ?>
                                                                            <span class="badge badge-success">
                                                                                <?php foreach( $cconf[ 'array' ][ 'array_index' ] as $index ): ?>
                                                                                    <?= $t->ee( $item[ $index ] ) ?>
                                                                                <?php endforeach; ?>
                                                                            </span><?= $cconf[ 'array' ][ 'replace' ] ?>
                                                                        <?php else: ?>
                                                                            <span class="badge badge-success"><?= $t->ee( $item ) ?> </span><?= $cconf[ 'array' ][ 'replace' ] ?>
                                                                        <?php endif;?>

                                                                    <?php endforeach; ?>

                                                                <?php else: ?>

                                                                    <span class="badge badge-success"><?= $t->ee( $t->data[ 'item' ][ $col ] ) ?></span>

                                                                <?php endif; ?>

                                                            <?php endif; ?>

                                                        <?php elseif( $cconf[ 'type'] === $t->data[ 'col_types' ][ 'ARRAY'] ): ?>

                                                            <?= $cconf[ 'source' ][ $t->data[ 'item' ][ $col] ] ?? $t->data[ 'item' ][ $col] ?>

                                                        <?php elseif( $cconf[ 'type'] === $t->data[ 'col_types' ][ 'INTEGER'] ): ?>

                                                            <?=  (int)$t->data[ 'item' ][ $col] ?>

                                                        <?php elseif( $cconf[ 'type'] === $t->data[ 'col_types' ][ 'LIMIT'] ): ?>

                                                            <?= Str::limit( $t->data[ 'item' ][ $col ], $cconf[ 'limitTo'] )?>

                                                        <?php elseif( $cconf[ 'type'] === $t->data[ 'col_types' ][ 'TEXT'] ): ?>

                                                            <?= $t->ee( $t->data[ 'item' ][ $col ] ) ?>

                                                        <?php elseif( $cconf[ 'type'] === $t->data[ 'col_types' ][ 'COUNTRY'] ): ?>

                                                            <?php if( $t->ee( $t->data[ 'item' ][ $col ] ) ): ?>
                                                                <?= array_column( Countries::getList(), 'name', 'iso_3166_2' )[ $t->ee( $t->data[ 'item' ][ $col ] ) ] ?>
                                                            <?php endif; ?>

                                                        <?php elseif( $cconf[ 'type'] === $t->data[ 'col_types' ][ 'WHO_IS_PREFIX' ] ): ?>

                                                            <?= $t->whoisPrefix( $t->ee( $t->data[ 'item' ][ $col ] ), false ) ?>

                                                        <?php elseif( $cconf[ 'type'] === $t->data[ 'col_types' ][ 'JSON'] ): ?>
                                                            <?php if( $t->data[ 'item' ][ $col ] !== null ): ?>
                                                                <a class="<?= $cconf[ "displayAs" ] !== 'btn' ?: 'btn btn-white' ?> json-view" href="#" data-type="<?= $cconf[ "valueFrom"] ?>" data-value='<?= $cconf[ "valueFrom"] === 'DB' ? $t->ee( $t->data[ 'item' ][ $col ] ) : str_replace( '%%COL%%', $t->ee( $t->data[ 'item' ][ $col ]), $cconf[ 'value' ] ) ?>'>
                                                                    <?= $cconf[ "displayAs" ] === 'btn' ? 'View Json' : $t->ee( $t->data[ 'item' ][ $col ] ) ?>
                                                                </a>
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

    <?php if( isset( $t->data[ 'view' ][ 'common' ] ) ): ?>
        <?= $t->insert( $t->data[ 'view' ][ 'common' ] ); ?>
    <?php endif; ?>
<?php $this->append() ?>