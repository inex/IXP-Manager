<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>
    <?= $t->feParams->pagetitle  ?>
<?php $this->append() ?>

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
                                                    <a href="<?= url( $cconf[ 'controller'] . '/' . $cconf[ 'action'] . '/' . $nameIdParam . $row[ $cconf['idField'] ] ) ?>">
                                                        <?= $t->ee( $row[$col] ) ?>
                                                    </a>

                                                <?php elseif( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'XLATE'] ): ?>

                                                    <?php if( isset($cconf[ 'xlator'][ $row[ $col ] ] ) ): ?>
                                                        <?= $cconf[ 'xlator' ][ $row[ $col ] ] ?>
                                                    <?php else: ?>
                                                        <?= $t->ee( $row[ $col ] ) ?>
                                                    <?php endif; ?>

                                                <?php elseif( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'YES_NO'] ): ?>

                                                    <?= $row[ $col ] ? 'Yes' : 'No' ?>

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

                                                <?php elseif( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'SCRIPT'] ): ?>

                                                    <?= $t->insert( $cconf['script'], [ 'row' => $row, 'col' => $col ] ) ?>

                                                <?php elseif( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'LABEL'] ): ?>

                                                    <?php if( isset( $cconf[ 'explode'] ) ): ?>

                                                        <?php if( strpos( $row[ $col ], $cconf[ 'explode' ][ 'delimiter' ] ) !== false ): ?>

                                                            <?php $exploded = explode( $cconf[ 'explode' ][ 'delimiter' ] , $row[ $col ] ); ?>

                                                            <?php foreach( $exploded as $explode ): ?>

                                                                <span class="label label-success"><?= $t->ee( $explode ) ?> </span><?= $cconf[ 'explode' ][ 'replace' ] ?>

                                                            <?php endforeach; ?>

                                                        <?php else: ?>
                                                            <span class="label label-success"><?= $t->ee( $row[ $col ] ) ?></span>

                                                        <?php endif;?>

                                                    <?php elseif( isset( $cconf[ 'array'] )  ): ?>

                                                        <?php foreach( $row[ $col ] as $item ): ?>

                                                            <span class="label label-success"><?= $t->ee( $item ) ?> </span><?= $cconf[ 'array' ][ 'replace' ] ?>

                                                        <?php endforeach; ?>

                                                    <?php else: ?>

                                                        <span class="label label-success"><?= $t->ee( $row[ $col ] ) ?></span>

                                                    <?php endif; ?>

                                                <?php elseif( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'ARRAY'] ): ?>

                                                    <?= $cconf[ 'source' ][ $row[ $col ] ] ?>

                                                <?php elseif( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'INTEGER'] ): ?>

                                                    <?= (int)$row[ $col ] ?>

                                                <?php else: ?>

                                                    Type?

                                                <?php endif; /* if( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'HAS_ONE'] ) */ ?>

                                            <?php else: ?>

                                                <?= $t->ee( $row[ $col ] ) ?>

                                            <?php endif;  /*  if(isset( $cconf[ 'type'] ) ) */ ?>

                                        </td>

                                    <?php endif; /* if( !is_array( $cconf ) ) */ ?>

                                <?php endforeach; ?>

                                <td>

                                    <?php if( $t->data[ 'view' ]['listRowMenu'] ): ?>

                                        <?= $t->insert( $t->data[ 'view' ]['listRowMenu'], [ 'row' => $row ] ) ?>

                                    <?php else: ?>

                                        <div class="btn-group">

                                            <a class="btn btn-sm btn-default" href="<?= route($t->feParams->route_prefix.'@view' , [ 'id' => $row[ 'id' ] ] ) ?>" title="Preview"><i class="glyphicon glyphicon-eye-open"></i></a>

                                            <?php if( !isset( $t->feParams->readonly ) || !$t->feParams->readonly ): ?>
                                                <a class="btn btn-sm btn-default" href="<?= route($t->feParams->route_prefix.'@edit' , [ 'id' => $row[ 'id' ] ] ) ?> " title="Edit"><i class="glyphicon glyphicon-pencil"></i></a>
                                                <a class="btn btn-sm btn-default" id='d2f-list-delete-<?= $row[ 'id' ] ?>' href="#" data-object-id="<?= $row[ 'id' ] ?>" title="Delete"><i class="glyphicon glyphicon-trash"></i></a>
                                            <?php endif;?>

                                        </div>

                                    <?php endif; ?>

                                </td>

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
