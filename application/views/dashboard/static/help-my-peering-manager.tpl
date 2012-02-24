{include file="header-tiny.tpl" pageTitle="IXP Manager :: Help"}

<h2>My Peering Manager :: Instructions</h2>

<h3>Introduction</h3>

<p>
Welcome to the INEX's Member Peering Manager. This tool will allow existing and new members alike
to manage existing and set-up new peerings with ease.
</p>


<h3>Features</h3>

The features you can avail of in this application include:

<ul>
    <li> Automatically generate and send peering requests with all relevent information; </li>
    <li> See who you are peered with on a per VLAN basis as seen by INEX's flow analysis; </li>
    <li> Set your own peering flags on a per member basis; </li>
    <li> Record notes to track your discussions with other members. </li>
</ul>

<h3>Important Concepts</h3>

<p>
You'll notice four icon columns to the left of the page and it's important to differentaite between
them to make maximum use of this application:
</p>

<dl>

<dt>The <em>State</em> Column:</dt>

<dd>
    <p>
    This column is for your own benefit and is set per VLAN.
    You can change its
    status by clicking on the icon. It will rotate through four states as follows (along with
    our suggested meanings but you can always apply your own!):
    </p>
    <table border="0">
    <tr>
        <td width="20"></td>
        <td width="22">
            <img src="{genUrl}/images/22x22/unknown.png" width="22" height="22" alt="[UNKNOWN]" />
        </td>
        <td width="10"></td>
        <td>
            Unknown - you have yet to set a status for this peer.
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <img src="{genUrl}/images/22x22/no.png" width="22" height="22" alt="[NO]" />
        </td>
        <td></td>
        <td>
            I have not yet contacted this member in relation to this VLAN.
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <img src="{genUrl}/images/22x22/yes.png" width="22" height="22" alt="[YES]" />
        </td>
        <td></td>
        <td>
            I am peered with this member on this VLAN.
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <img src="{genUrl}/images/22x22/waiting.png" width="22" height="22" alt="[WAITING]" />
        </td>
        <td></td>
        <td>
            I have contacted this member and I am awaiting a reply.
        </td>
    </tr>
    <tr>
        <td></td>
        <td>
            <img src="{genUrl}/images/22x22/never.png" width="22" height="22" alt="[NEVER]" />
        </td>
        <td></td>
        <td>
            This peer has declined to peer with me or I won't peer with them.
        </td>
    </tr>
    </table>

    <p>
    <strong>If you are an existing member you will need to update this to reflect your current status
    as they will all be set to <em>unknown</em> by default.</strong>
    </p>
</dd>

<dt>The <em>PM</em> (Peering Matrix) Column:</dt>

<dd>
    <p>
    This column is based on BGP traffic snooping on the INEX peering LANs for IPv4 and route server states.
    It is <em>per peering LAN</em> and will reflect your peering status for the VLAN you have
    selected. Some members have opted out of this and they will be shown as <em>N/A</em>.
    </p>
</dd>

<dt>The <em>RS</em> Column:</dt>

<dd>
    <p>
    This column indicates whether this member is a route server client or not.
    </p>

    <p>
    If both you and the member are route server clients, you may, for example, choose to just
    accept the multilateral peering and fore go the effort of setting up bilateral peerings.
    </p>
</dd>

<dt>The <em>IPv6</em> Column:</dt>

<dd>
    <p>
    This column will only display if you are IPv6 enabled for the VLAN you are viewing.
    </p>

    <p>
    When it is displayed, it will only show an icon for those other members that are IPv6 enabled. There
    are only two icons available: a smily face which you can use to indicate that you are peered over
    IPv6 with this member or, the fault, a crying face to indicate that you are not.
    </p>

    <p>
    Clicking the icon will change it.
    </p>
</dd>


<h3>Sending Peering Requests</h3>

<p>
By clicking on the peering email of a member, a preformatted mail will appear which you are free to
edit. However, you will be unable to change the from or destination email addresses which are
fixed to the peering contact details of each member.
</p>

<p>
A note is automatically added to your notes for that member's peering record when a mail is sent.
</p>

<h3>Notes</h3>

<p>
The column on the right has a <em>notes</em> icon. It will be gray scale to indicate that no notes are
recorded for that member and, if in colour, it will indicate that notes exist.
</p>

<p>
You can add any notes you like to track your peering (or other) conversations with that member. The
system will automatically add the date and username to the note to help track which members of
your organisation are working with another member.
</p>

{include file="footer-tiny.tpl"}
