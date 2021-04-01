<div class="alert alert-info mt-4" role="alert">
    <div class="d-flex align-items-center">
        <div class="text-center">
            <i class="fa fa-info-circle fa-2x"></i>
        </div>
        <div class="col-sm-12">
            <p>
                You can use this page to manage your VLANs. There are typically two types of VLANs at an IXP: the public peering LAN(s) and,
                where the service is offered, private VLANs between subsets of members (usually just two).
            </p>
            <p>
                VLANs belong to <a href="<?= route('infrastructure@list' ) ?>">infrastructures</a> and a unique key constraint exists between
                an infrastructure and the VLAN's 802.1q tag.
            </p>
            <p>
                <b>WARNING:</b> Please be aware of the distinction between the use of <em>DB ID</em> and <em>VLAN number / 802.1q tag</em>. We typically use
                <em>DB ID</em> in API calls and try to avoid using 802.1q tags as an ID. The reason for this is that tags may not be unique across infrastructures.
            </p>
        </div>
    </div>
</div>