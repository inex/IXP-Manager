<?php
/*
 * Bird Route Collector Configuration Template
 *
 *
 * You should not need to edit these files - instead use your own custom skins. If
 * you can't effect the changes you need with skinning, consider posting to the mailing
 * list to see if it can be achieved / incorporated.
 *
 * Skinning: hSkinning: https://docs.ixpmanager.org/features/skinning/
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
<?= $this->insert('api/v4/router/collector/bird3/header')   ?>

<?= $this->insert('api/v4/router/collector/bird3/community-filtering-definitions')   ?>

<?= $this->insert('api/v4/router/collector/bird3/rpki')   ?>

<?= $this->insert('api/v4/router/collector/bird3/filter-transit-networks')   ?>

<?= $this->insert('api/v4/router/collector/bird3/neighbor-template', [ 'ipproto' => $t->router->protocol === 6 ? 'ipv6' : 'ipv4' ] )   ?>

<?= $this->insert('api/v4/router/collector/bird3/neighbors', [ 'ipproto' => $t->router->protocol === 6 ? 'ipv6' : 'ipv4' ] ) ?>

<?= $this->insert('api/v4/router/collector/bird3/footer')   ?>
