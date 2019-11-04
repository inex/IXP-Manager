# Upgrade Notes

## Warning

These are @barryo's draft pre-release upgrade notes that will be 
used to draft the next upgrade release / instructions.

They are not production ready and should not be used unless you're
very very very brave.

## v5.3.0

### PPP History

```sql
UPDATE patch_panel_port_history ppph
SET ppph.cust_id = (
  SELECT c.id
  FROM cust c
  WHERE c.name = ppph.customer
)
```

### MRTG Graphs / Core Bundles


 
 
 
 