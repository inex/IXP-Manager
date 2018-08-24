
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
    <li> <code>wget <?= url( "/api/v4/test" ) ?>?apikey=<?= $example_api_key ?></code></li>
    <li> <a href="<?= url( "/api/v4/test" ) ?>?apikey=<?= $example_api_key ?>"><?= url( "/api/v4/test" ) ?>?apikey=<?= $example_api_key ?></a></li>
</ul>
</p>

<dl>

    <dt>IX-F Member List Export</dt>
    <dd>
        See <a href="http://ml.ix-f.net/">here for details on the IX-F Member List</a>
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
        <br><br>
    </dd>



</dl>