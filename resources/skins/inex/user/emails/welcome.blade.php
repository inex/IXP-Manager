@component('mail::message')

{{ config( 'identity.sitename' ) }} - Your Access Details


To whom it may concern,

@if( $resend )
**This email is being sent to you because either you requested a reminder of your account details or an administrator thought it appropriate to send you a reminder.**
@elseif( $user->peeringdb_id )
A new user account has been created for you on INEX's <?= config( 'ixp_fe.lang.customer.one' ) ?> portal (IXP Manager) as you logged in with your PeeringDB account.

@if( (int)config( 'auth.peeringdb.privs' ) === \IXP\Models\User::AUTH_CUSTUSER )
**Accounts created with PeeringDB have non-admin access by default. If you would like your privileges escalated, please email us at {{ config( 'identity.support_email') }} with your username ({{ $user->username }}).**
@endif

@else
A new user account has been created for you on INEX's <?= config( 'ixp_fe.lang.customer.one' ) ?> portal (IXP Manager).
@endif

You can login to your account using the following details:

|                                |                                                                  |
| ------------------------------ | ---------------------------------------------------------------  |
| **URL:     **                  | [{{ config( 'identity.url' ) }}]({{ config( 'identity.url' ) }}) |
| **Username:**                  | {{ $user->username }}                                       |
| **Password:**                  | (see below)                                                      |



Once logged in, you will have access to a number of features including:

* list of IXP members and peering contact details
* the peering manager tool
* your port and member-to-member traffic graphs
* ability to view and edit your company details
* your port configuration details
* the peering matrix
* route server, AS112 and other service information.


If you require any assistance, please contact INEX Operations on [operations@inex.ie](mailto:operations@inex.ie).

You will always find our current support contact details at: https://www.inex.ie/support



## Getting Your Password


To set your password, please use following link:

@component('mail::button', ['url' => route( "reset-password@show-reset-form", [ "token" => $token, "username" => $user->username ] ), 'color' => 'blue'])
    Reset password
@endcomponent


## Additional/Miscellaneous Benefits

### Route Collector Looking Glass

INEX runs a route collector, whose purpose is to allow us and our members to debug routing issues.

You will find the looking glass at: https://www.inex.ie/ixp/lg/



### IRC Channel

Members of the INEX operations team, along with many NOC personnel from INEX member
organisations can often be found on the `#inex-ops` IRC channel. This facility
has proved itself to be a useful communications medium for operational matters. The
`#inex-ops` IRC channel is encrypted using SSL.


|                                |                                                                  |
| ------------------------------ | ---------------------------------------------------------------  |
| **Server name:**               | `irc.inex.ie`, port 6697, SSL enabled                            |
| **Channel:**                   | `#inex-ops`                                                      |






Thanks and kind regards,


INEX Operations



@endcomponent