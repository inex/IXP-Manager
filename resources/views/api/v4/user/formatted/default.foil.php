#
# ============= BEGIN IXP MANAGER GENERATED SECTION =============
#
# Generated on: <?= date('Y-m-d H:i:s') ?>.
#
# Parameters:
#
#   - group:  <?= $t->group  ?>

#   - bcrypt: <?= $t->bcrypt ?>

#   - priv:   <?= $t->priv   ?>

#   - users:  <?= implode( ',', $t->reqUsers ) ?>

#

<?php foreach( $t->users as $u ): ?>

user=<?= $u->getUsername() ?> {
    member=<?= $t->group ?>

    login = des "<?= '$' . $t->bcrypt . substr( $u->getPassword(), 3 ) ?>"
}

<?php endforeach; ?>


#
# =============== END IXP MANAGER GENERATED SECTION =============
#
