
<?php foreach( $t->users as $u ): ?>

user=<?= $u->getUsername() ?> {
    member=adminXXXXXX
    login = des "<?= $u->getPassword() ?>"
}

<?php endforeach; ?>

