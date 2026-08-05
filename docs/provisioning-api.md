# Provisioning API

A stateless, machine-to-machine API for creating members, portal users and connections from an
external system such as an ordering or billing platform.

It exists because the existing API v4 is a read and export surface: it publishes switch
configuration, route server configuration, Nagios targets and DNS data, but has no endpoint
which creates a member, a user or a port. Those live only in the web controllers.

> **Note on the existing endpoints.** The POST endpoints under `admin/api/v4` — including
> `switch/{s}/switch-port-prewired` — cannot be called from outside a browser. That route group
> carries the `web` middleware, and `VerifyCsrfToken` excepts only `login`. This API sits in its
> own route group without `web`, authenticated by API key alone.

## Off by default

These endpoints do not exist until an operator asks for them:

```dotenv
IXP_API_PROVISIONING_ENABLED=true
```

Without it the routes are never registered and every path answers `404`. They are the only
endpoints in IXP Manager which let an external caller create business objects, so an upgrade
must not be able to expose an installation to something it did not choose.

Further keys under `ixp_api.provisioning`: `require_api_key` (default true), `allowed_ips`
(comma-separated addresses and CIDR ranges, empty means unrestricted) and `rate_limit`
(default 60 a minute, zero disables).

## Authentication

Every endpoint requires a **superuser** API key, passed in the header:

```
X-IXP-Manager-API-Key: ixpm_ident1234567_sec876543210...
```

A non-superuser key gets `403`; no key gets `401`.

Two operational points:

- **Keys expire after at most 12 months** (`ixp_fe.api_keys.max_expires_duration`). An
  unattended service will fail with `401` exactly once a year unless the key is rotated.
- **`allowed_ips` on an API key is not enforced anywhere in the code**, and request throttling
  is commented out. The key is therefore a full-access superuser credential. Restrict the
  endpoint at the web server or firewall to the address of the calling system.

## Conventions

- Base path: `/admin/api/v4/provisioning`
- Anything which creates or changes state uses `POST` or `DELETE` — never `GET`. There is
  no `PUT` yet: the API creates, reads and deletes, but does not change.
- Responses are JSON, with one exception worth knowing: `401` and `403` raised by the
  upstream authentication middleware are plain text (`Unauthorized.`, `API key expired`,
  `Insufficient permissions`), not JSON. Only the guards added here answer with `{message}`.
  A client must not assume a JSON body on an authentication failure.
- Validation failures are `422` with `message` and `errors`, the latter keyed by field.
- `201` for something created, `200` for a read or a no-op, `404` for an unknown id, `409` for
  a conflict which is not a validation problem.

## Discovery

```http
GET /admin/api/v4/provisioning/ping
GET /admin/api/v4/provisioning/switch
GET /admin/api/v4/provisioning/infrastructure
GET /admin/api/v4/provisioning/port/free
GET /admin/api/v4/provisioning/vlan/{vlan}/address
```

`ping` confirms the key and returns the resolved username — useful for verifying a deployment
without creating anything.

`port/free` walks every active switch and returns ports with no physical interface attached.
Only ports of type *unset* or *peering* are ever returned: core, management, monitor, fanout
and reseller ports are infrastructure, and offering them would let a caller reassign the
fabric.

```http
GET /port/free?infrastructure=1&limit=50
```

```json
{
  "ports": [
    { "switchport": 412, "name": "xe-0/0/9", "type": 0,
      "switch": 7, "switch_name": "swi1-fra", "infrastructure": 1 }
  ],
  "count": 1,
  "truncated": false
}
```

`vlan/{vlan}/address` lists a VLAN's address pool. `?free=1` restricts it to unassigned
addresses, `?protocol=4` or `6` to one family.

## Members

```http
POST /admin/api/v4/provisioning/member
GET  /admin/api/v4/provisioning/member/{cust}
POST /admin/api/v4/provisioning/member/{cust}/user
```

The member payload is validated with the same rules as the web form, plus a small addition —
see *Validation* below.

```json
{
  "name": "Example Networks Ltd",
  "shortname": "example",
  "abbreviatedName": "EXAMPLE",
  "type": 1,
  "status": 1,
  "datejoin": "2026-08-01",
  "autsys": 65550,
  "maxprefixes": 500,
  "maxprefixesv6": 500,
  "peeringemail": "peering@example.com",
  "peeringmacro": "AS-EXAMPLE",
  "nocemail": "noc@example.com"
}
```

`PeeringDB` lookup remains available at the existing endpoint
`GET /admin/api/v4/customer/query-peeringdb/asn/{asn}` and is a useful way to pre-fill this.

Creating a user sends the welcome email carrying the password reset link — without it the
account cannot be used. The customer comes from the route, so a `custid` in the body is
ignored. The API takes `enabled` (boolean, default true); the web form's inverted `disabled`
field is not reproduced.

## Connections

```http
POST   /admin/api/v4/provisioning/member/{cust}/connection
GET    /admin/api/v4/provisioning/member/{cust}/connection
DELETE /admin/api/v4/provisioning/connection/{vi}
```

Creates the virtual interface, the physical interface on a switch port, the VLAN interface and
its addresses — the equivalent of the web UI's "add a port" wizard.

```json
{
  "vlanid": 1,
  "switch": 7,
  "switchportid": 412,
  "speed": 10000,
  "rate_limit": 1000,
  "ipv4enabled": true,
  "ipv4address": "auto",
  "ipv4hostname": "example.peering.example.net",
  "ipv6enabled": true,
  "ipv6address": "auto",
  "ipv6hostname": "example.peering.example.net",
  "rsclient": true,
  "irrdbfilter": true
}
```

**`"auto"` allocates the next free address** in the VLAN. The web wizard requires an explicit
address because an operator picks one from a list rendered in the browser; an unattended caller
has no list.

An explicit address must already exist in the VLAN's pool. This is stricter than the web path,
which creates the address record on demand — for an unattended caller that turns a typo into a
stray address that nothing will ever clean up.

`status` defaults to *connected* and `duplex` to *full*; both can be set explicitly.

Deletion refuses with `409` if the virtual interface belongs to a core bundle.

## Onboarding

```http
POST /admin/api/v4/provisioning/onboarding
```

Creates a member, optionally a user, and optionally a connection **in one transaction**.

The three endpoints above can be called in sequence, but that sequence is not atomic: a caller
whose second request fails is left with a half-provisioned member. Here the whole order lands
or none of it does.

```json
{
  "reference": "ORD-2026-04711",
  "member":     { "...": "as above" },
  "user":       { "...": "as above, without custid" },
  "connection": { "...": "as above" }
}
```

Sections are nested because `name` means something different for a member, a user and a virtual
interface. `user` and `connection` are optional.

**`reference` makes retries safe.** It is the caller's own order identifier. A caller which
times out and retries with the same reference gets the original result back:

```json
{ "created": false, "reference": "ORD-2026-04711",
  "member": { "id": 97, "shortname": "example" } }
```

with status `200` rather than `201`. Without a reference, a retry creates a second member.

This endpoint does not orchestrate anything beyond the database. Rolling configuration out to
switches, regenerating route server configuration and updating reverse DNS remain the caller's
responsibility; it only makes the IXP Manager side of an order indivisible.

## Validation

The API form requests **extend the web ones** rather than restating their rules, so a rule
changed upstream applies here automatically. Each adds a declared delta, and `RulesParityTest`
subtracts that delta and asserts the remainder is identical — divergence fails a test rather
than surfacing as a behavioural difference.

The customer delta covers fillable attributes the web request does not validate. Three are
worth naming: the columns are `dateleave`, `MD5Support` and `isReseller`, while the web rules
refer to `dateleft` and `md5support`. Those two rules match nothing, so the values currently
reach `Customer::create()` unvalidated. The web rules are left alone; the API validates the
names the schema actually uses.

For connections, `ipv4address` / `ipv6address` are replaced rather than added to, since they
must also accept `"auto"`. Only the address *type* is relaxed - the wizard's conditional
requirement stays, so an enabled address family still demands an address. The delta adds the
fields the wizard has no form control for: `name`, `description`, `mtu`, `lag_framing`,
`fastlacp` and `busyhost`.

(`rate_limit` and `autoneg` were in that delta until this branch was rebased from v7.3.1 onto
`main`, where upstream had added them to the wizard request itself. RulesParityTest failed on
exactly that, which is what it is for.)

## Concurrency

Address allocation takes a row lock inside the request's transaction, so two orders provisioned
at the same moment cannot receive the same address. The unique indexes on
`vlaninterface.ipv4addressid` / `ipv6addressid` are the backstop.

Switch port assignment is checked before anything is written, and the write happens in the same
transaction.

## What is deliberately absent

- **No endpoint reserves an address on its own.** An address held by nothing but a promise
  leaks if the caller then fails. Allocation happens inside the connection transaction, where
  it either sticks or is rolled back with everything else.
- **No suspend or offboard endpoints yet.** Suspension needs somewhere to record the state each
  field held before it was changed, so that restoring is exact; there is no such place in the
  schema today.

## Machine-readable specification

`docs/provisioning-api.openapi.yaml` — OpenAPI 3.1, hand written, no generator package and no
build step. 11 paths, 22 schemas, every field derived from the controllers and the merged
validation rules rather than from this prose.

Worth knowing when reading it: a taken switch port at `/onboarding` answers `422`, not `409`.
`409` arises only from a reused reference whose member has since been deleted, from a raced
resource, and from the core-bundle guard on `DELETE /connection/{vi}`.
