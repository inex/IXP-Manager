<?php

// Front end config options for IXP Manager

return [

    /*
    |--------------------------------------------------------------------------
    | Customer Name Format
    |--------------------------------------------------------------------------
    |
    | How to display customer names on the front end. Used /most/ places we
    | reference a customer / member by name.
    |
    | The following substitutions are made:
    |   -  %n  - the customer name ($cust->getName())
    |   -  %a  - the customer abbreviated name ($cust->getAbbreviatedName())
    |   -  %s  - the customer shortname ($cust->getShortname())
    |   -  %i  - the customer's ASN ($cust->getAutsys()): "XXX"
    |   -  %j  - the customer's ASN ($cust->getAutsys()): "[ASXXX]"
    |   -  %k  - the customer's ASN ($cust->getAutsys()): "ASXXX - "
    |   -  %l  - the customer's ASN ($cust->getAutsys()): " - ASXXX"
    |
    | The %j,k,l options exist so that the extra characters can be excluded if the customer does not have an ASN (e.g. Associate member)
    |
    | Default (as of v4.1):
    | "%a [AS%j]"
    */
    'customer_name_format' => "%a %j",


];
