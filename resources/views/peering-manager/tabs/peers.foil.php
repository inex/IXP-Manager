<div class="row">
    <div class="col-sm-12">
        <p>
            You exchange routes by some mechanism (route server and / or bilateral peerings) with the following members.
        </p>
        <p>
            Any members shown with a red badge indicates that you can potentially improve your peering
            with that member on the LAN and protocol displayed.
        </p>
        <?= $t->insert( 'peering-manager/tabs/table', [ "listOfCusts" => $t->peered ] ); ?>
    </div>
</div>