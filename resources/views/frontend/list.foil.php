<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
        <?php if( isset( $t->feParams->pagetitle )  ): ?>
            <?=  $t->feParams->pagetitle  ?>
        <?php endif; ?>

        <?php if( isset( $t->feParams->pagetitlepostamble )  ): ?>
            <?= $t->feParams->pagetitlepostamble ?>
        <?php endif; ?>
<?php $this->append() ?>


<?php $this->section( 'page-header-postamble' ) ?>
    <?php if( $t->data[ 'view' ]['pageHeaderPreamble'] ): ?>
        <?= $t->insert( $t->data[ 'view' ]['pageHeaderPreamble'] ) ?>
    <?php else: ?>
        <div class="btn-group btn-group-sm ml-auto" role="group">
            <?php if( isset( $t->feParams->documentation ) && $t->feParams->documentation ): ?>
                <a target="_blank" class="btn btn-white" href="<?= $t->feParams->documentation ?>">
                    Documentation
                </a>
            <?php endif; ?>

            <?php if( !isset( $t->feParams->readonly ) || !$t->feParams->readonly ): ?>
                <a class="btn btn-white" href="<?= route($t->feParams->route_prefix.'@create') ?>">
                    <i class="fa fa-plus"></i>
                </a>
            <?php endif;?>
        </div>
    <?php endif;?>
<?php $this->append() ?>

<?php $this->section('content') ?>
    <div class="row">
        <div class="col-lg-12">
            <?= $t->alerts() ?>

            <?= $t->data[ 'view' ]['listPreamble'] ? $t->insert( $t->data[ 'view' ]['listPreamble'] ) : '' ?>

            <?php if( !count( $t->data[ 'rows' ] ) ): ?>
                <?php if( $t->data[ 'view' ]['listEmptyMessage'] ): ?>
                    <?= $t->insert( $t->data[ 'view' ]['listEmptyMessage'] ) ?>
                <?php else: ?>
                    <div class="alert alert-info" role="alert">
                        <div class="d-flex align-items-center">
                            <div class="text-center">
                                <i class="fa fa-info-circle fa-2x"></i>
                            </div>
                            <div class="col-sm-12">
                                <b>No <?= $t->feParams->nameSingular ?> exists.</b>
                                <?php if( !isset( $t->feParams->readonly ) || !$t->feParams->readonly ): ?>
                                    <a class="btn btn-white ml-2" href="<?= $t->feParams->addRoute ?? route($t->feParams->route_prefix.'@create') ?>">Create one...</a>
                                <?php endif;?>
                            </div>
                        </div>
                    </div>
                <?php endif; /* listEmptyMessage */ ?>
            <?php else:  /* !count( $t->data[ 'rows' ] ) */ ?>

                <table id="table-list" class="table collapse table-striped" width="100%">
                    <?php if( $t->data[ 'view' ]['listHeadOverride'] ): ?>
                        <?= $t->insert( $t->data[ 'view' ]['listHeadOverride'] ) ?>
                    <?php else: ?>
                        <thead class="thead-dark">
                            <tr>
                                <?php foreach( $t->feParams->listColumns as $col => $cconf ):?>

                                    <?php if( !is_array( $cconf ) || !isset( $cconf[ 'display'] ) ||  $cconf[ 'display']   ):?>
                                        <th>
                                            <?php if( is_array( $cconf ) ) :?>
                                                <?= $cconf[ 'title' ] ?>
                                            <?php else: ?>
                                                <?= $cconf ?>
                                            <?php endif;?>
                                        </th>
                                    <?php endif;?>

                                <?php endforeach;?>
                                <?php if( !isset( $t->feParams->hideactioncolumn ) || !$t->feParams->hideactioncolumn ): ?>
                                    <th>
                                        Actions
                                    </th> <!-- actions column -->
                                <?php endif; ?>
                            </tr>
                        </thead>
                    <?php endif; ?>
                    <tbody>
                        <?php foreach( $t->data[ 'rows' ] as $idx => $row ): ?>
                            <?php if( $t->data[ 'view' ]['listRowOverride'] ): ?>
                                <?= $t->insert( $t->data[ 'view' ]['listRowOverride'], [ 'row' => $row ] ) ?>
                            <?php else: ?>
                                <tr>
                                    <?php foreach( $t->feParams->listColumns as $col => $cconf ): ?>
                                        <?php if( !is_array( $cconf ) ): ?>
                                            <td>
                                                <?= $t->ee( $row[ $col ] ) ?>
                                            </td>
                                        <?php elseif( !isset( $cconf[ 'display'] ) || $cconf[ 'display']  ): ?>
                                            <td
                                                <?php if( isset( $cconf[ 'data-sort'] ) ): ?>
                                                     data-order="<?= $row[ $cconf[ 'data-sort'] ] ?>"
                                                <?php endif; ?>
                                            >
                                                <?php if(isset( $cconf[ 'type'] ) ): ?>
                                                    <?php if( $cconf[ 'type'] === $t->data[ 'col_types' ][ 'HAS_ONE'] ): ?>
                                                        <?php $nameIdParam = '' ; ?>

                                                        <?php if( isset( $cconf['nameIdParam'] ) ): ?>
                                                            <?php $nameIdParam = $cconf['nameIdParam'].'/'; ?>
                                                        <?php endif; ?>

                                                        <?php $params = '/' . $nameIdParam . $row[ $cconf['idField'] ] ; ?>

                                                        <?php if( isset( $cconf['nameIdOptionalParam'] ) ): ?>
                                                            <?php $params = '?' . $cconf['nameIdOptionalParam'] . '=' . $row[ $cconf['idField'] ] ; ?>
                                                        <?php endif; ?>

                                                        <a href="<?= url( $cconf[ 'controller'] . '/' . $cconf[ 'action'] . $params ) ?>">
                                                            <?= $t->ee( $row[$col] ) ?>
                                                        </a>

                                                    <?php elseif( $cconf[ 'type'] === $t->data[ 'col_types' ][ 'XLATE'] ): ?>

                                                        <?php if( isset($cconf[ 'xlator'][ $row[ $col ] ] ) ): ?>
                                                            <?= $cconf[ 'xlator' ][ $row[ $col ] ] ?>
                                                        <?php else: ?>
                                                            <?= $t->ee( $row[ $col ] ) ?>
                                                        <?php endif; ?>

                                                    <?php elseif( $cconf[ 'type'] === $t->data[ 'col_types' ][ 'YES_NO'] ): ?>

                                                        <?= $row[ $col ] ? "<span class='badge badge-success'>Yes</span>" : "<span class='badge badge-danger'>No</span>" ?>

                                                    <?php elseif( $cconf[ 'type'] === $t->data[ 'col_types' ][ 'INVERSE_YES_NO'] ): ?>

                                                        <?= !$row[ $col ] ? "<span class='badge badge-success'>Yes</span>" : "<span class='badge badge-danger'>No</span>" ?>

                                                    <?php elseif( $cconf[ 'type'] === $t->data[ 'col_types' ][ 'REPLACE'] ): ?>

                                                        <?= str_replace( '%%COL%%', $t->ee( $row[ $col ] ), $cconf[ 'subject' ] ) ?>

                                                    <?php elseif( $cconf[ 'type'] === $t->data[ 'col_types' ][ 'SPRINTF'] ): ?>

                                                        <?= sprintf( $cconf[ 'sprintf' ], $t->ee( $row[ $col ] ) ) ?>

                                                    <?php elseif( $cconf[ 'type'] === $t->data[ 'col_types' ][ 'DATETIME'] ): ?>

                                                        <?php if( $row[ $col ] !== null): ?>
                                                            <?= Carbon\Carbon::create( $row[ $col ] )->format( 'Y-m-d H:i:s' ) ?>
                                                        <?php endif; ?>

                                                    <?php elseif( $cconf[ 'type'] === $t->data[ 'col_types' ][ 'UNIX_TIMESTAMP'] ): ?>

                                                        <?php if( $row[ $col ] ): ?>
                                                            <?= Carbon\Carbon::createFromTimestamp( $row[ $col ] )->format( 'Y-m-d H:i:s' )  ?>
                                                        <?php endif; ?>

                                                    <?php elseif( $cconf[ 'type'] === $t->data[ 'col_types' ][ 'DATE'] ): ?>

                                                        <?php if( $row[ $col ] instanceof \DateTime ): ?>
                                                            <?= $row[ $col ]->format( 'Y-m-d' )  ?>
                                                        <?php elseif( $row[ $col ] ): ?>
                                                            <?= date('Y-m-d', strtotime( $row[ $col ] ) ) ?>
                                                        <?php endif; ?>

                                                    <?php elseif( $cconf[ 'type'] === $t->data[ 'col_types' ][ 'TIME'] ): ?>

                                                        <?= date('H:M:S', strtotime($row[ $col ] ) ) ?>

                                                    <?php elseif( $cconf[ 'type'] === $t->data[ 'col_types' ][ 'CONST'] ): ?>

                                                        <?= $cconf[ 'const' ][ $row[ $col ] ] ?>

                                                    <?php elseif( $cconf[ 'type'] === $t->data[ 'col_types' ][ 'SCRIPT'] ): ?>

                                                        <?php $params = [] ?>
                                                        <?php $error = false ?>

                                                        <?php foreach( $cconf[ 'params' ] as $key => $val ): ?>
                                                            <?php if( isset( $row[ $val ] ) ): ?>
                                                                <?php $params[ $key ] = $row[ $val ] ?>
                                                            <?php else: ?>
                                                                <?php $error = true ?>
                                                                <?php break; ?>
                                                            <?php endif; ?>
                                                        <?php endforeach; ?>

                                                        <?php if( !$error ): ?>
                                                            <?= $t->insert( $cconf['script'], $params ) ?>
                                                        <?php endif; ?>

                                                    <?php elseif( $cconf[ 'type'] === $t->data[ 'col_types' ][ 'LABEL'] ): ?>

                                                        <?php if( isset( $cconf[ 'explode'] ) ): ?>

                                                            <?php if( strpos( $row[ $col ], $cconf[ 'explode' ][ 'delimiter' ] ) !== false ): ?>

                                                                <?php $exploded = explode( $cconf[ 'explode' ][ 'delimiter' ] , $row[ $col ] ); ?>

                                                                <?php foreach( $exploded as $explode ): ?>

                                                                    <span class="badge badge-success"><?= $t->ee( $explode ) ?> </span><?= $cconf[ 'explode' ][ 'replace' ] ?>

                                                                <?php endforeach; ?>

                                                            <?php else: ?>
                                                                <span class="badge badge-success"><?= $t->ee( $row[ $col ] ) ?></span>

                                                            <?php endif;?>

                                                        <?php elseif( isset( $cconf[ 'array'] )  ): ?>

                                                            <?php foreach( $row[ $col ] as $item ): ?>
                                                                <?php if( isset( $cconf[ 'array' ][ 'index' ] ) ): ?>
                                                                    <span class="badge badge-success"><?= $t->ee( $item[ $cconf[ 'array' ][ 'index' ] ] ) ?> </span><?= $cconf[ 'array' ][ 'replace' ] ?>
                                                                <?php else: ?>
                                                                    <span class="badge badge-success"><?= $t->ee( $item ) ?> </span><?= $cconf[ 'array' ][ 'replace' ] ?>
                                                                <?php endif; ?>

                                                            <?php endforeach; ?>
                                                        <?php else: ?>
                                                            <span class="badge badge-success"><?= $t->ee( $row[ $col ] ) ?></span>
                                                        <?php endif; ?>
                                                    <?php elseif( $cconf[ 'type'] === $t->data[ 'col_types' ][ 'ARRAY'] ): ?>

                                                        <?= $cconf[ 'source' ][ $row[ $col ] ] ?? $row[ $col ] ?>

                                                    <?php elseif( $cconf[ 'type'] === $t->data[ 'col_types' ][ 'INTEGER'] ): ?>

                                                        <?= (int)$row[ $col ] ?>

                                                    <?php elseif( $cconf[ 'type'] === $t->data[ 'col_types' ][ 'LIMIT'] ): ?>

                                                        <?= Str::limit( $row[ $col ], $cconf[ 'limitTo'] )?>

                                                    <?php elseif( $cconf[ 'type'] === $t->data[ 'col_types' ][ 'TEXT'] ): ?>

                                                        <?= $t->ee( $row[ $col ] )?>

                                                    <?php elseif( $cconf[ 'type'] === $t->data[ 'col_types' ][ 'WHO_IS_PREFIX'] ): ?>
                                                        <?php if( $row[ $col ] !== null ): ?>
                                                            <?= $t->whoisPrefix( $row[ $col ], false )?>
                                                        <?php endif; ?>

                                                    <?php elseif( $cconf[ 'type'] === $t->data[ 'col_types' ][ 'JSON'] ): ?>

                                                        <?php if( $row[ $col ] !== null ): ?>
                                                            <a class="<?= $cconf[ "displayAs"] !== 'btn' ?: 'btn btn-white' ?> json-view" href="#" data-type="<?= $cconf[ "valueFrom"] ?>" data-value='<?= $cconf[ "valueFrom"] === 'DB' ? $row[ $col ] : str_replace( '%%COL%%', $t->ee( $row[ $col ] ), $cconf[ 'value' ] ) ?>'>
                                                                <?= $cconf[ "displayAs"] === 'btn' ? 'View Json' : $t->ee( $row[ $col ] ) ?>
                                                            </a>
                                                        <?php endif; ?>

                                                    <?php else: ?>

                                                        Type?

                                                    <?php endif; /* if( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'HAS_ONE'] ) */ ?>
                                                <?php else: ?>

                                                    <?= $t->ee( $row[ $col ] ) ?>

                                                <?php endif;  /*  if(isset( $cconf[ 'type'] ) ) */ ?>
                                            </td>
                                        <?php endif; /* if( !is_array( $cconf ) ) */ ?>
                                    <?php endforeach; ?>

                                    <?php if( !isset( $t->feParams->hideactioncolumn ) || !$t->feParams->hideactioncolumn ): ?>
                                        <td>
                                            <?php if( $t->data[ 'view' ]['listRowMenu'] ): ?>

                                                <?= $t->insert( $t->data[ 'view' ]['listRowMenu'], [ 'row' => $row ] ) ?>

                                            <?php else: ?>

                                                <div class="btn-group btn-group-sm">

                                                    <a id="e2f-list-view-<?= $row[ 'id' ] ?>" class="btn btn-white" href="<?= route($t->feParams->route_prefix.'@view' , [ 'id' => $row[ 'id' ] ] ) ?>" title="Preview">
                                                        <i class="fa fa-eye"></i>
                                                    </a>

                                                    <?php if( !isset( $t->feParams->readonly ) || !$t->feParams->readonly ): ?>
                                                        <a class="btn btn-white" id="e2f-list-edit-<?= $row[ 'id' ] ?>" href="<?= route($t->feParams->route_prefix.'@edit' , [ 'id' => $row[ 'id' ] ] ) ?> " title="Edit">
                                                            <i class="fa fa-pencil"></i>
                                                        </a>
                                                        <a class="btn btn-white btn-2f-list-delete" id='e2f-list-delete-<?= $row[ 'id' ] ?>' data-object-id="<?= $row[ 'id' ] ?>" href="<?= route( $t->feParams->route_prefix.'@delete' , [ 'id' => $row[ 'id' ] ]  )  ?>"  title="Delete">
                                                            <i class="fa fa-trash"></i>
                                                        </a>
                                                    <?php endif;?>
                                                </div>
                                            <?php endif; ?>
                                        </td>
                                    <?php endif; ?>
                                </tr>
                            <?php endif; /* if( $t->data[ 'view' ]['listRowOverride'] ): */ ?>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php endif;  /* /* !count( $t->data[ 'rows' ] ) */ ?>

            <?= $t->data[ 'view' ]['listPostamble'] ? $t->insert( $t->data[ 'view' ]['listPostamble'] ) : '' ?>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?php if( isset( $t->data[ 'view' ][ 'listScript' ] ) ): ?>
        <?= $t->insert( $t->data[ 'view' ][ 'listScript' ] ); ?>
    <?php endif; ?>

    <?php if( isset( $t->data[ 'view' ][ 'common' ] ) ): ?>
        <?= $t->insert( $t->data[ 'view' ][ 'common' ] ); ?>
    <?php endif; ?>

    <?php if( $t->data[ 'view' ][ 'listScriptExtra' ] ): ?>
        <?= $t->insert( $t->data[ 'view' ][ 'listScriptExtra' ] ); ?>
    <?php endif; ?>
<?php $this->append() ?>