#! /usr/bin/env bash

if [ "$EUID" -ne 0 ]
  then echo "Please run as root"
  exit
fi

# Kill all bird instances
#killall bird
#rm /srv/clients/*.conf

# Clients
sudo -u vagrant php /vagrant/artisan vagrant:generate-client-router-configurations
chmod a+x /srv/clients/start-reload-clients.sh
/srv/clients/start-reload-clients.sh

# Route servers
/vagrant/tools/vagrant/scripts/rs-api-reconfigure-all.sh

# Route collectors
/vagrant/tools/vagrant/scripts/rc-reconfigure.sh

# AS112
/vagrant/tools/vagrant/scripts/as112-reconfigure-bird2.sh

# Birdseye
php /vagrant/artisan vagrant:generate-birdseye-configurations


