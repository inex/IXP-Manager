ixp-manager:upgrade :: utility used for upgrading IXP Manager

Usage:
    ixp-manager:upgrade [options] [stage]

Where stage is one of:

    migrate-trunk-config - take MRTG trunk definitiions from the old Zend
        configuration file (application/configs/application.ini) and
        migrate them to a new one (config/grapher_trunks.php). It will
        backup any existing config/grapher_trunks.php file unless the
        option --no-backup is passed.
