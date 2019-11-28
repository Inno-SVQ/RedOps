var elixir = require('laravel-elixir');

/*
 |--------------------------------------------------------------------------
 | Elixir Asset Management
 |--------------------------------------------------------------------------
 |
 | Elixir provides a clean, fluent API for defining some basic Gulp tasks
 | for your Laravel application. By default, we are compiling the Sass
 | file for our application, as well as publishing vendor resources.
 |
 */

// Gentelella vendors path : vendor/bower_components/gentelella/vendors

elixir(function(mix) {

    /********************/
    /* Copy Stylesheets */
    /********************/

    // Bootstrap
    mix.copy('vendor/bower_components/gentelella/vendors/bootstrap/dist/css/bootstrap.min.css', 'public/css/bootstrap.min.css');

    // Font awesome
    mix.copy('vendor/bower_components/gentelella/vendors/font-awesome/css/font-awesome.min.css', 'public/css/font-awesome.min.css');

    // Gentelella
    mix.copy('vendor/bower_components/gentelella/build/css/custom.min.css', 'public/css/gentelella.min.css');

    // Nprogress
    mix.copy('vendor/bower_components/nprogress/nprogress.css', 'public/css/nprogress.css');

    //Datatables
    mix.copy('vendor/bower_components/datatables/media/css/jquery.dataTables.min.css', 'public/css/datatables/jquery.dataTables.min.css');

    /****************/
    /* Copy Scripts */
    /****************/

    // Bootstrap
    mix.copy('vendor/bower_components/gentelella/vendors/bootstrap/dist/js/bootstrap.min.js', 'public/js/bootstrap.min.js');

    // jQuery
    mix.copy('vendor/bower_components/gentelella/vendors/jquery/dist/jquery.min.js', 'public/js/jquery.min.js');

    // Gentelella
    mix.copy('vendor/bower_components/bootstrap-progressbar/bootstrap-progressbar.min.js', 'public/js/bootstrap-progressbar.min.js');

    // Nprogress
    mix.copy('vendor/bower_components/nprogress/nprogress.js', 'public/js/nprogress.js');

    // bootstrap-progressbar
    mix.copy('vendor/bower_components/nprogress/nprogress.js', 'public/js/bootstrap-progressbar.js');

    //Datatables
    //mix.copy('vendor/bower_components/datatables/media/js/jquery.dataTables.js', 'public/js/datatables/jquery.dataTables.js');
    //mix.copy('vendor/bower_components/datatables/media/js/dataTables.bootstrap.js', 'public/js/datatables/dataTables.bootstrap.js');


    /**************/
    /* Copy Fonts */
    /**************/

    // Bootstrap
    mix.copy('vendor/bower_components/gentelella/vendors/bootstrap/fonts/', 'public/fonts');

    // Font awesome
    mix.copy('vendor/bower_components/gentelella/vendors/font-awesome/fonts/', 'public/fonts');

    /**************/
    /* Copy Images */
    /**************/

    mix.copy('vendor/bower_components/datatables/media/images/sort_both.png', 'public/css/images/sort_both.png');
    mix.copy('vendor/bower_components/datatables/media/images/sort_asc.png', 'public/css/images/sort_asc.png');
    mix.copy('vendor/bower_components/datatables/media/images/sort_desc.png', 'public/css/images/sort_desc.png');

    mix.copy([
        'node_modules/laravel-echo/dist/echo.common.js',
        'node_modules/laravel-echo/dist/echo.js'
    ], 'public/js/all.js');

});
