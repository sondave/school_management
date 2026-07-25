<?php

/**
 * @link https://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license https://www.yiiframework.com/license/
 */

declare(strict_types=1);

namespace app\assets;

use yii\bootstrap5\BootstrapAsset;
use yii\web\AssetBundle;
use yii\web\View;
use yii\web\YiiAsset;

/**
 * Main application asset bundle.
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';
    public $baseUrl = '@web';
    public $js = [
        // jQuery
        'theme/js/jquery-3.7.1.min.js',
        // Feather Icon JS
        'theme/js/feather.min.js',
        // Slimscroll JS
        'theme/js/jquery.slimscroll.min.js',

        // Datatable JS
        'theme/js/jquery.dataTables.min.js',
        'theme/js/dataTables.bootstrap5.min.js',
        'https://cdn.datatables.net/buttons/2.4.2/js/dataTables.buttons.min.js',
        'https://cdn.datatables.net/buttons/2.4.2/js/buttons.bootstrap5.min.js',
        'https://cdnjs.cloudflare.com/ajax/libs/jszip/3.10.1/jszip.min.js',
        'https://cdn.datatables.net/buttons/2.4.2/js/buttons.html5.min.js',

        // Bootstrap Core JS
        'theme/js/bootstrap.bundle.min.js',
        // select 2
        'theme/plugins/select2/js/select2.min.js',
        // toast aleart
        'theme/plugins/toastr/toastr.min.js',
        'theme/plugins/toastr/toastr.js',
        // sweet alert
        'theme/plugins/sweetalert/sweetalert2.all.min.js',
        // Custom JS
        'theme/js/theme-script.js',
        'theme/js/script.js',

    ];
    public $css = [
        'theme/css/bootstrap.min.css',
        'theme/css/animate.css',
		'theme/plugins/select2/css/select2.min.css',
        'theme/css/dataTables.bootstrap5.min.css',
        'https://cdn.datatables.net/buttons/2.4.2/css/buttons.bootstrap5.min.css',
        'theme/plugins/fontawesome/css/fontawesome.min.css',
        'theme/plugins/fontawesome/css/all.min.css',
        'theme/css/style.css',
        // toast aleart
        'theme/plugins/toastr/toatr.css',
        'css/sidebar-active.css',

    ];
    public $jsOptions = [
        'position' => View::POS_HEAD,
    ];
    public $depends = [
        YiiAsset::class,
        BootstrapAsset::class
        // BootstrapPluginAsset::class
    ];
}
