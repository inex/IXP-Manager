
<footer>

    <p>
        IXP Manager V<?= APPLICATION_VERSION ?>
        &nbsp;&nbsp;|&nbsp;&nbsp;
        Copyright &copy; 2010 - <?= date('Y') ?> <a href="http://www.inex.ie/">Internet Neutral Exchange Association Company Limited By Guarantee</a>.
        &nbsp;|&nbsp;
        <a href="http://www.ixpmanager.org/">http://www.ixpmanager.org/</a>
        &nbsp;|&nbsp;
        <a href="<?= route( 'static/support' ) ?>">Contact Us</a>
    </p>

    <p>
        Licensed under GPL v2.0.
        &nbsp;|&nbsp;
        This Program is provided AS IS, without warranty.
        &nbsp;|&nbsp;
        Generated in
            <?= sprintf( "%0.3f", microtime(true) - APPLICATION_STARTTIME ) ?>
        seconds
    </p>

</footer>
