<?php $this->layout( 'layouts/ixpv4' );
/** @var object $t */
?>

<?php $this->section( 'page-header-preamble' ) ?>
    phpinfo()
<?php $this->append() ?>



<?php $this->section( 'content' ) ?>

    <iframe id="if-phpinfo"
            style="border: none; height: 100%; width: 100%;"
            src="<?= route( 'phpinfo' ) ?>"></iframe>

<?php $this->append() ?>


<?php $this->section( 'scripts' ) ?>

    <script type="text/javascript">

        $( "#if-phpinfo" ).on( 'load', function() {
            // Set inline style to equal the body height of the iframed content.
            this.style.height = this.contentWindow.document.body.offsetHeight + 'px';
        } );

    </script>

<?php $this->append() ?>

