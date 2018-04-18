<?php

/* @var $this yii\web\View */

$this->title = 'My Yii Application';
?>


<a href="/site/auth?authclient=qq"">test</a>

<div class="site-index">

    <div class="jumbotron">
        <h1>Congratulations!</h1>

        <p class="lead">You have successfully created your Yii-powered application.</p>
           
        <p><a class="btn btn-lg btn-success" href="http://www.yiiframework.com">Get started with Yii</a></p>
    </div>

    <div class="body-content">

        <div class="row">
            <div class="col-lg-4">
                <h2>Heading</h2>

                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et
                    dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip
                    ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu
                    fugiat nulla pariatur.</p>

                <p><a class="btn btn-default" href="http://www.yiiframework.com/doc/">Yii Documentation &raquo;</a></p>
            </div>
            <div class="col-lg-4">
                <h2>Heading</h2>

                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et
                    dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip
                    ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu
                    fugiat nulla pariatur.</p>

                <p><a class="btn btn-default" href="http://www.yiiframework.com/forum/">Yii Forum &raquo;</a></p>
            </div>
            <?=yii\authclient\widgets\AuthChoice::widget(['baseAuthUrl' => ['site/auth']]);?>
            <div class="col-lg-4">
                <h2>Heading</h2>

                <p>Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et
                    dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip
                    ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu
                    fugiat nulla pariatur.</p>

                <p><a class="btn btn-default" href="http://www.yiiframework.com/extensions/">Yii Extensions &raquo;</a></p>
            </div>
        </div>

    </div>
</div>
<?php
    //加载我们想要的js文件，，默认是在底部加载的 
    /**
     * POS_HEAD 头部
     * POS_END  底部 
     * POS_LOAD 相当于jQuery(window).load()
     * POS_READY 相当于jQuery(document).ready()
     * depends  依赖某些资源包 比如jq
     */
    $this->registerJsFile('js/2.js',['position'=>\yii\web\View::POS_HEAD,'depends'=>'yii\web\YiiAsset']);

    $this->registerCssFile('css/3.css',['depends'=>'yii\bootstrap\BootstrapAsset']);

    // 按需加载一些js代码段
    $js=<<<Js
    console.log('test registerJS');
Js;
    $this->registerJs($js);
    // 同理加载css
    $css="body{background-color:#DDD;}";
    $this->registerCss($css);

?>

