<div class="tw:max-w-lg">
    <h3 class="tw:mb-8">
      Welcome to the <?= ucfirst( config( 'ixp_fe.lang.customer.one') ) ?> Document Store!
    </h3>

    <p>
        <?= __( 'This is' ) ?> <b><?= __( "IXP Manager's" ) ?></b> <b><u>per-<?= config( 'ixp_fe.lang.customer.one') ?></u></b> document store allowing
        administrators to upload documents into individual <?= config( 'ixp_fe.lang.customer.one') ?>-silos.
    </p>

    <p>
        Any <?= config( 'ixp_fe.lang.customer.one') ?> which has one or more files uploaded to their own
        document store will be listed here. If a <?= config( 'ixp_fe.lang.customer.one') ?> does not appear
        in this list then it means that no files have been uploaded to their store.
    </p>

    <p>
        The per-<?= config( 'ixp_fe.lang.customer.one') ?> document store supports:
    </p>

    <ul>
        <li><?= __( 'Upload any file type.' ) ?></li>
        <li><?= __( 'Edit uploaded files including name, description, minimum access privilege and replacing the file itself.' ) ?></li>
        <li><?= __( 'Display of text (.txt) and display and parsing of Markdown (.md) files within IXP Manager.' ) ?></li>
        <li><?= __( 'Directory hierarchy allowing the categorization of files.' ) ?></li>
        <li><?= __( 'Each directory can have explanatory text.' ) ?></li>
        <li><?= __( 'Deletion of files and recursive deletion of directories.' ) ?></li>
        <li><?= __( 'Logging of destructive actions.' ) ?></li>
        <li><?= __( 'Please note that all actions except for viewing and downloading files are restricted to super users.' ) ?></li>
    </ul>

    <p>
        <b><?= __( 'For more information,' ) ?> <a target="_blank" href="https://docs.ixpmanager.org/latest/features/docstore/"><?= __( 'see the official documentation here' ) ?></a>.</b>
    </p>
</div>