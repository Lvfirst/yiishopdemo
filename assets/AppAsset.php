<?php
/**
 * @link http://www.yiiframework.com/
 * @copyright Copyright (c) 2008 Yii Software LLC
 * @license http://www.yiiframework.com/license/
 */

namespace app\assets;

use yii\web\AssetBundle;

/**
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class AppAsset extends AssetBundle
{
    public $basePath = '@webroot';  //物理路径
    public $baseUrl = '@web'; //网站根目录
    public $css = [
        'css/main.css',
        'css/navy.css',
        'css/owl.carousel.css',
        'css/owl.transitions.css',
        'css/animate.min.css',
        'css/config.css',
        'css/font-awesome.min.css'
    ];
    //  当资源不在浏览器能访问到的路径，可以定义这个方法，
    //  但是在发布资源的时候，会从这个资源复制一份到 /web/assets目录下
    // public $sourcePath='/tmp/src'; 
    public $js = [
        'js/jquery-migrate-1.2.1.js',
        'js/gmap3.min.js',
        'js/bootstrap-hover-dropdown.min.js',
        'js/owl.carousel.min.js',
        'js/css_browser_selector.min.js',
        'js/echo.min.js',
        'js/jquery.easing-1.3.min.js',
        'js/bootstrap-slider.min.js',
        'js/jquery.raty.min.js',
        'js/jquery.prettyPhoto.min.js',
        'js/jquery.customSelect.min.js',
        'js/wow.min.js',
        'js/scripts.js',
        'js/switchstylesheet.js',
        ['js/html5shiv.js','condition'=>'lte IE9','position'=>\yii\web\View::POS_HEAD],
        ['js/respond.min.js','condition'=>'lte IE9','position'=>\yii\web\View::POS_HEAD],
    ];
    public $depends = [
        'yii\web\YiiAsset',
        'yii\bootstrap\BootstrapAsset',
        'yii\bootstrap\BootstrapPluginAsset',
    ];

    //'yii\bootstrap\BootstrapPluginAsset',  这里引入了js/bootstrap.js,site下面的Nav依赖了bootstrap

    // public $cssOptions=[
    //     'noscript'=>true,//防止link引入css的外部js攻击 <noscript><link...></noscript>不做js解析操作
    // ];
    // 解决兼容性问题 若浏览器版本小于IE9是加载js文件 
    // public $jsOptions=[
    //     'condition'=>'lte IE9',
    //     'position'=>\yii\web\View::POS_HEAD  //在头部加载js  定义加载js的位置
    // ];
    // 指定发布时可以发布资源目录下的目录  like: css js
    // public $publishOptions=[
    //     'only'=>[
    //         'css',
    //         'js',
    //         'fonts',
    //     ];
    // ];
}
