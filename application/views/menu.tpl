
<table width="100%" class="menubar" cellpadding="0" cellspacing="0" border="0">
<tr>
    <td class="menubackgr" style="padding-left:5px;">
        <div id="myMenuID"></div>
        <script language="JavaScript" type="text/javascript" charset="t" defer="u">
            var myMenu =
            [
                {if $identity.user.privs eq 3}
                    [null,'Home','{genUrl controller="customer"}',null,'Home'],
                {elseif $identity.user.privs eq 2}
                    [null,'User Admin','{genUrl controller="cust-admin"}',null,'User Admin'],
                {elseif $identity.user.privs eq 1}
                    [null,'Dashboard','{genUrl controller="dashboard"}',null,'Dashboard'],
                {/if}
                _cmSplit,
                {if $identity.user.privs eq 3}
                    [null,'Super User',null,null,'Super User',
                    ['<img src="{genUrl}/images/joomla-admin/menu/globe4.png" />',     'Locations',            '{genUrl controller="location"}',
                                    null,'Locations' ],
                    ['<img src="{genUrl}/images/joomla-admin/menu/cabinets.png" />',   'Cabinets',             '{genUrl controller="cabinet"}',
                                    null,'Cabinets'  ],
                    ['<img src="{genUrl}/images/joomla-admin/menu/interface.png" />',  'Circuits',             '{genUrl controller="logical-circuit"}',
                                    null,'Circuits',

                        ['<img src="{genUrl}/images/joomla-admin/menu/interface.png" />', 'Logical Circuits',
                            '{genUrl controller="logical-circuit"}',
                            null,'Logical Circuits',
                        ],

                        ['<img src="{genUrl}/images/joomla-admin/menu/interface.png" />', 'Physical Circuits',
                             '{genUrl controller="physical-circuit"}',
                             null,'Physical Circuits',
                        ]

                    ],
                    ['<img src="{genUrl}/images/joomla-admin/menu/switch.png" />',     'Switches',             '{genUrl controller="switch"}',
                                    null,'Switches',
                        ['<img src="{genUrl}/images/joomla-admin/menu/interface.png" />',  'Switch Ports',  '{genUrl controller="switch-port"}',
                            null,'Switch Ports'   ]
                    ],
                    ['<img src="{genUrl}/images/joomla-admin/menu/connection.png" />', 'Patch Panels',         '{genUrl controller="patch-panel"}',
                                     null,'Patch Panels',
                        ['<img src="{genUrl}/images/joomla-admin/menu/interface.png" />',  'Patch Panel Ports',  '{genUrl controller="patch-panel-port"}',
                            null,'Patch Panel Ports'   ]
                    ],
                    ['<img src="{genUrl}/images/joomla-admin/menu/vendors.png" />',    'Vendors',              '{genUrl controller="vendor"}',
                                    null,'Vendors'   ],
                    ['<img src="{genUrl}/images/joomla-admin/menu/console.png" />',    'Console Connections',  '{genUrl controller="console-server-connection"}',
                                    null,'Console Connections'   ],
                    ['<img src="{genUrl}/images/joomla-admin/menu/vlan.png" />',       'VLANs',                '{genUrl controller="vlan"}',
                                    null, 'VLANs'   ]
                    ],
                    _cmSplit,
                    [null,'Admin',null,null,'Admin',
	                    ['<img src="{genUrl}/images/joomla-admin/menu/users.png" />','Members',  '{genUrl controller="customer"}',
	                                    null,'Members'   ],
	                    ['<img src="{genUrl}/images/joomla-admin/menu/interface.png" />', 'Interfaces', '{genUrl controller="virtual-interface"}', null, 'Interfaces',

	                            ['<img src="{genUrl}/images/joomla-admin/menu/interface.png" />','Physical Interfaces',  '{genUrl controller="physical-interface"}',
	                                        null, 'Physical Interfaces'   ],
	                            ['<img src="{genUrl}/images/joomla-admin/menu/interface.png" />','Virtual Interfaces',  '{genUrl controller="virtual-interface"}',
	                                        null, 'Virtual Interfaces'   ],
	                            ['<img src="{genUrl}/images/joomla-admin/menu/interface.png" />','VLAN Interfaces',  '{genUrl controller="vlan-interface"}',
	                                        null, 'VLAN Interfaces'   ]

	                    ],
                        ['<img src="{genUrl}/images/joomla-admin/menu/install.png" />', 'Provisioning', null, null, 'Provisioning',

                            ['<img src="{genUrl}/images/joomla-admin/menu/interface.png" />','New Interface',  '{genUrl controller="provision" action="interface"}',
                                     null, 'New Interface'   ]
                        ],
	                    ['<img src="{genUrl}/images/joomla-admin/menu/kontact.png" />','Contacts',  '{genUrl controller="contact"}',
	                                    null, 'Contacts'   ],
	                    ['<img src="{genUrl}/images/joomla-admin/menu/system-users.png" />','Users',  '{genUrl controller="user"}',
	                                    null, 'Users'   ],

	            	    ['<img src="{genUrl}/images/joomla-admin/menu/rack.png" />', 'IP Addresses', null, null, 'IP Addresses',

	                        ['<img src="{genUrl}/images/joomla-admin/menu/rack.png" />','IPv4 Addresses',  null, null, 'IPv4  Addresses',

		                        [ '<img src="{genUrl}/images/joomla-admin/menu/rack.png" />', 'Peering VLAN #1',
			                        	'{genUrl controller='ipv4-address' action='list' vlan='10'}', null, 'Peering VLAN #1'
								],
		                        [ '<img src="{genUrl}/images/joomla-admin/menu/rack.png" />', 'Peering VLAN #2',
		                        	'{genUrl controller='ipv4-address' action='list' vlan='12'}', null, 'Peering VLAN #2'
								],
		                        [ '<img src="{genUrl}/images/joomla-admin/menu/rack.png" />', 'VoIP Peering VLAN #1',
		                        	'{genUrl controller='ipv4-address' action='list' vlan='70'}', null, 'VoIP Peering VLAN #1'
								],
		                        [ '<img src="{genUrl}/images/joomla-admin/menu/rack.png" />', 'VoIP Peering VLAN #2',
		                        	'{genUrl controller='ipv4-address' action='list' vlan='72'}', null, 'VoIP Peering VLAN #2'
								]
							],

	                        ['<img src="{genUrl}/images/joomla-admin/menu/rack.png" />','IPv6 Addresses',  null, null, 'IPv6  Addresses',

		                        [ '<img src="{genUrl}/images/joomla-admin/menu/rack.png" />', 'Peering VLAN #1',
			                        	'{genUrl controller='ipv6-address' action='list' vlan='10'}', null, 'Peering VLAN #1'
								],
		                        [ '<img src="{genUrl}/images/joomla-admin/menu/rack.png" />', 'Peering VLAN #2',
		                        	'{genUrl controller='ipv6-address' action='list' vlan='12'}', null, 'Peering VLAN #2'
								],
		                        [ '<img src="{genUrl}/images/joomla-admin/menu/rack.png" />', 'VoIP Peering VLAN #1',
		                        	'{genUrl controller='ipv6-address' action='list' vlan='70'}', null, 'VoIP Peering VLAN #1'
								],
		                        [ '<img src="{genUrl}/images/joomla-admin/menu/rack.png" />', 'VoIP Peering VLAN #2',
		                        	'{genUrl controller='ipv6-address' action='list' vlan='72'}', null, 'VoIP Peering VLAN #2'
								]

							],

 	                    ],


	                    ['<img src="{genUrl}/images/joomla-admin/menu/drive-optical.png" />','Customer Kit',  '{genUrl controller="cust-kit"}',
	                                    null, 'Customer Kit'   ],
	                    ['<img src="{genUrl}/images/joomla-admin/menu/meetings.png" />','Meetings',  '{genUrl controller="meeting"}',
	                                    null, 'Meetings',
	                        ['<img src="{genUrl}/images/joomla-admin/menu/meetings.png" />', 'Add / Edit',  '{genUrl controller="meeting"}',
	                            null, 'Add / Edit'   ],
	                        ['<img src="{genUrl}/images/joomla-admin/menu/meetings.png" />', 'Presentations',  '{genUrl controller="meeting-item"}',
	                            null, 'Presentations'   ],
	                        ['<img src="{genUrl}/images/joomla-admin/menu/meetings.png" />', 'Member View',  '{genUrl controller="meeting" action="read"}',
	                                null, 'Member View'   ],
	                        ['<img src="{genUrl}/images/joomla-admin/menu/meetings.png" />', 'Instructions',  '{genUrl controller="admin" action="static" page="instructions-meetings"}',
	                                null, 'Instructions'   ]
	                    ],
	                    ['<img src="{genUrl}/images/joomla-admin/menu/contents.png" />','Change Log',  '{genUrl controller="change-log"}',
	                                    null, 'Change Log'   ],
	                    ['<img src="{genUrl}/images/joomla-admin/menu/config.png" />','IRRDB Config',  '{genUrl controller="irrdb-config"}',
	                                    null, 'IRRDB Config'   ],
	                    ['<img src="{genUrl}/images/joomla-admin/menu/config.png" />', 'Utils', null, null, 'Utils',
	                        ['<img src="{genUrl}/images/joomla-admin/menu/php.png" />','PHP Info',  '{genUrl controller="utils" action="phpinfo"}',
	                            null, 'PHP Info'   ],
	                        ['<img src="{genUrl}/images/joomla-admin/menu/php.png" />','APC Info',  '{genUrl controller="utils" action="apcinfo"}',
	                            null, 'APC Info'   ]
	                    ]
                    ],
                    _cmSplit,
                    [null,'Monitoring',null,null,'Monitoring',
                    ['<img src="{genUrl}/images/joomla-admin/menu/contents.png" />','SEC Events',  '{genUrl controller="sec-viewer"}',
                                    null, 'SEC Events'   ]
                    ],
                    _cmSplit,
                {/if}
                {if $identity.user.privs neq 2}
                    [null,'Member Information',null,null,'Member Information',
                        ['<img src="{genUrl}/images/joomla-admin/menu/switch.png" />','Switch Configuration','{genUrl controller="dashboard" action="switch-configuration"}',null,'Switch Configuration'],
                        ['<img src="{genUrl}/images/joomla-admin/menu/users.png" />','Member Details','{genUrl controller="dashboard" action="members-details-list"}',null,'Member Details'],
                        ['<img src="{genUrl}/images/joomla-admin/menu/meetings.png" />','Meetings','{genUrl controller="meeting" action="read"}',null,'Meetings'],
                        {if $identity.user.privs eq 1}
                            ['<img src="{genUrl}/images/joomla-admin/menu/controlpanel.png" />',
                                'SEC Event Log', '{genUrl controller="sec-viewer" action="read"}', null,
                                'SEC Event Log'
                            ]
                        {/if}
                    ],
                    _cmSplit,
                {/if}
                {if $identity.user.privs neq 2}
                    [null,'Peering',null,null,'Peering',
                        ['<img src="{genUrl}/images/joomla-admin/menu/joomla_16x16.png" />','Peering Matrices', null, null, 'Peering Matrices',

                            {foreach from=$config.peering_matrix.public key=index item=lan}
                                ['<img src="{genUrl}/images/joomla-admin/menu/joomla_16x16.png" />',
                                    '{$lan.name}', '{genUrl controller="dashboard" action="peering-matrix" lan=$index}',
                                    'ixp_new_window',
                                    '{$lan.name}'
                                ],
                            {/foreach}

                        ],
                        {if $identity.user.privs eq 1 and $customer->isFullMember()}
                            ['<img src="{genUrl}/images/joomla-admin/menu/joomla_16x16.png" />',
                                'My Peering Manager','{genUrl controller="dashboard" action="my-peering-matrix"}',
                                null, 'My Peering Manager'
                            ],
                            {/if}
                    ],
                    _cmSplit,
                {/if}
                {if $identity.user.privs eq 1 or $identity.user.privs eq 3}
                    [null,'Documentation',null,null,'Documentation',
                        ['<img src="{genUrl}/images/joomla-admin/menu/document.png" />','Fees and Charges',
                            '{genUrl controller="dashboard" action="static" page="fees"}',
                            null, 'Fees and Charges'
                        ],
                        ['<img src="{genUrl}/images/joomla-admin/menu/document.png" />','Equipment Housing',
                            '{genUrl controller="dashboard" action="static" page="housing"}',
                            null, 'Equipment Housing'
                        ],
                        ['<img src="{genUrl}/images/joomla-admin/menu/document.png" />','Miscellaneous Benefits',
                            '{genUrl controller="dashboard" action="static" page="misc-benefits"}',
                            null, 'Miscellaneous Benefits'
                        ],
                        ['<img src="{genUrl}/images/joomla-admin/menu/document.png" />','Technical Support',
                            '{genUrl controller="dashboard" action="static" page="support"}',
                            null, 'Technical Support'
                        ],
                        ['<img src="{genUrl}/images/joomla-admin/menu/document.png" />','Connecting Switches',
                            '{genUrl controller="dashboard" action="static" page="switches"}',
                            null, 'Connecting Switches'
                        ],
                        ['<img src="{genUrl}/images/joomla-admin/menu/document.png" />','Port Security Policies',
                            '{genUrl controller="dashboard" action="static" page="port-security"}',
                            null, 'Port Security Policies'
                        ],
                        ['<img src="{genUrl}/images/joomla-admin/menu/document.png" />','AS112 Service',
                            '{genUrl controller="dashboard" action="as112"}',
                            null, 'AS112 Service'
                        ],
                        ['<img src="{genUrl}/images/joomla-admin/menu/document.png" />','Route Servers',
                            '{genUrl controller="dashboard" action="rs-info"}',
                            null, 'Route Servers'
                        ]
                    ],
                {/if}
                {if $identity.user.privs eq 3}
                    [null,'Statistics','{genUrl controller="customer" action="statistics-overview"}',null,'Statistics',
                        ['<img src="{genUrl}/images/joomla-admin/menu/system-users.png" />', 'Last Logins',  '{genUrl controller="user" action="last"}',
                            null, 'Last Logins'   ],
                        ['<img src="{genUrl}/images/joomla-admin/menu/rack.png" />','Overall Peering Statistics',  '{genUrl controller="dashboard" action="traffic-stats"}',
                            null, 'Overall Peerings Statistics'   ],
                        ['<img src="{genUrl}/images/joomla-admin/menu/users.png" />','Member Statistics - Aggregate',  '{genUrl controller="customer" action="statistics-overview"}',
                            null, 'Member Statistics - Aggregate'   ],
                        ['<img src="{genUrl}/images/joomla-admin/menu/users.png" />','Member Statistics - By LAN', null, null, 'Member Statistics - By LAN',

                             {foreach from=$config.peering_matrix.public key=index item=lan}
                                 ['<img src="{genUrl}/images/joomla-admin/menu/users.png" />',
                                     '{$lan.name}', '{genUrl controller="customer" action="statistics-by-lan" lan=$lan.number}',
                                     null,
                                     '{$lan.name}'
                                 ],
                             {/foreach}

                        ],
                        ['<img src="{genUrl}/images/joomla-admin/menu/users.png" />','List Members',  '{genUrl controller="customer" action="statistics-list"}',
                             null, 'List Members'   ],
                        ['<img src="{genUrl}/images/joomla-admin/menu/rack.png" />', 'Trunk Graphs',  '{genUrl controller="dashboard" action="trunk-graphs"}',
                             null, 'Trunk Graphs'   ],
                        ['<img src="{genUrl}/images/joomla-admin/menu/rack.png" />', 'Switch Aggregate Graphs',  '{genUrl controller="dashboard" action="switch-graphs"}',
                              null, 'Switch Aggregate Graphs'   ],
                        ['<img src="{genUrl}/images/joomla-admin/menu/rack.png" />', 'League Table',  '{genUrl controller="customer" action="league-table"}',
                            null, 'League Table'   ],
                        ['<img src="{genUrl}/images/joomla-admin/menu/rack.png" />', '95th Percentiles', '{genUrl controller="customer" action="ninety-fifth"}',
                             null, '95th Percentiles'   ]
                    ],
                {elseif $identity.user.privs eq 1 and $customer->isFullMember()}
                    [null,'Statistics','{genUrl controller="dashboard" action="statistics"}',null,'Statistics',
                        ['<img src="{genUrl}/images/joomla-admin/menu/rack.png" />','Overall Peering Statistics',  '{genUrl controller="dashboard" action="traffic-stats"}',
                            null, 'Overall Peerings Statistics'   ],
                        ['<img src="{genUrl}/images/joomla-admin/menu/rack.png" />', 'My Statistics', '{genUrl controller="dashboard" action="statistics"}',
                            null, 'My Statistics'   ],
                        ['<img src="{genUrl}/images/joomla-admin/menu/rack.png" />', 'Trunk Graphs',  '{genUrl controller="dashboard" action="trunk-graphs"}',
                            null, 'Trunk Graphs'   ],
                        ['<img src="{genUrl}/images/joomla-admin/menu/rack.png" />', 'Switch Aggregate Graphs',  '{genUrl controller="dashboard" action="switch-graphs"}',
                            null, 'Switch Aggregate Graphs'   ]
                    ],
                {/if}
                _cmSplit,
                {if $identity.user.privs eq 1}
                    [null, 'Support','{genUrl controller="dashboard" action="static" page="support"}',null,'Support'],
                    _cmSplit,
                {/if}
                {if $identity.user.privs eq 3 and isset( $config.menu.staff_links )}
                    [null, 'Staff Links', null, null, 'Staff Links',

                     {foreach from=$config.menu.staff_links item=i}

                         [ '<img src="{genUrl}/images/joomla-admin/menu/globe1.png" />',
                             '{$i.name}', '{$i.link}', null, '{$i.name}'
                         ],

                     {/foreach}

                    ],
                    _cmSplit,
                {/if}
                {if $identity.user.privs neq 1}
                    [null,'Profile','{genUrl controller="profile"}',null,'Profile'],
                {else}
                    [null,'Profile','{genUrl controller="profile"}',null,'Profile',
                        ['<img src="{genUrl}/images/joomla-admin/menu/controlpanel.png" />', 'SEC Event Notifications',  '{genUrl controller="dashboard" action="sec-event-email-config"}',
                            null, 'SEC Event Notifications'   ]
                    ],
                {/if}
                {if isset( $session->switched_user_from ) and $session->switched_user_from}
                    [null,'[Switch Back]','{genUrl controller="auth" action="switch-back"}',null,'[Switch Back]']
                {else}
                    [null,'[Logout]','{genUrl controller="auth" action="logout"}',null,'[Logout]']
                {/if}
            ];
            cmDraw ('myMenuID', myMenu, 'hbr', cmThemeOffice, 'ThemeOffice');
        </script>
    </td>
</tr>
</table>

<div id="bd">

<br />

