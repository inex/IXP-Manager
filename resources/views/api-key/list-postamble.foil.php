
<?php if( isset( $t->data[ 'rows' ][ 0 ] ) ) : ?>
    <?php $example_api_key = config( 'ixp_fe.api_keys.show_keys' ) ? $t->data[ 'rows' ][ 0 ][ 'apiKey' ] : Str::limit( $t->data[ 'rows' ][ 0 ][ 'apiKey' ] , 6 ) ?>
<?php else: ?>
    <?php $example_api_key = '$your_api_key' ?>
<?php endif; ?>

<div class="card mt-4">
    <div class="card-header">
        <h3>Available API Endpoints</h3>
    </div>
    <div class="card-body">
        <p>
            Please see the <a href="http://docs.ixpmanager.org/features/api/">official API documentation here.</a>
        </p>
        <p>

            The API key can be passed in the header (preferred) or on the URL. For example:
        <ul>
            <li>
                <code>curl -X GET -H "X-IXP-Manager-API-Key: <?= $example_api_key?>" <?= url( "/api/v4/test" ) ?></code>
            </li>
            <li>
                <code>wget <?= url( "/api/v4/test" ) ?>?apikey=<?= $example_api_key ?></code>
            </li>
            <li>
                <a href="<?= url( "/api/v4/test" ) ?>?apikey=<?= $example_api_key ?>"><?= url( "/api/v4/test" ) ?>?apikey=<?= $example_api_key ?></a>
            </li>
        </ul>
        </p>

        <dl>

            <dt>IX-F Member List Export</dt>
            <dd>
                See <a href="https://ixpdb.euro-ix.net/en/">here for details on the IX-F Member List</a>
                (or <a href="https://github.com/euro-ix/json-schemas/">the GitHub repo</a>)
                and <a href="http://docs.ixpmanager.org/features/ixf-export/">here for IXP Manager's IX-F
                    Member List export instructions</a>.<br><br>
                Examples:
                <ul>
                    <?php foreach( \IXP\Utils\Export\JsonSchema::EUROIX_JSON_VERSIONS as $v ): ?>
                        <li>
                            <code>
                                <a href="<?= url( "/api/v4/member-export/ixf/" . $v )?>">
                                    <?= url( "/api/v4/member-export/ixf/" . $v ) ?>
                                </a>
                            </code>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </dd>


            <dt>Ping Endpoint</dt>
            <dd>
                A simple JSON endpoint providing IXP Manager version and IX information.<br>
                <ul>
                    <li>
                        <a target="_blank" href="<?= route('api-v4:ping') ?>"><?= route('api-v4:ping') ?></a>
                    </li>
                </ul>
            </dd>

            <dt>Test Endpoint</dt>
            <dd>
                A simple test endpoint which is useful for testing if your are providing the API key correctly.
                <em>Note that it will show that you are authenticated also if it receives a valid cookie.</em>
                <br>
                <ul>
                    <li>
                        <a target="_blank" href="<?= route('api-v4:test') ?>"><?= route('api-v4:test') ?></a>
                    </li>
                </ul>
            </dd>


            <?php if( Auth::getUser()->isSuperUser() ): ?>

                <dt>Automated Provisioning</dt>
                <dd>
                    <a href="https://www.inex.ie/">INEX</a> auto-provisions our peering LANs which use a mixture of switching
                    technologies (as of 2019, VXLAN on Arista on one network and VXLAN on Cumulus on another). We have
                    <a href="https://www.ixpmanager.org/presentations">presented on this a number of times in 2017</a> and we
                    have <a href="https://github.com/inex/ixp-manager-provisioning">open-sourced a GitHub repository</a> with
                    our provisioning scripts.<br><br>
                    While INEX uses SaltStack directly or Cumulus or with Napalm on Arista, the schema we have designed via the
                    following URLs should be sufficient to allow you to use any provisioning tool.<br><br>
                    These are per-switch <em>(use the switch name in the url)</em> YAML outputs for generating different configuration
                    aspects.<br><br>
                    You may change <code>.yaml</code> to <code>.json</code> in the URL if you prefer. If you wish to use the switch
                    database ID rather than the name, alter the URL for: <code>s#switch-name/{switchname}#switch/{id}#</code>.
                    <ul>
                        <li>Layer2 Interfaces: <code><?= url( "/api/v4/provisioner/layer2interfaces/switch-name" ) ?>/{switchname}.yaml</code></li>
                        <li>Layer3 Interfaces: <code><?= url( "/api/v4/provisioner/layer3interfaces/switch-name" ) ?>/{switchname}.yaml</code></li>
                        <li>Routing: <code><?= url( "/api/v4/provisioner/routing/switch-name" ) ?>/{switchname}.yaml</code></li>
                        <li>Basic Switch Information: <code><?= url( "/api/v4/provisioner/switch/switch-name" ) ?>/{switchname}.yaml</code></li>
                        <li>VLANs: <code><?= url( "/api/v4/provisioner/vlans/switch-name" ) ?>/{switchname}.yaml</code></li>
                    </ul>
                </dd>



                <dt>Sflow MAC Address Mapping</dt>
                <dd>
                    When configuring <a href="https://docs.ixpmanager.org/features/sflow-p2p/">Sflow based peer to peer graphs</a>,
                    the mechanism to associate sflow samples (based on source and destination MAC addreesses) to VLAN interfaces
                    requires a MAC to VLAN interface dictionary.
                    <br><br>
                    As IXP Manager <a href="https://docs.ixpmanager.org/features/layer2-addresses/">supports layer2 / MAC addresses in two ways</a>
                    (learned versus configured), there are two endpoints.
                    <ul>
                        <li>Learned: <code><a href="<?= url( "/api/v4/sflow-db-mapper/learned-macs" ) ?>"><?= url( "/api/v4/sflow-db-mapper/learned-macs" ) ?></a></code></li>
                        <li>Configured: <code><a href="<?= url( "/api/v4/sflow-db-mapper/configured-macs" ) ?>"><?= url( "/api/v4/sflow-db-mapper/configured-macs" ) ?></a></code></li>
                    </ul>
                    The JSON output is structured as follows:
                    <ul>
                        <li> outer object indexed by infrastructure ID </li>
                        <li> each infrastructure object has per VLAN objects indexed by the VLAN <b>tag</b> (NB: this is not the VLAN database ID but the VLAN tag) </li>
                        <li> each VLAN object has key/value pairs of <code>macaddress: vlaninterfaceid</code> </li>
                    </ul>
                </dd>


            <?php endif; ?>


        </dl>
    </div>
</div>


