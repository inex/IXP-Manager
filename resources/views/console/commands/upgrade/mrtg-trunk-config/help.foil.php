
ixp-manager:upgrade:mrtg-trunk-config
=====================================

Usage:
    ixp-manager:upgrade:mrtg-trunk-config [--no-backup] ini-file

This command takes MRTG trunk definitiions from an INI file in the the old Zend
configuration file (application/configs/application.ini) format and migrates
them to a new one (config/grapher_trunks.php). It will backup any existing
config/grapher_trunks.php file unless the option --no-backup is passed.

The easiest thing to do is extract your trunk definitiions from
