-- Views used on the IXP Manager database

-- view: view_cust_current_active
--
-- This is used to pick up all currently active members.  This can further 
-- be refined by checking for customer type.

DROP VIEW IF EXISTS view_cust_current_active;
CREATE VIEW view_cust_current_active AS
	SELECT * FROM cust cu
	WHERE
		cu.datejoin  <= CURDATE()
	AND	(
			( cu.dateleave IS NULL )
		OR	( cu.dateleave < '1970-01-01' )
		OR	( cu.dateleave >= CURDATE() )
		)
	AND	(cu.status = 1 OR cu.status = 2);

-- view: view_vlaninterface_details_by_custid
--
-- This is used to pick up all interesting details from virtualinterfaces.

DROP VIEW IF EXISTS view_vlaninterface_details_by_custid;
CREATE VIEW view_vlaninterface_details_by_custid AS
	SELECT
        	`pi`.`id` AS `id`,
		vi.custid,
		pi.virtualinterfaceid,
		pi.monitorindex,
		vi.name AS virtualinterfacename,
		vlan.number AS vlan,
		vlan.name AS vlanname,
		vlan.id AS vlanid,
		vlan.rcvrfname,
		vli.id AS vlaninterfaceid,
		vli.ipv4enabled,
		vli.ipv4hostname,
		vli.ipv4canping,
		vli.ipv4monitorrcbgp,
		vli.ipv6enabled,
		vli.ipv6hostname,
		vli.ipv6canping,
		vli.ipv6monitorrcbgp,
		vli.as112client,
		vli.mcastenabled,
		vli.ipv4bgpmd5secret,
		vli.ipv6bgpmd5secret,
		vli.rsclient,
		vli.irrdbfilter,
		vli.busyhost,
		vli.notes,
		v4.address AS ipv4address,
		v6.address AS ipv6address
	FROM
		physicalinterface pi,
		virtualinterface vi,
		vlaninterface vli
	LEFT JOIN (ipv4address v4) ON vli.ipv4addressid = v4.id
	LEFT JOIN (ipv6address v6) ON vli.ipv6addressid = v6.id
	LEFT JOIN vlan ON vli.vlanid = vlan.id
	WHERE
		pi.virtualinterfaceid = vi.id
	AND	vli.virtualinterfaceid = vi.id;

-- view: view_switch_details_by_custid
--
-- This is used to pick up all interesting details from switches.

DROP VIEW IF EXISTS view_switch_details_by_custid;
CREATE VIEW view_switch_details_by_custid AS
	SELECT
		vi.id AS id,
		vi.custid,
		vi.name AS virtualinterfacename,
		pi.virtualinterfaceid,
		pi.status,
		pi.speed,
		pi.duplex,
		pi.monitorindex,
		pi.notes,
		sp.name AS switchport,
		sp.id AS switchportid,
		sp.ifName AS spifname,
		sw.name AS switch,
		sw.id AS switchid,
		sw.vendorid,
		sw.snmppasswd,
		sw.infrastructure,
		ca.name AS cabinet,
		ca.cololocation AS colocabinet,
		lo.name AS locationname,
		lo.shortname AS locationshortname
	FROM
		virtualinterface vi,
		physicalinterface pi,
		switchport sp,
		switch sw,
		cabinet ca,
		location lo
	WHERE
		pi.virtualinterfaceid = vi.id
	AND	pi.switchportid = sp.id
	AND	sp.switchid = sw.id
	AND	sw.cabinetid = ca.id
	AND	ca.locationid = lo.id
;
