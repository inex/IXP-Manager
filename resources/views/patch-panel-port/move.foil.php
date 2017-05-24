<?php
/** @var Foil\Template\Template $t */

$this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'title' ) ?>
    Move Patch Panel Port - <?= $t->ppp->getPatchPanel()->getName() ?> :: <?= $t->ppp->getName() ?>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>

    <div class="well">
        <?= Former::open()->method( 'POST' )
            ->action( url( 'patch-panel-port/move' ) )
            ->customWidthClass( 'col-sm-3' )
        ?>


            <?= Former::text( 'current-pos' )
                ->label( 'Current position :' )
                ->value( $t->ppp->getPatchPanel()->getName() . ' :: ' . $t->ppp->getName() )
                ->blockHelp( 'help text' )
                ->disabled( true );
            ?>

            <?= Former::select( 'pp' )
                ->label( 'New Patch Panel:' )
                ->placeholder( 'Choose a Patch Panel' )
                ->options( $t->ppAvailable )
                ->addClass( 'chzn-select' )
                ->blockHelp( 'help text' );
            ?>

            <?= Former::select( 'master-port' )
                ->label( 'New Master Port:' )
                ->placeholder( 'Choose a Master port' )
                ->addClass( 'chzn-select' )
                ->blockHelp( 'help text3' );
            ?>

            <?php if( $t->ppp->hasSlavePort() ): ?>
                <?= Former::select( 'slave-port' )
                    ->label( 'New Slave/Duplex Port:' )
                    ->placeholder( 'Choose a Duplex port' )
                    ->addClass( 'chzn-select' )
                    ->blockHelp( 'help text' );
                ?>
            <?php endif; ?>



        <?= Former::hidden( 'id' )
            ->value( $t->ppp->getId() )
        ?>

        <?= Former::hidden( 'has-duplex' )
            ->value( $t->ppp->hasSlavePort() ? true : false )
        ?>

        <?=Former::actions(
            Former::primary_submit( 'Save Changes' ),
            Former::default_link( 'Cancel' )->href( url( 'sflowReceiver/list/' ) ),
            Former::success_button( 'Help' )->id( 'help-btn' )
        )->id('btn-group');?>

        <?= Former::close() ?>
    </div>
<?php $this->append() ?>

<?php $this->section('scripts') ?>
    <script>
        $( document ).ready(function() {
            $( "#pp" ).change(function(){
                setPPP();
            });

            <?php if( $t->ppp->hasSlavePort() ): ?>
                $( "#master-port" ).change(function(){
                    nextPort = parseInt($( "#master-port" ).val()) + parseInt(1);
                    if( $( '#slave-port option[value="'+nextPort+'"]' ).length ) {
                        $( '#slave-port' ).val( nextPort );
                        $( '#slave-port' ).trigger("chosen:updated");
                    }
                });
            <?php endif; ?>
        });

        /**
         * hide the help sections at loading
         */
        $( 'p.help-block' ).hide();

        /**
         * display / hide help sections on click on the help button
         */
        $( "#help-btn" ).click( function() {
            $( "p.help-block" ).toggle();
        });


        /**
         * set all the Patch Panel Panel Port available for the Patch Panel selected
         */
        function setPPP(){
            $( "#master-port" ).html( "<option value=\"\">Loading please wait</option>\n" ).trigger( "chosen:updated" );
            <?php if( $t->ppp->hasSlavePort() ): ?>
                $( "#slave-port" ).html( "<option value=\"\">Loading please wait</option>\n" ).trigger( "chosen:updated" );
            <?php endif; ?>

            ppId = $( "#pp" ).val();

            url = "<?= url( '/api/v4/patch-panel' )?>/" + ppId + "/patch-panel-port-free";
            datas = {pppId: <?= $t->ppp->getId() ?> };
            $.ajax( url , {
                data: datas,
                type: 'POST'
            })
                .done( function( data ) {
                    var options = "<option value=\"\">Choose a switch port</option>\n";
                    $.each( data.listPorts, function( key, value ){
                        options += "<option value=\"" + key + "\">" + value + "</option>\n";
                    });
                    $( "#master-port" ).html( options );
                    <?php if( $t->ppp->hasSlavePort() ): ?>
                        $( "#slave-port" ).html( options );
                    <?php endif; ?>
                })
                .fail( function() {
                    throw new Error( "Error running ajax query for api/v4/switcher/$id/switch-port" );
                    alert( "Error running ajax query for switcher/$id/customer/$custId/switch-port/$spId" );
                })
                .always( function() {
                    $( "#master-port" ).trigger( "chosen:updated" );
                    <?php if( $t->ppp->hasSlavePort() ): ?>
                        $( '#slave-port' ).trigger("chosen:updated");
                    <?php endif; ?>
                });
        }
    </script>
<?php $this->append() ?>