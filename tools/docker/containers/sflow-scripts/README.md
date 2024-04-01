# Notes

* Need to use a secure connection or restore the default mysql password plugin:
```
mysql --protocol=TCP --port 33060 -u root
ALTER USER 'ixpmanager' IDENTIFIED WITH mysql_native_password BY 'ixpmanager';
```

## nfdump

Only accept from from a single exporter and only extension 6,7,8,10,11 are accepted. Run a given command when files are rotated 
and automatically expire flows:
```nfcapd -w -D -T 6,7,8,10,11 -n upstream,192.168.1.1,/netflow/spool/upstream -p 23456 -B 128000 -s 100 -x '/path/command -r %d/%f'  -P /var/run/nfcapd/nfcapd.pid -e```

From ```man nfcapd```:
Extensions:
           v5/v7/v9/IPFIX extensions:
            1 input/output interface SNMP numbers.
            2 src/dst AS numbers.
            3 src/dst mask, (dst)TOS, direction.
            4 line Next hop IP addr line
            5 line BGP next hop IP addr line
->          6 src/dst vlan id labels
->          7 counter output packets
->          8 counter output bytes
            9 counter aggregated flows
->         10 in_src/out_dst MAC address
->         11 in_dst/out_src MAC address
           12 MPLS labels 1-10
           13 Exporting router IPv4/IPv6 address
           14 Exporting router ID
           15 BGP adjacent prev/next AS
           16 time stamp flow received by the collector
           NSEL/ASA/NAT extensions
           26 NSEL     ASA event, xtended event, ICMP type/code
           27 NSEL/NAT xlate ports
           28 NSEL/NAT xlate IPv4/IPv6 addr
           29 NSEL     ASA ACL ingress/egress acl ID
           30 NSEL     ASA username
           NEL/NAT extensions
           31 NAT event, ingress egress vrfid
           32 NAT Block port allocation - block start, end step and size
           latency extension
           64 nfpcapd/nprobe client/server/application latency"},
