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
        <div class="tw-border-l-4 p-4 alert-dismissible mb-4 tw-hidden" id="alertBlock" role="alert">
            <div class="d-flex align-items-center">
                <div class="text-center"><i class="fa fa-check-circle fa-2x "></i></div>
                <div class="col-sm-12 alertText"></div>
            </div>
        </div>

        <div class="card col-sm-12">
            <div class="card-body">
                <?= $t->form ?>
            </div>
        </div>

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
                showAlert("tw-bg-green-100 tw-border-green-500 tw-text-green-700",response.data.message,true);
            })
            .catch(function (error) {
                showAlert("tw-bg-red-100 tw-border-red-500 tw-text-red-700",error.message);
                console.log(error);
            });
    })
</script>
<?php $this->append() ?>
