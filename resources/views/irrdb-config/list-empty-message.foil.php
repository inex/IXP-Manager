
<div class="alert alert-info" role="alert">
    <b>No IRRDB entries exist in the database.</b> <a href="<?= route( $t->feParams->route_prefix . '@add') ?>">Add one...</a>
</div>

<div class="alert alert-warning" role="alert">

    It is unusual not to have any IRRDB entries as IXP Manager seeds this table during installation. Please
    <a href="http://docs.ixpmanager.org/install/manually/#initial-database-objects" target="_blank">review
    this section of the installation documentation</a> <em>(Initial Database Objects)</em> and ensure you
    have seeded your database correctly.

</div>
