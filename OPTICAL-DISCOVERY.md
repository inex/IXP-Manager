# Optional Arista optical media discovery

Some Arista switches report an empty or unrecognized MAU-MIB media type even when
ENTITY-MIB identifies the installed transceiver. The existing switch SNMP poll can
optionally use those descriptions as a fallback. Valid MAU classifications always
take precedence. Other vendors and switches outside the allowlist are unchanged.

Disabled by default. Set these environment variables and rebuild the application
configuration cache using the normal deployment workflow:

```
IXPM_OPTICS_ENABLED=true
IXPM_OPTICS_SWITCH_IDS=1,2,3
```

Use your installation's switch IDs. A nonempty list takes precedence over the
legacy single-ID setting `IXPM_OPTICS_SWITCH_ID`. The vendor name must be Arista
(case-insensitive).

Exact Ethernet interface names take precedence; a breakout interface can inherit
from its immediate physical parent only. Unknown or conflicting descriptions are
ignored. When ENTITY-MIB polling fails, an existing known classification is retained
if MAU-MIB is empty; the warning contains a generic error, not the SNMP exception.
A successful empty inventory is distinct from a failed poll. Physical transceiver
removal has not been hardware-tested.

Pilot one switch with a non-saving poll, then compare a normal poll against a saved
inventory baseline before enabling more switches. Check port identity, interface
index, speed and media. To disable, set `IXPM_OPTICS_ENABLED=false` and rebuild the
configuration cache. Disabling discovery does not undo previously saved media values.

Originally released at https://github.com/repstein2/ixpmanager-optical-discovery.
The released implementation was validated on IXP Manager 7.3.1 across 28 Arista
switches at three Community IX exchanges, populating 204 previously empty media
values with no unexpected identity/count/index/speed changes in the recorded
baseline comparisons. This is historical deployment evidence, not a live test of
the current upstream revision. DWDM wavelength/frequency discovery, OSFP,
IPv6-only SNMP management, and 800G/1.6T hardware are outside the validated scope.

See `tests/Optics/README.md` for the synthetic regression commands and limitations.
