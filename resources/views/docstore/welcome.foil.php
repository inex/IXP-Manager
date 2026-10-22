<div class="tw:max-w-lg">
    <h3 class="tw:mb-8"><?= __( 'Welcome to the Document Store!' ) ?></h3>

    <p>
        <?= __( ':app has a document store allowing administrators to upload documents to be made generally available for specific user classes (public, customer user, customer admin, superadmin). The document store supports:', [
            'app' => '<b>IXP Manager</b>',
        ] ) ?>
    </p>

    <ul>
        <li><?= __( 'Upload any file type.' ) ?></li>
        <li><?= __( 'Edit uploaded files including name, description, minimum access privilege and replacing the file itself.' ) ?></li>
        <li><?= __( 'For non-public documents, logging and reporting of downloads (total downloads and unique user downloads).' ) ?></li>
        <li><?= __( 'Display of text (.txt) and display and parsing of Markdown (.md) files within IXP Manager.' ) ?></li>
        <li><?= __( 'Directory hierarchy allowing the categorization of files.' ) ?></li>
        <li><?= __( 'Each directory can have explanatory text.' ) ?></li>
        <li><?= __( 'Deletion of files and recursive deletion of directories.' ) ?></li>
        <li><?= __( 'Logging of destructive actions.' ) ?></li>
        <li><?= __( 'Please note that all actions except for viewing and downloading files are restricted to super users.' ) ?></li>
    </ul>

    <p>
        <?= __( 'Use the :createDirectory and the :uploadFile buttons on the top right to start populating the directory store.', [
            'createDirectory' => '<em>' . __( 'Create Directory' ) . '</em>',
            'uploadFile'      => '<em>' . __( 'Upload File' ) . '</em>',
        ] ) ?>
    </p>

    <p>
        <b><?= __( 'For more information, :seeTheDocs.', [
            'seeTheDocs' => '<a target="_blank" href="https://docs.ixpmanager.org/latest/features/docstore/">'
                . __( 'see the official documentation' ) . '</a>',
        ] ) ?></b>
    </p>
</div>