<div class="tw-max-w-lg">

    <?php if( Auth::getUser()->isSuperUser() ): ?>
        <h3 class="tw-mb-8">
          Welcome to the <?= ucfirst( config( 'ixp_fe.lang.customer.one') ) ?> Document Store for <?= $t->cust->name ?>
        </h3>

        <p>
            This is <b>IXP Manager's</b> <b><u>per-<?= config( 'ixp_fe.lang.customer.one') ?></u></b> document store allowing
            administrators to upload documents into individual <?= config( 'ixp_fe.lang.customer.one') ?>-silos.
        </p>

        <p>
            Files are also sometimes attached to patch panel ports (e.g. LoAs, test results, etc.). Where these exist for this customer,
            you will find a virtual <em>Patch Panel Port Files</em> directory below listing all of these for convenience.
        </p>

        <p>
            <b>For more information, <a target="_blank" href="https://docs.ixpmanager.org/features/docstore/">see the official documentation here</a>.</b>
        </p>

    <?php else: ?>

        <h3 class="tw-mb-8">Welcome To Your Document Store for <?= $t->cust->name ?></h3>

        <p>
            If <?= config( 'identity.orgname' ) ?> has uploaded any files that are accessible by you then they will appear here.
        </p>

        <p>
            Files are also sometimes attached to patch panel ports (e.g. LoAs, test results, etc.). Where these exist for you,
            you will find a virtual <em>Patch Panel Port Files</em> directory below listing all of these.
        </p>

        <p>
            <b>For more information, <a target="_blank" href="https://docs.ixpmanager.org/features/docstore/">see the official documentation here</a>.</b>
        </p>
    <?php endif; ?>
</div>