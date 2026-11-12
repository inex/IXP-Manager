# Arista optical media discovery regressions

From the repository root:

```
IXPM_SOURCE_DIR="$PWD" php tests/Optics/standalone/test-parser.php
IXPM_SOURCE_DIR="$PWD" php tests/Optics/standalone/test-discovery.php
php tests/Optics/standalone/test-interface-mapping.php
```

These dependency-free synthetic checks exercise the parser and discovery status
handling. The interface-mapping test exercises a copy of the matching algorithm;
it does not replace a database-backed `Switcher::snmpPollSwitchPorts()` integration
test. The discovery test defines an isolated SNMP stub and must run in its own PHP
process, outside the application autoloader.

No tests connect to switches or require production credentials. Physical removal,
OSFP, IPv6-only SNMP management, and 800G/1.6T hardware have not been validated.
