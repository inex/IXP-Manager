<?php $this->layout( 'layouts/ixpv4' );
/** @var object $t */
?>

<?php $this->section( 'title' ) ?>
    <a href="<?= route( 'customer@list' )?>">
        Customer
    </a>
<?php $this->append() ?>



<?php $this->section( 'page-header-postamble' ) ?>

    <li>
        <a href="<?=  route( "customer@overview" , [ "id" => $t->c->getId() ] ) ?>" >
            <?= $t->ee( $t->c->getName() ) ?>
        </a>
    </li>
    <li>Welcome Email</li>

<?php $this->append() ?>


<?php $this->section( 'content' ) ?>

    <div class="row">

        <div class="col-md-12">

            <?= $t->alerts() ?>

            <div class="well">
                Please see the <a target="_blank" href="http://docs.ixpmanager.org/usage/customers/#welcome-emails">official documentation</a> for information on welcome emails and instructions on how to customise the content.
            </div>

            <legend>Send Welcome Email</legend>

            <?= Former::open()->method( 'POST' )
                ->action( route( 'customer@send-welcome-email' ) )
                ->addClass( 'col-md-10' );
            ?>
            <?= Former::text( 'to' )
                ->label( 'To' );
            ?>

            <?= Former::text( 'cc' )
                ->label( 'CC' );
            ?>

            <?= Former::text( 'bcc' )
                ->label( 'BCC' );
            ?>

            <?= Former::text( 'subject' )
                ->label( 'Subject' );
            ?>

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
                        <textarea class="form-control" style="font-family:monospace;" rows="30" id="message" name="message"><?= old( 'message' ) ?? $t->body ?></textarea>
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
                Former::default_link( 'Cancel' )->href( route( "customer@overview" , [ "id" => $t->c->getId() ] ) )
            );
            ?>


            <?= Former::hidden( 'id' )
                ->value( $t->c->getId() )
            ?>
            <?= Former::close() ?>

        </div>
    </div>


<?php $this->append() ?>