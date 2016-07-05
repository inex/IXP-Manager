<html>
<body>

<pre>
Guest: <?= var_dump( Auth::guest() ) ?>
Authd: <?= var_dump( Auth::check() ) ?>

<?= '**[ ' . asset('someimage.png') . ' ]**' ?>

<?= var_dump(session_save_path() ) ?>

<?php if ( Auth::check() ): ?>
    <?= dd( Auth::user() ) ?>
<?php endif ?>

</pre>
</body>
</html>
