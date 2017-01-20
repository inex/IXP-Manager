<?php $this->layout('layouts/ixpv4') ?>

<?php $this->section('title') ?>
    <a href="<?= url('patch-panel-port/list')?>">Patch Panel Port</a>
<?php $this->append() ?>

<?php $this->section('page-header-postamble') ?>
    <li>Edit</li>
<?php $this->append() ?>


<?php $this->section('content') ?>



<?= Former::open()->method('POST')->action(url('patch-panel-port/add/'.$t->params['patchPanelPort']->getId()))->customWidthClass('col-sm-3');?>

    <?= Former::text('ppp-name')->label('Patch Panel Port Name');?>
    <?= Former::text('patch-panel')->label('Patch Panel')?>
    <?= Former::select('switch')->label('Switch')->fromQuery($t->params['listSwitch'], 'name')->placeholder('Choose a switch')->addClass('chzn-select')?>
    <?= Former::select('switch-port')->label('Switch Port')->placeholder('Choose a switch port')->addClass('chzn-select')?>
    <?= Former::select('customer')->label('Customer')->fromQuery($t->params['listCustomers'], 'name')->placeholder('Choose a customer')->addClass('chzn-select')?>
    <?= Former::select('state')->label('States')->options($t->params['listStates'])->placeholder('Choose a states')->addClass('chzn-select')?>
    <?= Former::textarea('note')->label('Note')?>
    <?= Former::date('assigned-at')->label('Assigned At')?>
    <?= Former::date('connected-at')->label('Connected At')?>
    <?= Former::date('ceased-requested-at')->label('Ceased Requested At')?>
    <?= Former::date('ceased-at')->label('Ceased Requested At')?>
    <?= Former::date('last-state-change-at')->label('Last State change At')?>
    <?= Former::radios('chargeable')
        ->radios(array(
            'Yes' => array('chargeable' => 'yes', 'value' => '1'),
            'No' => array('chargeable' => 'no', 'value' => '0'),
        ))->inline()->check($t->params['patchPanelPort']->getChargeableInt())?>

    <?= Former::radios('internal-use')
        ->radios(array(
            'Yes' => array('name' => 'internal-use', 'value' => '1'),
            'No' => array('name' => 'internal-use', 'value' => '0'),
        ))->inline()->check($t->params['patchPanelPort']->getInternalUseInt())?>

    <?= Former::hidden('patch-panel-port-id')->value($t->params['patchPanelPort']->getId())?>
    <?=Former::actions( Former::primary_submit('Save Changes'),
        Former::default_button('Cancel')
    );?>

<?= Former::close() ?>


<?php $this->append() ?>


<?php $this->section('scripts') ?>
<script>
    $(document).ready(function() {
        $("#switch").change(function(){
            switchId = $("#switch").val();
            $.ajax({
                url: "<?= url('patch-panel-port/getSwitchPort/')?>",
                data: {switchId: switchId},
                type: 'GET',
                dataType: 'JSON',
                success: function (data) {
                    if(data.success){

                    }
                    else{

                    }
                }

            });
        });
    });
</script>
<?php $this->append() ?>
