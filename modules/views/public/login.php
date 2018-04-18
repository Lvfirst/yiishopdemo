<?php
use yii\bootstrap\ActiveForm; // 创建Form组件
use yii\helpers\Html; //引入html类
use app\assets\AdminLoginAsset;//登录的资源控制
AdminLoginAsset::register($this);
$this->title='imooc';
?>
<?php $this->beginPage();?>
<!DOCTYPE html>
<html class="login-bg">
<head>
	<title><?=$this->title;?>- 后台管理</title>
    <?php $this->head();?>
    <?php $this->registerMetaTag(['name'=>'viewport','content'=>'width=device-width,initial-scale=1.0']);?>
	<?php $this->registerMetaTag(['http-equiv'=>'Content-Type','content'=>'text/html; charset=utf-8']);?>
</head>
<body>
<?php $this->beginBody();?>

    <div class="row-fluid login-wrapper">
        <a class="brand" href="index.html"></a>
		<?php $form=ActiveForm::begin(
			[
				'fieldConfig'=>[
					'template'=>'{input}{error}',
				],
			]
			); ?>
        <div class="span4 box">
            <div class="content-wrap">
                <h6><?=$this->title;?>- 后台管理</h6>
                <?=$form->field($model,'adminuser')->textInput(['class'=>'span12','placeholder'=>'管理员账号']);?>
                <?=$form->field($model,'adminpass')->passwordInput(['class'=>'span12','placeholder'=>'管理员密码']);?>
                <!-- <input class="span12" type="text" placeholder="管理员账号" /> -->
                <!-- <input class="span12" type="password" placeholder="管理员密码" /> -->
                <a href="<?=yii\helpers\Url::to(['public/seekpassword']);?>" class="forgot">忘记密码?</a>
                <?=$form->field($model,'remeberMe')->checkbox([
                	'id'=>'remember-me',
                	'template'=>'<div class="remember">{input}<label for="remember-me">记住我</label></div>'
                	]);?>
               <!--  <div class="remember">
                    <input id="remember-me" type="checkbox" />
                    <label for="remember-me">记住我</label>
                </div> -->
                <?=Html::submitButton('登录',['class'=>'btn-glow primary login']);?>
                <!-- <a class="btn-glow primary login" href="index.html">登录</a> -->
            </div>
        </div>
		<?php ActiveForm::end(); ?>
       <!--  <div class="span4 no-account">
            <p>没有账户?</p>
            <a href="signup.html">注册</a>
        </div> -->
    </div>

	
    <!-- pre load bg imgs -->
   
<?php  $this->endBody();?>
</body>
</html>
<?php   $this->endPage();?>

<?php 
    $js=<<<JS
    $(function () {
            // bg switcher
            var \$btns = $(".bg-switch .bg");
            \$btns.click(function (e) {
                e.preventDefault();
                \$btns.removeClass("active");
                $(this).addClass("active");
                var bg = $(this).data("img");
                $("html").css("background-image", "url('img/bgs/" + bg + "')");
            });
        });    
JS;
    $this->registerJs($js);
?>