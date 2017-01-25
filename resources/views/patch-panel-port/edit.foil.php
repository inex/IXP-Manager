<?php $this->layout('layouts/ixpv4') ?>

<?php $this->section('title') ?>
    <a href="<?= url('patch-panel-port/list')?>">Patch Panel Port</a>
<?php $this->append() ?>

<?php $this->section('page-header-postamble') ?>
    <li>Edit</li>
<?php $this->append() ?>


<?php $this->section('content') ?>



<?= Former::open()->method('POST')->action(url('patch-panel-port/add/'.$t->params['patchPanelPort']->getId()))->customWidthClass('col-sm-3')->addClass('col-md-10');?>
    <?= Former::text('ppp-name')->label('Patch Panel Port Name');?>
    <?= Former::text('patch-panel')->label('Patch Panel')?>

    <div class="well">
        <?= Former::select('switch')->label('Switch')->fromQuery($t->params['listSwitch'], 'name')->placeholder('Choose a switch')->addClass('chzn-select')?>
        <?= Former::select('switch-port')->label('Switch Port')->fromQuery($t->params['listSwitchPort'], 'name')->placeholder('Choose a switch port')->addClass('chzn-select')?>
    </div>
    <div class="well">
        <?= Former::select('customer')->label('Customer')->fromQuery($t->params['listCustomers'], 'name')->placeholder('Choose a customer')->addClass('chzn-select')?>
    </div>
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

        $("#ppp-name").prop('readonly', true);
        $("#patch-panel").prop('readonly', true);

        $("#switch").change(function(){
            $("#switch-port").html("<option value=\"\">Loading please wait</option>\n");
            $("#switch-port").trigger("chosen:updated");
            switchId = $("#switch").val();
            $.ajax({
                url: "<?= url('patch-panel-port/getSwitchPort/')?>",
                data: {switchId: switchId},
                type: 'GET',
                dataType: 'JSON',
                success: function (data) {
                    if(data.success){
                        var options = "<option value=\"\">Choose a switch port</option>\n";
                        $.each(data.response,function(key, value){
                            options += "<option value=\"" + value.id + "\">" + value.name + " (" + value.type + ")</option>\n";
                        });
                        $("#switch-port").html(options);
                        $("#switch-port").trigger("chosen:updated");
                    }
                }
            });
        });

        $("#switch-port").change(function(){
            if($("#switch").val() != ''){
                switchPortId = $("#switch-port").val();
                $.ajax({
                    url: "<?= url('patch-panel-port/getCustomerForASwitchPort/')?>",
                    data: {switchPortId: switchPortId},
                    type: 'GET',
                    dataType: 'JSON',
                    success: function (data) {
                        if(data.success){
                            $("#customer").html("<option value=\"" + data.response.id + "\">" + data.response.name + "</option>\n");
                            $("#customer").trigger("chosen:updated");
                        }
                        else{
                            $("#customer").html("");
                            $("#customer").trigger("chosen:updated");
                        }
                    }

                });
            }

        });

        $("#customer").change(function(){
            if($("#switch").val() == ''){
                customerId = $("#customer").val();
                $.ajax({
                    url: "<?= url('patch-panel-port/getCustomerForASwitchPort/')?>",
                    data: {switchPortId: switchPortId},
                    type: 'GET',
                    dataType: 'JSON',
                    success: function (data) {
                        if(data.success){
                            $("#customer").html("<option value=\"" + data.response.id + "\">" + data.response.name + "</option>\n");
                            $("#customer").trigger("chosen:updated");
                        }
                        else{
                            $("#customer").html("");
                            $("#customer").trigger("chosen:updated");
                        }
                    }

                });
            }

        });
    });
</script>
<?php $this->append() ?>
