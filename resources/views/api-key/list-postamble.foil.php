
<?php if( isset( $t->data[ 'rows' ][ 0 ] ) ) : ?>
    <?php $example_api_key = $t->data[ 'rows' ][ 0 ][ 'apiKey' ] ?>
<?php else: ?>
    <?php $example_api_key = '$your_api_key' ?>
<?php endif; ?>

<br>
<h3>Available API Endpoints</h3>

<p>
    Please see the <a href="http://docs.ixpmanager.org/features/api/">official API documentation here.</a>
</p>
<p>

    The API key can be passed in the header (preferred) or on the URL. For example:
<ul>
    <li> <code>curl -X GET -H "X-IXP-Manager-API-Key: <?= $example_api_key?> " <?= url( "/api/v4/test" ) ?></code></li>
    <li> <code>wget <?= url( "/api/v4/test" ) ?>apikey=<?= $example_api_key ?></code></li>
    <li> <a href="<?= url( "/api/v4/test" ) ?>apikey=<?= $example_api_key ?>"><?= url( "/api/v4/test" ) ?>apikey=<?= $example_api_key ?></a></li>
</ul>
</p>

<dl>

    <dt>IX-F Member List Export</dt>
    <dd>
        See <a href="http://ml.ix-f.net/">here for details on the IX-F Member List</a>
        and <a href="http://docs.ixpmanager.org/features/ixf-export/">here for IXP Manager's IX-F
            Member List export instructions</a>.<br><br>
        Example:
        <code>
            <a href="<?= url( "/api/v4/member-export/ixf/0.6")?>">
                <?= url( "/api/v4/member-export/ixf/0.6") ?>
            </a>
        </code>
        <br><br>
    </dd>

    <?php if( Auth::user()->isSuperUser() ): ?>

        <dt>Salt Provisioning</dt>
        <dd>
            YAML Export Example:
            <code>
                <a href="<?= url( "/api/v4/provisioner/yaml/switch/{switch_id}")?>">
                    <?= url( "/api/v4/provisioner/yaml/switch/{switch_id}" ) ?>
                </a>
            </code>
            <br>
            <code>
                <a href="<?= url( "/api/v4/provisioner/yaml/switch-name/{switch_name}") ?>">
                    <?= url( "/api/v4/provisioner/yaml/switch-name/{switch_name}") ?>
                </a>
            </code>
            <br><br>
        </dd>

        <dt>Patch Panel Ports</dt>
        <dd>
            <ul>
                <li>
                    Get details for an <b>active</b> patch panel port:<br>
                    <code><?= url( "/api/v4/patch-panel-port/{id}") ?> </code> where <code>id</code> is the patch panel port ID.
                    <br><br>
                    If a switch port has an allocated customer, the JSON response is:<br>
                    <pre>
        {
           "id": 38,
           "number": 1,
           "name": "1\/2",
           "coloRef": "TCIE-1234",
           "ticketRef": "234",
           "stateId": 2,
           "state": "Awaiting Xconnect",
           "notes": "* 2017-03-14 [barryo]: Public",
           "privateNotes": "* 2017-03-14 [barryo]: Private",
           "assignedAt": "2017-03-14T00:00:00+00:00",
           "connectedAt": null,
           "ceaseRequestedAt": null,
           "ceasedAt": null,
           "internalUse": false,
           "chargeableId": 2,
           "chargeable": "No",
           "isDuplex": true,
           "isDuplexMaster": true,
           "duplexMasterId": null,
           "duplexSlaveId": 39,
           "loaCode": "RJGtwp2x0Ii7feG7HybaY6nkc",
           "ownedById": 1,
           "ownedBy": "Customer",
           "isHistorical": false,
           "files": [{
                "id":1,
                "name":"Screen Shot 2017-03-13 at 15.57.30.png",
                "type":"image\/png",
                "uploadedAt":"2017-03-13T00:00:00+00:00",
                "uploadedBy":"barryo",
                "size":37100,
                "private":false
            }]
        }</pre>

                    You can get <em>deeper</em> information by adding a <code>/1</code> which adds the following to the above:
                    <pre>
            patchPanel: {
                id: 3,
                cabinetId: 1,
                name: "NAME-01",
                coloRef: "COLO-REF-01",
                cableTypeId: 1,
                cableType: "UTP",
                connectorTypeId: 1,
                connectorType: "RJ45",
                active: true
            },
            switchPort: {
                switchId: 30,
                switch: "swi1-deg1-1",
                name: "X670G2-48x-4q Port 1",
                ifName: "1:1",
                physicalInterfaceId: 2,
                physicalInterface: {
                    statusId: 4,
                    status: "Awaiting X-Connect"
                }
            }</pre>
                    <br><br>
                    Lastly, if the switch port does not exist, a 404 HTTP response is thrown.
                </li>
                <li>
                    Set notes and private notes: [POST]<br>
                    <code><?php url( "/api/v4/patch-panel-port/notes/{id}") ?></code> where <code>id</code> is the patch panel port ID. For example:
                    <br>
                    <pre>curl -X POST  -H "X-IXP-Manager-API-Key: key" --data "notes=a&private_notes=b" <?= url( "/api/v4/patch-panel-port/{id}/notes") ?></pre>
                    Additionally, you can send a <code>&pi_status=X</code> where <code>X</code> is a physical interface state. By doing this, you can
                    update the state through this API call. The code is forgiving - if no physical interface exists, it will fail silently and continue on.
                    <br>
                    If the switch port does not exist, a 404 HTTP response is thrown.
                </li>
                <li>
                    Upload a file: [POST]<br>
                    <code><?= url( "/api/v4/patch-panel-port/upload-file/{id}" ) ?></code> where <code>id</code> is the patch panel port ID.
                    File should be sent as <code>upl</code>. Success yields:
                    <br>
                    <pre>{"success":true, "message": "File uploaded and saved.", "id": newFileId}</pre>
                </li>
                <li>
                    Delete a file: [GET]<br>
                    <code><?= url( "api/v4/patch-panel-port/delete-file/{fileid}") ?></code>.
                </li>
                <li>
                    Toggle a file's privacy status: [GET]<br>
                    <code><?= url( "/api/v4/patch-panel-port/toggle-file-privacy/{fileid}" ) ?></code>.
                    <br>
                    <pre>{"success":true, "isPrivate": true/false}</pre>
                </li>
            </ul>
        </dd>

        <dt>Switch Ports</dt>
        <dd>
            <ul>
                <li>
                    Get the customer who has been allocated a switch port:<br>
                    <code><?= url( "/api/v4/switch-port/{id}/customer" ) ?> </code> where <code>id</code> is the switch port ID.
                    <br><br>
                    If a switch port has an allocated customer, the JSON response is:<br>
                    <code>{"customerFound":true,"id":102,"name":"Packet Shifters Ltd"}</code><br>
                    where the enclosed id is the customer object id.
                    <br><br>
                    If a switch port does not have an allocated customer, the JSON response is:<br>
                    <code>{"customerFound":false}</code>
                    <br><br>
                    Lastly, if the switch port does not exist, a 404 HTTP response is thrown.
                </li>
            </ul>
        </dd>

    <?php endif; ?>

</dl>