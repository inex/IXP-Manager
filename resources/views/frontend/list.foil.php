<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>
    <?= $t->data[ 'feParams' ]->pagetitle  ?>
<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>
    <?php if( !isset( $t->data[ 'feParams' ]->readonly ) || !$t->data[ 'feParams' ]->readonly ): ?>
        <li class="pull-right">
            <div class="btn-group btn-group-xs" role="group">
                <a type="button" class="btn btn-default" href="<?= action($t->controller.'@add') ?>">
                    <span class="glyphicon glyphicon-plus"></span>
                </a>
            </div>
        </li>
    <?php endif;?>
<?php $this->append() ?>

<?php $this->section('content') ?>
    <?= $t->alerts() ?>

    <?= $t->view['listPreamble'] ? $t->insert( $t->view['listPreamble'] ) : '' ?>

    <?php if( !count( $t->data[ 'data' ] ) ): ?>

        <div class="alert alert-info" role="alert">
            <b>No <?= ucfirst( $t->data[ 'feParams' ]->pagetitle ) ?> exist.</b> <a href="<?= action($t->controller.'@add') ?>">Add one...</a>
        </div>

    <?php else:  /* !count( $t->data[ 'data' ] ) */ ?>

        <table id="table-list" class="table collapse">

            <thead>
                <tr>
                    <?php foreach( $t->data[ 'feParams' ]->listColumns as $col => $cconf ):?>

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

            <tbody>

                <?php foreach( $t->data[ 'data' ] as $idx => $row ): ?>

                    <tr>

                        <?php foreach( $t->data[ 'feParams' ]->listColumns as $col => $cconf ): ?>

                            <?php if( !is_array( $cconf ) ): ?>

                                <td>
                                     <?= $t->ee( $row[ $col ] ) ?>
                                </td>

                            <?php elseif( !isset( $cconf[ 'display'] ) || $cconf[ 'display']  ): ?>

                                <td>

                                    <?php if(isset( $cconf[ 'type'] ) ): ?>

                                        <?php if( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'HAS_ONE'] ): ?>

                                            <a href="<?= url( $cconf[ 'controller'] . '/' . $cconf[ 'action'] . '/id/' . $row[ $cconf['idField'] ] ) ?>">
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

                                            <?= date('Y-m-d H:M:S', strtotime($row[ $col ] ) ) ?>

                                        <?php elseif( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'DATE'] ): ?>

                                            <?= date('Y-m-d', strtotime( $row[ $col ] ) ) ?>

                                        <?php elseif( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'TIME'] ): ?>

                                            <?= date('H:M:S', strtotime($row[ $col ] ) ) ?>

                                        <?php elseif( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'SCRIPT'] ): ?>
                                            <?= $t->insert( $cconf['script'], [ 'row' => $row, 'col' => $col ] ) ?>
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

                            <?php if( $t->view['listRowMenu'] ): ?>

                                <?= $t->insert( $t->view['listRowMenu'], [ 'row' => $row ] ) ?>

                            <?php else: ?>

                                <div class="btn-group">

                                    <a class="btn btn-sm btn-default" href="<?= action($t->controller.'@view' , [ 'id' => $row[ 'id' ] ] ) ?>" title="Preview"><i class="glyphicon glyphicon-eye-open"></i></a>

                                    <?php if( !isset( $t->data[ 'feParams' ]->readonly ) || !$t->data[ 'feParams' ]->readonly ): ?>
                                        <a class="btn btn-sm btn-default" href="<?= action($t->controller.'@edit' , [ 'id' => $row[ 'id' ] ] ) ?> " title="Edit"><i class="glyphicon glyphicon-pencil"></i></a>
                                        <a class="btn btn-sm btn-default" id='d2f-list-delete-<?= $row[ 'id' ] ?>' href="#" data-object-id="<?= $row[ 'id' ] ?>" title="Delete"><i class="glyphicon glyphicon-trash"></i></a>
                                    <?php endif;?>

                                </div>

                            <?php endif; ?>

                        </td>

                    </tr>

                <?php endforeach; ?>

            </tbody>

        </table>

    <?php endif;  /* /* !count( $t->data[ 'data' ] ) */ ?>

    <?= $t->view['listPostamble'] ? $t->insert( $t->view['listPostamble'] ) : '' ?>


<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?php if( isset( $t->view[ 'listScript' ] ) ): ?>
        <?= $t->insert( $t->view[ 'listScript' ] ); ?>
    <?php endif; ?>
    <?php if( isset( $t->view[ 'script' ] ) ): ?>
        <?= $t->insert( $t->view[ 'script' ] ); ?>
    <?php endif; ?>
<?php $this->append() ?>
