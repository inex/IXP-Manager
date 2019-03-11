let mix = require('laravel-mix');

/*
 |--------------------------------------------------------------------------
 | Mix Asset Management
 |--------------------------------------------------------------------------
 |
 | Mix provides a clean, fluent API for defining some Webpack build steps
 | for your Laravel application. By default, we are compiling the Sass
 | file for your application, as well as bundling up your JS files.
 |
 */

mix.scripts(
    [
        'node_modules/jquery/dist/jquery.min.js',
        'node_modules/jquery-ui-dist/jquery-ui.min.js',
        'node_modules/select2/dist/js/select2.min.js',
        'node_modules/bootbox/bootbox.js',
        'node_modules/popper.js/dist/umd/popper.min.js',
        'node_modules/bootstrap/dist/js/bootstrap.min.js',
        'node_modules/datatables.net/js/jquery.dataTables.min.js',
        'node_modules/datatables.net-bs4/js/dataTables.bootstrap4.min.js',
        'node_modules/datatables.net-responsive/js/dataTables.responsive.min.js',
        'node_modules/datatables.net-responsive-bs4/js/responsive.bootstrap4.min.js',

        'node_modules/blueimp-file-upload/js/jquery.fileupload.js',
        'node_modules/jquery-knob/dist/jquery.knob.min.js',
        'node_modules/clipboard/dist/clipboard.min.js',
        'node_modules/moment/moment.js',
        'node_modules/ip-address/dist/ip-address-globals.js',



        // etc. etc.
    ],
    'public/js/ixp-pack.js'
);

mix.styles(
    [
        'node_modules/bootstrap/dist/css/bootstrap.min.css',
        'node_modules/datatables.net-bs4/css/dataTables.bootstrap4.min.css',
        'node_modules/select2/dist/css/select2.min.css',
        'node_modules/font-awesome/css/font-awesome.css',
        'node_modules/datatables.net-bs4/css/dataTables.bootstrap4.min.css',
        'node_modules/datatables.net-responsive-bs4/css/responsive.bootstrap4.css',

        'public/css/draganddrop.css',
        'public/css/ixp-manager.css',
        // etc. etc.
    ],
    'public/css/ixp-pack.css',

);

mix.copyDirectory( 'node_modules/font-awesome/fonts', 'public/fonts' );
