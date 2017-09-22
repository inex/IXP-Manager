<?php
/** @var Foil\Template\Template $t */

$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>
    <?= $t->data[ 'feParams' ]->pagetitle  ?>
<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>
    <li class="pull-right">
    <div class="btn-group btn-group-xs" role="group">
        <a type="button" class="btn btn-default" href="<?= action ($t->controller.'@addAction') ?>">
            <span class="glyphicon glyphicon-plus"></span>
        </a>
    </div>
</li>
<?php $this->append() ?>

<?php $this->section('content') ?>
    <?= $t->alerts() ?>

    <?php if( !count( $t->data[ 'data' ] ) ): ?>
        <div class="alert alert-info" role="alert">
            <b>No active <?= $t->data[ 'feParams' ]->nameSingular ?> exist.</b> <a href="<?= action ($t->controller.'@addAction') ?>">Add one...</a>
        </div>
    <?php else:  /* !count( $t->patchPanels ) */ ?>
        <table id='table-list' class="table collapse" >
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
                </tr>
            </thead>
            <tbody>
            <?php foreach( $t->data[ 'data' ] as $idx => $row ):?>
                <?php foreach( $t->data[ 'feParams' ]->listColumns as $col => $cconf ):?>
                    <?php if( !is_array( $cconf ) ):?>
                        <td> <?= $row[ $col ] ?> </td>
                    <?php elseif (!isset( $cconf[ 'display'] ) || $cconf[ 'display']  ): ?>
                        <?php if(isset( $cconf[ 'type'] ) ): ?>
                            <?php if( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'HAS_ONE'] ): ?>
                                <td>
                                    <?php  $hasOneId = $cconf['idField'] ?>
                                    <a href="<?= url( $cconf[ 'controller'].'/'.$cconf[ 'action'].'/id/'.$row[ $hasOneId ] ) ?>"> <?= $row[$col] ?> </a>
                                </td>
                            <?php elseif( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'XLATE'] ): ?>
                                <td>
                                    <?php if( isset( $cconf[ 'xlator' ][ $row[ $col ] ] ) ): ?>
                                        <?= $cconf[ 'xlator' ][ $row.$col ] ?>
                                    <?php else: ?>
                                        <?= $row[ $col ] ?>
                                    <?php endif;?>
                                </td>
                            <?php elseif( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'YES_NO'] ): ?>
                                <td>
                                    <?php if($row[ $col ] ):?>
                                        Yes
                                    <?php else: ?>
                                        No
                                    <?php endif;?>
                                </td>
                            <?php elseif( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'REPLACE'] ): ?>
                                <td>
                                    <?= str_replace( '%%COL%%', $row[ $col ], $cconf[ 'subject' ] ) ?>
                                </td>
                            <?php elseif( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'SPRINTF'] ): ?>
                                <td>
                                    <?= sprintf( $cconf[ 'sprintf' ], $row[ $col ] ) ?>
                                </td>
                            <?php elseif( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'DATETIME'] ): ?>
                                <td>
                                    <?= date('Y-m-d H:M:S', strtotime($row[ $col ] ) ) ?>
                                </td>
                            <?php elseif( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'DATE'] ): ?>
                                <td>
                                    <?= date('Y-m-d', strtotime($row[ $col ] ) ) ?>
                                </td>
                            <?php elseif( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'TIME'] ): ?>
                                <td>
                                    <?= date('H:M:S', strtotime($row[ $col ] ) ) ?>
                                </td>
                            <?php elseif( $cconf[ 'type'] == $t->data[ 'col_types' ][ 'SCRIPT'] ): ?>
                                {tmplinclude file=$cconf.script}
                            <?php else: ?>
                                <td>Type?</td>
                            <?php endif;?>
                        <?php else: ?>
                            <td> <?= $row[ $col ] ?> </td>
                        <?php endif;?>
                    <?php endif;?>
                <?php endforeach;?>
                <td>
                    <div class="btn-group">
                        <a class="btn btn btn-default" href="<?= action ($t->controller.'@viewAction' , [ 'id' => $row[ 'id' ] ] ) ?>" title="Preview"><i class="glyphicon glyphicon-eye-open"></i></a>
                        <?php if( !isset( $t->data[ 'feParams' ]->readonly ) or !$t->data[ 'feParams' ]->readonly ): ?>
                            <a class="btn btn btn-default" href="<?= action ($t->controller.'@editAction' , [ 'id' => $row[ 'id' ] ] ) ?> " title="Edit"><i class="glyphicon glyphicon-pencil"></i></a>
                            <a class="btn btn btn-default" id='list-delete-<?= $row[ 'id' ] ?>' href="<?= action ($t->controller.'@deleteAction' , [ 'id' => $row[ 'id' ] ] ) ?>" title="Delete"><i class="glyphicon glyphicon-trash"></i></a>
                        <?php endif;?>
                    </div>
                </td>
            <?php endforeach;?>
            <tbody>
        </table>
    <?php endif;  /* !count( $t->patchPanels ) */ ?>

<?php $this->append() ?>
<?php $this->section( 'scripts' ) ?>
    <?php if( isset( $t->view[ 'listScript' ] ) ): ?>
    <?= $t->insert( $t->view[ 'listScript' ] ); ?>
    <?php endif; ?>
<?php $this->append() ?>