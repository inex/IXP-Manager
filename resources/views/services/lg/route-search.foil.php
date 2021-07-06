<?php $this->layout('services/lg/layout') ?>

<?php $this->section('title') ?>
    <small>Route Search</small>
<?php $this->append() ?>

<?php $this->section('content') ?>
    <div class="card col-sm-12">
        <div class="card-body">

            <?= Former::open()->method( 'get' )
                ->action( '#' )
                ->customInputWidthClass( 'col-sm-6' )
                ->addClass( 'col-md-10' )
                ->actionButtonsCustomClass( "grey-box");
            ?>

                <?= Former::text( 'net' )
                    ->id( 'net' )
                    ->label( 'IP Address/Prefix' )
                    ->placeholder( '192.0.2.0/24 | 2001:db8:7:2::/64' )
                    ->blockHelp( '' );
                ?>

                <?= Former::radios( 'Internal Use' )
                    ->label( ' ' )
                    ->radios([
                        'Lookup table' => [ 'name' => 'source_selector', 'value' => 'table' ],
                        'Lookup protocol' => [ 'name' => 'source_selector', 'value' => 'protocol'],
                    ])->check( 'table' )
                    ->blockHelp( '' );
                ?>

                <?= Former::select( 'source' )
                    ->id( 'source' )
                    ->label( 'Source' )
                    ->placeholder( 'Choose a source' )
                    ->addClass( 'chzn-select' )
                    ->blockHelp( '' );
                ?>

                <?=Former::actions( Former::primary_submit( 'Search' )->id( 'submit' )->class( "mb-2 mb-sm-0"),
                    Former::success_button( 'Help' )->id( 'help-btn' )->class( "mb-2 mb-sm-0")
                );?>

            <?= Former::close() ?>
        </div>
    </div>
    <div class="modal fade" id="route-modal" role="dialog">
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
            </div>
        </div>
    </div>
<?php $this->append() ?>

<?php $this->section('scripts') ?>
    <?= $t->insert('services/lg/js/route-search') ?>
<?php $this->append() ?>