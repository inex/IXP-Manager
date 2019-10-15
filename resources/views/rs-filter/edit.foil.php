<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Route Server Filter
    /
    <?= $t->c->getName() ?> / <?= $t->rsf ? 'Edit' : 'Add' ?>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>

    <div class="btn-group btn-group-sm" role="group">

        <?php if( $t->rsf ): ?>
            <a class="btn btn-white" href="<?= route('rs-filter@view', [ "id" => $t->rsf->getId() ] ) ?>" title="view route serve filter">
                <i class="fa fa-eye"></i>
            </a>
        <?php endif; ?>

    </div>

<?php $this->append() ?>

<?php $this->section('content') ?>
    <div class="row">

        <div class="col-sm-12">

            <div class="card">
                <div class="card-body">

                    <?= Former::open()
                        ->method( 'post' )
                        ->action( route( 'rs-filter@store' ) )
                        ->customInputWidthClass( 'col-lg-4 col-md-6 col-sm-6' )
                        ->customLabelWidthClass( 'col-sm-4 col-md-4 col-lg-3' )
                        ->actionButtonsCustomClass( "grey-box");
                    ?>

                    <?= Former::select( 'peer_id' )
                        ->label( 'Peer' )
                        ->fromQuery( $t->peers, 'name' )
                        ->placeholder( 'Choose a Peer' )
                        ->addClass( 'chzn-select' );
                    ?>

                    <?= Former::select( 'vlan_id' )
                        ->label( 'Lan' )
                        ->fromQuery( $t->vlans, 'name' )
                        ->placeholder( 'Choose a Lan' )
                        ->addClass( 'chzn-select' );
                    ?>

                    <?= Former::select( 'protocol' )
                        ->label( 'Protocol' )
                        ->fromQuery( Entities\Router::$PROTOCOLS )
                        ->placeholder( 'Choose the protocol' )
                        ->addClass( 'chzn-select' );
                    ?>

                    <?= Former::text( 'prefix' )
                        ->label( 'Prefix' )
                        ->blockHelp( "" );
                    ?>

                    <?= Former::select( 'action_advertise' )
                        ->label( 'Action Advertise' )
                        ->fromQuery( Entities\RouteServerFilter::$ADVERTISE_ACTION_TEXT )
                        ->placeholder( 'Choose advertise action' )
                        ->addClass( 'chzn-select' );
                    ?>

                    <?= Former::select( 'action_receive' )
                        ->label( 'Action Receive' )
                        ->fromQuery( Entities\RouteServerFilter::$RECEIVE_ACTION_TEXT )
                        ->placeholder( 'Choose receive action' )
                        ->addClass( 'chzn-select' );
                    ?>

                    <?= Former::hidden( 'id' )
                        ->value( $t->rsf ? $t->rsf->getId() : '' )
                    ?>

                    <?= Former::hidden( 'custid' )
                        ->value( $t->rsf ? $t->rsf->getCustomer()->getId() : $t->c->getId() )
                    ?>

                    <?= Former::actions(
                        Former::primary_submit( $t->rsf ? 'Save Changes' : 'Add' )->class( "mb-2 mb-sm-0" ),
                        Former::secondary_link( 'Cancel' )->href(  route( 'rs-filter@list', [ "custid" => $t->rsf ? $t->rsf->getCustomer()->getId() : $t->c->getId() ] ) )->class( "mb-2 mb-sm-0" ),
                        Former::success_button( 'Help' )->id( 'help-btn' )->class( "mb-2 mb-sm-0" )
                    );
                    ?>

                    <?= Former::close() ?>

                </div>

            </div>


        </div>

    </div>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
    <?= $t->insert( 'rs-filter/js/edit' ); ?>
<?php $this->append() ?>