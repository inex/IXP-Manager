

<!-- <div id="bd"> -->
</div>


<div id="ft">
    <div id="footer_full">

        <p>
            IXP Manager V.1.0
            &nbsp;&nbsp;|&nbsp;&nbsp;
            Your IP: {$smarty.server.REMOTE_ADDR}
            {if isset( $session->last_login_from ) and $session->last_login_from neq ''}
                Last login from {$session->last_login_from} at {$session->last_login_at|date_format:"%Y-%m-%d %H:%M:%S"}
            {/if}

            {if $hasIdentity and isset( $config.change_log.enabled ) and $config.change_log.enabled}
                &nbsp;&nbsp;|&nbsp;&nbsp;
                {if isset( $change_log_has_updates ) and $change_log_has_updates}
                    <a href="{genUrl controller='change-log' action='read' items='new'}">Change Log</a> (<span style="color: red">{$change_log_has_updates} new</span>)
                {else}
                    <a href="{genUrl controller='change-log' action='read'}">Change Log</a>
                {/if}
            {/if}
            <br />
            Copyright &copy; Internet Neutral Exchange Association Ltd. 2009 - {$smarty.now|date_format:"%Y"}. Licensed under GPL v2.0. Find us on <a href="https://github.com/inex">github.com/inex</a>.

        </p>

    </div>
</div>

<!-- <div id="doc4"> -->
</div>

</body>
</html>
