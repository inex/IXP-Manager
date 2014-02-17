# Upgrade Instructions

Please see the following page for upgrade instructions:

> https://github.com/inex/IXP-Manager/wiki/Installation-09-Upgrading-IXP-Manager

----------------------------------

# CHANGE LOG DEPRECATED

As of version v3.6.8, GitHub's [release management](https://github.com/inex/IXP-Manager/releases) will replace this changelog. GitHub's release manager allows is to track tags more efficiently, uses syntax highlighting and hot linking of GitHub commit SHAs, issues, etc. It should provide an all round better experience.

----------------------------------


# v3.6.6 (20140203)

Added example scripts to reconfigure Bird route servers via an API call and referenced
these in the wiki: https://github.com/inex/IXP-Manager/wiki/Route-Server

Integrated Travis-CI to test route server configuration generation:
 - https://github.com/inex/IXP-Manager/wiki/Continuous-Integration
 - https://github.com/inex/IXP-Manager/wiki/CI%20-%20Configuration%20Generation

Fixed Juniper support in l2database.

Pull request https://github.com/inex/IXP-Manager/pull/68 closed. This means if you
were relying on the `application.ini` parameter, you should check your login pages:

    ;; offset to use on auth pages (Bootstrap CSS classes)
    identity.biglogoconf.offset = offset4

This parameter is now obsolite and has been removed.

Schema update required:

    ALTER TABLE `switch` 
        ADD `serialNumber` VARCHAR(255) DEFAULT NULL AFTER `osVersion`;

- [NF] Ensure the API is aware of mainteance mode (9cffb45 - Barry O'Donovan - 2014-02-03)
- [IM] Test API function (e994547 - Barry O'Donovan - 2014-02-03)
- [BF] Use a default HTTP status code if none specified (b63c146 - Barry O'Donovan - 2014-02-03)
- [NF] Store switch serial number. Closes #109 (87138f4 - Barry O'Donovan - 2014-02-03)
- [DB] Add column to the switch table to store serial numbers (6a09719 - Barry O'Donovan - 2014-02-03)
- [DB] Doctrine creates these functions by default - relates to #41 (0223a6a - Barry O'Donovan - 2014-02-03)
- [HK] Missed this file from the last commit (2d33434 - Barry O'Donovan - 2014-02-03)
- [NF] Filter by contact rolls / display contact roles. Closes #98 (4082d3f - Barry O'Donovan - 2014-02-03)
- [NF] Adding ability to filter customers by state and closed / current. Fixes #97 (fb5f6ae - Barry O'Donovan - 2014-02-03)
- [NF] Adding ability to filter customers by type (see #97) (788030a - Barry O'Donovan - 2014-02-03)
- [IM] Only show current customers in top right drop down (e32e77d - Barry O'Donovan - 2014-02-03)
- [BF] Fix #80 - no overall aggregate graphs for errors / discards (398b57c - Barry O'Donovan - 2014-02-03)
- [DB] Address character set issues raised by issue #60 (7b49864 - Barry O'Donovan - 2014-02-03)
- [BF] Fixes #68 - positioning of logo on login pages [skip ci] (abd3b08 - Barry O'Donovan - 2014-02-01)
- [IM] Implement #29 - show contact / user last updated timestamp (eea853c - Barry O'Donovan - 2014-02-01)
- [BF] Fixes long standing #7 - nav bar covering content [skip-ci] (ef681b6 - Barry O'Donovan - 2014-02-01)
- [IM] Close #127 - allow >100 entries is DataTables pagination options [skip-ci] (4dde392 - Barry O'Donovan - 2014-02-01)
- [BF] Fix #108 - blank email when updating resold customer details (a231647 - Barry O'Donovan - 2014-02-01)
- [BF] Fix #129 - loaded ASN details from RIPE (d86a889 - Barry O'Donovan - 2014-02-01)
- [IM] Focus on username on login page [skip-ci] (37749d9 - Barry O'Donovan - 2014-02-01)
- [TP] Upgrade to jQuery 1.11 and press all JS/CSS bundles [skip-ci] (68f9d03 - Barry O'Donovan - 2014-02-01)
- [BF] added note about fixing junpier ex series support (ab142d2 - Nick Hilliard - 2014-01-31)
- [IM] Add new Bird config check and sample cron file (2ae9b1d - Barry O'Donovan - 2014-01-31)
- [BF] fixed support for juniper ex switches based on commits 96e61a6 and 6c3edc8 [IM] refactored mapping code (0848952 - Nick Hilliard - 2014-01-26)
- [IM] better debugging (d6541d6 - Nick Hilliard - 2014-01-26)
- [IM] use textual representation of OIDs instead of numerical [IM] updated debugging output format (f579136 - Nick Hilliard - 2014-01-26)
- [DB] Fixes #110 - ORM Designer type string used incorrectly instead of text (fdfc40e - Barry O'Donovan - 2014-01-24)
- [BF] Foreign ref update for fix Juniper detection (d884256 - Barry O'Donovan - 2014-01-13)
- [BF] Code errors fixed (cb63326 - Barry O'Donovan - 2014-01-13)
- [HK] Adding / playing with Travis for CI (ee383ff - Barry O'Donovan - 2014-01-02)
- [BF] check if juniper early on so we can do some juniper specific stuff later (8cbbbf2 - Nick Hilliard - 2013-12-16)
- [IM] le hacque to work around juniper semantics (fa302a3 - Nick Hilliard - 2013-12-14)
- [NF] Add sample scripts to reconfigured Bird route servers via API (3e7e36a - Barry O'Donovan - 2013-12-02)


Views updated / changed / add:

```
application/views/auth/login.phtml
application/views/auth/lost-password.phtml
application/views/auth/lost-username.phtml
application/views/auth/reset-password.phtml
application/views/contact/list-toolbar.phtml
application/views/customer/list-toolbar.phtml
application/views/customer/overview-tabs/contacts.phtml
application/views/frontend/js/list.js
application/views/header-css.phtml
application/views/header-js.phtml
```

# v3.6.5 (20131202)

Primarily some major improvements to the Bird route server configuration generation which is now
live in INEX. This includes:

 - querying RADB databases for all possible origin ASNs for an AS-SET / number 
   and this is checked by the Bird configuration generator; 
 - next hop hijacking no longer possible;
 - multiple vlan interfaces for one member on one vlan now supported;
 - configuration generation available via API now as well as the CLI; and
 - updated martian lists for IPv4 and v6 from IANA special purpose registries.


Database schema update required:

    CREATE TABLE irrdb_asn (
        id BIGINT AUTO_INCREMENT NOT NULL, 
        customer_id INT NOT NULL, 
        asn INT NOT NULL, 
        protocol INT NOT NULL, 
        first_seen DATETIME NOT NULL, 
        INDEX IDX_87BFC5569395C3F3 (customer_id), 
        UNIQUE INDEX custasn (asn, protocol, customer_id), 
        PRIMARY KEY(id)
    ) 
        DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
    
    ALTER TABLE irrdb_asn 
        ADD CONSTRAINT FK_87BFC5569395C3F3 
        FOREIGN KEY (customer_id) 
        REFERENCES cust (id);

The following views were updated / added:

    application/views/router-cli/collector/bird/header.cfg
    application/views/router-cli/server-testing/quagga-linux-setup-up.cfg
    application/views/router-cli/server/bird/header.cfg
    application/views/router-cli/server/bird/neighbor.cfg


- [HK] Ignore INEX conf files (3be071d - Barry O'Donovan - 2013-12-02)
- [IM] Double up on filters for the prefix analysis tool (for now) (d1a02b5 - Barry O'Donovan - 2013-11-27)
- [IM] It is easier to browse the file when we do not have hundreds of lines of prefixes (0f17612 - Barry O'Donovan - 2013-11-27)
- [IM] corrected some spelling mistakes (db87cae - Nick Hilliard - 2013-11-27)
- [IM] Add in AS path check and some sanity checks (f0377dc - Barry O'Donovan - 2013-11-27)
- [NF] Populate possible ASNs in AS paths for route collectors (c49f698 - Barry O'Donovan - 2013-11-27)
- [DB] Proper names (6563f07 - Barry O'Donovan - 2013-11-26)
- [DB] Do not forget the repo (7a54a11 - Barry O'Donovan - 2013-11-26)
- [DB] Table to hold ASNs that may appear in a rs client's path (03d5b0c - Barry O'Donovan - 2013-11-26)
- [IM] Better error logging (cd6c7e3 - Barry O'Donovan - 2013-11-26)
- [IM] More improvements for multiple members on the same vlan (8136ad1 - Barry O'Donovan - 2013-11-26)
- [IM] Prevent BGP NEXT_HOP Hijacking, move to templates and allow multiple interfaces per customer on the same vlan (fad673a - Barry O'Donovan - 2013-11-26)
- [NF] Router server conf now available via API and some improvements also (f218f28 - Barry O'Donovan - 2013-11-22)
- [IM] updated BIRD martian lists with IANA special purpose registry entries (54e6c71 - Nick Hilliard - 2013-11-20)

# v3.6.4 (20131118)

Generation of route collector configurations has been updated to allow it to be
accessed via APIv1. Bird configuration target also added.

Significant changes from the previous incarnation include:

- route collector generation can now take an optional Smarty configuration file
  just as the route server generators do. 
- The ASN parameter has been deprecated in favour of the above.

See: https://github.com/inex/IXP-Manager/wiki/Route-Collector

Templates added / changed:

    application/views/router-cli/collector/bird/footer.cfg
    application/views/router-cli/collector/bird/header.cfg
    application/views/router-cli/collector/bird/index.cfg
    application/views/router-cli/collector/bird/neighbor.cfg
    application/views/router-cli/collector/quagga/bgp.cfg
 
- [IM] Fixes to Bird RC configuration (5aa25dc - Barry O'Donovan - 2013-11-18)
- [IM] Remove config variable in favour of Smarty config files (b23d19e - Barry O'Donovan - 2013-11-18)
- [NF] Route collector via Bird - first pass for quarantine system (629175d - Barry O'Donovan - 2013-11-15)
- [NF] Route collector configuration generation now also available via APIv1 (8cb6112 - Barry O'Donovan - 2013-11-15)
- [BF] RIPE does not like blank lines... (c0528bc - Barry O'Donovan - 2013-11-15)
- [BF] Looks like RIPE no longer supports this keyword - LONGACK is now the default (fefdc56 - Barry O'Donovan - 2013-11-15)


# v3.6.3 (20131115)

 - [IM] Final pass at RIR object generation (34f2ee3 - Barry O'Donovan - 2013-11-15)

Views updated:

    application/modules/apiv1/views/_skins/inex/rir/tmpl/as-set-inex-connected.tpl
    application/modules/apiv1/views/_skins/inex/rir/tmpl/autnum-as43760.tpl

# v3.6.2 (20131113)

- [NF] Example of separate RS AS sets for v4 and v6 (4b2d759 - Barry O'Donovan - 2013-11-13)
- [IM] Include new module views in the changed views finder (61c0de7 - Barry O'Donovan - 2013-11-13)- [NF] Add RIR objects for IXP connected ASs and IXP route server connected ASs (6bf476c - Barry O'Donovan - 2013-11-13)

Views updated / changed in this release:

    application/modules/apiv1/views/rir/tmpl/as-set-ixp-rs-v4.tpl
    application/modules/apiv1/views/rir/tmpl/as-set-ixp-rs-v6.tpl


# v3.6.1 (20131113)

This release contains a number of small bug fixes, minor features and a new
API function to create and email RIR objects. 

See: https://github.com/inex/IXP-Manager/wiki/RIR-Objects



Schema update required:

    ALTER TABLE api_keys 
        CHANGE allowedIPs allowedIPs VARCHAR(65500) DEFAULT NULL;
    
    ALTER TABLE company_billing_detail 
        ADD billingAddress3 VARCHAR(255) DEFAULT NULL AFTER billingAddress2;

New application.ini parameter (which is not required):
    
    ; the traffic_daily table can get pretty full and most of the long term information
    ; are in the MRTG / other stats files anyway. If you want to keep this data in the
    ; database, set the following to false. If it is true, when the daily task runs
    ; to populate this table, it will also delete any entries older than
    ; cli.traffic_differentials.stddev_calc_length days (this parameter is set above).
    
    cli.traffic_daily.delete_old = true

New optional application.ini parameter for RIR object generation:

    ;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;;
    ;;
    ;; Generated RIR objects  
    ;;
    ;; See: https://github.com/inex/IXP-Manager/wiki/RIR-Objects
    ;;
    
    ;rir.ripe_password = 'supersecret'


Views updated / changed in this release:

    application/modules/apiv1/views/_skins/inex/rir/tmpl/as-set-inex-connected.tpl
    application/modules/apiv1/views/_skins/inex/rir/tmpl/as-set-inex-rs.tpl
    application/modules/apiv1/views/_skins/inex/rir/tmpl/autnum-as2128.tpl
    application/modules/apiv1/views/_skins/inex/rir/tmpl/autnum-as43760.tpl
    application/modules/apiv1/views/rir/tmpl/as-set-ixp-connected.tpl
    application/modules/apiv1/views/rir/tmpl/as-set-ixp-rs.tpl
    application/modules/apiv1/views/rir/tmpl/autnum.tpl
    application/views/_skins/lonap/public/member-details.phtml
    application/views/customer/email/billing-details-changed.phtml
    application/views/customer/forms/billing-registration.phtml
    application/views/router-cli/server/bird/neighbor.cfg



- [NF] Example of separate RS AS sets for v4 and v6 (4b2d759 - Barry O'Donovan - 2013-11-13)
- [IM] Include new module views in the changed views finder (61c0de7 - Barry O'Donovan - 2013-11-13)- [NF] Add RIR objects for IXP connected ASs and IXP route server connected ASs (6bf476c - Barry O'Donovan - 2013-11-13)
- [NF] Add RIR objects for IXP connected ASs and IXP route server connected ASs (2c9f7bb - Barry O'Donovan - 2013-11-13)
- [NF] Route server AS object generation for RIRs (df8929b - Barry O'Donovan - 2013-11-13)
- [BF] Harden variable checks for sflow presentation script (d601dd3 - Barry O'Donovan - 2013-11-13)
- [NF] Working implementation or rIR object generation for autnum: (d1e1d9b - Barry O'Donovan - 2013-11-12)
- [NF] Early stages of code to update and IXP's RIPE database objects (f74c000 - Barry O'Donovan - 2013-11-12)
- [IM] added --insanedebug to allow debugging of all sflowtool input (c94877f - Nick Hilliard - 2013-11-04)
- [BF] fixed #91 (sflow graph coloring is reverse to mrtg graph coloring) (efe18df - Nick Hilliard - 2013-11-04)
- [BF] Issue with member details fixed (84dcdd8 - Barry O'Donovan - 2013-11-04)
- [BF] Untested fix for issue #100 (27a8d49 - Barry O'Donovan - 2013-11-02)
- [IM] Forgot to add the new field to the email notification (cf59ee3 - Barry O'Donovan - 2013-11-02)
- [IM] Add extra address field for billing - Fixes #96 (8200d34 - Barry O'Donovan - 2013-11-02)
- [DB] Schema update for issue #96 (8e8e3f7 - Barry O'Donovan - 2013-11-02)
- [NF] Delete old traffic_daily entries that are no longer needed (31236e7 - Barry O'Donovan - 2013-11-01)
- [NF] Allow the deletion of IP addresses (if not in use) (9033095 - Barry O'Donovan - 2013-10-28)
- [IM] Record last API key usage (54d8a83 - Barry O'Donovan - 2013-10-18)



# v3.6.0 (20131018)

Add API V1 with proof of concept API functionality for mailing list management. 

See: https://github.com/inex/IXP-Manager/wiki/API-V1
See: https://github.com/inex/IXP-Manager/wiki/Mailing-List-Management#api-v1-interface


Schema change required:

    CREATE TABLE api_keys (
        id BIGINT AUTO_INCREMENT NOT NULL, 
        user_id INT NOT NULL, 
        apiKey VARCHAR(255) NOT NULL, 
        expires DATETIME DEFAULT NULL, 
        allowedIPs VARCHAR(65500) DEFAULT NULL, 
        created DATETIME NOT NULL, 
        lastseenAt DATETIME DEFAULT NULL, 
        lastseenFrom VARCHAR(255) DEFAULT NULL, 
    
        UNIQUE INDEX UNIQ_87A61477800A1141 (apiKey), 
        INDEX IDX_87A61477A76ED395 (user_id), 
        PRIMARY KEY(id)
    ) 
        DEFAULT CHARACTER SET utf8 
        COLLATE utf8_unicode_ci 
        ENGINE = InnoDB;
    
    ALTER TABLE api_keys 
        ADD CONSTRAINT FK_87A61477A76ED395 
        FOREIGN KEY (user_id) 
        REFERENCES user (id);


Templates changed / added:

    application/views/api-key/list-row-menu.phtml
    application/views/cli/mailing-list-sync-script.sh => application/views/mailing-list-cli/mailing-list-sync-script.sh
    application/views/frontend/view.phtml
    application/views/header.phtml
    application/views/mailing-list-cli/mailing-list-sync-script-apiv1.sh
    application/views/mailing-list-cli/mailing-list-sync-script.sh
    application/views/profile/index.phtml



- [I+] Keep Curl quiet (4235ead - Barry O'Donovan - 2013-10-18)
- [BF] Fix tmp path (857e1db - Barry O'Donovan - 2013-10-18)
- [BF] Fix verbosity (c76482d - Barry O'Donovan - 2013-10-18)
- [I+] Update references to mailing list CLI actions (e64ea94 - Barry O'Donovan - 2013-10-18)
- [I+] Update references to mailing list CLI actions (98f76d0 - Barry O'Donovan - 2013-10-18)
- [BF] Update foreign ref to OSS-Framework for reset password fix (8cf5acb - Barry O'Donovan - 2013-10-18)
- [BF] Update foreign ref to OSS-Framework for reset password fix (7103bd0 - Barry O'Donovan - 2013-10-18)
- [IM] Delete a user's API keys when deleting the user (408b228 - Barry O'Donovan - 2013-10-18)
- [N+] Complete API V1 mailing list management functions. (f33f765 - Barry O'Donovan - 2013-10-18)
- [IM] Use the correct password hashing (59ae043 - Barry O'Donovan - 2013-10-18)
- [NF] Mailing List management via APIv1 (WIP) (851d823 - Barry O'Donovan - 2013-10-16)
- [N+] POC of a sample API call (220f049 - Barry O'Donovan - 2013-10-16)
- [N+] API key management complete (414b1ec - Barry O'Donovan - 2013-10-15)
- [NF] API (v1) - work in progress (777a64b - Barry O'Donovan - 2013-10-15)
- [DB] Add schema for API keys (38d681e - Barry O'Donovan - 2013-10-15)


# v3.5.4 (20131012)

Migrate to new sflow backend for P2P graphs - see #82.


Updated views:

    application/views/statistics/p2p-single.phtml
    application/views/statistics/p2p.phtml

- [IM] Complete migration to new sflow p2p graphs backend API - relates to #82 (2bc365a - Barry O'Donovan - 2013-10-12)
- [I+] And first pass at drill down for p2p (4b34ba7 - Barry O'Donovan - 2013-10-05)
- [IM] First pass at new sflow p2p graphs backend API - relates to #82 (7b36ee2 - Barry O'Donovan - 2013-10-05)
- [NF] Include location information with route server / collector VLAN interface details - closes #83 (2f0411d - Barry O'Donovan - 2013-10-03)

# v3.5.3 (20131003)

Route Server configuration generation for Bird and Quagga as well as test platform created.

See: https://github.com/inex/IXP-Manager/wiki/Route-Server

See: https://github.com/inex/IXP-Manager/wiki/Route-Server-Testing


New / changed views:

    application/views/router-cli/server-testing/quagga-linux-setup-down.cfg
    application/views/router-cli/server-testing/quagga-linux-setup-up.cfg
    application/views/router-cli/server-testing/quagga.cfg
    application/views/router-cli/server/bird/footer.cfg
    application/views/router-cli/server/bird/header.cfg
    application/views/router-cli/server/bird/neighbor.cfg
    application/views/router-cli/server/quagga/footer.cfg
    application/views/router-cli/server/quagga/header.cfg
    application/views/router-cli/server/quagga/neighbor.cfg


- [NF] Route server testing framework (fcc4f05 - Barry O'Donovan - 2013-10-03)
- [BF] Proper check for false (3cd56a3 - Barry O'Donovan - 2013-10-03)
- [IM] Fixes to route server Quagga configuration which includes: (36cb63d - Barry O'Donovan - 2013-10-03)
- [IM] Language (02ad8b8 - Barry O'Donovan - 2013-10-03)
- [IM] Make IRRDB filtering optional for Bird. Quagga TBD. (baf909a - Barry O'Donovan - 2013-09-24)
- [IM] New argument to limit config generation to single customer (4d86883 - Barry O'Donovan - 2013-09-24)
- [BF] Route server configuration generation - first (untested) pass (88c9feb - Barry O'Donovan - 2013-09-24)
- [IM] Support for --config parameter (090656e - Barry O'Donovan - 2013-09-24)




# v3.5.2 (20130930)

Minor new features, bug fixes and improvements.

- [BF] fixed IXP customer labels for peak output [IM] Added IXP Manager name to graphs (b4c5429 - Nick Hilliard - 2013-09-25)
- [IM] support dot1qVlanFdbId/jnxExVlanTag vlan mapping (a9a0c61 - Nick Hilliard - 2013-09-24)
- [IM] refactor code to abstract some functions and clean things up (4f24c0b - Nick Hilliard - 2013-09-24)
- [IM] add warning in debug mode if vlan is not specified (6a9086a - Nick Hilliard - 2013-09-23)
- [IM] added command-line options for debugging [IM] added support for PBRIDGE-MIB with fallback to BRIDGE-MIB [IM] added support for vlans (a761ffc - Nick Hilliard - 2013-09-19)
- [NF] Encapsulated git command for #69 (ada8824 - Barry O'Donovan - 2013-09-18)
- [NF] Make Smokeping graphs available to members also (48d0d71 - Barry O'Donovan - 2013-09-18)

Views changed since v3.5.1:

    application/views/auth/reset-password.phtml
    application/views/customer/detail.phtml
    application/views/customer/overview-tabs/ports/port.phtml
    application/views/peering-manager/index-potential-bilateral.phtml
    application/views/profile/index.phtml
    application/views/smokeping/member-drilldown.phtml
    application/views/statistics/member-drilldown.phtml

Changes to `application.ini`:

- `identity.switch_domain` can be removed as it is no longer used (unless you are using it in your own custom skins).


# v3.5.1 (201309
