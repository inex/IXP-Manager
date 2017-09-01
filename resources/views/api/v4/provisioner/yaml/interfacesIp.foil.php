interfacesip:

<div>
    <?php foreach( $t->cis as $ci ): ?>
        <div>
            - name: <?= $ci['name'] ?><br/>
            &nbsp;&nbsp;description: <?= $ci['description'] ?><br/>
            &nbsp;&nbsp;shutdown: <?= $ci['enabled'] ? 'No' : 'Yes' ?><br/>
            &nbsp;&nbsp;bfd: <?= $ci['bfd'] ? 'Yes' : 'No' ?><br/>
            &nbsp;&nbsp;ipv4: <?= isset($ci['ip']) ? $ci['ip'] : '' ?><br/>
        </div>
    <?php endforeach; ?>
    <div>
        - description: Loopback interface<br/>
        &nbsp;&nbsp;ipv4: <?= $t->switch->getLoopbackIP() ? $t->switch->getLoopbackIP() : '' ?><br/>
        &nbsp;&nbsp;loopback: <?= $t->switch->getLoopbackIP() ? 'yes' : 'no' ?><br/>
        &nbsp;&nbsp;Name: <?= $t->switch->getLoopbackName() ? $t->switch->getLoopbackName() : '' ?><br/>
</div>