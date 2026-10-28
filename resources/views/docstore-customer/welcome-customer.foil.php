<div class="tw:max-w-lg">

    <?php if( Auth::getUser()->isSuperUser() ): ?>
        <h3 class="tw:mb-8">
          <?= __c( 'Welcome to the :Customer Document Store for :name', [ 'name' => $t->ee( $t->cust->name ) ] ) ?>
        </h3>

        <p>
            <?= __c( "This is :app :perCustomer document store allowing administrators to upload documents into individual :customer-silos.", [
                'app'         => '<b>' . __( "IXP Manager's" ) . '</b>',
                'perCustomer' => '<b><u>' . __c( 'per-:customer' ) . '</u></b>',
            ] ) ?>
        </p>

        <p>
            <?= __( 'Files are also sometimes attached to patch panel ports (e.g. LoAs, test results, etc.). Where these exist for this customer, you will find a virtual :patchPanelPortFiles directory below listing all of these for convenience.', [
                'patchPanelPortFiles' => '<em>' . __( 'Patch Panel Port Files' ) . '</em>',
            ] ) ?>
        </p>

        <p>
            <b><?= __( 'For more information, :seeTheDocs.', [
            'seeTheDocs' => '<a target="_blank" href="https://docs.ixpmanager.org/latest/features/docstore/">'
                . __( 'see the official documentation' ) . '</a>',
        ] ) ?></b>
        </p>

    <?php else: ?>

        <h3 class="tw:mb-8">Welcome To Your Document Store for <?= $t->ee( $t->cust->name ) ?></h3>

        <p>
            If <?= config( 'identity.orgname' ) ?> has uploaded any files that are accessible by you then they will appear here.
        </p>

        <p>
            <?= __( 'Files are also sometimes attached to patch panel ports (e.g. LoAs, test results, etc.). Where these exist for you, you will find a virtual :patchPanelPortFiles directory below listing all of these.', [
                'patchPanelPortFiles' => '<em>' . __( 'Patch Panel Port Files' ) . '</em>',
            ] ) ?>
        </p>

        <p>
            <b><?= __( 'For more information, :seeTheDocs.', [
            'seeTheDocs' => '<a target="_blank" href="https://docs.ixpmanager.org/latest/features/docstore/">'
                . __( 'see the official documentation' ) . '</a>',
        ] ) ?></b>
        </p>
    <?php endif; ?>
</div>