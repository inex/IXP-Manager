<?php
    /** @var object $t */

use IXP\Models\PatchPanelPort;

$this->layout( 'layouts/ixpv4' );
    $ppp = $t->ppp; /** @var $ppp PatchPanelPort */
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Patch Panel Port
    /
    Email : <?= $t->ee( $ppp->name() )?>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>
    <div class="col-sm-12">
        <?= $t->alerts() ?>

        <?= Former::open()->method( 'POST' )
            ->action( route ( 'patch-panel-port-email@send' , [ 'ppp' =>  $ppp->id , 'type' => $t->emailType  ] ) )
            ->actionButtonsCustomClass( "grey-box");
        ?>
        <?= Former::text( 'email_to' )
            ->label( 'To' );
        ?>

        <?= Former::text( 'email_cc' )
            ->label( 'CC' );
        ?>

        <?= Former::text( 'email_bcc' )
            ->label( 'BCC' );
        ?>

        <?= Former::text( 'email_subject' )
            ->label( 'Subject' );
        ?>

        <?php if( $t->emailType !== PatchPanelPort::EMAIL_LOA ): ?>
            <?= Former::checkbox( 'loa' )
                ->label( 'Attach LoA as a PDF' )
                ->check( $t->emailType === PatchPanelPort::EMAIL_CONNECT || $t->emailType === PatchPanelPort::EMAIL_LOA )
                ->value( 1 )
                ->inline()
            ?>
        <?php endif; ?>

        <div class="col-lg-offset-2 col-sm-offset-2">
            <div class="card mt-4">
                <div class="card-header">
                    <ul class="nav nav-tabs card-header-tabs">
                        <li role="presentation" class="nav-item">
                            <a class="tab-link-body-note nav-link active" href="#body">Body</a>
                        </li>
                        <li role="presentation" class="nav-item">
                            <a class="tab-link-preview-note nav-link" href="#preview">Preview</a>
                        </li>
                    </ul>
                </div>

                <div class="tab-content card-body">
                    <div role="tabpanel" class="tab-pane show active" id="body">
                        <textarea class="form-control" style="font-family:monospace;" rows="30" id="email_text" name="email_text"><?= $t->ee( $t->body )?></textarea>
                    </div>
                    <div role="tabpanel" class="tab-pane" id="preview">
                        <div class="bg-light p-4 well-preview">
                            Loading...
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?= Former::actions(
            Former::primary_submit( 'Send Email' ),
            Former::secondary_link( 'Cancel' )->href( route ( 'patch-panel-port@list-for-patch-panel' , [ 'pp' => $ppp->patch_panel_id ] ) )
        );
        ?>

        <?= Former::hidden( 'emailType' )
            ->value( $t->emailType )
        ?>

        <?= Former::hidden( 'patch_panel_port_id' )
            ->value( $ppp->id )
        ?>
        <?= Former::close() ?>

    </div>
<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>

    <script>

        /**
         * allow the value to be display as a tag
         */
        function allowValue( event ){
            event.cancel = checkEmail( event.item );
        }

        /**
         * check if the value is an email
         */
        function checkEmail(text){
            let filter = /^[\w-.+]+@[a-zA-Z0-9.-]+.[a-zA-z0-9]{2,4}$/;

            if (!filter.test(text)) {
               return true;
            } else {
                return false;
            }
        }
    </script>

<?php $this->append() ?>