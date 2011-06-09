{tmplinclude file="header.tpl" pageTitle="IXP Manager :: Member Dashboard"}

<div class="yui-g">

<div id="content">

<table class="adminheading" border="0">
<tr>
    <th class="Meeting">
        Instructions for Adding Meetings
    </th>
</tr>
</table>

<p>
<br />
To add new meetings, you must do two things:
</p>

<ul>
    <li> add a new meeting via the menu <em>Admin->Meetings->Add / Edit</em>; and then </li>
    <li> add all the meeting presentations via the menu <em>Admin->Meetings->Presentations</em>. </li>
</ul>

<p>
You can view how the meeting entries look by accessing the meeting page via either of
<em>Admin -> Meetings -> Member View</em> or <em>Member Information -> Meetings</em>.
</p>

<h3>Adding a New Meeting</h3>

<p>
The general layout of a meeting entry is shown below. In square brackets I have identified the
entires required in the <em>Add New</em> meeting form to show how and where they are used.
</p>

<div class="meeting" style="padding-left: 50px; padding-right: 150px;">

<div class="meeting title">
    <h1>[Title] &ndash; [Date]</h1>
    <h4>In [Venue] at [Time]</h4>
</div>

<p>[Preamble]</p>

<p>
<em>List of Presentations</em>
</p>

<p>
Other meeting content also includes:
</p>

<p>
<em>List of Other Presentations</em>
</p>

<p>[Postamble]</p>

</div>


<p>
Note that <em>[Date]</em> should be selected from the pop up and is entered as YYYY-MM-DD but
is formatted on the page to something such as: <em>Thursday, June 24, 2010</em>.
</p>

<p>
When entering the venue, note that it will be preseeded by <em>In</em>.
</p>

<p>
Look at existing entries as an example. To see the form for an existing entry, right
click on its table row and select <em>Edit</em>.
</p>



<h3>Adding Meeting Items</h3>

The general format for a meeting item / presentation is shown below detailing how the entry is made up
from the form.

<div class="meetingitem" style="padding-left: 50px; padding-right: 150px;">

<dl>
<dt>
    <div class="meetingitem title">

        <div class="meetingitem title icons">
            <a href="#">
                <img src="{genUrl}/images/22x22/presentation.png" width="22" height="22"
                            alt="[PRESENTATION]" class="meetingitem title icons" />
            </a>
            <a href="#">
                <img src="{genUrl}/images/22x22/video.png" width="22" height="22"
                        alt="[VIDEO]" class="meetingitem title icons" />
            </a>
        </div>

        <h1>
            [Title] &ndash; [Name (with link to email address if provided]
        </h1>

        <h4>
            [Role], [Company with link to site of URL provided]
        </h4>
    </div>
</dt>
<dd>
[Summary, if provided]
</dd>

</dl>

</div>

<p>
Note the following:
</p>

<ul>
    <li> If an email is provided, the presenter's name will be an email link; </li>
    <li> If a company URL is provided (such as <code>http://www.inex.ie/</code>), the company name will link to the company site; </li>
    <li> If a video link is provided, the video camera icon will be visable and link to the video; </li>
    <li> If the <em>Other Content?</em> box is ticked, the presentation is shown under the <em>Other meeting content</em> text;
    <li> If a presentation is uploaded, the projector icon will allow a user to download it. </li>
</ul>

<p>
Again, the best way to fully understand this is to look at entries for existing presentations.
</p>


<h3>Viewing Meeting Information for Members</h3>

<p>
Members can view meeting details including links to presentations, videos and speaker email addresses through the IXP Manager at
<a href="{genUrl controller="meeting" action="read"}">Member Information -> Meetings</a>. This page is automatically generated
based on the entries above.
</p>



<h3>Viewing Meeting Information for the Public</h3>

<p>
There is a publically access HTML feed of the meetings at <a href="{genUrl controller="meeting" action="simple"}">https://www.inex.ie/ixp/meeting/simple</a>.
This feed is then included in an <code>iframe</code> on the main INEX website at <a href="https://www.inex.ie/media/meetings-public">https://www.inex.ie/media/meetings-public</a>.
Again, this page is automatically generated from the entries above but links to presentations, video and email addresses of speakers are not included.
</p>

<h3>Composing Meeting Information Emails</h3>

<p>
One is often required to send information of a meeting to someone by email. There is now an email composition feature available which will also
automatically generate the email content based on the entries above. In the <a href="{genUrl controller="meeting"}">Admin -> Meetings -> Add / Edit</a> section,
right click on the meeting you wish to compose a mail about and select <em>Compose mail for this meeting...</em>.
</p>







</div>

</div>

{tmplinclude file="footer.tpl"}
