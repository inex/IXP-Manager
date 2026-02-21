<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Down for Maintenance</title>

    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">
    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <!-- Styles -->
    <style>
        html, body {
            background-color: #fff;
            color: #636b6f;
            font-family: 'Nunito', sans-serif;
            font-weight: 100;
            height: 100vh;
            margin: 0;
        }

        .full-height {
            height: 100vh;
        }

        .flex-center {
            align-items: center;
            display: flex;
            justify-content: center;
        }

        .position-ref {
            position: relative;
        }

        .code {
            font-size: 36px;
            padding: 0 15px 0 15px;
            text-align: center;
        }

        .message {
            font-size: 26px;
            text-align: center;
        }
    </style>
</head>
<body>

<div class="flex-center position-ref" style="margin-top: 60px;">

    <div style="display: flex; justify-content: center;">
        @if( config( "identity.biglogo" ) )
            <img class="tw-inline img-fluid tw-w-full tw-max-w-sm tw-mx-auto" src="<?= config( "identity.biglogo" ) ?>" />
        @else
            <img src="/images/ixp-manager.png" alt="" />
        @endif
    </div>

</div>


<div class="flex-center position-ref" style="margin-top: 80px; font-weight: 900; color: #434b4f;">
    <div class="code">
        Planned Maintenance
    </div>

</div>

<div class="flex-center position-ref message" style="margin-top: 120px; font-size: 18px;">

    IXP Manager is undergoing planned maintenance. Please check back later.

</div>

<div class="flex-center position-ref message" style="margin-top: 20px; font-size: 18px;">

    For support, please email&nbsp;<a href="mailto:{{ config( 'identity.support_email' ) }}">{{ config( 'identity.support_email' ) }}</a>.

</div>



</body>
</html>
