{if isset( $config.use_minified_js ) and $config.use_minified_js}
    <script type="text/javascript" src="{genUrl}/js/min.bundle-v12.js"></script>
{else}
    <script type="text/javascript" src="{genUrl}/js/200-jquery-1.7.js"></script>
    <script type="text/javascript" src="{genUrl}/js/210-jquery-ui-1.8.16.custom.js"></script>
    <script type="text/javascript" src="{genUrl}/js/220-jquery.dataTables.js"></script>
    <script type="text/javascript" src="{genUrl}/js/230-jquery.contextMenu.js"></script>
    <script type="text/javascript" src="{genUrl}/js/240-jquery.json-2.3.js"></script>
    <script type="text/javascript" src="{genUrl}/js/245-jquery-cookie.js"></script>
    <script type="text/javascript" src="{genUrl}/js/250-jquery-colorbox.js"></script>
    <script type="text/javascript" src="{genUrl}/js/260-wysihtml5-0.3.0_rc1.js"></script>
    <script type="text/javascript" src="{genUrl}/js/300-chosen.jquery.js"></script>
    <script type="text/javascript" src="{genUrl}/js/310-throbber.js"></script>
    <script type="text/javascript" src="{genUrl}/js/700-php.js"></script>
    <script type="text/javascript" src="{genUrl}/js/800-bootstrap.js"></script>
    <script type="text/javascript" src="{genUrl}/js/830-bootstrap-wysihtml5.js"></script>
    <script type="text/javascript" src="{genUrl}/js/900-ixpmanager.js"></script>
{/if}
