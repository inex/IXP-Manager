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

    <?= Former::checkbox('duplex')?>
    <span id='duplex-port-area' style="display: none">
        <?= Former::select('partner-port')->label('Partner Port')->fromQuery($t->params['partnerPorts'], 'name')->placeholder('Choose a partner port')->addClass('chzn-select')?>
    </span>


    <div class="well">
        <?= Former::default_button()->addClass('reset-button-well')->icon('glyphicon glyphicon-refresh')->title('Reset')->style('margin-top : 1%')->id('resetSwitchSelect');?>
        <?= Former::select('switch')->label('Switch')->fromQuery($t->params['listSwitch'], 'name')->placeholder('Choose a switch')->addClass('chzn-select')?>
        <?= Former::select('switch-port')->label('Switch Port')->fromQuery($t->params['listSwitchPort'], 'name')->placeholder('Choose a switch port')->addClass('chzn-select')?>
    </div>
    <div class="well">
        <?= Former::default_button()->addClass('reset-button-well')->icon('glyphicon glyphicon-refresh')->title('Reset')->id('resetCustomer');?>
        <?= Former::select('customer')->label('Customer')->fromQuery($t->params['listCustomers'], 'name')->placeholder('Choose a customer')->addClass('chzn-select')?>
    </div>
    <?= Former::select('state')->label('States')->options($t->params['listStates'])->placeholder('Choose a states')->addClass('chzn-select')?>
    <?= Former::textarea('note')->label('Note')?>
    <?= Former::date('assigned-at')->label('Assigned At')->append('<button class="btn-default btn" onclick="setToday(\'assigned-at\')" type="button">Today</button>')?>
    <?= Former::date('connected-at')->label('Connected At')->append('<button class="btn-default btn" onclick="setToday(\'connected-at\')" type="button">Today</button>')?>
    <?= Former::date('ceased-requested-at')->label('Ceased Requested At')->append('<button class="btn-default btn" onclick="setToday(\'ceased-requested-at\')" type="button">Today</button>')?>
    <?= Former::date('ceased-at')->label('Ceased Requested At')->append('<button class="btn-default btn" onclick="setToday(\'ceased-at\')" type="button"">Today</button>')?>
    <?= Former::text('last-state-change-at')->label('Last State change At')?>
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

    <?= Former::hidden('date')->id('date')->value(date('Y-m-d'))?>

<?= Former::close() ?>


<?php $this->append() ?>


<?php $this->section('scripts') ?>
<script>
    function setToday(inputName){
        $("#"+inputName).val($("#date").val());
    }

    $(document).ready(function() {

        if($('#switch-port').val() != null){
            setCustomer();
        }

        $('#duplex').change(function(){
            if(this.checked){
                $("#duplex-port-area").show();
            }
            else{
                $("#duplex-port-area").hide();
            }
        });
        $("#duplex").show();



        $("#ppp-name").prop('readonly', true);
        $("#patch-panel").prop('readonly', true);
        $("#last-state-change-at").prop('readonly', true);

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
            setCustomer();
        });

        function setCustomer(){
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
        }

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
                            $("#switch").html("<option value=\"" + data.response.id + "\">" + data.response.name + "</option>\n");
                            $("#switch").trigger("chosen:updated");
                        }
                        else{
                            $("#customer").html("");
                            $("#customer").trigger("chosen:updated");
                        }
                    }

                });
            }

        });

        $("#resetCustomer").click(function(){

                options = "<option value=''> Choose a customer</option>\n";
                <?php foreach ($t->params['listCustomers'] as $id => $customer): ?>
                    customer = '<?= $customer ?>';
                    options += "<option value=\"" + <?= $id ?> + "\">" + customer  + "</option>\n";
                <?php endforeach; ?>
                $("#customer").html(options);
                $("#customer").trigger("chosen:updated");


        });

        $("#resetSwitchSelect").click(function(){
            if($("#switch").val() != null && $("#switch-port").val() != null){
                options = "<option value=''> Select a customer</option>\n";
                <?php foreach ($t->params['listSwitch'] as $id => $switch): ?>
                $switch = '<?= $switch ?>';
                options += "<option value=\"" + <?= $id ?> + "\">" + $switch  + "</option>\n";
                <?php endforeach; ?>
                $("#switch").html(options);
                $("#switch").trigger("chosen:updated");
                $("#switch-port").html('');
                $("#switch-port").trigger("chosen:updated");
            }

        });
    });
</script>
<?php $this->append() ?>
