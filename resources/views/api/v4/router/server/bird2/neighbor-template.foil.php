<?php
/*
 * Bird Route Server Configuration Template
 *
 *
 * You should not need to edit these files - instead use your own custom skins. If
 * you can't effect the changes you need with skinning, consider posting to the mailing
 * list to see if it can be achieved / incorporated.
 *
 * Skinning: https://ixp-manager.readthedocs.io/en/latest/features/skinning.html
 *
 * Copyright (C) 2009 - 2019 Internet Neutral Exchange Association Company Limited By Guarantee.
 * All Rights Reserved.
 *
 * This file is part of IXP Manager.
 *
 * IXP Manager is free software: you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation, version v2.0 of the License.
 *
 * IXP Manager is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
 * FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
 * more details.
 *
 * You should have received a copy of the GNU General Public License v2.0
 * along with IXP Manager.  If not, see:
 *
 * http://www.gnu.org/licenses/gpl-2.0.html
 */
?>

########################################################################################
########################################################################################
#
# Route Server client configuration
#
########################################################################################
########################################################################################


template bgp tb_rsclient {
    local as routeserverasn;
    source address routeserveraddress;
    strict bind yes;

    # give RPKI-RTR a chance to start and populate
    # (RPKI is /really/ quick)
    connect delay time 30;

<?php if( $t->router->rfc1997_passthru ): ?>    interpret communities off;  # enable rfc1997 well-known community pass through
<?php endif; ?>

    <?= $t->ipproto ?> {
        export all;
<?php if( $t->router->protocol == 6 ): ?>
        missing lladdr ignore;
<?php endif; ?>
    };

    rs client;
}
