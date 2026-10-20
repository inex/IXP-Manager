<div class="tw:max-w-lg">
    <h3 class="tw:mb-8">
      <?= __c( 'Welcome to the :Customer Document Store!' ) ?>
    </h3>

    <p>
        <?= __c( "This is :app :perCustomer document store allowing administrators to upload documents into individual :customer-silos.", [
            'app'         => '<b>' . __( "IXP Manager's" ) . '</b>',
            'perCustomer' => '<b><u>' . __c( 'per-:customer' ) . '</u></b>',
        ] ) ?>
    </p>

    <p>
        <?= __c( 'Any :customer which has one or more files uploaded to their own document store will be listed here. If a :customer does not appear' ) ?>
        in this list then it means that no files have been uploaded to their store.
    </p>

    <p>
        <?= __c( 'The per-:customer document store supports:' ) ?>
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
        <b><?= __( 'For more information, :seeTheDocs.', [
            'seeTheDocs' => '<a target="_blank" href="https://docs.ixpmanager.org/latest/features/docstore/">'
                . __( 'see the official documentation' ) . '</a>',
        ] ) ?></b>
    </p>
</div>