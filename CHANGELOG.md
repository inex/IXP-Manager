

Schema update required:

    CREATE TABLE ContactToGroup (
        contact_id INT NOT NULL, contact_group_id BIGINT NOT NULL, 
        INDEX IDX_7DB29B53E7A1254A (contact_id), 
        INDEX IDX_7DB29B53647145D0 (contact_group_id), 
        PRIMARY KEY(contact_id, contact_group_id)
    ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
    
    CREATE TABLE ContactGroup (
        id BIGINT AUTO_INCREMENT NOT NULL, name VARCHAR(20) NOT NULL, 
        description VARCHAR(255) NOT NULL, type VARCHAR(20) NOT NULL, 
        active TINYINT(1) NOT NULL, `limit` INT NOT NULL, created DATETIME NOT NULL, 
        UNIQUE INDEX UNIQ_C1EA42DD5E237E06 (name), 
        PRIMARY KEY(id)
    ) DEFAULT CHARACTER SET utf8 COLLATE utf8_unicode_ci ENGINE = InnoDB;
    
    ALTER TABLE ContactToGroup ADD CONSTRAINT FK_7DB29B53E7A1254A 
        FOREIGN KEY (contact_id) REFERENCES contact (id);

    ALTER TABLE ContactToGroup ADD CONSTRAINT FK_7DB29B53647145D0 
        FOREIGN KEY (contact_group_id) REFERENCES ContactGroup (id);

    ALTER TABLE contact 
        ADD user_id INT DEFAULT NULL, 
        ADD position VARCHAR(50) DEFAULT NULL, 
        ADD notes LONGTEXT DEFAULT NULL, 
        CHANGE name name VARCHAR(255) NOT NULL, 
        CHANGE phone phone VARCHAR(50) DEFAULT NULL, 
        CHANGE mobile mobile VARCHAR(50) DEFAULT NULL, 
        CHANGE facilityaccess facilityaccess TINYINT(1) NOT NULL, 
        CHANGE mayauthorize mayauthorize TINYINT(1) NOT NULL;
    
    CREATE UNIQUE INDEX UNIQ_4C62E638A76ED395 ON contact (user_id)

    ALTER TABLE contact ADD CONSTRAINT FK_4C62E638A76ED395 
        FOREIGN KEY (user_id) REFERENCES user (id);


# V3.0.8 - 2013

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
