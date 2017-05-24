<!DOCTYPE html>
<html lang="en">

<head>

    <!--  IXP MANAGER - template directory: resources/[views|skins] -->

    <base href="<?= url("") ?>">
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta http-equiv="Content-Type" content="text/html; charset=utf8" />

    <title><?= config('identity.orgname', '' ) ?> IXP Manager</title>

<?= $this->insert('resources/css') ?>

<?php if( ( Auth::guest() || !Auth::user()->isSuperUser() ) /* && ( !isset( $mode ) || $mode != 'fluid' ) */ ): ?>
    <style>
        html, body {
          background-color: #eee;
        }

        body {
            padding-top: 40px;
        }
    </style>
<?php endif ?>

</head>
<body>
<?= $this->renderSection('body') ?>

<?= $this->insert('resources/js') ?>
</body>
</html>
