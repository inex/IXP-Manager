<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Technical Support and Contact Information
<?php $this->append() ?>


<?php $this->section( 'content' ) ?>


<div class="alert alert-info mt-4" role="alert">
    <div class="d-flex align-items-center">
        <div class="text-center">
            <i class="fa fa-question-circle fa-2x"></i>
        </div>
        <div class="col-sm-12">
            <h4>
                <p>
                    Technical Support: <a href="mailto:operations@inex.ie">operations@inex.ie</a>
                </p>

                <p>
                    Billing / Accounts: <a href="mailto:accounts@inex.ie">accounts@inex.ie</a>
                </p>

                Sales / Marketing: <a href="mailto:sales@inex.ie">sales@inex.ie</a>

            </h4>
        </div>
    </div>
</div>

<p class="mb-4">
    Regular technical support at INEX is provided on an office hours basis from 08:00 to 18:00 GMT,
    Monday through Friday. The normal communications channel for technical support is email to
        <a href="mailto:operations@inex.ie">operations@inex.ie</a>. INEX aims for 4 hour turnaround on all
    email support requests. INEX operations staff are also available by telephone on +353-1-5313339.

</p>

<div class="card">
    <div class="card-header">
        <h3>Emergency 24x7x365 Support</h3>
    </div>
    <div class="card-body">
        <p>
            An 24-hour support hotline is available on +353-1-5313339 for emergency
            calls which fall outside normal office hours. This support facility is intended for emergencies
            only, including:
        </p>

        <ul>
            <li> INEX critical system failures causing loss of service to members </li>
            <li> Emergency out-of-hours access to INEX cages for members who house routers there </li>
        </ul>

        <p>
            If there is no immediate answer from this phone, please leave a message and it will be
            attended to immediately.
        </p>
    </div>
</div>


<div class="card mt-4">
    <div class="card-header">
        <h3>Technical Support Summary</h3>
    </div>
    <div class="card-body">
        <table>
            <tr>
                <td></td>
                <td align="right"><strong>Email:</strong></td>
                <td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
                <td align="left"><a href="mailto:operations@inex.ie">operations@inex.ie</a></td>
            </tr>
            <tr>
                <td></td>
                <td align="right"><strong>Phone:</strong></td>
                <td></td>
                <td align="left">+353 1 5313339</td>
            </tr>
            <tr>
                <td></td>
                <td align="right"><strong>Hours:</strong></td>
                <td></td>
                <td align="left">09:00 to 18:00 GMT, Monday to Friday</td>
            </tr>
            <tr>
                <td></td>
                <td align="right"><strong>24h Emergency:</strong></td>
                <td></td>
                <td align="left">+353 1 5313339</td>
            </tr>
        </table>
    </div>
</div>


<?php $this->append() ?>
