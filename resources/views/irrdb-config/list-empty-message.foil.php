<div class="alert alert-info mt-4" role="alert">
    <div class="d-flex align-items-center">
        <div class="text-center">
            <i class="fa fa-question-circle fa-2x"></i>
        </div>
        <div class="col-sm-12">
            <b>No IRRDB entries exist in the database.</b> <a href="<?= route( $t->feParams->route_prefix . '@create') ?>">Create one...</a>
        </div>
    </div>
</div>

<div class="alert alert-warning mt-4" role="alert">
    <div class="d-flex align-items-center">
        <div class="text-center">
            <i class="fa fa-exclamation-circle fa-2x"></i>
        </div>
        <div class="col-sm-12">
            It is unusual not to have any IRRDB entries as IXP Manager seeds this table during installation. Please
            <a href="http://docs.ixpmanager.org/install/manually/#initial-database-objects" target="_blank">review
                this section of the installation documentation</a> <em>(Initial Database Objects)</em> and ensure you
            have seeded your database correctly.
        </div>
    </div>
</div>