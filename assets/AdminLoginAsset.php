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
class AdminLoginAsset extends AssetBundle
{
    public $basePath = '@webroot';  //物理路径
    public $baseUrl = '@web'; //网站根目录
    public $css = [
       'admin/css/bootstrap/bootstrap.css',
       'admin/css/bootstrap/bootstrap-responsive.css',
       'admin/css/bootstrap/bootstrap-overrides.css',
       'admin/css/lib/jquery-ui-1.10.2.custom.css',
       'admin/css/lib/font-awesome.css',
       'admin/css/layout.css',
       'admin/css/elements.css',
       'admin/css/icons.css',
       'admin/css/compiled/signin.css',
    ];
    //  当资源不在浏览器能访问到的路径，可以定义这个方法，
    //  但是在发布资源的时候，会从这个资源复制一份到 /web/assets目录下
    // public $sourcePath='/tmp/src'; 
    public $js = [
        'admin/js/bootstrap.min.js',
        'admin/js/theme.js',
        ['http://html5shim.googlecode.com/svn/trunk/html5.js','condition'=>'lte IE9','position'=>\yii\web\View::POS_HEAD],

    ];
    public $depends = [
        'yii\web\YiiAsset',
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
