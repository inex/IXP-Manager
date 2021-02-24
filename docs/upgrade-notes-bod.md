# Upgrade Notes

## Warning

These are @barryo's draft pre-release upgrade notes that will be 
used to draft the next upgrade release / instructions.

They are not production ready and should not be used unless you're
very very very brave.

## v5.3.0

UPDATE cust SET dateleave = NULL where CAST(`dateleave` AS CHAR(10)) = '0000-00-00'

###

IX-F Export - infrastructure country, facility country, city.

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


 
 
```
 ./artisan grapher:backend:mrtg:upgrade -B cp | grep log


cat cb-aggregate-00001-sidea-bits.log| awk  '{ if( NF == 3 ) { print $1, $3, $2; } else { print $1, $3, $2, $5, $4; } }' | head
```
 
 
 
 ### 2FA
 
 * sessions moved to database (encrypted)
 * changing password logs out all other sessions
 * session handler needs to be changed to database. Need a code check for
 this I think.
