#! /usr/bin/env bash

for handle in as112-vix1-ipv4 as112-vix1-ipv6 as112-vix2-ipv4 as112-vix2-ipv6; do

    /vagrant/tools/vagrant/scripts/ixpm-reconfigure-routers-bird2.sh -h $handle

done
