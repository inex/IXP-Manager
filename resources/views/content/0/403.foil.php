<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'title' ) ?>
    Support / Contact Details
<?php $this->append() ?>


<?php $this->section('content') ?>

<div class="alert alert-danger tw-text-4xl">
    <strong>Error 403!</strong> Access to this page is forbidden.
</div>

<br /><br />

<p>
This page is protected by an access control list. All visits are logged. Please contact the site administrator if you believe this is a mistake.
</p>

<br /><br />


<?php $this->append() ?>
