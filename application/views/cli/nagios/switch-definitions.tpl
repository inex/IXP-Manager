#
# This file contains static definitions for use with the IXP Manager
# Nagios configuration templates and is following by dynamic host
# configurations.
#
# To edit the static definitions, edit:
#    applicationviews/cli/nagios/switch-definitions.tpl
# rather than this file directly as it is automatically generated.
#

define host{ldelim}
        name                            inex-production-switch
        notifications_enabled           1               ; Host notifications are enabled
        event_handler_enabled           0               ; Host event handler is enabled
        flap_detection_enabled          1               ; Flap detection is enabled
        process_perf_data               1               ; Process performance data
        retain_status_information       1               ; Retain status information across program restarts
        retain_nonstatus_information    1               ; Retain non-status information across program restarts

        checks_enabled                  1
        check_command                   check-host-alive
        max_check_attempts              3               ; number of not 'UP' checks to register as hard
        check_interval                  5               ; time between checks
        retry_interval                  1               ; time between checks if host is not 'UP'

        check_period                    24x7

        notification_interval           60
        notification_period             24x7
        notification_options            u,d,r

        low_flap_threshold              0
        high_flap_threshold             0

        register                        0               ; DONT REGISTER THIS DEFINITION - ITS NOT A REAL HOST, JUST A TEMPLATE!

        contact_groups                  inex-operations
{rdelim}


define service{ldelim}
        name                            inex-production-switch-service    ; The 'name' of this service template, referenced in other service definitions
        active_checks_enabled           1               ; Active service checks are enabled
        passive_checks_enabled          1               ; Passive service checks are enabled/accepted
        parallelize_check               1               ; Active service checks should be parallelized (disabling this can lead to major performance problems)
        obsess_over_service             1               ; We should obsess over this service (if necessary)
        check_freshness                 0               ; Default is to NOT check service 'freshness'
        notifications_enabled           1               ; Service notifications are enabled
        event_handler_enabled           0               ; Service event handler is enabled
        flap_detection_enabled          1               ; Flap detection is enabled
        process_perf_data               1               ; Process performance data
        retain_status_information       1               ; Retain status information across program restarts
        retain_nonstatus_information    1               ; Retain non-status information across program restarts
        contact_groups                  inex-operations

        max_check_attempts              3
        normal_check_interval           5
        retry_check_interval            1
        check_period                    24x7

        notification_interval           60
        notification_period             24x7
        notification_options            w,u,c,r

        low_flap_threshold              0
        high_flap_threshold             0

        is_volatile                     0

        register                        0               ; DONT REGISTER THIS DEFINITION - ITS NOT A REAL SERVICE, JUST A TEMPLATE!
{rdelim}


#
# Dynamically generated configurations follow:
#

