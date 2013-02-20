# V3.0.1 - 20130220

[DB] ORM schema update due to update of ORM Manager. Inc. change to VLAN table. (cb70971 - Barry O'Donovan - 2013-02-20)
[IM] Meetings updated with some bugfixes: (a81e554 - Barry O'Donovan - 2013-02-14)
[BF] IXP FrontEnd extends AuthRequired which is an issue for public display of meeting details (6610127 - Barry O'Donovan - 2013-02-14)
[BF] Some pages are public access and don't require this for non-logged in users (30834ec - Barry O'Donovan - 2013-02-14)
[BF] IXP V3 using Doctrine2 from PEAR/Git rather than SVN (3041603 - Barry O'Donovan - 2013-02-13)
[BF] Small bug fixes from going live with V3 on INEX (23a64b2 - Barry O'Donovan - 2013-02-13)
[HK] Refactor INEX_ library to more appropriate IXP_ library (b9ddc24 - Barry O'Donovan - 2013-02-12)
[BF] Fix table width in Chrome (1f96d5b - Barry O'Donovan - 2013-01-10)
[HK] Freshly pressed CSS/JS files (1831cbf - Barry O'Donovan - 2013-01-10)
[HK] Update Bootstrap to 2.2.1 (1f1032e - Barry O'Donovan - 2013-01-10)
[BF] Missing end div (7333917 - Barry O'Donovan - 2013-01-05)
[BF] Typo (78ceb65 - Barry O'Donovan - 2013-01-05)
[IM] Better initial consistency with menu options (9d70abc - Barry O'Donovan - 2013-01-05)
[BF] When one tried to edit a switch port, they always got the Add Port(s) form (96f9ca0 - Barry O'Donovan - 2013-01-05)
[BF] Typo in variable name in user welcome email (d732b59 - Barry O'Donovan - 2013-01-05)
[BF] I missed the associates tab in my refactoring - bugs fixed (28029ce - Barry O'Donovan - 2013-01-05)
[IM/BF] Push 64bit interpretation of MySQL NULL date up the function chain (65f9dfe - Barry O'Donovan - 2013-01-05)
[BF] This relates to the previous IXP Manager. Updated for OSS Frontend. (1e0cc96 - Barry O'Donovan - 2013-01-04)
[IM] One can now set the default country for forms. (4690259 - Barry O'Donovan - 2013-01-04)
[IM] Remove static reference and replace with config variable (e5232bc - Barry O'Donovan - 2013-01-04)
[IM] Remove hardcoded reference to INEX (e5b5e0e - Barry O'Donovan - 2013-01-04)
[BF/IM] Fix reference to old Doctrine1 code (6229201 - Barry O'Donovan - 2013-01-04)
[IM] Update welcome email (f0eddfe - Barry O'Donovan - 2012-12-20)
[BF] This should be a dist file so local installs can have their own ignored copy (2efa72f - Barry O'Donovan - 2012-12-18)
[BF] On a clean / fresh install there are no candidate users to set as parents (ab2ccdc - Barry O'Donovan - 2012-12-15)
[BF] Check that DateLeave is a DateTime object before calling methods on it (2eb02f8 - Barry O'Donovan - 2012-12-15)
[BF] Incorrectly named class (43e8ee8 - Barry O'Donovan - 2012-12-15)
[HK] Add schema diagrams (eef662a - Barry O'Donovan - 2012-12-12)
[IM] Adding vendors to fixtures (0b7a559 - Barry O'Donovan - 2012-12-12)
[BF] Min password length is 8 (9298ad9 - Barry O'Donovan - 2012-12-12)
[IM] Use better cross-os sh-banhs (365c7f3 - Barry O'Donovan - 2012-12-12)
[IM] Updating fixtures.php to match documentation on GitHub (92abccc - Barry O'Donovan - 2012-12-12)


# V3.0.0 - 20121212

Initial release of version 3.0.0.

IXP Manager V3 was officially released on 2012-12-12 and primarily featured a significant amount of backend changes:

* code refactoring
* migration to Doctrine2
* removal of all non JQuery JS libraries
* better library consistancy and API interfaces
* security audit

IXP Manager V3 is primarily about INEX trying to fashion IXP Manager as a true open source project rather than something INEX specific.
