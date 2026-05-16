<?php
/*
 * Bird Route Collector Configuration Template
 *
 *
 * You should not need to edit these files - instead use your own custom skins. If
 * you can't effect the changes you need with skinning, consider posting to the mailing
 * list to see if it can be achieved / incorporated.
 *
 * Skinning: https://ixp-manager.readthedocs.io/en/latest/features/skinning.html
 *
 * Copyright (C) 2009 - 2025 Internet Neutral Exchange Association Company Limited By Guarantee.
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
# Collector peer configuration
#
########################################################################################
########################################################################################


template bgp tb_rcpeer {
    local as routerasn;
    source address routeraddress;
    strict bind yes;
<?php if ( $p_passive = config('custom.router.' . $t->handle . '.passive') ): ?>
    passive <?= $p_passive ?>;
<?php endif; ?>
<?php if ( $p_restart_time = config('custom.router.' . $t->handle . '.err_wait_time') ): ?>
    error wait time <?= $p_restart_time ?>;
<?php endif; ?>

<?php if( config('app.env') === 'vagrant' ): ?>
    # needed for loopback interface binding
    multihop;
<?php endif; ?>

    # give RPKI-RTR a chance to start and populate
    # (RPKI is /really/ quick)
    connect delay time 30;

    <?= $t->ipproto ?> {
<?php if ( $p_prefix_limit = config('custom.router.' . $t->handle . '.prefix_limit') ): ?>
    receive limit <?= $p_prefix_limit ?>;
<?php endif; ?>

        export none;
    };

}
