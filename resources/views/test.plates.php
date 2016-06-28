<html>
<body>

<pre>
Guest: <?= var_dump( Auth::guest() ) ?>
Authd: <?= var_dump( Auth::check() ) ?>

<?= '**[ ' . asset('someimage.png') . ' ]**' ?>


<?php if ( Auth::check() ): ?>
    <?= dd( Auth::user() ) ?>
<?php endif ?>

</pre>
</body>
</html>
