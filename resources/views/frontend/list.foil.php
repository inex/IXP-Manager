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

                        <th></th>

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
                                    {if isset($cconf.xlator[ $row.$col ])}{$cconf.xlator[ $row.$col ]}{else}{$row.$col}{/if}
                                </td>
                            <?php elseif( $cconf[ 'type'] == $t->view[ 'col_types' ][ 'YES_NO'] ): ?>
                                <td>
                                    <?php if($row.$col):?>
                                        Yes
                                    <?php else: ?>
                                        No
                                    <?php endif;?>
                                </td>
                            <?php elseif( $cconf[ 'type'] == $t->view[ 'col_types' ][ 'REPLACE'] ): ?>
                                <td>
                                    {str_replace( '%%COL%%', $row.$col, $cconf.subject )}
                                </td>
                            <?php elseif( $cconf[ 'type'] == $t->view[ 'col_types' ][ 'SPRINTF'] ): ?>
                                <td>
                                    {sprintf( $cconf.sprintf, $row.$col )}
                                </td>
                            <?php elseif( $cconf[ 'type'] == $t->view[ 'col_types' ][ 'DATETIME'] ): ?>
                                <td>
                                    {$row.$col|date_format:'%Y-%m-%d %H:%M:%S'}
                                </td>
                            <?php elseif( $cconf[ 'type'] == $t->view[ 'col_types' ][ 'DATE'] ): ?>
                                <td>
                                    {$row.$col|date_format:'%Y-%m-%d'}
                                </td>
                            <?php elseif( $cconf[ 'type'] == $t->view[ 'col_types' ][ 'TIME'] ): ?>
                                <td>
                                    {$row.$col|date_format:'%H:%M:%S'}
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
            <?php endforeach;?>
            <tbody>
        </table>
    <?php endif;  /* !count( $t->patchPanels ) */ ?>

<?php $this->append() ?>
<?php $this->section( 'scripts' ) ?>
    <script>



        $(document).ready( function() {


            $( '#table-list' ).show();


        });
    </script>
<?php $this->append() ?>