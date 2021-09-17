<?php /*
    MRTG Configuration Templates

    Please see: https://github.com/inex/IXP-Manager/wiki/MRTG---Traffic-Graphs

    You should not need to edit these files - instead use your own custom skins. If
    you can't effect the changes you need with skinning, consider posting to the mailing
    list to see if it can be achieved / incorporated.

    Skinning: https://github.com/inex/IXP-Manager/wiki/Skinning
    */

    $now = microtime(true);
?>
<?= $this->insert('services/grapher/mrtg/header'); ?>

<?= $this->insert('services/grapher/mrtg/custom-header'); ?>

<?= $this->insert('services/grapher/mrtg/aggregates' ); ?>

<?= $this->insert('services/grapher/mrtg/location-aggregates'); ?>

<?= $this->insert('services/grapher/mrtg/switch-aggregates'); ?>

<?= $this->insert('services/grapher/mrtg/trunks'); ?>

<?= $this->insert('services/grapher/mrtg/member-ports'); ?>

<?= $this->insert('services/grapher/mrtg/core-bundles'); ?>

<?= $this->insert('services/grapher/mrtg/custom-footer'); ?>

<?= $this->insert('services/grapher/mrtg/footer', [ 'gentime' => microtime(true) - $now ] ); ?>
