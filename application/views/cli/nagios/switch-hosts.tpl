#
# {$sw.name} - {$sw.Cabinet.cololocation}, {$sw.Cabinet.Location.name}
#

define host {ldelim}
        use                     inex-production-switch
        host_name               {$sw.name}
        alias                   {$sw.name}
        address                 {$sw.ipv4addr}
{rdelim}


