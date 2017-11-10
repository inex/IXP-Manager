<?php $this->layout( 'layouts/ixpv4' ) ?>

<?php $this->section( 'title' ) ?>
    <a href="<?= route ( 'ipAddress@list', [ 'protocol' => $t->protocol ] ) ?>">IP Address</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>
        Add IP Address
    </li>
<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>
    <li class="pull-right">
        <div class="btn-group btn-group-xs" role="group">
            <a type="button" class="btn btn-default" href="<?= route ( 'ipAddress@list', [ 'protocol' => $t->protocol ] ) ?>" title="list">
                <span class="glyphicon glyphicon-list"></span>
            </a>
        </div>
    </li>
<?php $this->append() ?>


<?php $this->section( 'content' ) ?>
<?= $t->alerts() ?>

    <?= Former::open()->method( 'POST' )
        ->action( route ('ipAddress@store' ) )
        ->customWidthClass( 'col-sm-3' )
        ->addClass( 'col-md-10' );
    ?>

    <?= Former::select( 'vlan' )
        ->label( 'Vlan' )
        ->fromQuery( $t->vlans, 'name' )
        ->placeholder( 'Choose a Vlan' )
        ->addClass( 'chzn-select' )
        ->blockHelp( '' )
    ?>


    <?= Former::text( 'network' )
        ->label( 'Network' )
        ->blockHelp( '' )
    ?>

    <?= Former::checkbox( 'skip' )
        ->label( ' ' )
        ->text( 'Skip over existing addresses without throwing an error' )
        ->blockHelp('' );
    ?>

    <?=Former::actions( Former::primary_submit( 'Save Changes' ),
        Former::default_link( 'Cancel' )->href( route ( 'ipAddress@list', [ 'protocol' => $t->protocol ] ) ),
        Former::success_button( 'Help' )->id( 'help-btn' )
    );?>


<?= Former::close() ?>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>

<?php $this->append() ?>