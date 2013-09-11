
# Upgrade Instructions

Please see the following page for upgrade instructions:

> https://github.com/inex/IXP-Manager/wiki/Installation-09-Upgrading-IXP-Manager

# v3.4.10 (20130911)

Continue adding Nagios improvements - this time for monitoring members.

One of the templates (`views/nagios-cli/conf/members-service-rcmon.cfg`) monitors 
member BGP sessions to the route collector. For this, an SNMP v2 password is required
and as such the following has been added to `application.ini.dist`:

    ; Used by nagios-cli.gen-members-conf action to monitor route collector BGP sessions
    ;router.collector.conf.snmppasswd = 'xxxx'

If it is not set, that service will not be added to Nagios by default. Remember - skin
the Nagios configuration directory rather than editing those files directly.

- [BF] Ignore hosts that are not connected (870f6c9 - Barry O'Donovan - 2013-09-11)
- [IM] Reimplement busy hosts flag for Nagios (78e4e6a - Barry O'Donovan - 2013-09-11)
- [IM] Reimplement busy hosts flag for Nagios (8a225b1 - Barry O'Donovan - 2013-09-11)
- [BF] Only add customer hostgroup if there are hosts (76b7b3e - Barry O'Donovan - 2013-09-11)
- [BF] Check correct protocol enabled and limit hosts to those that can be pinged (80af008 - Barry O'Donovan - 2013-09-11)
- [BF] Typo on default route masklen (5e6da1d - Barry O'Donovan - 2013-09-11)
- [IM] Nagios member monitoring (3c7cd7b - Barry O'Donovan - 2013-09-11)
- [IM] Route collector Nagios monitoring service (dd6191a - Barry O'Donovan - 2013-09-11)
- [IM] Utility function to provide an array of all VLAN interfaces on a given IXP (5d7d210 - Barry O'Donovan - 2013-09-11)


# v3.4.9 (20130909)

Improve and refactor Nagios configuration generation for IXP switches.

See: https://github.com/inex/IXP-Manager/wiki/Nagios

Schema update required:

    ALTER TABLE vendor 
        ADD shortname VARCHAR(255) DEFAULT NULL, 
        ADD nagios_name VARCHAR(255) DEFAULT NULL;


- [IM] Update Nagios switch config generator with changes to vendor table (3768516 - Barry O'Donovan - 2013-09-06)
- [DB] Update fixtures to reflect changes to vendor table (4468595 - Barry O'Donovan - 2013-09-06)
- [DB] Add two new columns to vendor table (a12bb02 - Barry O'Donovan - 2013-09-06)
- [IM] Nagios config more dynamic (051aa57 - Barry O'Donovan - 2013-09-06)
- [HK] s/inex/ixp (f84d075 - Barry O'Donovan - 2013-09-06)
- [HK] Migrate Nagios config to dedicated controller (47e3164 - Barry O'Donovan - 2013-09-06)


# v3.4.8 (20130905)


Add TACACS (and RADIUS) configuration generation.

See: https://github.com/inex/IXP-Manager/wiki/TACACS
See: https://github.com/inex/IXP-Manager/wiki/RADIUS


New ''application.ini'' parameters:

    ;; The Peering Manager allows your members to send peering requests to other members
    ;; that contain all the necessary peering details.
    ;;
    ;; See: https://github.com/inex/IXP-Manager/wiki/Peering-Manager
    ;;
    ;; For testing / experimentation you can enabled test mode below and, when enabled, all
    ;; peering requests will be send to the testemail. 
    ;;
    ;; NB: This does not apply to any BCC emails you add. The CC recipient in the request
    ;; dialog will be ignored in test mode.
    ;;
    ;; Normally, the peering manager adds a note to the peer's notes and sets a request last 
    ;; sent date when a peering request is sent. In test mode, this will not happen. 
    ;; If you want this to happen in test mode, set testnote and testdate to true below.
    
    peeringmanager.testmode = false
    peeringmanager.testemail = "user@example.com"
    peeringmanager.testnote = false
    peeringmanager.testdate = false

- [NF] TACACS configuration imported from Perl scripts (first pass) (3ed8738 - Barry O'Donovan - 2013-09-05)
- [IM] Make Smokeping more configurable for multi-IXP mode (1437274 - Barry O'Donovan - 2013-09-04)
- [IM] Peering Manager - disable recording or requests in test mode (111f1ff - Barry O'Donovan - 2013-09-04)
- [IM] Allow peering manager test mode via application.ini (93e26e3 - Barry O'Donovan - 2013-09-04)
- [BF] Do not forget to calculate the IXP statistics (7b5eb17 - Barry O'Donovan - 2013-09-04)


# v3.4.7 (20130902)

Add ARPA DNS zone generation.

See: https://github.com/inex/IXP-Manager/wiki/ARPA-DNS-Population


- [IM] Doc updates and only include enabled interfaces (0d3d964 - Barry O'Donovan - 2013-09-02)
- [NF] Arpa DNS Zone generation (7442925 - Barry O'Donovan - 2013-09-02)
- [IM] AS112 documentation is now dynamic for available AS112 services (764c43e - Barry O'Donovan - 2013-09-02)


# v3.4.6 (20130902)

Add AS112 configuration generation support.

See https://github.com/inex/IXP-Manager/wiki/AS112

- [IM] Change file to path (6b68b10 - Barry O'Donovan - 2013-09-02)
- [NF] AS112 automated configuration (0fba814 - Barry O'Donovan - 2013-09-02)
- [BF] Need explicit permit to overcome the implicit deny (aaef856 - Barry O'Donovan - 2013-09-02)

# v3.4.5 (20130901)

Add route collector configuration generator support. 

See https://github.com/inex/IXP-Manager/wiki/Route-Collector

- [IM] Route Collector - allow user to specify ASN on the command line (677536d - Barry O'Donovan - 2013-09-02)
- [IM] Route Collector - allow user to specify target on the command line (5bc50df - Barry O'Donovan - 2013-09-02)
- [IM] Route Collector - allow limiting by protocol on command line (0662a30 - Barry O'Donovan - 2013-09-02)
- [IM] Update / improve route collector code (ad5c0b9 - Barry O'Donovan - 2013-08-31)
- [IM] Check for defined route collector IPv4 address (53d2466 - Barry O'Donovan - 2013-08-31)
- [NF] First pass at new route collector generation (3eb62cf - Barry O'Donovan - 2013-08-31)
- [IM] New CLI utility fn for resolving VLAN IDs (943ada9 - Barry O'Donovan - 2013-08-31)
- [IM] Move CLI IXP resolution up to action class (b642cbc - Barry O'Donovan - 2013-08-31)
- [BF] Incorrect calculation of max values for aggregate ports (7ddf4bb - Barry O'Donovan - 2013-08-26)



# v3.4.4 (20130831)

- [BF] not all switches support this (91a04fa - Barry O'Donovan - 2013-08-09)
- [BF] Bad logic in admin overview graphs (5d7f2a8 - Barry O'Donovan - 2013-08-07)
- [BF] Some leftovers from multi IXP graph work (07f6642 - Barry O'Donovan - 2013-08-07)
- [NF] Support for polling whether a port is a lag or not (baa5842 - Barry O'Donovan - 2013-08-07)
- [DB] Add new field to switchport to identify LAG ports (beba464 - Barry O'Donovan - 2013-08-07)
- [BF] s/findOneBy/find (4d5ec32 - Barry O'Donovan - 2013-08-07)
- [BF] Weed out mis-spellings causing issues (ecb788f - Barry O'Donovan - 2013-08-07)



# v3.4.3 (20130809)

Add Smokeping support. 

See https://github.com/inex/IXP-Manager/wiki/Smokeping

A schema update is required:

    ALTER TABLE switchport ADD lagIfIndex INT DEFAULT NULL;
    ALTER TABLE ixp ADD smokeping VARCHAR(255) DEFAULT NULL;


Ensure you perform a `git submodule update`.


- [IM] Oppss... forgot to serve Smokeping via IXP Manager (63d3187 - Barry O'Donovan - 2013-08-09)
- [IM] Tweaks from production (ad5e374 - Barry O'Donovan - 2013-08-09)
- [IM] Only show Smokeping link if there are graphs (f034406 - Barry O'Donovan - 2013-08-09)
- [BF] Misnamed variable (9e3c8c6 - Barry O'Donovan - 2013-08-09)
- [BF] Ensure pinging is enabled for at least one protocol (4474ecf - Barry O'Donovan - 2013-08-09)
- [NF] Add verbosity to daily traffic stats (0654172 - Barry O'Donovan - 2013-08-07)
- [NF] Make Smokeping graphs available within IXP Manager (e13aabb - Barry O'Donovan - 2013-08-07)
- [DB] Add new field to switchport to identify LAG ports (beba464 - Barry O'Donovan - 2013-08-07)
- [DB] Add URL for Smokeping to the database (per IXP) (e0c5a29 - Barry O'Donovan - 2013-08-06)
- [IM] Ensure the interface is connected and trafficing - and that we are allowed send pings (98746aa - Barry O'Donovan - 2013-08-06)
- [BF] Reseller must be set (1ddaddc - Barry O'Donovan - 2013-08-06)
- [NF] Smokeping configuration generator (first pass) (1b8fa05 - Barry O'Donovan - 2013-08-03)
- [IM] Updates as part of updating the MRTG documentation (dee1773 - Barry O'Donovan - 2013-08-03)
- [IM] Updates as part of updating the MRTG documentation (b20dc3b - Barry O'Donovan - 2013-08-03)


# v3.4.2 (20130803)

This version brings an integrated MRTG configuration generator to replace the Perl version we have been using. See the following link for documentation:

https://github.com/inex/IXP-Manager/wiki/MRTG---Traffic-Graphs

Using an integrated generator removes the amount of configuration necessary compared to the Perl version, adds new features such as automated *MaxBytes* calculation and makes the configuration much more easy to add to and to skin.

- [IM] Allow writing of MRTG config directly to file (a9263b6 - Barry O'Donovan - 2013-08-03)
- [IM] Dynamically calculate max bytes / packets for the exchange (aab4c26 - Barry O'Donovan - 2013-08-03)
- [IM] Do not assume we have aggregates (068583e - Barry O'Donovan - 2013-08-03)
- [IM] Fix types, ensure consisitency with Perl equivalent (fc99924 - Barry O'Donovan - 2013-08-03)
- [IM] Refactored some templates and fixed skinning issues for MRTG conf generation (3aeee83 - Barry O'Donovan - 2013-08-02)
- [NF] Internal / IXPtool based Mrtg Configuration Generator (first pass) (a955648 - Barry O'Donovan - 2013-08-02)

# v3.4.1 (20130803)

Bug fix release from v3.4.0 for issues discovered when going to production.

Ensure you perform a `git submodule update`.

- [BF] Remove references to old conf options for aggregate graphs (9b12871 - Barry O'Donovan - 2013-08-03)
- [BF] Better to not assume all users on all machines have set a default timezone. (5bdd94c - Barry O'Donovan - 2013-08-03)
- [BF] Doctrine entities cannot be cached (a973b7e - Barry O'Donovan - 2013-08-03)
- [IM] Make it abuntently clear to the admin when maintenance mode is enabled (b9b5efe - Barry O'Donovan - 2013-08-03)

# v3.4.0 (20130802)

This is a major new update which supports multiple IXPs with customers being members of one or more.

A "separate IXP" is an IXP that is not connected in anyway to another. E.g. in different cities with no inter-connection.

See: https://github.com/inex/IXP-Manager/wiki/Multi-IXP-Functionality

This update also links VLANs to infrastructures so you can now use the same VLAN tag in different infrastructures. 


**NB:** There are a lot of upgrade steps required in this - please follow the instructions carefully.

A schema update is required:

    CREATE TABLE customer_to_ixp (
        customer_id INT NOT NULL, 
        ixp_id INT NOT NULL, 
        INDEX IDX_E85DBF209395C3F3 (customer_id), 
        INDEX IDX_E85DBF20A5A4E881 (ixp_id), 
        PRIMARY KEY(customer_id, ixp_id)
    ) 
       DEFAULT CHARACTER SET utf8 
       COLLATE utf8_unicode_ci 
       ENGINE = InnoDB;
    
    ALTER TABLE customer_to_ixp 
        ADD CONSTRAINT FK_E85DBF209395C3F3 
            FOREIGN KEY (customer_id) REFERENCES cust (id);
    
    ALTER TABLE customer_to_ixp 
        ADD CONSTRAINT FK_E85DBF20A5A4E881 
            FOREIGN KEY (ixp_id) REFERENCES ixp (id);
    
    ALTER TABLE vlan 
        ADD infrastructureid INT DEFAULT NULL;
    
    ALTER TABLE vlan 
        ADD CONSTRAINT FK_F83104A1721EBF79 
            FOREIGN KEY (infrastructureid) REFERENCES infrastructure (id);
    
    CREATE INDEX IDX_F83104A1721EBF79 ON vlan (infrastructureid);
    
    DROP INDEX UNIQ_D129B19064082763 
        ON infrastructure;
        
    CREATE UNIQUE INDEX IXPSN 
        ON infrastructure (shortname, ixp_id);
    
    ALTER TABLE ixp
        ADD mrtg_path VARCHAR(255) DEFAULT NULL, 
        ADD mrtg_p2p_path VARCHAR(255) DEFAULT NULL,
        ADD aggregate_graph_name VARCHAR(255) DEFAULT NULL;
    
    ALTER TABLE infrastructure 
        ADD aggregate_graph_name VARCHAR(255) DEFAULT NULL;
    
    ALTER TABLE traffic_daily 
        ADD ixp_id INT DEFAULT NULL;
    
    ALTER TABLE traffic_daily 
        ADD CONSTRAINT FK_1F0F81A7A5A4E881 
            FOREIGN KEY (ixp_id) REFERENCES ixp (id);
    
    CREATE INDEX IDX_1F0F81A7A5A4E881 
        ON traffic_daily (ixp_id);

VLANs must be linked with infrastructures - immediately or the frontend will not work. You can use a simple SQL query as follows and then, correct the VLANs to the correct infrastructures in the web interface:

    UPDATE `vlan` SET `infrastructureid` = 1;
    ALTER TABLE vlan 
       CHANGE infrastructureid infrastructureid INT NOT NULL;

All existing customers must be linked to the default / pre-existing IXP. To achieve this, run:

    bin/ixptool.php -f -a database-migration-cli.v340-customers-to-ixps

In the below, you will see references to *edit you IXP(s)* - for multi-IXP mode, there is a new menu option on the left called *IXPs*. For single-IXP mode users, there is a link to edit IXPs on the *Infrastructures* page.

Up to now, the MRTG graph paths were defined in `application.ini` as follows:

    mrtg.path = http://www.example.com/mrtg
    mrtg.p2ppath = http://www.example.com/sflow/sflow-graph.php

This no longer works in the context of supporting multiple IXPs. Please **remove these** and edit your IXP(s) or  use the following SQL:

    UPDATE `ixp` SET `mrtg_path` = 'http://www.example.com/mrtg';
    UPDATE `ixp` SET `mrtg_p2p_path` = 'http://www.example.com/sflow/sflow-graph.php';

The daily traffic information needs to be multi-IXP aware (see changes to `traffic_daily` above). We 
need to associate the current entries with the default IXP and then add a constraint such that IXP (and 
customer) is required on these entries (may take a moment to execute):

    UPDATE `traffic_daily` SET `ixp_id` = 1;
    
    ALTER TABLE traffic_daily 
        CHANGE ixp_id ixp_id INT NOT NULL, 
        CHANGE cust_id cust_id INT NOT NULL;

Up to now, we hard coded aggregate IXP and infrastructure graphs in the `application.ini` file using a configuration option such as:

    mrtg.traffic_graphs[] = "aggregate::Aggregate Graphs" 
    mrtg.traffic_graphs[] = "network1::Infrastructure #1" 

where `aggregate` was the file name on the MRTG server and `Aggregate Graphs` was the frontend title. You can now remove these 
entries as they are no longer used. Instead, edit your IXP(s) or use SQL such as the following to set the file name:

    UPDATE `ixp` SET `aggregate_graph_name` = 'aggregate' WHERE `id` = 1;

Then, edit your infrastructures and set the *Aggregate Graph Name* (e.g. `network1` above) appropriately. The titles are now worked out automatically. 

If you are using inter-switch / PoP graphs, they will have been configured as follows in `application.ini`:

    mrtg.trunk_graphs[] = "core-sw1-sw2-lan1::PoP 1 to PoP 2 (LAN1)"

Please update these by preceeding them with `1::`:

    mrtg.trunk_graphs[] = "1::core-sw1-sw2-lan1::PoP 1 to PoP 2 (LAN1)"

The `1::` represents the IXP ID where these graphs reside. For an upgrade to this new multi-IXP code, all your existing entries will be for IXP ID 1.


This release also adds support for a maintenance mode - please see [the documentation here](https://github.com/inex/IXP-Manager/wiki/Maintenance-Mode).


There has been a lot of schema changes in this release. You should validate your schema as follows:

    APPLICATION_PATH/bin/doctrine2-cli.php orm:validate-schema

Rather then enumerating the many commits, I will try and enumerate the highlights here:

- [NF] Maintenance mode - allow an admin to put IXP Manager into maintenance to lock out users while an upgrade is in process (41047b2 - Barry O'Donovan - 2013-08-02)
- [IM] Trunk graphs now multi-IXP aware (0def35d - Barry O'Donovan - 2013-08-01)
- [IM] Switch aggregate graphs now multi-IXP aware (f4bd5c2 - Barry O'Donovan - 2013-08-01)
- [IM] Allow single-IXP mode to edit their only IXP with some safety barriers (07e8339 - Barry O'Donovan - 2013-08-01)
- [IM] Make public graphs multi-IXP aware and move config from application.ini to database (ceab79d - Barry O'Donovan - 2013-08-01)
- [BF] RIPE seem to have changed their whois server (via @listerr) (4cc0fc7 - Barry O'Donovan - 2013-08-01)
- [NF] Exporting Member Details (93e049b - Barry O'Donovan - 2013-08-01)
- [IM] League tables / daily traffic updated for multi-IXP support (47c7e6a - Barry O'Donovan - 2013-07-31)
- [IM] P2P graphs multi-IXP aware (6041354 - Barry O'Donovan - 2013-07-31)
- [IM] Member and member drilldown graphs for multi IXP in member portal (4d7b415 - Barry O'Donovan - 2013-07-31)
- [IM] Member dashboard supports multiple IXPs for aggregrate graph (5a3ef39 - Barry O'Donovan - 2013-07-31)
- [IM] Member drilldown IXP aware (3a5ea3e - Barry O'Donovan - 2013-07-31)
- [IM] Member overview graphs IXP aware (5bb558e - Barry O'Donovan - 2013-07-31)
- [IM] Better stats overview <-> p2p navigation (fe384ac - Barry O'Donovan - 2013-07-31)
- [IM] Remove config options for MRTG paths and migrate them to the database (0bfbeed - Barry O'Donovan - 2013-07-31)
- [IM] Member stats list and view updated for multi-IXP / infra (652d990 - Barry O'Donovan - 2013-07-31)
- [DB] Migration script to link existing customers with the default / original IXP (2363fc5 - Barry O'Donovan - 2013-07-30)
- [DB] Schema changes needed for v3.4.0 (04632d7 - Barry O'Donovan - 2013-07-24)
- [NF] Filter private VLANs for a specific infrastructure (5ac1d83 - Barry O'Donovan - 2013-07-23)
- [IM] Tidy up IXP/infra and add filter to list infras for a given IXP (177a1db - Barry O'Donovan - 2013-07-23)
- [BF] Check for infras and custs before deleting an IXP (4d61f94 - Barry O'Donovan - 2013-07-23)
- [NF] Show switches for a specific infrastructure (dc017e5 - Barry O'Donovan - 2013-07-23)
- [IM] Catch deletion exceptions if the infra has switches assigned to it (0a2b97c - Barry O'Donovan - 2013-07-23)
- [DB] Private properties cause a lot of errors when serializing objects (bcc7731 - Barry O'Donovan - 2013-07-23)
- [DB] Adding mrtg_path and mrtg_p2p_path fields to Infrastructure table (e1b34ae - Nerijus Barauskas - 2013-07-11)
- [IM] New table in dashbord showing customer ports by IXP. And also location and infrastructure names prpendend by ixp short name if multiIXP mode enabled. (f627d88 - Nerijus Barauskas - 2013-07-10)
- [IM] On siwtch configuration allows user to select IXP if multiIXP mode enabled (040053d - Nerijus Barauskas - 2013-07-10)
- [IM] application/views/customer/overview-tabs/ports/port.phtml more IXP information if multiIXP mode enabled (a6c3aaf - Nerijus Barauskas - 2013-07-10)
- [IM] Hidding private Vlans then adding adding IPv4 or IPv6 addresses (aa7fae0 - Nerijus Barauskas - 2013-07-09)
- [DB] Adding relation between Vlan and Infrastructure (167226c - Nerijus Barauskas - 2013-07-08)
- [IM] Customer detail list can be filtered by ixp (b56936e - Nerijus Barauskas - 2013-07-08)
- [IM] Adding IXPs list to customer overview menu (c7bb07f - Nerijus Barauskas - 2013-07-08)
- [IM] Adding IXP information to customer detail page (e39ffdb - Nerijus Barauskas - 2013-07-08)
- [IM] Sorting customer names in page header (e297632 - Nerijus Barauskas - 2013-07-05)
- [DB] Adding relation many to many between Customers and IXPs (07e1527 - Nerijus Barauskas - 2013-07-05)
- [IM] Allow to enable multi IXP mode (89e27aa - Nerijus Barauskas - 2013-07-05)

# v3.3.4 (20130801)

New feature to export member details - see https://github.com/inex/IXP-Manager/wiki/Exporting-Member-Details


- [BF] (Very) stale reference to older code (7978423 - Barry O'Donovan - 2013-08-01)
- [NF] Exporting Member Details (93e049b - Barry O'Donovan - 2013-08-01)

# v3.3.3 (20130730)

- [IM] Fix #46 - Do not display new member on "Recent Members" (in member login) until they have a connected physical interface (61c0846 - Barry O'Donovan - 2013-07-30)
- [BF] Fix #51 - Known MAC Addresses only lists MACs of interfaces which have ipv4 and ipv6 assigned (7affd56 - Barry O'Donovan - 2013-07-30)
- [BF] Fix #50 (eed758d - Barry O'Donovan - 2013-07-30)

# v3.3.2 (20130724)

Major new feature - CLI tool to email a report on ports with error / discard counts. See the [documentation](https://github.com/inex/IXP-Manager/wiki/Email-Notifications).

Also a number of minor fixes but one significat change:

The email notification code for port utilisation and traffic deltas have moved to a more specific controller.

This means you may need to update your cronjob scripts for these as follows:

* `cli.examine-port-utilisation` becomes `statistics-cli.email-port-utilisation`
* `cli.examine-traffic-deltas` becomes `statistics-cli.email-traffic-deltas`
* `cli.upload-traffic-stats-to-db` becomes `statistics-cli.upload-traffic-stats-to-db`

The [documentation](https://github.com/inex/IXP-Manager/wiki/Email-Notifications) has been updated.

Also, in the event you have skinned these emails, the templates for these emails changes from `customer/email` to `statistics-cli/email`.

- [IM] Include date with error / discard report (1c799b3 - Barry O'Donovan - 2013-07-24)
- [NF] CLI tool to email a report on ports with error / discard counts (686689b - Barry O'Donovan - 2013-07-24)
- [IM|HK] Move traffic stats to database CLI tool to a better home (748452c - Barry O'Donovan - 2013-07-24)
- [IM|HK] Move email notifications for traffic deltas and port utilisation to a better home (4ed554c - Barry O'Donovan - 2013-07-24)
- [BF] For LAG / multiple ports from the same customer we incorrectly showed the same graph for all (cfdad6f - Barry O'Donovan - 2013-07-24)
- [IM] Introduce caching to IXPs and infrastructures to prevent needless database queries (cbe2aa5 - Barry O'Donovan - 2013-07-23)
- [BF] Stale reference to infrastructures (8f9122b - Barry O'Donovan - 2013-07-23)

# v3.3.1 (20130723)

We can now set which infrastructure is considered the primary or default infrastructre. This is useful for some frontend presentation.

A schema change is required (as well as setting one infrastructure as the primary which **is required**):

    ALTER TABLE infrastructure 
        ADD `isPrimary` TINYINT(1) NOT NULL, 
        CHANGE ixp_id ixp_id INT NOT NULL;
    
    UPDATE `infrastructure` SET `isPrimary` = 1 WHERE id = 1;

- [IM] Tidy up stale infrastructure references (45d7e47 - Barry O'Donovan - 2013-07-23)
- [NF] We can set primary infrastructures per IXP (5ccdd32 - Barry O'Donovan - 2013-07-23)
- [BF] Stale reference to old infrastructure references (0dfbbca - Barry O'Donovan - 2013-07-23)
- [NF] Filter private VLANs for a specific infrastructure (5ac1d83 - Barry O'Donovan - 2013-07-23)
- [BF] Stale reference to old infrastructure references (760edaa - Barry O'Donovan - 2013-07-23)
- [BF] Link to infra table (56918bf - Barry O'Donovan - 2013-07-23)
- [IM] Do not show peering manager to assoc users (3322a06 - Barry O'Donovan - 2013-07-23)




# v3.3.0 (20130723)

This is part one of a significant update to allow IXP Manager to manage multiple IXPs with shared customers.

This first part adds managed infrastructure support rather than just using integers to represent infrastructures.

An infrastructre is a physically diverse network for peering. For example, INEX has two infrastructres with 
a peering LAN on each. All members connect to the 'primary peering infrastructure' while those looking
for resiliency also join the secondary peering infrastructure.

A schema update is required as follows - run SQL queries to update database:


    CREATE TABLE ixp (
        id INT AUTO_INCREMENT NOT NULL, 
        name VARCHAR(255) DEFAULT NULL, 
        shortname VARCHAR(255) DEFAULT NULL, 
        address1 VARCHAR(255) DEFAULT NULL, 
        address2 VARCHAR(255) DEFAULT NULL, 
        address3 VARCHAR(255) DEFAULT NULL, 
        address4 VARCHAR(255) DEFAULT NULL, 
        country VARCHAR(255) DEFAULT NULL, 
        UNIQUE INDEX UNIQ_FA4AB7F64082763 (shortname), 
        PRIMARY KEY(id)
    ) 
        DEFAULT CHARACTER SET utf8 
        COLLATE utf8_unicode_ci 
        ENGINE = InnoDB;
    
    CREATE TABLE infrastructure (
        id INT AUTO_INCREMENT NOT NULL, 
        ixp_id INT DEFAULT NULL, 
        name VARCHAR(255) DEFAULT NULL, 
        shortname VARCHAR(255) DEFAULT NULL, 
        UNIQUE INDEX UNIQ_D129B19064082763 (shortname), 
        INDEX IDX_D129B190A5A4E881 (ixp_id), 
        PRIMARY KEY(id)
    ) 
        DEFAULT CHARACTER SET utf8 
        COLLATE utf8_unicode_ci 
        ENGINE = InnoDB;
    
    ALTER TABLE infrastructure 
        ADD CONSTRAINT FK_D129B190A5A4E881 
            FOREIGN KEY (ixp_id) REFERENCES ixp (id);


Even if not using this new multiple IXP feature, you need to add your only IXP to the database:

    INSERT INTO ixp ( `name`, `shortname`, `address1`, `address2`, `address3`, `address4`, `country` )
        VALUES ( 'IXP name', 'ixp', 'address1', 'address2', 'address3', 'address4', 'IE' );

When defining your switches, you will have given them an infrastructure. For example, INEX runs
two separate peering LANs for resiliency and these are Infrastruture #1 and #2. Even if you only
have one, you need to insert these infrastructure(s) using SQL queries such as:

    INSERT INTO `infrastructure` ( `id`, `name`, `shortname`, `ixp_id` ) 
        VALUES ( 1, 'Infrastructure #1', '#1', 1 );
    INSERT INTO `infrastructure` ( `id`, `name`, `shortname`, `ixp_id` ) 
        VALUES ( 2, 'Infrastructure #2', '#2', 1 );

Be sure that the infrastructure numbers you used in your switch definitions match the
`id` you used above. 

**NB:** It is okay to use `NULL` as the infrastructure for management devices such as
console servers, management switches, etc. But, even if running one IXP with one 
infrastructure, you must create that infrastructre with ID 1 and set the switch 
infrastructure colume for those peering switches to 1 also before doing the following.

We now need to link the infrastrcture table to the switch table:

    ALTER TABLE switch 
        CHANGE infrastructure 
            infrastructure INT DEFAULT NULL;
    
    ALTER TABLE switch 
        ADD CONSTRAINT FK_6FE94B18D129B190 
            FOREIGN KEY (infrastructure) REFERENCES infrastructure (id);
    
    CREATE INDEX IDX_6FE94B18D129B190 
        ON switch (infrastructure);


On my own system (@barryo), some house keeping SQL updates were also required. You can execute these
safely as they will have no effect if not needed.


    ALTER TABLE cust 
        CHANGE isReseller 
            isReseller TINYINT(1) NOT NULL;
    
    ALTER TABLE switchport 
        CHANGE active 
            active TINYINT(1) NOT NULL;
    
    ALTER TABLE company_billing_detail 
        CHANGE purchaseOrderRequired 
            purchaseOrderRequired TINYINT(1) NOT NULL;




- [IM] Tidy up IXP/infra and add filter to list infras for a given IXP (177a1db - Barry O'Donovan - 2013-07-23)
- [BF] Check for infras and custs before deleting an IXP (4d61f94 - Barry O'Donovan - 2013-07-23)
- [NF] Show switches for a specific infrastructure (dc017e5 - Barry O'Donovan - 2013-07-23)
- [IM] Catch deletion exceptions if the infra has switches assigned to it (0a2b97c - Barry O'Donovan - 2013-07-23)
- [IM] Infrastructure is not required - e.g. in the case of mgmt switches (031a584 - Barry O'Donovan - 2013-07-23)
- [IM] Improve multi-ixp mode not enabled error (832719b - Barry O'Donovan - 2013-07-23)
- [BF|IM] Better error and correct type. (NB-T2BOD) (b2a7300 - Barry O'Donovan - 2013-07-23)
- [IM] Only show IXP in multi-IXP mode (78eacb0 - Barry O'Donovan - 2013-07-23)
- [IM] Restructure menu order to make it more sensible (8a52a10 - Barry O'Donovan - 2013-07-23)
- [IM] Do not neet these restrictions on infra shortname (e2b1658 - Barry O'Donovan - 2013-07-23)
- [DB] private -> protected (5c68887 - Barry O'Donovan - 2013-07-23)
- [DB] Private properties cause a lot of errors when serializing objects (bcc7731 - Barry O'Donovan - 2013-07-23)
- [HK] Complete changeset for @rowanthorpe pull request #37 (cf2badc - Barry O'Donovan - 2013-07-16)
- [IM] If adding a new admin user, ensure they start with no notes pending (59b813d - Barry O'Donovan - 2013-07-16)
- [IM] Various improvements for multi IXP framework (1312ac8 - Barry O'Donovan - 2013-07-06)
- [IM] Update DQL to use new infrastructure table (a6315c7 - Barry O'Donovan - 2013-07-06)
- [IM] Add SQL queries for upgrade (5bbb405 - Barry O'Donovan - 2013-07-06)
- [IM] Updating switcher add and editing forms (722f2f8 - Nerijus Barauskas - 2013-07-05)
- [IM] Allowing to enable multi IXP mode (89e27aa - Nerijus Barauskas - 2013-07-05)
- [DB] Adding relation between switcher and interface (dd44ce2 - Nerijus Barauskas - 2013-07-05)
- [NF] Adding CRUD for Infrastructures and hiden CRUD for IXPs (75960b1 - Nerijus Barauskas - 2013-07-05)
- [DB] Adding IXP and Infrastructure Entities (4af9daa - Nerijus Barauskas - 2013-07-05)

# v3.2.2 (20130716)

- [BF] Small bug fix in "mark all notes as read" (35368e7 - Barry O'Donovan - 2013-07-16)
- [IM/CR] Typos and better naming (04c24a9 - Barry O'Donovan - 2013-07-15)
- [HK] Typo (5be397c - Barry O'Donovan - 2013-07-15)
- [NF] Authentication emails are now sent in plaintext and HTML (23f5f14 - Barry O'Donovan - 2013-07-15)
- [NF] Adding auth email plain text templates (cb6035c - Nerijus Barauskas - 2013-07-11)
- [IM] Adding Mark All As Read Button in customer unread notes list (eb6e9c8 - Nerijus Barauskas - 2013-07-10)
- [IM] Hidding private Vlans then adding adding IPv4 or IPv6 addresses (aa7fae0 - Nerijus Barauskas - 2013-07-09)
- [BF] Fix user / contact deletion from user list (4fac457 - Barry O'Donovan - 2013-07-02)


# v3.2.1

Update to `application.ini` required. Please find the `peeringdb.url` entry and update to:

    peeringdb.url = "https://www.peeringdb.com/view.php?asn=%ASN%"

- [CR/BF] Improve billing detail change notifications (more) (1614177 - Barry O'Donovan - 2013-07-05)
- [CR/BF] Improve billing detail change notifications (5e2c672 - Barry O'Donovan - 2013-07-05)
- [HK] Stray comment (3799492 - Barry O'Donovan - 2013-07-05)
- [BF] Fix contact (pre)view - fixes #28 (a26d1f1 - Barry O'Donovan - 2013-07-02)
- [BF] Fix cancel location on billing form - fixes #24 (763cb94 - Barry O'Donovan - 2013-07-02)
- [BF] Better password length consistancy. Fixes #23 (0da4614 - Barry O'Donovan - 2013-07-02)
- [IM] Use ASN for PeeringDB links - resolves #26 (ff4711f - Barry O'Donovan - 2013-07-02)
- [BF] Must also remove last login records when removing a user (79115ae - Barry O'Donovan - 2013-06-28)
- [NF] Sending information about updated billig details (446843b - Nerijus Barauskas - 2013-06-19)


# v3.2.0 (20130628)

Minor version bump as we've added a major new feature - reseller support.

See: https://github.com/inex/IXP-Manager/wiki/Reseller-Functionality

Schema update required:

    ALTER TABLE cust ADD reseller INT DEFAULT NULL, ADD isReseller TINYINT(1) NOT NULL DEFAULT 0;
    ALTER TABLE cust ADD CONSTRAINT FK_997B25A18015899 FOREIGN KEY (reseller) REFERENCES cust (id);
    CREATE INDEX IDX_997B25A18015899 ON cust (reseller);
    ALTER TABLE physicalinterface ADD fanout_physical_interface_id INT DEFAULT NULL;
    ALTER TABLE physicalinterface ADD CONSTRAINT FK_5FFF4D602E68AB8C FOREIGN KEY (fanout_physical_interface_id) REFERENCES physicalinterface (id);
    CREATE UNIQUE INDEX UNIQ_5FFF4D602E68AB8C ON physicalinterface (fanout_physical_interface_id);
    UPDATE `cust` SET `peeringpolicy` = '' WHERE `peeringpolicy` = '0';

New `application.ini` configuration options:

    reseller.enabled = true / false
    reseller.no_billing_for_resold_customers = true / false


- [IM] Show if a customer is a reseller or not on the list (823e39d - Barry O'Donovan - 2013-06-28)
- [IM] More accurate information in customer list for status and type (cec4274 - Barry O'Donovan - 2013-06-28)
- [IM] Show a reseller customer their resold customers (f479c99 - Barry O'Donovan - 2013-06-28)
- [IM] Show resellers their fanout and uplink ports also (64d401e - Barry O'Donovan - 2013-06-28)
- [IM] Show port types on statistics overview and drilldown pages (b4e6577 - Barry O'Donovan - 2013-06-28)
- [CR/IM] Code review of reseller and fanout port functionality (712eb31 - Barry O'Donovan - 2013-06-27)
- [HK] Better language (690e314 - Barry O'Donovan - 2013-06-27)
- [CR/IM] A bit of refactoring (719dd23 - Barry O'Donovan - 2013-06-27)
- [CR/HK] Better documentation (3166454 - Barry O'Donovan - 2013-06-27)
- [CR/HK] Better error messages (bfe850c - Barry O'Donovan - 2013-06-27)
- [CR/HK] Better documentation (6a1bb6f - Barry O'Donovan - 2013-06-27)
- [HK] Better language (25ec40d - Barry O'Donovan - 2013-06-27)
- [DB] Update schema changes required for resellers (2dfcfd4 - Barry O'Donovan - 2013-06-27)
- [HK] Freshly pressed CSS / JS (6166e0c - Barry O'Donovan - 2013-06-27)
- [IM] Use a namespace for Doctrine2 cache (c5dcd68 - Barry O'Donovan - 2013-06-27)
- [IM] Adding more port options for reseller customer in customer overview. Sloves IXP-7 (99f85e3 - Nerijus Barauskas - 2013-06-24)
- [NF] Additional js files for differnet remove dialog box (8bf54df - Nerijus Barauskas - 2013-06-24)
- [IM] Improvements in removing virtual interface (ccfb66e - Nerijus Barauskas - 2013-06-24)
- [IM] Improvements then removing physical interface (f1807c0 - Nerijus Barauskas - 2013-06-24)
- [IM] Separating peering from fanout (3c08e5a - Nerijus Barauskas - 2013-06-24)
- [NF] Allowing to set fanout port in add-wizard. Solves IXP-6 (25aefe9 - Nerijus Barauskas - 2013-06-24)
- [IM] Moving shared function to interfaces trait (216a59c - Nerijus Barauskas - 2013-06-24)
- [NF] Function to get resold customers names list (9bd3311 - Nerijus Barauskas - 2013-06-24)
- [NF] Resold customer list in reseller overview page. Solves IXP-5. (897782f - Nerijus Barauskas - 2013-06-24)
- [NF] Linking a fanout port to a physical interface. Part if IXP-4 (4029583 - Nerijus Barauskas - 2013-06-21)
- [IM] Including TYPE FANOUT to get it in the list when editing physical interface. (e375248 - Nerijus Barauskas - 2013-06-21)
- [NF] Adding function getRelatedInterface wich returns FanoutPhysicalInterface if you call it from PeerPhysicalInterface and vice verse (5cb6f27 - Nerijus Barauskas - 2013-06-21)
- [IM] Updating proxies (69761eb - Nerijus Barauskas - 2013-06-21)
- [IM] Adding form Fanout port form element into physical Interface add edit form. Part of IXP-4 (7132725 - Nerijus Barauskas - 2013-06-21)
- [IM] Checking if if customer have related Physical interfaces then changing resller state, reseller and resold customer state. (3afec0e - Nerijus Barauskas - 2013-06-21)
- [IM] Changes to Virtual Interface page for fanout. Solves IXP-3 (d28d87f - Nerijus Barauskas - 2013-06-20)
- [IM] Hiding billing details if 'reseller.no_billing_for_resold_customers' in application.ini is set to true and reseller mode is enabled. Solves IXP-2 (49597b7 - Nerijus Barauskas - 2013-06-20)
- [IM] Rename overview section billing to details (c09a5a2 - Nerijus Barauskas - 2013-06-20)
- [IM] Showing reseller information in customer overview. [Solves IXP-1] (8c28e6d - Nerijus Barauskas - 2013-06-20)
- [IM] Updating entities and proxies after db schema canges (ecddb6b - Nerijus Barauskas - 2013-06-20)
- [DB] Physical interfaces do not have to have a fanout port (3749e14 - Barry O'Donovan - 2013-06-20)
- [IM] Not allowing to change reseller state if resller have resold customers (282f32d - Nerijus Barauskas - 2013-06-20)
- [DB] Link pysical interfaces for fanout and peering (15d57db - Barry O'Donovan - 2013-06-20)
- [DB] Link virtual interfaces for fanout and peering (952e96f - Barry O'Donovan - 2013-06-20)
- [IM] Setting reseller data then adding updating customer (b26b5ea - Nerijus Barauskas - 2013-06-20)
- [NF] Function to return resellers names array (ffb62bb - Nerijus Barauskas - 2013-06-20)
- [IM] Adding form element for reseller fields in customer form (f61f5ee - Nerijus Barauskas - 2013-06-20)
- [NF] Function to check if resller mode is enabled (7528bd6 - Nerijus Barauskas - 2013-06-20)
- [NF] Add FANOUT type port for resellers (15779b2 - Barry O'Donovan - 2013-06-19)
- [DB] Schema update for reseller members (1e44de4 - Barry O'Donovan - 2013-06-19)
 


# v3.1.8

New recommended `application.ini` configuration option:

    resources.doctrine2cache.namespace                          = 'IXPManager'

- [HK] Freshly pressed CSS / JS (6166e0c - Barry O'Donovan - 2013-06-27)
- [IM] Use a namespace for Doctrine2 cache (c5dcd68 - Barry O'Donovan - 2013-06-27)
- [IM] Better logic for showing contacts role and groups information (30fc572 - Nerijus Barauskas - 2013-06-19)
- [NF] New function to get group and role names array by contacts ip array (b7c17d4 - Nerijus Barauskas - 2013-06-19)
- [IM] Adding bullets for contact role and also showing groups and roles in contact preview (367c423 - Nerijus Barauskas - 2013-06-19)
- [IM] Adding bullet for customer status (32af76d - Nerijus Barauskas - 2013-06-19)
- [BF] Fixing group dropdown lists in contact form (653c38a - Nerijus Barauskas - 2013-06-19)
- [IM] Position in contacts is not required field (069a36c - Nerijus Barauskas - 2013-06-19)
- [BF] After chosen update nead to update some scripts (250ac97 - Nerijus Barauskas - 2013-06-19)
- [BF] Paths and spelling (a473ff3 - Barry O'Donovan - 2013-06-19)
- [HK] Move LONAP graph sync scripts (0f54632 - Barry O'Donovan - 2013-06-19)
- [IM/BF] Aggregate logs improvements (c3808e6 - Barry O'Donovan - 2013-06-19)
- [IM] Update / improve / bugfix migration script (510c4af - Barry O'Donovan - 2013-06-19)
- [HK] LONAP - Update LONAP footer (7f8ae4b - Barry O'Donovan - 2013-06-19)
- [BF] Few code fixes (bcc289b - Nerijus Barauskas - 2013-06-19)
- [NF] Agregating logs (c624852 - Nerijus Barauskas - 2013-06-19)
- [BF] fixing privileges (2db5aaf - Nerijus Barauskas - 2013-06-19)
- [IM] More informative error message (bab4f37 - Nerijus Barauskas - 2013-06-18)
- [NF] Scripts to migrate graphs from RRD to MRTG (8ba4b4c - Nerijus Barauskas - 2013-06-18)
- [BF] Fix header in graphs pages (a2fd6d0 - Barry O'Donovan - 2013-06-13)


# v3.1.7

[IM] Fixes for customer dashboard area (67e35e9 - Barry O'Donovan - 2013-06-13)
[NF] Static docs no longer need to be explicitly defined (e795d9f - Barry O'Donovan - 2013-06-13)
[HK] Freshly pressed CSS / JS (new Colorbox version) (dfa5f46 - Barry O'Donovan - 2013-06-13)
[IM] Hide peering manager / matrix / rs prefix tools if disabled by configuration (2fd2e57 - Barry O'Donovan - 2013-06-13)
[BF] Various fixes to user view of member and switches (b499b64 - Barry O'Donovan - 2013-06-13)
[HK] Update refs to OSS_SNMP (e059064 - Barry O'Donovan - 2013-06-11)
[NF] Script to identify private VLANs via SNMP and add to IXP Manager (ec6b76c - Barry O'Donovan - 2013-06-11)
[IM] INEX doesn't allow single member VLANs but LONAP does. This corrects and allows for that. (17a86f7 - Barry O'Donovan - 2013-06-11)
[IM] If you are not an admin, you do not need to know these details (907cc63 - Barry O'Donovan - 2013-06-10)


# v3.1.6 

- [HK] Misnamed - corrected here (6c4bbbe - Barry O'Donovan - 2013-06-10)
- [HK] Add the l2database script to GitHub (d7059a5 - Barry O'Donovan - 2013-06-10)
- [BF] The schema currently is for last seen only (4a9bad2 - Barry O'Donovan - 2013-06-10)
- [IM] Hide P2P links if p2p graphs are not enabled (0ef5e71 - Barry O'Donovan - 2013-06-10)
- [IM] Better to just exclude associate members (49689be - Barry O'Donovan - 2013-06-10)
- [IM] Better image missing image (dc82b14 - Barry O'Donovan - 2013-06-10)
- [IM] More robust and better config for graphs (a811e4f - Barry O'Donovan - 2013-06-10)
- [NF] initd script for mrtg for Debian / Ubuntu (3a38435 - Barry O'Donovan - 2013-06-10)
- [HK] Freshly pressed CSS / JS (72199b5 - Barry O'Donovan - 2013-06-10)
- [IM] Improve about page (b69f956 - Barry O'Donovan - 2013-06-10)
- [IM] Small fixes and improvements. (905fc59 - Barry O'Donovan - 2013-06-10)
- [BF] Fix logic (2098e9e - Barry O'Donovan - 2013-06-10)


# v3.1.5


This version adds complete user login history rather than just 'last login'
with full UI integration. To enable this, you need to add the following to 
your configuration file:

    resources.auth.oss.login_history.enabled = 1
    resources.auth.oss.login_history.entity = "\\Entities\\UserLoginHistory"


- [IM] Bring constants in line with perl libs (12bd310 - Barry O'Donovan - 2013-06-10)
- [IM] Remove non-existant graphs from intra-switch graphs category dropdown (3f2f3b8 - Barry O'Donovan - 2013-06-07)
- [IM] Use ifName directly when generting MRTG configurations (cda2cab - Barry O'Donovan - 2013-06-07)
- [IM] Use ifName directly when generting MRTG configurations (eac33c9 - Barry O'Donovan - 2013-06-07)
- [BF] When editing contacts customer users customer wasn't changed (4ff4b17 - Nerijus Barauskas - 2013-06-07)
- [IM] Including user id in DQL query (00ca385 - Nerijus Barauskas - 2013-06-07)
- [IM] Adding option to enable login history (a979ddf - Nerijus Barauskas - 2013-06-07)
- [IM] Adding links to login history (2415b7b - Nerijus Barauskas - 2013-06-07)
- [IM] Add link should be hidden if readonly is set to true also (aa69006 - Nerijus Barauskas - 2013-06-07)
- [IM] Updating customers overiew tabs by adding login tab (587319b - Nerijus Barauskas - 2013-06-07)
- [NF] Adding login history controller (9028344 - Nerijus Barauskas - 2013-06-07)
- [IM] Making work ordering on oDataTables as defined in feParams (163792c - Nerijus Barauskas - 2013-06-07)
- [IM] Allowing actions on smaller screens (60337d0 - Nerijus Barauskas - 2013-06-07)
- [BF] Removing unused form property (35d9a29 - Nerijus Barauskas - 2013-06-07)
- [IM] Set switch port type to peering when adding to a virtual interface (ed46c49 - Barry O'Donovan - 2013-06-07)
- [IM] Small tweaks and fixes on UI (dd152ae - Barry O'Donovan - 2013-06-07)
- [IM] Do not show if disabled (c1a1ef3 - Barry O'Donovan - 2013-06-07)
- [IM] Small tweaks / fixes (a07f019 - Barry O'Donovan - 2013-06-07)
- [NF] Showing RS and AS112 client status in customer overview page (0e6763b - Nerijus Barauskas - 2013-06-07)


# v3.1.4

Schema change required:

    CREATE TABLE user_logins (
        id BIGINT AUTO_INCREMENT NOT NULL, 
        user_id INT NOT NULL, 
        ip VARCHAR(39) NOT NULL, 
        at DATETIME NOT NULL, 
        
        INDEX IDX_6341CC99A76ED395 (user_id), 
        INDEX at_idx (at, user_id), 
        PRIMARY KEY(id)
    ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
    
    ALTER TABLE user_logins ADD CONSTRAINT FK_6341CC99A76ED395 FOREIGN KEY (user_id) REFERENCES user (id);


v3.1.3 contained partial database update which breaks some functionality. This commit fixes that.

Also add migration scripts for LONAP.


- [IM] Better / appropriate name (b9559a4 - Barry O'Donovan - 2013-06-07)
- [CR/IM] Small tweaks and fixes (86c7b6c - Barry O'Donovan - 2013-06-07)
- [CR/IM] Small tweaks and fixes (5608a98 - Barry O'Donovan - 2013-06-07)
- [CR/IM] Small tweaks and fixes (cbd9a92 - Barry O'Donovan - 2013-06-07)
- [IM] Updating other Entities (8a1afe1 - Nerijus Barauskas - 2013-06-07)
- [IM] Updating other Entities (70b1226 - Nerijus Barauskas - 2013-06-07)
- [NF] New entity, repository proxy files for UserLoginHistory entity (6313d2e - Nerijus Barauskas - 2013-06-07)
- [DB] Renaming field from when to at (6ca1b3b - Nerijus Barauskas - 2013-06-07)



# v3.1.3

Sponsored by: FIXME

Schema change required:

    CREATE TABLE netinfo ( 
        id INT AUTO_INCREMENT NOT NULL, 
        vlan_id INT NOT NULL, 
        protocol INT NOT NULL, 
        property VARCHAR(255) NOT NULL, 
        ix INT NOT NULL,  
        value LONGTEXT NOT NULL, 
       
        INDEX IDX_F843DE6B8B4937A1 (vlan_id), 
        INDEX VlanProtoProp ( protocol, property, vlan_id ), 
        PRIMARY KEY( id )
    ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
    
    ALTER TABLE netinfo ADD CONSTRAINT FK_F843DE6B8B4937A1 FOREIGN KEY (vlan_id) REFERENCES vlan (id);


See: https://github.com/inex/IXP-Manager/wiki/Network-Information

**NB: CHANGES REQUIRED TO `application.ini` - SEE ABOVE LINK**

New replacement for the old Network Information table which was quite inflexible - the 
new table is a collection of key / value pairs making it much more useful.



- [IM/BF/CR] Fixes for new network information code (ce5d152 - Barry O'Donovan - 2013-06-06)
- [IM] Use 4/6 for protocol identifiers (2052ce5 - Barry O'Donovan - 2013-06-06)
- [IM] Allowing to add/edit/remove network information (51abad5 - Nerijus Barauskas - 2013-06-06)
- [NF] New function for network inforamtion (307f5c0 - Nerijus Barauskas - 2013-06-06)
- [IM] Better language (55f6643 - Nerijus Barauskas - 2013-06-05)
- [NF] Adding net-info controller with list add and remove actions first pass (37c49a8 - Nerijus Barauskas - 2013-06-05)
- [NF] Adding new link to net info (5a8c4a5 - Nerijus Barauskas - 2013-06-05)
- [IM] Adding new paramters for netinfo properties (60d143b - Nerijus Barauskas - 2013-06-05)
- [IM] Adding constants (966fe26 - Nerijus Barauskas - 2013-06-05)
- [NF] Adding new function for Vlan's network inforamtion (93aa6db - Nerijus Barauskas - 2013-06-05)


# v3.1.2

Lots of miscelanous improvements from the LONAP set up (some de-INEXification).

Also allow one to enter a new IP address when adding / editing VLAN interfaces.

Some fixes for jQuery 1.9.

- [IM] Move common functionality into a trait (0bd640b - Barry O'Donovan - 2013-06-06)
- [HK] We don't have CIsco switches anymore... (f63a5ab - Barry O'Donovan - 2013-06-05)
- [HK] Update application.ini.dist (4f9fbd2 - Barry O'Donovan - 2013-06-05)
- [BF] Function call attr changed to prop where checking if checkbox is checked (ced6417 - Nerijus Barauskas - 2013-06-05)
- [BF] jQuery s/attr/prop for checkboxes. (aef3f1f - Barry O'Donovan - 2013-06-05)
- [IM] Do not show meetings in menu also if it is disabled (6b30453 - Barry O'Donovan - 2013-06-05)
- [IM] Extracting same function from different places (c59fd7e - Nerijus Barauskas - 2013-06-04)
- [BF] $.browser was removed in jQuery 1.9 (8aff681 - Barry O'Donovan - 2013-06-04)
- [IM] Disable this disabled controller (ee8d91e - Barry O'Donovan - 2013-06-04)
- [IM] Hide disabled controllers from view (e243369 - Barry O'Donovan - 2013-06-04)
- [IM] Add new company entities to fixtures (149fa1f - Barry O'Donovan - 2013-06-04)
- [IM] Show abbreviated name on overview (6062db4 - Barry O'Donovan - 2013-06-04)
- [BF] Clear the cache after adding new switches (a6e5437 - Barry O'Donovan - 2013-06-04)
- [BF] Bad index selecetd (f810a11 - Nerijus Barauskas - 2013-06-04)
- [NF] Selecting next IPv6 address then enabling IPv6 and customer is select on Virtual Interface wizard (06d1b8e - Nerijus Barauskas - 2013-06-04)
- [IM] Allowing to imput or select IPv4 and IPv6 on virtual interface wizard (ef4742c - Nerijus Barauskas - 2013-06-04)
- [HK] Update OSS_SNMP ref (60d36f3 - Barry O'Donovan - 2013-06-04)
- [IM] Type should not be required (7364faa - Barry O'Donovan - 2013-06-04)
- [BF] Better logic in javacripts (b6d2d92 - Nerijus Barauskas - 2013-06-04)
- [IM] Allow one to turn off P2P graphs (9416aba - Barry O'Donovan - 2013-06-04)
- [IM] Allowint to add new IPv4 or IPv6 address or select exiting ones then adding editing VlanInterface (7747073 - Nerijus Barauskas - 2013-06-04)
- [NF] New js file for lonap to get next IPv6 then enabling IPv6 in VlanInterface (83122b6 - Nerijus Barauskas - 2013-06-04)
- [NF] New action to get next IPv6 addres for customer (341ce74 - Nerijus Barauskas - 2013-06-04)
- [NF] New function to get IPv6 addresses array for given customer (6ef9a70 - Nerijus Barauskas - 2013-06-04)
- [IM] Do not hang when loading dashboard without aggregate graphs (294edf6 - Barry O'Donovan - 2013-06-04)
- [IM] Configurable offset for authentication page logos (802f3cf - Barry O'Donovan - 2013-06-04)
- [BF] Check that $user is defined before accessing it (8986fb8 - Barry O'Donovan - 2013-06-04)
- [IM] Partial commit on allowing to select or imput IPv4 or IPv6 addresses then adding or editing Vlan Interface (dd6bfd5 - Nerijus Barauskas - 2013-06-04)


# v3.1.1

Second phase of v3.1.0. Update to this tag and then execute:

    git submodule init
    git submodule update


- [HK] These scripts are redundant now that we are using submodules
- [HK] Add OSS clone of Minify library as external (b62c2be - Barry O'Donovan - 2013-06-04)
- [HK] Add OSS clone of Bootstrap-Zend-Framework library as external (6448780 - Barry O'Donovan - 2013-06-04)
- [HK] Added these two submodules in the parent directory in error (c742f9a - Barry O'Donovan - 2013-06-04)
- [HK] Add IXP Manager GitHub wiki as external (13f79ef - Barry O'Donovan - 2013-06-04)
- [HK] Add Throbber.js library as external (174c3dc - Barry O'Donovan - 2013-06-04)
- [HK] Add Bootbox library as external (ad1f4b0 - Barry O'Donovan - 2013-06-04)
- [HK] Add Minify library as external (d697a93 - Barry O'Donovan - 2013-06-04)
- [HK] Add OSS clone of Bootstrap-Zend-Framework library as external (a39ae64 - Barry O'Donovan - 2013-06-04)
- [HK] Add OSS-SNMP library as external (45f8ca2 - Barry O'Donovan - 2013-06-04)
- [HK] Add OSS-Framework library as external (82cbd3f - Barry O'Donovan - 2013-06-04)
- [HK] Add Smarty library as external (2817c76 - Barry O'Donovan - 2013-06-04)
- [HK] Add Zend library as external (eecc1fa - Barry O'Donovan - 2013-06-04)


# v3.1.0

**NB:** This is a major version bump as we are changing the manner in
which third party libraries are included with IXP Manager.

Upgrade to this tag specifically first, then to the next tag (v3.1.1)
and follow the instructions there.

We use a large number of external / third party libraries and it's 
very easy to get out of sync with these. By using Git submodules we
can make the install procedure easier as well as ensure the known
supported versions of third party libraries are used.

After upgrading to this version, remove the following directories from
your `library/` directory:

* `Bootbox` 
* `Bootstrap-Zend-Framework` 
* `Minify` 
* `Smarty` 
* `Throbber.js` 
* `wiki` 
* `Zend` 
* `OSS-Framework.git` 
* `OSS_SNMP.git` 

Then proceed to the instructions for v3.1.1.

# v3.0.18

Update schema:

    ALTER TABLE cust 
        ADD abbreviatedName VARCHAR(30) DEFAULT NULL, 
        ADD MD5Support VARCHAR(255) DEFAULT NULL;
    
    UPDATE cust SET abbreviatedName = name;
    UPDATE cust SET MD5Support = 'UNKNOWN';
    
    ALTER TABLE switchport 
        CHANGE active active TINYINT(1) NOT NULL DEFAULT 1;
    
    ALTER TABLE company_billing_detail 
        ADD purchaseOrderRequired TINYINT(1) NOT NULL DEFAULT 0, 
        ADD invoiceMethod VARCHAR(255) DEFAULT NULL, 
        ADD invoiceEmail VARCHAR(255) DEFAULT NULL, 
        ADD billingFrequency VARCHAR(255) DEFAULT NULL;


Primarily a refactoring of customer / billing / registration details.

- [DB] Merge in lonap schema updates (c6f7333 - Barry O'Donovan - 2013-05-31)
- [IM] Regenerated js file using new script (9ea89b9 - Nerijus Barauskas - 2013-05-28)
- [NF] Adding update oss js files script (84ab33e - Nerijus Barauskas - 2013-05-28)
- [IM] Changes after review (5f61ab3 - Nerijus Barauskas - 2013-05-28)
- [IM] Adding billing tab in customer overview page (997437b - Nerijus Barauskas - 2013-05-28)
- [BF] Small fixes (605529a - Nerijus Barauskas - 2013-05-28)
- [IM] Allow to select empty values for some dropdown lists (4ceb37e - Nerijus Barauskas - 2013-05-28)
- [IM] Spliting edit int two action. Edit customer details and edit billing/registration details (74829f0 - Nerijus Barauskas - 2013-05-28)
- [IM] Removing customer billing-registration detailsf from customer form (9a4cc8d - Nerijus Barauskas - 2013-05-28)
- [NF] Adding customer billing-registration action (21b5bb8 - Nerijus Barauskas - 2013-05-28)
- [NF] Adding customer billing/registration form (ac0a4ec - Nerijus Barauskas - 2013-05-28)
- [IM] Adding new field to form to reflect schema changes (8999e39 - Nerijus Barauskas - 2013-05-27)
- [HK] Regenerating proxies. (6622ba2 - Nerijus Barauskas - 2013-05-27)
- [IM] Updating entities after schema changes (52c26f2 - Nerijus Barauskas - 2013-05-27)
- [DB] Adding new fields to the schema (487a556 - Barry O'Donovan - 2013-05-27)



# v3.0.17

- [IM] Add RIPE and ARIN from RADB (c4f3c2b - Barry O'Donovan - 2013-05-31)
- [HK] Freshly pressed CSS/JS (6903325 - Barry O'Donovan - 2013-05-28)
- [HK] Merging (45fb167 - Barry O'Donovan - 2013-05-28)
- [HK] Freshly pressed CSS/JS (78b5085 - Barry O'Donovan - 2013-05-28)
- [IM] Chosen width fixing should be on request, not default (f5f7e1d - Barry O'Donovan - 2013-05-28)
- [HK] Small BF and freshly presses CSS / JS (e616eca - Barry O'Donovan - 2013-05-28)
- [IM] Regenerated js file using new script (9ea89b9 - Nerijus Barauskas - 2013-05-28)
- [IM] Fix Chosen width issues (5c19e08 - Barry O'Donovan - 2013-05-28)
- [NF] Adding update oss js files script (84ab33e - Nerijus Barauskas - 2013-05-28)
- [HK] Freshly pressed CSS/JS (9e696b2 - Barry O'Donovan - 2013-05-28)
- [BF] Wrong variable for minified css/js check (ebad231 - Barry O'Donovan - 2013-05-28)


# v3.0.16

Bring miscelaneous JS fucntions into line with OSS-Framework.

Upgrade external JS and CSS libraries.

- [HK] Freshly pressed CSS / JS (e385b53 - Barry O'Donovan - 2013-05-28)
- [IM] Update Chosen (4bf3505 - Barry O'Donovan - 2013-05-28)
- [IM] Freshly pressed JS / CSS (8d81aa6 - Barry O'Donovan - 2013-05-28)
- [IM] Upgrade colorbox (28798b7 - Barry O'Donovan - 2013-05-28)
- [IM] Upgrade datatables (d4fbd0c - Barry O'Donovan - 2013-05-28)
- [IM] Update jQuery CSS and images (4ef322c - Barry O'Donovan - 2013-05-28)
- [IM] Update jQuery JS files (60e406e - Barry O'Donovan - 2013-05-28)
- [IM] Upgrade Bootstrap to 2.3.2 from 2.2.1 (59c0f48 - Barry O'Donovan - 2013-05-28)
- [HK] Freshly pressed JS / CSS bundles (e0a16bd - Barry O'Donovan - 2013-05-28)
- [HK] Change layout of JS files (f4ab938 - Barry O'Donovan - 2013-05-28)
- [HK] Dead code (ec4a58e - Barry O'Donovan - 2013-05-28)
- [BF] fn() referenced as fn (c770041 - Barry O'Donovan - 2013-05-27)
- [NF] adding combined.js file generated by OSS-Framework library (90f54dc - Nerijus Barauskas - 2013-05-27)
- [IM] Removing old scripts (5453f91 - Nerijus Barauskas - 2013-05-27)
- [IM] Updating function names form tt_ to oss (077ed90 - Nerijus Barauskas - 2013-05-27)
- [BF] Groups was not working as it should (143e63d - Nerijus Barauskas - 2013-05-27)
- [NF] Allowing to set additional information popover (e97f95a - Nerijus Barauskas - 2013-05-24)


# v3.0.15

**NB: This is a TWO stage migration process, please follow instructions carefully!**

Checkout commit with specific reference:

    git pull
    git checkout da13666cbbb2fc0c105b5e69ca3f317f89465388

Then update schema:

    CREATE TABLE 
        company_registration_detail (
            id INT AUTO_INCREMENT NOT NULL,
            registeredName VARCHAR(255) DEFAULT NULL,
            companyNumber VARCHAR(255) DEFAULT NULL,
            jurisdiction VARCHAR(255) DEFAULT NULL,
            address1 VARCHAR(255) DEFAULT NULL,
            address2 VARCHAR(255) DEFAULT NULL,
            address3 VARCHAR(255) DEFAULT NULL,
            townCity VARCHAR(255) DEFAULT NULL,
            postcode VARCHAR(255) DEFAULT NULL,
            country VARCHAR(255) DEFAULT NULL,
            PRIMARY KEY(id)
        )
    ENGINE = InnoDB;
    
    CREATE TABLE
        company_billing_detail (
            id INT AUTO_INCREMENT NOT NULL,
            billingContactName VARCHAR(255) DEFAULT NULL,
            billingAddress1 VARCHAR(255) DEFAULT NULL, 
            billingAddress2 VARCHAR(255) DEFAULT NULL,
            billingTownCity VARCHAR(255) DEFAULT NULL,
            billingPostcode VARCHAR(255) DEFAULT NULL,
        billingCountry VARCHAR(255) DEFAULT NULL,
        billingEmail VARCHAR(255) DEFAULT NULL,
        billingTelephone VARCHAR(255) DEFAULT NULL,
        vatNumber VARCHAR(255) DEFAULT NULL,
        vatRate VARCHAR(255) DEFAULT NULL,
        PRIMARY KEY(id)
    )
    ENGINE = InnoDB;
    
    ALTER TABLE cust
        ADD company_registered_detail_id INT DEFAULT NULL,
        ADD company_billing_details_id INT DEFAULT NULL,
        ADD peeringmacrov6 VARCHAR(255) DEFAULT NULL;
    
    ALTER TABLE cust
        ADD CONSTRAINT FK_997B25A98386213 FOREIGN KEY (company_registered_detail_id) REFERENCES company_registration_detail (id),
        ADD CONSTRAINT FK_997B25A84478F0C FOREIGN KEY (company_billing_details_id) REFERENCES company_billing_detail (id);
    
    CREATE INDEX IDX_997B25A98386213 ON cust (company_registered_detail_id);
    CREATE INDEX IDX_997B25A84478F0C ON cust (company_billing_details_id);

Restart memcached and run script:

    bin/migration-scripts/billing-details.php

Checkout commit with specific reference:

    git checkout cd0e2c406b28f30bccd4c3c508751b9fed324125

Then restart memcached and update schema again:

    ALTER TABLE cust 
        DROP billingContact,
        DROP billingAddress1,
        DROP billingAddress2,
        DROP billingCity,
        DROP billingCountry;
        

Checkout latest commit / head:

    git checkout v3.0.15 (or inex-live, etc)



- [HK] Set purpose at top of file (a574ebb - Barry O'Donovan - 2013-05-27)
- [BF] Left required field after testing (ed1dc21 - Nerijus Barauskas - 2013-05-23)
- [IM] Using new OSS_Form_Element_DatabaseDropdown (ca54926 - Nerijus Barauskas - 2013-05-23)
- [BF] Making simlar names in database for consitance (798dd8e - Nerijus Barauskas - 2013-05-20)
- [BF] Non existent function called (a161d6b - Nerijus Barauskas - 2013-05-20)
- [IM] Updating code to user CompanyBillingDetail entity insted of customer billing fields (e5682a1 - Nerijus Barauskas - 2013-05-20)
- [DB] Removing billing fields from customer table (c1f0a17 - Nerijus Barauskas - 2013-05-20)
- [NF] Addig migration script for billing details (da13666 - Nerijus Barauskas - 2013-05-20)
- [IM] Updating form to use company billing/registration detail entities (b947b11 - Nerijus Barauskas - 2013-05-20)
- [DB] Schema and entity fixes (869442f - Nerijus Barauskas - 2013-05-20)
- [DB] Rgenerating proxies (520b2f8 - Nerijus Barauskas - 2013-05-20)
- [DB] Update schema (331ad46 - Barry O'Donovan - 2013-05-20)
- [DB] Update schema (0efb652 - Barry O'Donovan - 2013-05-20)
- [DB] Schema fix and add registered company name (1a07245 - Barry O'Donovan - 2013-05-20)


# v3.0.14

Schema update required:

    ALTER TABLE switchport ADD active TINYINT(1) NOT NULL DEFAULT 1

This update adds a lot of functionality to the switch / switch port management
fucntionality in the IXP Manager interface. In  particular, switches are now
added by SNMP polling the switch to discover ports, make, model, etc.

Port states are polled live in the interface also and switch ports can be
updated and edited in bulk.

See: https://github.com/inex/IXP-Manager/wiki/Switch-and-Switch-Port-Management

- [IM] Add help messages and a help link to documentation (47e7619 - Barry O'Donovan - 2013-05-24)
- [IM] Default to SNMP add for switches and allow manual add from there (aabd968 - Barry O'Donovan - 2013-05-24)
- [IM] Better integration of different switch port pages (8e3eff8 - Barry O'Donovan - 2013-05-24)
- [IM] Refactor CLI SNMP actions using new entitiy functions (6a9f789 - Barry O'Donovan - 2013-05-24)
- [DB] [HK] Schema / generated entities clean up (5b52ee2 - Barry O'Donovan - 2013-05-24)
- [IM/CR] Major refactor of SNMP polling code (246308e - Barry O'Donovan - 2013-05-23)
- [CR] Small tweaks and fixes. (39cf69d - Barry O'Donovan - 2013-05-23)
- [IM] Better AJAX handling (74ed6f6 - Nerijus Barauskas - 2013-05-23)
- [BF] Fixing logic (4d1b02f - Nerijus Barauskas - 2013-05-23)
- [IM] Tidying up code (a1656cd - Nerijus Barauskas - 2013-05-22)
- [IM] Making single row type update as ajax (45e6bf8 - Nerijus Barauskas - 2013-05-22)
- [NF] True /false script for frontend (aee7f81 - Nerijus Barauskas - 2013-05-22)
- [IM] Updating older code to reflect new active field (4c36969 - Nerijus Barauskas - 2013-05-22)
- [DB] Adding active field to swichport table (c61d353 - Nerijus Barauskas - 2013-05-22)
- [HK] Regenerating proxies after shema changes (2055343 - Nerijus Barauskas - 2013-05-22)
- [BF] Wrong logic then reducing log inforamtion (f4c6104 - Nerijus Barauskas - 2013-05-21)
- [NF] SNMP Poll first pass (3222570 - Nerijus Barauskas - 2013-05-21)
- [NF] Adding view for SNMP Poll (5d223d6 - Nerijus Barauskas - 2013-05-21)
- [IM] Adding link to snmp-poll for switch (cde0422 - Nerijus Barauskas - 2013-05-21)
- [WIP] Switchport discovery by SNMP (501cb2f - Barry O'Donovan - 2013-05-20)
- [NF] Switch polling by SNMP (a24cab2 - Barry O'Donovan - 2013-05-20)
- [HK] This field was a typo and is now removed (3dd5935 - Barry O'Donovan - 2013-05-20)

# V3.0.13

Skipped this tag in error. 

*This tag is (un)intentionally left blank.*


# V3.0.12

Schema update required:

    ALTER TABLE switchport ADD ifIndex INT DEFAULT NULL;

The new data for switchports that is collected via  SNMP polling 
missed ``ifIndex`` which is essential for polling individual 
datapoints for a port later. This is also added here.

This version adds that and it is backwards compatible so should "just
work".

# V3.0.11

This is a follow on from V3.0.10 to extend SNMP polling to switch ports.

- [IM] Frontend for polled switch port data (b5a20ad - Barry O'Donovan - 2013-04-25)
- [BF] Wrong parameter for limiting view of ports by switch (8f08491 - Barry O'Donovan - 2013-04-25)
- [IM] Add switchport polling to the CLI poller (2c57237 - Barry O'Donovan - 2013-04-25)

# V3.0.10

Schema update required:

    ALTER TABLE switch ADD hostname VARCHAR(255) DEFAULT NULL;
    
    ALTER TABLE switchport 
        ADD ifName VARCHAR(255) DEFAULT NULL, 
        ADD ifAlias VARCHAR(255) DEFAULT NULL, 
        ADD ifHighSpeed INT DEFAULT NULL, 
        ADD ifMtu INT DEFAULT NULL, 
        ADD ifPhysAddress VARCHAR(17) DEFAULT NULL, 
        ADD ifAdminStatus INT DEFAULT NULL, 
        ADD ifOperStatus INT DEFAULT NULL, 
        ADD ifLastChange INT DEFAULT NULL, 
        ADD lastSnmpPoll DATETIME DEFAULT NULL;
    
    ALTER TABLE `switch` 
        ADD os VARCHAR(255) DEFAULT NULL, 
        ADD osDate DATETIME DEFAULT NULL, 
        ADD osVersion VARCHAR(255) DEFAULT NULL, 
        ADD lastPolled DATETIME DEFAULT NULL;


Library update required:

    bin/library-init.sh


Configuration update required - add:

    includePaths.osssnmp    = APPLICATION_PATH "/../library/OSS_SNMP.git"



Primarily, this version brings switch polling via SNMP to gather information such 
as model, operating system and version. This information is then visable on the
admin frontend. To keep this up to date, set up a cronjob such as:

    10 * * * * /path/to/ixp-manager/bin/ixptool.sh -a switch-cli.snmp-poll

This command is safe and will only overwrite one existing database field: `switch.model`.
See the following link for details on switch model / OS discovery: 

https://github.com/opensolutions/OSS_SNMP/wiki/Device-Discovery

We'll be adding support for Extreme and possibly Cisco ourselves soon if no one
gets there first.

Documentation for this will be added to: https://github.com/inex/IXP-Manager/wiki/Updating-Switches-and-Ports-via-SNMP



- [NF] Plug switch information polled via SNMP into the frontend (26a347a - Barry O'Donovan - 2013-04-24)
- [IM] Finishing switch data via SNMP poll on production for real data (0b35ece - Barry O'Donovan - 2013-04-24)
- [NF] Beginnings of switch poller. Commiting for testing. (cb63255 - Barry O'Donovan - 2013-04-24)
- [NF] New CLI controller for switch functions (9ee6baf - Barry O'Donovan - 2013-04-24)
- [HK] Remove errant semi-colon (29c1e70 - Barry O'Donovan - 2013-04-24)
- [DB] Add new entries for SNMP polled switches (ee42c08 - Barry O'Donovan - 2013-04-23)
- [DB] Entities and proxies updated (120c1f9 - Barry O'Donovan - 2013-04-23)
- [DB] Update schema to allow for polling of details from switches via SNMP (f5d3d77 - Barry O'Donovan - 2013-04-23)
- [DB] Add the ORM Designer file to the repository (3151d22 - Barry O'Donovan - 2013-04-23)
- [HK] Add new external library (e5b92bf - Barry O'Donovan - 2013-04-23)




# V3.0.9

Schema update required:


    CREATE TABLE contact_to_group (
        contact_id INT NOT NULL, contact_group_id BIGINT NOT NULL, 
        INDEX IDX_FCD9E962E7A1254A (contact_id), 
        INDEX IDX_FCD9E962647145D0 (contact_group_id), 
        PRIMARY KEY(contact_id, contact_group_id)
    ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
    
    CREATE TABLE contact_group (
        id BIGINT AUTO_INCREMENT NOT NULL, name VARCHAR(20) NOT NULL, 
        description VARCHAR(255) NOT NULL, type VARCHAR(20) NOT NULL, 
        active TINYINT(1) NOT NULL, `limited_to` INT NOT NULL, created DATETIME NOT NULL, 
        UNIQUE INDEX UNIQ_40EA54CA5E237E06 (name), PRIMARY KEY(id)
    ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
    
    ALTER TABLE contact_to_group ADD CONSTRAINT FK_FCD9E962E7A1254A 
        FOREIGN KEY (contact_id) REFERENCES contact (id);
    
    ALTER TABLE contact_to_group ADD CONSTRAINT FK_FCD9E962647145D0 
        FOREIGN KEY (contact_group_id) REFERENCES contact_group (id);
    
    ALTER TABLE contact 
        ADD user_id INT DEFAULT NULL, 
        ADD position VARCHAR(50) DEFAULT NULL, 
        ADD notes LONGTEXT DEFAULT NULL, 
        CHANGE name name VARCHAR(255) NOT NULL, 
        CHANGE phone phone VARCHAR(50) DEFAULT NULL, 
        CHANGE mobile mobile VARCHAR(50) DEFAULT NULL, 
        CHANGE facilityaccess facilityaccess TINYINT(1) NOT NULL, 
        CHANGE mayauthorize mayauthorize TINYINT(1) NOT NULL;
    
    ALTER TABLE contact ADD CONSTRAINT FK_4C62E638A76ED395 
        FOREIGN KEY (user_id) REFERENCES user (id);
    
    CREATE UNIQUE INDEX UNIQ_4C62E638A76ED395 ON contact (user_id);
    
    ALTER TABLE user DROP FOREIGN KEY FK_8D93D649727ACA70;
    DROP INDEX IDX_8D93D649727ACA70 ON user;
    ALTER TABLE user DROP parent_id;
    
    INSERT INTO contact_group ( name, description, type, active, limited_to, created ) VALUES ( 'Billing', 'Contact for billing matters', 'ROLE', 1, 0, NOW() );
    INSERT INTO contact_group ( name, description, type, active, limited_to, created ) VALUES ( 'Technical', 'Contact for technical matters', 'ROLE', 1, 0, NOW() );
    INSERT INTO contact_group ( name, description, type, active, limited_to, created ) VALUES ( 'Admin', 'Contact for admin matters', 'ROLE', 1, 0, NOW() );
    INSERT INTO contact_group ( name, description, type, active, limited_to, created ) VALUES ( 'Marketing', 'Contact for marketing matters', 'ROLE', 1, 0, NOW() );

**You must also recreate your views:**

    mysql -u root -p password [dbname] < tools/sql/views.sql

Please ensure that you go through all your users and assign / create contacts for them. See 
`tools/migration_scripts/contact-contactgroups.php` as a sample simple script for some of this.


- [NF] Updated profile to allow users update their contact information
- [NF] User / Contact integration. See: https://github.com/inex/IXP-Manager/wiki/Contacts-and-Users
- [NF] Introduction of Contact Roles and Groups. See: https://github.com/inex/IXP-Manager/wiki/Contact-Groups
- [NF] Integrate contact fields into user's profile
- [NF] Note Watching - see https://github.com/inex/IXP-Manager/wiki/Customer-Notes 
- [IM] Better redirection when adding / editing virtual interfaces (224fce5 - Barry O'Donovan - 2013-04-04)
- [IM] Do not assume physical / VLAN interfaces exist for a virtual interface (1b55c11 - Barry O'Donovan - 2013-04-04)
- [IM] Better error messages and redirection on adding phys / vlan interfaces (6fa56ac - Barry O'Donovan - 2013-04-04)
- [BF] Broken link (d8abe24 - Barry O'Donovan - 2013-04-04)


# V3.0.8 - 20130403

Schema update required:

    CREATE TABLE cust_notes (
             id BIGINT AUTO_INCREMENT NOT NULL, customer_id INT NOT NULL, private TINYINT(1) NOT NULL, 
             title VARCHAR(255) NOT NULL, note LONGTEXT NOT NULL, created DATETIME NOT NULL,
             updated DATETIME NOT NULL, 
             INDEX IDX_6377D8679395C3F3 (customer_id), PRIMARY KEY(id)
         ) 
         DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
    
    ALTER TABLE cust_notes ADD CONSTRAINT FK_6377D8679395C3F3 FOREIGN KEY (customer_id) REFERENCES cust (id);
    ALTER TABLE cust DROP notes

- [NF] Customer Notes - see https://github.com/inex/IXP-Manager/wiki/Customer-Notes

# V3.0.7 - 20130403

- [IM] Cancel editing a customer should return to customer overview
- [SF] Added sflow support

# V3.0.6 - 20130307

Schema update required:

    ALTER TABLE cust ADD peeringDb VARCHAR(255) DEFAULT NULL


Config file update required:

    ; The URL used to display PeeringDB entries.
    ; %ID% is replaced with the customer's peeringDB entry ID as entered in the customer's record.
    ;
    peeringdb.url = "https://www.peeringdb.com/private/participant_view.php?id=%ID%"


- [NF] Add click-to-view PeeringDB to customers
- [DB] Add peeringDb entry to customer record (e3f72b4 - Barry O'Donovan - 2013-03-07)
- [IM] Some frontend UI improvements (95435b2 - Barry O'Donovan - 2013-03-07)
- [IM] Improve frontend UI flow for customer dashboard actions (3e43948 - Barry O'Donovan - 2013-03-07)
- [IM] Frontend glue between overview and add / edit / delete contacts (9a18228 - Barry O'Donovan - 2013-03-05)
- [IM] IRRDB not required for non-peering members (0c2e6cf - Barry O'Donovan - 2013-03-05)
- [IM] Don't throw ugly errors on dev system if these are not set for the customer (86cc9bb - Barry O'Donovan - 2013-03-05)
- [BF] There is no IRRDB for new customer - fixes #6 (b3c2d53 - Barry O'Donovan - 2013-03-04)
- [BF] Netinfo was passed to the view *after* it was actually needed for welcome email (e1deff8 - Barry O'Donovan - 2013-02-25)
- [BF] Broken link (3103933 - Barry O'Donovan - 2013-02-22)
- [IM] Flow between interface and user editing back to customer overview (58dc7d5 - Barry O'Donovan - 2013-02-22)
- [HK] Rafactor to make source of IRRDB info more obvious (580f5cb - Barry O'Donovan - 2013-02-22)
- [IM] Show max prefixes (by customer and by vlan interfaces) and IRRDB source in customer overview
- [BF] Fix sorting on last logins list (593105b - Barry O'Donovan - 2013-02-22)
- [IM] Add AS-SET information to the rs prefixes help page (d5937b7 - Barry O'Donovan - 2013-02-22)
- [IM] Move position of warning bullet for rs prefixes (3e8dbe0 - Barry O'Donovan - 2013-02-22)

# V3.0.5 - 20130222

Schema Updates Required:

    UPDATE cust SET irrdb = null WHERE irrdb = 0;
    ALTER TABLE cust ADD CONSTRAINT FK_997B25A666E98DF FOREIGN KEY (irrdb) REFERENCES irrdbconfig (id);
    CREATE INDEX IDX_997B25A666E98DF ON cust (irrdb)

- [NF] Customers can now see their route server prefixes (4b1cef7 - Barry O'Donovan - 2013-02-22)
- [DB] Link IRRDB table to customer table.
- Route server prefix analysis / frontend on ''rs_prefixes'' table
    - Sumary table of customers and prefixes
    - Individual customer routes, filtered by protocol
    - Datatables integration allowign pagination, sorting and as you type searching
    - Customer overview link and indication when there are routes blocked
    - Route classifications are:
        - Advertised and accepted
        - Advertised but not accepted
        - Not advertised but acceptible

# V3.0.4 - 20130221

Schema Updates Required:

    RENAME TABLE rs_dropped_prefixes TO rs_prefixes;

- [DB] Refactor rs_dropped_prefixes to rs_prefixes as well as associated controller and view refactoring

# V3.0.3 - 20130221

- [NF] Correcting misunderstanding of the rs_dropped_routes table (78312d4 - Barry O'Donovan - 2013-02-21)
- [IM] Show customer ASN in header of dropped prefixes (e75f273 - Barry O'Donovan - 2013-02-21)
- [NF] Show user last logged in time (and where from) in customer overview (c77c257 - Barry O'Donovan - 2013-02-21)
- [NF] Show console server connections in customer overview (if they have them) (838ab2e - Barry O'Donovan - 2013-02-21)
- [HK] Useful git command for formatted logs (e87f95b - Barry O'Donovan - 2013-02-21)
- [NF] Frontend glue for prefixes dropped by the route servers (624862a - Barry O'Donovan - 2013-02-21)
- [BF] Fix display of members of a private VLAN (b2ed0f7 - Barry O'Donovan - 2013-02-21)
- [IM] Add p2p graphs link to customer overview tabs (a35e0e9 - Barry O'Donovan - 2013-02-21)
- [NF] Refactored customer overview layout -> now sporting tabbed panes (8043c4c - Barry O'Donovan - 2013-02-21)
- [IM] For admins, colsolidate Profile and Logout menu into a My Account menu (f5b4f78 - Barry O'Donovan - 2013-02-20)
- [IM] Remove redundant Home menu item. The title does that anyway. (fc36562 - Barry O'Donovan - 2013-02-20)
- [IM] Add Twitter account link to INEX footer (5abe8b8 - Barry O'Donovan - 2013-02-20)
- [IM] Move About menu item to tidy it up a bit (c92db2b - Barry O'Donovan - 2013-02-20)


# V3.0.2 - 20130220

Schema Updates Required:

    ALTER TABLE vlan ADD private TINYINT(1) NOT NULL

- [IM] Private VLANs should not be public information (bafe5b4 - Barry O'Donovan - 2013-02-20)
- [NF] Show customers their own private VLAN services (e1848ce - Barry O'Donovan - 2013-02-20)
- [IM] Allow VLAN repository functions to limit results by VLAN type (c36f2ef - Barry O'Donovan - 2013-02-20)
- [NF] New page to list all private VLANs and the customers attached to them (615bd06 - Barry O'Donovan - 2013-02-20)
- [NF] List a customer's private VLANs in their overview page (efca8db - Barry O'Donovan - 2013-02-20)
- [NF] Frontend glue for private VLANs (92dc80a - Barry O'Donovan - 2013-02-20)
- [BF] The customer ID for add interface wizard can also come via the URL path (a4952b9 - Barry O'Donovan - 2013-02-20)
- [DB] Schema update required for private VLANs (58ede71 - Barry O'Donovan - 2013-02-20)
- [N+] Schema updates for private VLANs (e1a1e16 - Barry O'Donovan - 2013-02-20)


# V3.0.1 - 20130220

- [DB] ORM schema update due to update of ORM Manager. Inc. change to VLAN table. (cb70971 - Barry O'Donovan - 2013-02-20)
- [IM] Meetings updated with some bugfixes: (a81e554 - Barry O'Donovan - 2013-02-14)
- [BF] IXP FrontEnd extends AuthRequired which is an issue for public display of meeting details (6610127 - Barry O'Donovan - 2013-02-14)
- [BF] Some pages are public access and don't require this for non-logged in users (30834ec - Barry O'Donovan - 2013-02-14)
- [BF] IXP V3 using Doctrine2 from PEAR/Git rather than SVN (3041603 - Barry O'Donovan - 2013-02-13)
- [BF] Small bug fixes from going live with V3 on INEX (23a64b2 - Barry O'Donovan - 2013-02-13)
- [HK] Refactor INEX_ library to more appropriate IXP_ library (b9ddc24 - Barry O'Donovan - 2013-02-12)
- [BF] Fix table width in Chrome (1f96d5b - Barry O'Donovan - 2013-01-10)
- [HK] Freshly pressed CSS/JS files (1831cbf - Barry O'Donovan - 2013-01-10)
- [HK] Update Bootstrap to 2.2.1 (1f1032e - Barry O'Donovan - 2013-01-10)
- [BF] Missing end div (7333917 - Barry O'Donovan - 2013-01-05)
- [BF] Typo (78ceb65 - Barry O'Donovan - 2013-01-05)
- [IM] Better initial consistency with menu options (9d70abc - Barry O'Donovan - 2013-01-05)
- [BF] When one tried to edit a switch port, they always got the Add Port(s) form (96f9ca0 - Barry O'Donovan - 2013-01-05)
- [BF] Typo in variable name in user welcome email (d732b59 - Barry O'Donovan - 2013-01-05)
- [BF] I missed the associates tab in my refactoring - bugs fixed (28029ce - Barry O'Donovan - 2013-01-05)
- [IM/BF] Push 64bit interpretation of MySQL NULL date up the function chain (65f9dfe - Barry O'Donovan - 2013-01-05)
- [BF] This relates to the previous IXP Manager. Updated for OSS Frontend. (1e0cc96 - Barry O'Donovan - 2013-01-04)
- [IM] One can now set the default country for forms. (4690259 - Barry O'Donovan - 2013-01-04)
- [IM] Remove static reference and replace with config variable (e5232bc - Barry O'Donovan - 2013-01-04)
- [IM] Remove hardcoded reference to INEX (e5b5e0e - Barry O'Donovan - 2013-01-04)
- [BF/IM] Fix reference to old Doctrine1 code (6229201 - Barry O'Donovan - 2013-01-04)
- [IM] Update welcome email (f0eddfe - Barry O'Donovan - 2012-12-20)
- [BF] This should be a dist file so local installs can have their own ignored copy (2efa72f - Barry O'Donovan - 2012-12-18)
- [BF] On a clean / fresh install there are no candidate users to set as parents (ab2ccdc - Barry O'Donovan - 2012-12-15)
- [BF] Check that DateLeave is a DateTime object before calling methods on it (2eb02f8 - Barry O'Donovan - 2012-12-15)
- [BF] Incorrectly named class (43e8ee8 - Barry O'Donovan - 2012-12-15)
- [HK] Add schema diagrams (eef662a - Barry O'Donovan - 2012-12-12)
- [IM] Adding vendors to fixtures (0b7a559 - Barry O'Donovan - 2012-12-12)
- [BF] Min password length is 8 (9298ad9 - Barry O'Donovan - 2012-12-12)
- [IM] Use better cross-os sh-banhs (365c7f3 - Barry O'Donovan - 2012-12-12)
- [IM] Updating fixtures.php to match documentation on GitHub (92abccc - Barry O'Donovan - 2012-12-12)


# V3.0.0 - 20121212

Initial release of version 3.0.0.

IXP Manager V3 was officially released on 2012-12-12 and primarily featured a significant amount of backend changes:

* code refactoring
* migration to Doctrine2
* removal of all non JQuery JS libraries
* better library consistancy and API interfaces
* security audit

IXP Manager V3 is primarily about INEX trying to fashion IXP Manager as a true open source project rather than something INEX specific.
