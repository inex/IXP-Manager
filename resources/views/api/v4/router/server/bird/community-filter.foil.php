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
 * Copyright (C) 2009-2016 Internet Neutral Exchange Association Company Limited By Guarantee.
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

##
## Standard IXP community filter
##

function ixp_community_filter(int peerasn)
{
        if !(source = RTS_BGP) then
                return false;

        # default community filtering schema doesn't support ASN32, as there
        # are only 6 octets available for numbering.  We need
        # draft-raszuk-wide-bgp-communities to become reality.

        # barryo - draft-ietf-large-community is a new (better?) option here

        if peerasn > 65535 then
                return true;

        # Implement widely used community filtering schema.
        if (0, peerasn) ~ bgp_community then
                return false;
        if (routeserverasn, peerasn) ~ bgp_community then
                return true;
        if (0, routeserverasn) ~ bgp_community then
                return false;

        return true;
}
