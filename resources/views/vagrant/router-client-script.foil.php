#! /usr/bin/env bash
#
# Copyright (C) 2009 - 2024 Internet Neutral Exchange Association Company Limited By Guarantee.
# All Rights Reserved.
#
# This file is part of IXP Manager.
#
# IXP Manager is free software: you can redistribute it and/or modify it
# under the terms of the GNU General Public License as published by the Free
# Software Foundation, version 2.0 of the License.
#
# IXP Manager is distributed in the hope that it will be useful, but WITHOUT
# ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or
# FITNESS FOR A PARTICULAR PURPOSE.  See the GNU General Public License for
# more details.
#
# You should have received a copy of the GNU General Public License v2.0
# along with IXP Manager.  If not, see:
#
# http://www.gnu.org/licenses/gpl-2.0.html
#

mkdir -p <?= $t->directory ?>
chown -R vagrant: <?= $t->directory ?>

mkdir -p /var/log/bird
mkdir -p /var/run/bird



php /vagrant/artisan vagrant:generate-client-router-configurations

<?php foreach( $t->confNames as $confName ): ?>

<?php
    $confFile = "{$t->directory}/{$confName}.conf";
    $socket   = "/var/run/bird/{$confName}.ctl";
?>

# are we running or do we need to be started?
/usr/sbin/birdc -s <?= $socket ?> show memory &>/dev/null

if [[ $? -ne 0 ]]; then
    /usr/sbin/bird -c <?= $confFile ?> -s <?= $socket ?>  &>/dev/null

else
    /usr/sbin/birdc -s <?= $socket ?> configure  &>/dev/null
fi

<?php endforeach; ?>



