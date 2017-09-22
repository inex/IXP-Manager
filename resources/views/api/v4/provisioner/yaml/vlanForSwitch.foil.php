vlans:

<div>
    <?php foreach( $t->sList as $s ): ?>
        <div>
            - name: <?= $s['name'] ?><br/>
            &nbsp;&nbsp;private: <?= $s['private'] ? 'Yes' : 'No' ?><br/>
            &nbsp;&nbsp;tag: <?= $s['number'] ?><br/>
        </div>
    <?php endforeach; ?>
</div>
