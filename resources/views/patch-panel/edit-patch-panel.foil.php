<?php $this->layout('layouts/ixpv4') ?>

<?php $this->section('title') ?>
    <a href="<?= url('patch-panel')?>">Patch Panel</a>
<?php $this->append() ?>

<?php $this->section('page-header-postamble') ?>
<li>Add</li>
<?php $this->append() ?>


<?php $this->section('content') ?>

    <form class="form-horizontal" method="post" action="<?= url('patch-panel/add') ?>">
        <div class="form-group">
            <label for="pp-name" class="col-sm-2 control-label">Patch Panel Name</label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="pp-name" name="pp-name" placeholder="Patch Panel Name" required>
            </div>
        </div>
        <div class="form-group">
            <label for="colocation" class="col-sm-2 control-label">Colocation reference</label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="colocation" name="colocation" placeholder="Colocation reference" required>
            </div>
        </div>
        <div class="form-group">
            <label for="cabinets" class="col-sm-2 control-label">Cabinet</label>
            <div class="col-sm-3">
                <select data-placeholder="Choose a cabinet" id="cabinets" name="cabinets"  class="chzn-select">
                    <option value="0"></option>
                    <?php foreach( $t->params['listCabinets'] as $cabinet ): ?>
                        <option value="<?= $cabinet->getId() ?>"> <?= $cabinet->getName() ?></option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="cable-types" class="col-sm-2 control-label">Cable Type</label>
            <div class="col-sm-3">
                <select data-placeholder="Choose a cable type" id="cable-types" name="cable-types" class="chzn-select">
                    <option value="0"></option>
                    <?php foreach( $t->params['listCableTypes'] as $id => $nameCable ): ?>
                        <option value="<?= $id ?>"> <?= $nameCable ?></option>
                    <?php endforeach;?>
                </select>

            </div>
        </div>
        <div class="form-group">
            <label for="connector-types" class="col-sm-2 control-label">Connector Type</label>
            <div class="col-sm-3">
                <select data-placeholder="Choose a connector type" id="connector-types" name="connector-types" class="chzn-select">
                    <option value="0"></option>
                    <?php foreach( $t->params['listConnectorTypes'] as $id => $nameConn ): ?>
                        <option value="<?= $id ?>"> <?= $nameConn ?></option>
                    <?php endforeach;?>
                </select>
            </div>
        </div>
        <div class="form-group">
            <label for="number-ports" class="col-sm-2 control-label">Number of Ports</label>
            <div class="col-sm-3">
                <input type="number" class="form-control" id="number-ports" name="number-ports" placeholder="" required>
            </div>
        </div>
        <div class="form-group">
            <label for="port-prefix" class="col-sm-2 control-label">Port Name Prefix</label>
            <div class="col-sm-3">
                <input type="text" class="form-control" id="port-prefix" name="port-prefix"  placeholder="Optional port prefix">
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-primary">Save changes</button>
                <button type="submit" class="btn btn-default">Cancel</button>
            </div>
        </div>
        <?= csrf_field() ?>
    </form>

<?php $this->append() ?>


<?php $this->section('scripts') ?>
    <script>
        $(document).ready(function() {

            $( "#pp-name" ).blur(function() {
                if($("#colocation").val() == ''){
                    $("#colocation").val($("#pp-name").val());
                }
            });

        });
    </script>
<?php $this->append() ?>
