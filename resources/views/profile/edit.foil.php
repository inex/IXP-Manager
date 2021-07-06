<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' )
?>

<?php $this->section( 'page-header-preamble' ) ?>
    My Profile
<?php $this->append() ?>

<?php $this->section('content') ?>
    <div class="row">
        <div class="col-md-12">
            <?= $t->alerts() ?>
        </div>
    </div>

    <div class="row">
        <?= $t->insert( 'profile/password-form' ); ?>
        <?= $t->insert( 'profile/details-form', [ 'details' => $t->details] ); ?>
    </div>

    <div class="row mt-4">
        <?= $t->insert( 'profile/2fa-form' ); ?>

        <?= $t->insert( 'profile/notes-form', [ 'notesNotifications' => $t->notesNotifications ] ); ?>

        <?= $t->insert( 'profile/mailling-lists-form', [ 'mailingListSubscriptions' => $t->mailingListSubscriptions ] ); ?>
    </div>
<?php $this->append() ?>