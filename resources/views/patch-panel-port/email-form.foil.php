<?php
    /** @var object $t */
    $this->layout( 'layouts/ixpv4' )
?>

<?php $this->section('headers') ?>
<!--    <link rel="stylesheet" type="text/css" href="--><?//= asset( 'bower_components/bootstrap-tagsinput/dist/bootstrap-tagsinput.css' ) ?><!--" />-->
<?php $this->append() ?>


<?php $this->section( 'title' ) ?>
    <a href="<?= route ( 'patch-panel-port/list/patch-panel' , [ 'id' => $t->ppp->getPatchPanel()->getId() ] ) ?>">
        Patch Panel Port
    </a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>
        Email : <?= $t->ee( $t->ppp->getName() )?>
    </li>
<?php $this->append() ?>

<?php $this->section( 'content' ) ?>

    <?= $t->alerts() ?>

    <?= Former::open()->method( 'POST' )
        ->action( route ( 'patch-panel-port@send-email' , [ 'id' =>  $t->ppp->getId() , 'type' => $t->emailType  ] ) )
        ->addClass( 'col-md-10' );
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

        <?php if( $t->emailType != \Entities\PatchPanelPort::EMAIL_LOA ): ?>
            <?= Former::checkbox( 'loa' )
                ->label( 'Attach LoA as a PDF' )
                ->check( $t->emailType == 1 /* connect */ || $t->emailType == 4 /* send loa */ )
                ->value( 1 )
            ?>
        <?php endif; ?>

        <div class="col-lg-offset-2 col-sm-offset-2">

            <ul class="nav nav-tabs">
                <li role="presentation" class="active">
                    <a class="tab-link-body-note" href="#body">Body</a>
                </li>
                <li role="presentation">
                    <a class="tab-link-preview-note" href="#preview">Preview</a>
                </li>
            </ul>

            <div class="tab-content">
                <div role="tabpanel" class="tab-pane active" id="body">
                    <textarea class="form-control" style="font-family:monospace;" rows="30" id="email_text" name="email_text"><?= $t->ee( $t->body )?></textarea>
                </div>
                <div role="tabpanel" class="tab-pane" id="preview">
                    <div class="well well-preview" style="background: rgb(255,255,255);">
                        Loading...
                    </div>
                </div>
            </div>

            <br><br>
        </div>

        <?= Former::actions(
                Former::primary_submit( 'Send Email' ),
                Former::default_link( 'Cancel' )->href( route ( 'patch-panel-port/list/patch-panel' , [ 'id' => $t->ppp->getPatchPanel()->getId() ] ) )
            );
        ?>

        <?= Former::hidden( 'emailType' )
            ->value( $t->emailType )
        ?>

        <?= Former::hidden( 'patch_panel_port_id' )
            ->value( $t->ppp->getId() )
        ?>
    <?= Former::close() ?>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>

<script>

    /**
     * allow the value to be display as a tag
     */
    function allowValue(event){
        event.cancel = checkEmail(event.item);
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