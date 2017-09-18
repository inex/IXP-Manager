<?php
/** @var Foil\Template\Template $t */
/** @var $t->active */

$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>
    <?= $t->view[ 'feParams' ]->pagetitle  ?>
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

    <?php if( !count( $t->view[ 'data' ] ) ): ?>
        <div class="alert alert-info" role="alert">
            <b>No active <?= $t->view[ 'feParams' ]->nameSingular ?> exist.</b> <a href="<?= action ($t->controller.'@addAction') ?>">Add one...</a>
        </div>
    <?php else:  /* !count( $t->patchPanels ) */ ?>
        <table id='table-list' class="table collapse" >
            <thead>
                <tr>
                    <?php foreach( $t->view[ 'feParams' ]->listColumns as $col => $cconf ):?>

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
            <?php foreach( $t->view[ 'data' ] as $idx => $row ):?>
                <?php foreach( $t->view[ 'feParams' ]->listColumns as $col => $cconf ):?>
                    <?php if( !is_array( $cconf ) ):?>
                        <td> <?= $row[ $col ] ?> </td>
                    <?php elseif (!isset( $cconf[ 'display'] ) || $cconf[ 'display']  ): ?>
                        <?php if(isset( $cconf[ 'type'] ) ): ?>
                            <?php if( $cconf[ 'type'] == $t->view[ 'col_types' ][ 'HAS_ONE'] ): ?>
                                <td>
                                    <?php  $hasOneId = $cconf['idField'] ?>
                                    <a href="<?= url( $cconf[ 'controller'].'/'.$cconf[ 'action'].'/id/'.$row[ $hasOneId ] ) ?>"> <?= $row[$col] ?> </a>
                                </td>
                            <?php elseif( $cconf[ 'type'] == $t->view[ 'col_types' ][ 'XLATE'] ): ?>
                                <td>
                                    <?php if( isset( $cconf[ 'xlator' ][ $row[ $col ] ] ) ): ?>
                                        <?= $cconf[ 'xlator' ][ $row.$col ] ?>
                                    <?php else: ?>
                                        <?= $row[ $col ] ?>
                                    <?php endif;?>
                                </td>
                            <?php elseif( $cconf[ 'type'] == $t->view[ 'col_types' ][ 'YES_NO'] ): ?>
                                <td>
                                    <?php if($row[ $col ] ):?>
                                        Yes
                                    <?php else: ?>
                                        No
                                    <?php endif;?>
                                </td>
                            <?php elseif( $cconf[ 'type'] == $t->view[ 'col_types' ][ 'REPLACE'] ): ?>
                                <td>
                                    <?= str_replace( '%%COL%%', $row[ $col ], $cconf[ 'subject' ] ) ?>
                                </td>
                            <?php elseif( $cconf[ 'type'] == $t->view[ 'col_types' ][ 'SPRINTF'] ): ?>
                                <td>
                                    <?= sprintf( $cconf[ 'sprintf' ], $row[ $col ] ) ?>
                                </td>
                            <?php elseif( $cconf[ 'type'] == $t->view[ 'col_types' ][ 'DATETIME'] ): ?>
                                <td>
                                    <?= date('Y-m-d H:M:S', strtotime($row[ $col ] ) ) ?>
                                </td>
                            <?php elseif( $cconf[ 'type'] == $t->view[ 'col_types' ][ 'DATE'] ): ?>
                                <td>
                                    <?= date('Y-m-d', strtotime($row[ $col ] ) ) ?>
                                </td>
                            <?php elseif( $cconf[ 'type'] == $t->view[ 'col_types' ][ 'TIME'] ): ?>
                                <td>
                                    <?= date('H:M:S', strtotime($row[ $col ] ) ) ?>
                                </td>
                            <?php elseif( $cconf[ 'type'] == $t->view[ 'col_types' ][ 'SCRIPT'] ): ?>
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
                        <?php if( !isset( $t->view[ 'feParams' ]->readonly ) or !$t->view[ 'feParams' ]->readonly ): ?>
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
    <script>


        $(document).ready(function() {

            $( '#table-list' ).dataTable({
                "aLengthMenu": [ [ 10, 25, 50, 100, 500, -1 ], [ 10, 25, 50, 100, 500, "All" ] ],
                "sDom": "<'row-fluid'<'span6'l><'span6'f>r>t<'row-fluid'<'span6'i><'span6'p>>",
                "bAutoWidth": false,
                <?php $count=0 ?>
                <?php if( isset( $t->view[ 'feParams']->listOrderBy ) ): ?>
                    <?php foreach( $t->view[ 'feParams']->listColumns as $col => $cconf ): ?>
                        <?php if( !is_array( $cconf ) || !isset( $cconf[ 'display' ] ) || $cconf[ 'display' ] ): ?>
                            <?php if( isset( $t->view[ 'feParams']->listOrderBy ) && $t->view[ 'feParams']->listOrderBy == $col ): ?>
                                'aaSorting': [[ <?= $count ?> , <?php if( isset( $t->view[ 'feParams']->listOrderByDir ) && $t->view[ 'feParams']->listOrderByDir =="DESC" ): ?> 'desc'<?php else: ?> 'asc' <?php endif;?> ]],
                            <?php endif; ?>
                            <?php $count = $count + 1 ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                <?php endif; ?>
                'aoColumns': [
                    <?php foreach( $t->view[ 'feParams']->listColumns as $col => $cconf ): ?>
                    <?php if( !is_array( $cconf ) || !isset( $cconf[ 'display' ] ) || $cconf[ 'display' ] ): ?>
                     null ,
                    <?php endif; ?>
                    <?php endforeach; ?>
                    { 'bSortable': false, "bSearchable": false, "sWidth": "150px" }
                ]
            });

            $( '#table-list' ).show();

        });

    </script>
<?php $this->append() ?>