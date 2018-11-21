<?php $this->layout( 'layouts/ixpv4' ) ?>

<?php $this->section( 'title' ) ?>
    <a href="<?= route( 'switch@list' )?>">Switches</a>
<?php $this->append() ?>

<?php $this->section( 'page-header-postamble' ) ?>
    <li>View / Edit Ports for <?= $t->s->getName() ?> (via SNMP)</li>
<?php $this->append() ?>

<?php $this->section( 'page-header-preamble' ) ?>

    <?= $t->insert( "switch-port/page-header-preamble", [ "data" => [ "params" => [ "switch" => $t->s->getId(), "switches" => $t->switches ] ] , "feParams" => (object)[ "route_prefix" => "switch-port", "route_action" => "snmp-poll" ] ] ) ?>

<?php $this->append() ?>

<?php $this->section( 'content' ) ?>

<div class="row">

    <div class="col-sm-12">

        <?= $t->alerts() ?>

        <?php if( count( $t->ports ) ): ?>
            <nav class="navbar navbar-default">
                <div id="actions-area">

                    <div class="navbar-header">
                        <a class="navbar-brand" href="#"> With selected:</a>
                    </div>

                    <form class="navbar-form navbar-left form-inline">

                        <div class="form-group">
                            <a href="#" class="btn btn-danger input-sp-action" id="poll-group-delete">Delete</a>
                        </div>
                        |
                        <div class="form-group">
                            <label for="shared-type">Set type:</label>
                            <select id="shared-type" name="shared-type" class="form-control input-sp-action">
                                <option value="" label="Choose a type">Choose a type</option>
                                <?php foreach( Entities\SwitchPort::$TYPES as $idx => $name ): ?>
                                    <option value="<?= $idx ?>" label="<?= $name ?>"><?= $name ?></option>
                                <?php endforeach; ?>
                            </select>

                        </div>
                        |
                        <div class="form-group">
                            <a href="#" class="btn btn-success input-sp-action" id="poll-group-active">Set Active</a>
                        </div>
                        |
                        <div class="form-group">
                            <a href="#" class="btn btn-warning input-sp-action" id="poll-group-inactive">Set Inactive</a>
                        </div>

                        <div id="loading" class="form-group" style="margin-left: 10px"></div>

                    </form>

                </div>
            </nav>
        <?php endif; ?>

        <table id="list-port" class="table table-bordered table-hover">

            <thead>
            <tr>
                <th>
                    <input type="checkbox" name="select-all" id="select-all" value="" />
                    &nbsp; &nbsp;
                    <i id="checkbox-reverse" style="cursor: pointer" class="glyphicon glyphicon-retweet"></i>
                </th>
                <th>Name</th>
                <th>Customer</th>
                <th>Description</th>
                <th>Alias</th>
                <th>Active</th>
                <th>Type</th>
                <th>Status</th>
            </tr>
            </thead>

            <tbody>
                <?php if( count( $t->ports ) ): ?>
                    <?php foreach( $t->ports as $port ): ?>

                        <tr id="poll-tr-<?= $port[ "port" ]->getId() ?>" class="poll-tr">
                            <td>
                                <input type="checkbox" class="sp-checkbox" name="switch-port[<?= $port[ "port"]->getId() ?>]" id="switch-port-<?= $port[ "port"]->getId() ?>" value="<?= $port[ "port"]->getId() ?>" />
                            </td>
                            <td>
                                <?= $port[ "port"]->getIfName() ?>
                            </td>
                            <td>
                                <?php if( $port[ "port"]->getPhysicalInterface() ): ?>

                                    <?php $cust = $port[ "port"]->getPhysicalInterface()->getVirtualInterface()->getCustomer() ?>
                                    <a href="<?= route( 'customer@overview', [ 'id' => $cust->getId() , 'tab' => 'ports' ] ) ?>">
                                        <?= $cust->getShortname() ?>
                                    </a>

                                <?php endif; ?>
                            </td>
                            <td>
                                <?= $port[ "port"]->getName() ?>
                            </td>
                            <td>
                                <?= $port[ "port"]->getIfAlias() ?>
                            </td>
                            <td>
                                <?= $port[ "port"]->getActive() ? "Yes" : "No" ?>
                            </td>
                            <td>
                                <div style="float: left;">
                                    <select id="port-type-<?= $port[ "port"]->getId() ?>" class="chzn-select" style="width: 150px">
                                        <?php foreach( Entities\SwitchPort::$TYPES as $idx => $name ): ?>
                                            <option value="<?= $idx ?>" label="<?= $name ?>" <?= $port[ "port"]->getType() == $idx ? "selected='selected'" : "" ?>>
                                                <?= $name ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>
                                <div id="port-type-state-<?= $port[ "port"]->getId() ?>" style="float: left; padding: 2px 0px 0px 5px; width: 30px; margin-left:10px"></div>
                            </td>

                            <td>
                                <?php if( $port[ "bullet" ] ==  "new" ): ?>
                                    <span class="badge badge-success">New</span>
                                <?php elseif( $port[ "bullet" ] ==  "db" ): ?>
                                    <span class="badge badge-danger">DB Only</span>
                                <?php endif; ?>
                            </td>
                        </tr>

                    <?php endforeach; ?>

                <?php else: ?>

                    <tr align="center">
                        <td colspan="8">
                            <b>SNMP polling information failed or there are no Ethernet ports on this switch</b>
                        </td>
                    </tr>

                <?php endif; ?>

            </tbody>

        </table>

    </div>

</div>

<?php $this->append() ?>

<?php $this->section( 'scripts' ) ?>
<?= $t->insert( 'switch-port/js/snmp-poll' ); ?>
<?php $this->append() ?>
