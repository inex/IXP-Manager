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
        $( document ).ready(function() {
            $( "#if-phpinfo" ).css( 'height', $('#if-phpinfo,html')[ 0 ].scrollHeight + 'px' )
        });
    </script>
<?php $this->append() ?>