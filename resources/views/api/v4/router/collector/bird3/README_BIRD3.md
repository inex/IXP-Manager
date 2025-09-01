# BIRD3 Template

Templates here are an initial effort at BIRD 3 collector router.

**2025-09-01 - rob@lonap.net. It is working not yet fully tested at LONAP. Further updates may be needed.**

## Skinning

As always, you can skin your own version of any of these template files by making a copy of them
in your local skin directory.

These files would live in:

```
/srv/ixpmanager/resources/skins/<your_skin>/api/v4/router/collector/bird3
```

If you make any important changes, please let us know, so they can be considered for the
default templates, so you don't need to skin it forever.

## Updates

This template is largely the same as the previous BIRD 2 template, but with the following changes:

### BIRD Peer Template

Updated to use BIRD's peer template feature. This avoids repeating the same configuration for every BGP peer. 
(This is already used in the route server template for BIRD 2.)

As such, there is a new file, `neighbor-template.foil.php` which defines the BIRD template `tb_rcpeer`. 
BIRD will configure all BGP peers to inherit the same settings from this template.

**If you made any custom changes to `neighbors.foil.php`, you may need to undo these and put them in `neighbor-template.foil.php`** instead.

### BIRD 3 Threads

BIRD 3 introduces new multi-threaded capabilities.

`threads.foil.php` contains default threads configuration of `threads 1;`. This means BIRD will mostly behave the same as it did for BIRD 2. (At least for BGP.)

However, **this setting is in the process of being deprecated** and is replaced with a new format, thread groups:

```
thread group worker {
    threads N;
    default;
}
```

**Due to a bug in BIRD <= 3.1.2**, this new style threads configuration does not currently work. [It is due to be fixed in BIRD 3.1.3.](http://trubka.network.cz/pipermail/bird-users/2025-August/018353.html)

You may need to skin `threads.foil.php` to insert the correct config for the version of BIRD you are running.

The template also allows you to configure it per server instance if necessary. Skin `threads.foil.php` file to:

```
api/v4/router/collector/bird3/threads_<server_handle>.foil.php
```

For example: 

```
/srv/ixpmanager/resources/skins/<your_skin>/api/v4/router/collector/bird3/threads_rc01-ipv4.foil.php
```

If this file exists, it will be used instead of the default `threads.foil.php` for this instance.

This allows you to vary the number of threads per-instance. This is useful if your servers have a different number of cores, (e.g. during upgrades) or to test multi-threaded capability on one instance before enabling it on other servers, for example.

### BIRD 3 and Looking Glass Compatibility

BIRD 3 has changed the format of output for BGP attributes.

This means the looking glass (birdseye/IXP Manager) does not not work correctly with BIRD 3 as-is.

For now, this template tweaks BIRD 3 to display BGP attributes in the expected (BIRD 2) format for compatibility with the looking glass:

```
# BIRD 3 -- use BIRD 2 attributes format (for looking glass):
cli "/run/bird/bird-<?= $t->handle ?>.ctl" {
    v2 attributes;
};
```

If you remove this setting, however, you may need to manually stop bird, kill any running processes, remove any .ctl files in `/run/bird`. On reconfigure, it seems to re-create the socket `.ctl` files as `root` user instead of `bird`, so it will break the looking glass or other scripts expecting to access it as `bird` user.

### Custom config tweaks

LONAP is upgrading our collector from a Cisco router to BIRD.

The template looks for a few optional settings which can bet set per-instance in the custom config file `/srv/ixpmanager/config/custom.php`

```php /srv/ixpmanager/config/custom.php
    // Extra options for route server/collector config.
    'router' => [
        'rc01-ipv4' => [
            'passive'       => 'on', // be quiet for test/non-production
            'err_wait_time' => '120,600', // less aggressive error restarts
            'prefix_limit'  => '20000', // default max prefix to apply (unless member is higher)
        ],
        'rc01-ipv6' => [
            'passive'       => 'on', // be quiet for test/non-production
            'err_wait_time' => '120,600', // less aggressive error restarts
            'prefix_limit'  => '5000', // default max prefix to apply (unless member is higher)
        ],
    ],
```

- `passive` - The default config actively tries to establish BGP sessions in the usual manner. This setting enables BGP passive mode. (A peer may establish a session to us, but we do not try to establish a BGP session to them.) This is used while testing BIRD config templates/new servers etc. from live IXP Manager, but do not want the server to try to bring up sessions with members. (Otherwise, members will query this and ask us what it is/should they peer with it/why are we trying to open sessions etc...)

BIRD does not have as many options as Cisco for maximum prefix limits, warn and shutdown/restart actions.

Cisco allows setting a default for all pees (unless configured otherwise by peer) and separate max prefix warn and/or shutdown thresholds based on either a fixed number or warn percentage.

This Cisco config sets defaults for all peers (unless configured otherwise) of 20000 prefixes, log a warning at 90%, restart after 5 minutes. A few peers announce more than 20000 prefixes and are explicitly configured.

Most peers announce only a small number of prefixes. On the Cisco collector, we did not bother configuring a per-peer maximum prefix.

If a peer exceeds 20000, it's safe to assume a route leak has occurred, so we shut the session down.

This has the benefit of alerting the operator if a route leak occurs (by shutting the session down), but 
not causing administrative burden or inconvenience by warning or shutting down on an increase from 5 to 7 
prefixes, for example.


```
! defaults
neighbor lonap maximum-prefix 20000 90 restart 5

! big peer:
neighbor x.x.x.x peer-group lonap
neighbor x.x.x.x maximum-prefix 50000
```

As far as I can see, BIRD does not have direct equivalents for this. For example:

 - There is no separate restart timer for max prefix, only a single timer for _all_ error restarts.
 - Only a single maximum prefix action (`import limit [number | off ] [action warn | block | restart | disable]`) e.g. either warn *or* restart, but not "warn at X, restart at Y". You cannot configure both warning _and_ restart limits.
 - No "warn at percentage"

We set defaults in the peer template, but of course, if a maximum prefix is set in IXP Manager for the peer, it will use this number and always override our default.

The following settings aim to use the template to approximate the nice behaviour we had with Cisco.

- `prefix_limit` - Sets a "default" maximum prefix limit, above which, the sessions will be shut down and restarted after an interval. 

- `err_wait_time` - Allows to change the default wait/BGP restart timers.

BIRD does not allow two max prefix limit actions, but allows a maximum prefix to be configured in two places:

```
import limit [number | off ] [action warn | block | restart | disable]
```

- Maximum prefix limit after filtering/policy has been applied (e.g, routes accepted)


```
receive limit [number | off ] [action warn | block | restart | disable]
```

- Works the same as import prefix limit but before filtering/policy has been applied (e.g, routes received, filtered routes are counted towards the limit).

This is useful, as we can set `import limit` to warn only at the actual number of prefixes set in IXP Manager, and also set `receive limit` to our high "oopsie" default at which we restart the session.

Using this template/config options, the peer will inherit the following settings:

```
template bgp tb_rcpeer {
     error wait time 120,600;

     [...]

    ipv4 {
        receive limit 20000;
        export none;
    };
}
```

Then in each peer:

```yaml
protocol bgp pb_as12345_vli280_ipv4 from tb_rcpeer {
    description "AS12345 - Member 1";
    neighbor 5.57.81.xx as 12345;

    ipv4 {
        import where f_import_as12345();
        import limit 400 action warn;

    };
}
```

So we will warn at 400 (As set in IXP Manager for the member), but shut down/restart at 20000.

**Unless** the peer's maximum prefix setting exceeds our default, in which case, the higher limit is used
for `receive limit`:

```
protocol bgp pb_as65001_vli42_ipv4 from tb_rcpeer {
    description "AS65001 - Big Peer";
    neighbor 5.57.80.xxx as 65001;

    ipv4 {
        import where f_import_as65001();
        import limit 185000 action warn;
        receive limit 185000;
    };
}
```

This has the benefit of allowing us to configure the true number of maximum prefixes in IXP Manager. The looking glass
will be able to show a warning at the correct level because we have not shut down the sessions or set a higher
number to avoid shutting down for small numbers of prefix increases.

These settings are entirely optional. If preferred, you can leave unconfigured for the previous default behaviour which is to accept everything with no maximum prefix set.

I believe this is not very useful, however: the looking glass gives up after a certain number of prefixes
to prevent server load/browser meltdown. Nobody will actually _see_ 20000 prefixes. But if we never shut the
session down either, the operator will not get any immediate indication that there is any problem.

