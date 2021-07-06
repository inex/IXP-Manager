<div class="row">
    <div class="col-sm-12">
        <p>
            Any peers appearing below are here because you (or one of your colleagues) selected to have
            them <em>Rejected / Ignored</em> in the drop down actions.
        </p>
        <?= $t->insert( 'peering-manager/tabs/table', [ "listOfCusts" => $t->rejected ] ); ?>
    </div>
</div>