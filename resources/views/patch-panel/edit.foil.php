<?php $this->layout('layouts/ixpv4') ?>

<?php $this->section('title') ?>
    <a href="<?= url('patch-panel')?>">Patch Panel</a>
<?php $this->append() ?>

<?php $this->section('page-header-postamble') ?>
    <li><?= $t->params['breadCrumb']?></li>
<?php $this->append() ?>


<?php $this->section('content') ?>



<?= Former::open()->method('POST')->action(url('patch-panel/add'))->customWidthClass('col-sm-3');?>

    <?= Former::text('pp-name')->label('Patch Panel Name');?>
    <?= Former::text('colocation')->label('Colocation reference')?>
    <?= Former::select('cabinets')->label('Cabinet')->fromQuery($t->params['listCabinets'], 'name')->placeholder('Choose a cabinet')->addClass('chzn-select')?>
    <?= Former::select('cable-types')->label('Cable type')->options($t->params['listCableTypes'])->placeholder('Choose a cable type')->addClass('chzn-select')?>
    <?= Former::select('connector-types')->label('Connector Type')->options($t->params['listConnectorTypes'])->placeholder('Choose a connector type')->addClass('chzn-select')?>
    <?= Former::number('number-ports')->label('Number of Ports')->appendIcon('nb-port glyphicon glyphicon-info-sign')->help(($t->params['patchPanel'] != null) ? 'Existing : '.$t->params['patchPanel']->getNumbersPatchPanelPorts() : '');?>

    <?= Former::text('port-prefix')->label('Port Name Prefix')->placeholder('Optional port prefix')->appendIcon('prefix glyphicon glyphicon-info-sign')?>

    <?= Former::date('installation-date')->label('Installation Date')->append('<button class="btn-default btn" id="date-today" type="button">Today</button>')?>
    <?= Former::hidden('patch-panel-id')->value($t->params['patchPanelId'])?>
    <?= Former::hidden('date')->id('date')->value(date('Y-m-d'))?>
    <?=Former::actions( Former::primary_submit('Save Changes'),
                        Former::default_button('Cancel')
    );?>

<?= Former::close() ?>





<?php $this->append() ?>


<?php $this->section('scripts') ?>
    <script>
        $(document).ready(function() {
            if($("#port-prefix").val() != ''){
                $("#port-prefix").prop('readonly', true);
            }

            $(".glyphicon-nb-port").parent().attr('data-toggle','popover').attr('title' , 'Help').attr('data-content' , 'Please select the number of ports that you want to create for that ptach panel');
            $(".glyphicon-prefix").parent().attr('data-toggle','popover').attr('title' , 'Help').attr('data-content' , 'need text');

            $("#date-today").click(function() {
                $("#installation-date").val($("#date").val());
            });

            $("[data-toggle=popover] ").popover({ placement: 'right',container: 'body', trigger: "hover"});
            $( "#pp-name" ).blur(function() {
                if($("#colocation").val() == ''){
                    $("#colocation").val($("#pp-name").val());
                }
            });

        });
    </script>
<?php $this->append() ?>
