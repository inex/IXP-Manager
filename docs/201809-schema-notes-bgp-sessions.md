# Notes for `bgp_sessions` for Release Notes

Need a trigger on `bgpsessiondata`:

```sql
CREATE TRIGGER bgp_sessions_update AFTER INSERT ON `bgpsessiondata` FOR EACH ROW 

  BEGIN
  
      IF NOT EXISTS ( SELECT 1 FROM bgp_sessions WHERE srcipaddressid = NEW.srcipaddressid AND protocol = NEW.protocol AND dstipaddressid = NEW.dstipaddressid ) THEN
        INSERT INTO bgp_sessions
      ( srcipaddressid, protocol, dstipaddressid, packetcount, last_seen, source )
        VALUES
          ( NEW.srcipaddressid, NEW.protocol, NEW.dstipaddressid, NEW.packetcount, NOW(), NEW.source );
      ELSE
          UPDATE bgp_sessions SET
                last_seen   = NOW(),
                packetcount = packetcount + NEW.packetcount
          WHERE
                srcipaddressid = NEW.srcipaddressid AND protocol = NEW.protocol AND dstipaddressid = NEW.dstipaddressid;
      END IF;
      
      IF NOT EXISTS ( SELECT 1 FROM bgp_sessions WHERE dstipaddressid = NEW.srcipaddressid AND protocol = NEW.protocol AND srcipaddressid = NEW.dstipaddressid ) THEN
        INSERT INTO bgp_sessions
      ( srcipaddressid, protocol, dstipaddressid, packetcount, last_seen, source )
        VALUES
          ( NEW.dstipaddressid, NEW.protocol, NEW.srcipaddressid, NEW.packetcount, NOW(), NEW.source );
      ELSE
        UPDATE bgp_sessions SET
            last_seen   = NOW(),
            packetcount = packetcount + NEW.packetcount
        WHERE
            dstipaddressid = NEW.srcipaddressid AND protocol = NEW.protocol AND srcipaddressid = NEW.dstipaddressid;
      END IF;
  
  END

```

To populate the data from `bgpsessiondata`:


```sql
INSERT INTO bgp_sessions (srcipaddressid, dstipaddressid, protocol, packetcount, last_seen, source)
    SELECT
        srcipaddressid, dstipaddressid, protocol, count(packetcount) AS packetcount, max(timestamp) AS last_seen, any_value (source) AS source
    FROM
        bgpsessiondata
    GROUP BY
        srcipaddressid, dstipaddressid, protocol;
```
