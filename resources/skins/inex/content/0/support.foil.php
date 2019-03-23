<?php
    /** @var Foil\Template\Template $t */
    $this->layout( 'layouts/ixpv4' );
?>

<?php $this->section( 'page-header-preamble' ) ?>
    Technical Support and Contact Information
<?php $this->append() ?>


<?php $this->section( 'content' ) ?>

<div class="container tw-mx-auto">

<div class="tw-bg-blue-lightest tw-border-t tw-border-b tw-border-blue-500 tw-text-blue-dark tw-px-6 tw-mb-8" role="alert">
    <div class="tw-flex tw-flex-wrap">
        <div class="tw-flex-1 tw-px-6 tw-py-2 tw-m-2">
            <p class="tw-font-bold">Technical Support</p>
            <p class="tw-text-sm">
                <a href="mailto:operations@inex.ie">operations@inex.ie</a><br>
                +353 1 531 3339
            </p>
        </div>
        <div class="tw-flex-1 tw-px-6 tw-py-2 tw-m-2">
            <p class="tw-font-bold">Billing / Accounts</p>
            <p class="tw-text-sm">
                <a href="mailto:accounts@inex.ie">accounts@inex.ie</a><br>
                +353 1 433 2052
            </p>
        </div>
        <div class="tw-flex-1 tw-px-6 tw-py-2 tw-m-2">
            <p class="tw-font-bold">Sales / Marketing</p>
            <p class="tw-text-sm">
                <a href="mailto:sales@inex.ie">sales@inex.ie</a><br>
            </p>
        </div>
    </div>
</div>


<p class="tw-mb-6">
    Regular technical support at INEX is provided on an office hours basis from 08:00 to 18:00 GMT,
    Monday through Friday. The normal communications channel for technical support is email to
    <a href="mailto:operations@inex.ie">operations@inex.ie</a>. INEX aims for 4 hour turnaround on all
    email support requests. INEX operations staff are also available by telephone on +353-1-5313339.

</p>

<h3 class="tw-mb-2">Emergency 24x7x365 Support</h3>

<p>
    An 24-hour support hotline is available on +353-1-5313339 for emergency
    calls which fall outside normal office hours. This support facility <b>is intended for emergencies
    only</b> such as INEX critical system failures causing loss of service to members.
    If there is no immediate answer from this phone, please leave a message and it will be
    attended to immediately.
</p>

</div>


<?php $this->append() ?>
