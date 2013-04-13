#!/usr/bin/php
<?php

// MySQL version of a simple script to retrospectively create contacts for CUSTADMIN users
//
// Required for migrations in V3.0.9 to ensure no user is without a related contact.
//
// Barry O'Donovan 20130413
//
// VERY SIMPLE / ROUGH / HACKISH

@mysql_connect( 'host', 'user', 'pass' );
mysql_select_db( 'database' );

$q = mysql_query( "SELECT u.id, u.custid, u.email FROM user u LEFT JOIN cust ON u.custid = cust.id "
    . "WHERE u.id NOT IN ( SELECT user_id AS id FROM contact WHERE user_id IS NOT NULL ) AND privs  = 2" 
);

while( $r = mysql_fetch_assoc( $q ) )
{
    $q2 = mysql_query( 'SELECT * FROM cust WHERE id = ' . $r['custid'] );
    
    if( !( $r2 = mysql_fetch_assoc( $q2 ) ) )
        die( 'failed' );
        
    echo "INSERT INTO contact ( custid, name, email, mobile, facilityaccess, mayauthorize, creator, created, user_id )\n"
        . "    VALUES ( {$r['custid']}, '{$r2['name']} Admin Account', '{$r['email']}', '{$r2['noc24hphone']}', 0, 0, 'barryo', NOW(), {$r['id']} );\n\n";
}

mysql_close();


