<?php
/** @var Foil\Template\Template $t */
$this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
IXP Manager Settings
<?php $this->append() ?>

<?php $this->section('content') ?>
<div class="row">
    <div class="col-12">

        <?= $t->alerts() ?>

        <h3>Welcome to IXP Manager's UI frontend for the <code>.env</code> configuration file.</h3>

        <p>
            You are seeing this page because we have found element(s) in your <code>.env</code> file that are not compatible with the IXP Manager UI.
            Please see the above (first) issue and correct it, and then try again.
        </p>

        <p>
            We also have <a href="https://docs.ixpmanager.org/<?= DOCUMENTATION_VERSION ?>/features/settings/" target="_blank">documentation on the
            supported dotEnv features here</a>.
        </p>



    </div>
</div>
<?php $this->append() ?>

<?php $this->section('scripts') ?>
<script type="module">
    function showAlert(classes, message, hide = false) {
        $("html, body").animate({ scrollTop: 0 }, "slow");
        $('#alertBlock .alertText').html(message);
        $('#alertBlock').addClass(classes).removeClass("tw-hidden");
        if(hide) {
            setTimeout(function(e) {
                $('#alertBlock').addClass("tw-hidden");
            }, 3000);
        }
    }

    const envForm = $('#envForm');
    $(document).on('click','#updateButton',function() {
        const url = envForm.attr('action');
        const data = envForm.serialize();
        axios.post(url,data)
            .then(function(response) {
                showAlert("tw-bg-green-100 tw-border-green-500 tw-text-green-700",response.data.message);
            })
            .catch(function (error) {
                showAlert("tw-bg-red-100 tw-border-red-500 tw-text-red-700",error.message);
                console.log(error);
            });
    })
</script>
<?php $this->append() ?>
