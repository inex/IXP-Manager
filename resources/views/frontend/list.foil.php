<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>
    <?php if( isset( $t->feParams->pagetitlepostamble )  ): ?>
        <?php if( Route::has( $t->feParams->route_prefix_page_title . '@list' ) ): ?>
            <a id="d2f-list-a" href="<?= route($t->feParams->route_prefix_page_title . '@list') ?>">
         <?php endif; ?>
            <?=  $t->feParams->pagetitle  ?>
         <?php if( Route::has( $t->feParams->route_prefix_page_title . '@list' ) ): ?>
            </a>
        <?php endif; ?>
    <?php else: ?>
        <?= $t->feParams->pagetitle  ?>
    <?php endif; ?>
<?php $this->append() ?>


<?php if( isset( $t->feParams->pagetitlepostamble )  ): ?>
    <?php $this->section( 'page-header-postamble' ) ?>
        <li class="active"> <?= $t->feParams->pagetitlepostamble ?> </li>
    <?php $this->append() ?>
<?php endif; ?>

<?php $this->section( 'page-header-preamble' ) ?>
    <?php if( $t->data[ 'view' ]['pageHeaderPreamble'] ): ?>

        <?= $t->insert( $t->data[ 'view' ]['pageHeaderPreamble'] ) ?>

    <?php else: ?>

        <?php if( !isset( $t->feParams->readonly ) || !$t->feParams->readonly ): ?>
            <li class="pull-right">
                <div class="btn-group btn-group-xs" role="group">
                    <a type="button" class="btn btn-default" href="<?= route($t->feParams->route_prefix.'@add') ?>">
                        <span class="glyphicon glyphicon-plus"></span>
                    </a>
                </div>
            </li>
        <?php endif;?>

    <?php endif;?>
<?php $this->append() ?>

<?php $this->section('content') ?>
    <div class="row">

        <div class="col-sm-12">

            <?= $t->alerts() ?>

            <?= $t->data[ 'view' ]['listPreamble'] ? $t->insert( $t->data[ 'view' ]['listPreamble'] ) : '' ?>

            <?php if( !count( $t->data[ 'rows' ] ) ): ?>

                <?php if( $t->data[ 'view' ]['listEmptyMessage'] ): ?>

                    <?= $t->insert( $t->data[ 'view' ]['listEmptyMessage'] ) ?>

                <?php else: ?>

                    <div class="alert alert-info" role="alert">
                        <b>No <?= ucfirst( $t->feParams->pagetitle ) ?> exist.</b> <a href="<?= route($t->feParams->route_prefix.'@add') ?>">Add one...</a>
                    </div>

                <?php endif; /* listEmptyMessage */ ?>

            <?php else:  /* !count( $t->data[ 'rows' ] ) */ ?>

                <table id="table-list" class="table collapse">

                    <?php if( $t->data[ 'view' ]['listHeadOverride'] ): ?>

                        <?= $t->insert( $t->data[ 'view' ]['listHeadOverride'] ) ?>

                    <?php else: ?>

                        <thead>

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

                            <th></th> <!-- actions column -->

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

                                        <td>

                                            <?php if(isset( $cconf[ 'type'] ) ): ?>

                                                <?php if( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'HAS_ONE'] ): ?>
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

                                                <?php elseif( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'XLATE'] ): ?>

                                                    <?php if( isset($cconf[ 'xlator'][ $row[ $col ] ] ) ): ?>
                                                        <?= $cconf[ 'xlator' ][ $row[ $col ] ] ?>
                                                    <?php else: ?>
                                                        <?= $t->ee( $row[ $col ] ) ?>
                                                    <?php endif; ?>

                                                <?php elseif( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'YES_NO'] ): ?>

                                                    <?= $row[ $col ] ? "<label class='label label-success'>Yes</label>" : "<label class='label label-danger'>No</label>" ?>

                                                <?php elseif( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'REPLACE'] ): ?>

                                                    <?= str_replace( '%%COL%%', $t->ee( $row[ $col ] ), $cconf[ 'subject' ] ) ?>

                                                <?php elseif( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'SPRINTF'] ): ?>

                                                    <?= sprintf( $cconf[ 'sprintf' ], $t->ee( $row[ $col ] ) ) ?>

                                                <?php elseif( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'DATETIME'] ): ?>

                                                    <?php if( $row[ $col ] != null): ?>
                                                        <?= $row[ $col ]->format( 'Y-m-d H:i:s' )  ?>
                                                    <?php endif; ?>

                                                <?php elseif( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'UNIX_TIMESTAMP'] ): ?>

                                                    <?php if( $row[ $col ] ): ?>
                                                        <?= Carbon\Carbon::createFromTimestamp( $row[ $col ] )->format( 'Y-m-d H:i:s' )  ?>
                                                    <?php endif; ?>

                                                <?php elseif( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'DATE'] ): ?>

                                                    <?= date('Y-m-d', strtotime( $row[ $col ] ) ) ?>

                                                <?php elseif( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'TIME'] ): ?>

                                                    <?= date('H:M:S', strtotime($row[ $col ] ) ) ?>

                                                <?php elseif( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'RESOLVE_CONST'] ): ?>

                                                    <?= $cconf[ 'const' ][ $row[ $col ] ] ?>

                                                <?php elseif( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'SCRIPT'] ): ?>

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

                                            <div class="btn-group">

                                                <a id="d2f-list-view-<?= $row[ 'id' ] ?>" class="btn btn-sm btn-default" href="<?= route($t->feParams->route_prefix.'@view' , [ 'id' => $row[ 'id' ] ] ) ?>" title="Preview"><i class="glyphicon glyphicon-eye-open"></i></a>

                                                <?php if( !isset( $t->feParams->readonly ) || !$t->feParams->readonly ): ?>
                                                    <a class="btn btn-sm btn-default" id="d2f-list-edit-<?= $row[ 'id' ] ?>" href="<?= route($t->feParams->route_prefix.'@edit' , [ 'id' => $row[ 'id' ] ] ) ?> " title="Edit"><i class="glyphicon glyphicon-pencil"></i></a>
                                                    <a class="btn btn-sm btn-default" id='d2f-list-delete-<?= $row[ 'id' ] ?>' href="#" data-object-id="<?= $row[ 'id' ] ?>" title="Delete"><i class="glyphicon glyphicon-trash"></i></a>
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

<?php $this->append() ?>
