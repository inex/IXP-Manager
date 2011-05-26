
{foreach from=$locations item=loc key=name}

define hostgroup {ldelim}
        hostgroup_name          INEX-Switches-{$name}
        alias                   INEX Switches at {$name}
        members                 {php}echo implode( ', ', $this->get_template_vars( 'loc') );{/php}

{rdelim}

{/foreach}


define hostgroup {ldelim}
        hostgroup_name          INEX-Production-Switches
        alias                   INEX Production Switches (all)
        members                 {php}echo implode( ', ', $this->get_template_vars( 'all') );{/php}

{rdelim}


define hostgroup {ldelim}
        hostgroup_name          INEX-Switches-Brocade
        alias                   INEX Brocade Switches
        members                 {php}echo implode( ', ', $this->get_template_vars( 'vendor_brocade') );{/php}

{rdelim}


define hostgroup {ldelim}
        hostgroup_name          INEX-Switches-Cisco
        alias                   INEX Cisco Switches
        members                 {php}echo implode( ', ', $this->get_template_vars( 'vendor_cisco') );{/php}

{rdelim}


define hostgroup {ldelim}
        hostgroup_name          INEX-Switches-MRV
        alias                   INEX MRV Switches
        members                 {php}echo implode( ', ', $this->get_template_vars( 'vendor_mrv') );{/php}

{rdelim}



define service{ldelim}
        use                             inex-production-switch-service
        hostgroup_name                  INEX-Switches-Brocade
        service_description             Chassis
        check_command                   check_foundry_chassis!fjvrGzHqr
{rdelim}

define service  {ldelim}
        use                             inex-production-switch-service
        service_description             Temperature
        hostgroup_name                  INEX-Switches-Cisco
        check_command                   check_cisco_temperature!fjvrGzHqr!32!38
{rdelim}



define service{ldelim}
        use                             inex-production-switch-service
        hostgroup_name                  INEX-Production-Switches
        service_description             ping - IPv4
        check_command                   check_ping_ipv4!10!100.0,10%!200.0,20%
{rdelim}

define service  {ldelim}
        use                             inex-production-switch-service
        service_description             SSH
        hostgroup_name                  INEX-Production-Switches
        check_command                   check_ssh
{rdelim}

