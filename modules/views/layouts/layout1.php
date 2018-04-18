<?php 
    use app\assets\AdminAsset;
     use yii\bootstrap\Html;
     use yii\widgets\Breadcrumbs;
    AdminAsset::register($this);
?>
<?php $this->beginPage();?>
<!DOCTYPE html>
<html lang="<?=Yii::$app->language;?>">
<head>
    <title><?=Html::encode($this->title);?>- 后台管理</title>
    <?php $this->head();?>
    <?php $this->registerMetaTag(['name'=>'viewport','content'=>'width=device-width, initial-scale=1.0']);?>
    
    <?php $this->registerMetaTag(['http-equiv'=>'Content-Type','content'=>'text/html; charset=utf-8']);?>
    
</head>
<body>
    <?php $this->beginBody();?>

    <!-- navbar -->
    <div class="navbar navbar-inverse">
        <div class="navbar-inner">
            <button type="button" class="btn btn-navbar visible-phone" id="menu-toggler">
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
                <span class="icon-bar"></span>
            </button>
            
            <a class="brand" href="index.html" style="font-weight:700;font-family:Microsoft Yahei">后台管理</a>

            <ul class="nav pull-right">                
                <li class="hidden-phone">
                    <input class="search" type="text" />
                </li>
                <li class="notification-dropdown hidden-phone">
                    <a href="#" class="trigger">
                        <i class="icon-warning-sign"></i>
                        <span class="count">6</span>
                    </a>
                    <div class="pop-dialog">
                        <div class="pointer right">
                            <div class="arrow"></div>
                            <div class="arrow_border"></div>
                        </div>
                        <div class="body">
                            <a href="#" class="close-icon"><i class="icon-remove-sign"></i></a>
                            <div class="notifications">
                                <h3>你有 6 个新通知</h3>
                                <a href="#" class="item">
                                    <i class="icon-signin"></i> 新用户注册
                                    <span class="time"><i class="icon-time"></i> 13 分钟前.</span>
                                </a>
                                <a href="#" class="item">
                                    <i class="icon-signin"></i> 新用户注册
                                    <span class="time"><i class="icon-time"></i> 18 分钟前.</span>
                                </a>
                                <a href="#" class="item">
                                    <i class="icon-signin"></i> 新用户注册
                                    <span class="time"><i class="icon-time"></i> 49 分钟前.</span>
                                </a>
                                <a href="#" class="item">
                                    <i class="icon-download-alt"></i> 新订单
                                    <span class="time"><i class="icon-time"></i> 1 天前.</span>
                                </a>
                                <div class="footer">
                                    <a href="#" class="logout">查看所有通知</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                
                <li class="notification-dropdown hidden-phone">
                    <a href="#" class="trigger">
                        <i class="icon-envelope-alt"></i>
                    </a>
                    <div class="pop-dialog">
                        <div class="pointer right">
                            <div class="arrow"></div>
                            <div class="arrow_border"></div>
                        </div>
                        <div class="body">
                            <a href="#" class="close-icon"><i class="icon-remove-sign"></i></a>
                            <div class="messages">
                                <a href="#" class="item">
                                    <img src="/assets/admin/img/contact-img.png" class="display" />
                                    <div class="name">Alejandra Galván</div>
                                    <div class="msg">
                                        There are many variations of available, but the majority have suffered alterations.
                                    </div>
                                    <span class="time"><i class="icon-time"></i> 13 min.</span>
                                </a>
                                <a href="#" class="item">
                                    <img src="/assets/admin/img/contact-img2.png" class="display" />
                                    <div class="name">Alejandra Galván</div>
                                    <div class="msg">
                                        There are many variations of available, have suffered alterations.
                                    </div>
                                    <span class="time"><i class="icon-time"></i> 26 min.</span>
                                </a>
                                <a href="#" class="item last">
                                    <img src="/assets/admin/img/contact-img.png" class="display" />
                                    <div class="name">Alejandra Galván</div>
                                    <div class="msg">
                                        There are many variations of available, but the majority have suffered alterations.
                                    </div>
                                    <span class="time"><i class="icon-time"></i> 48 min.</span>
                                </a>
                                <div class="footer">
                                    <a href="#" class="logout">View all messages</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </li>
                <li class="dropdown">
                    <a href="#" class="dropdown-toggle hidden-phone" data-toggle="dropdown">
                        账户管理
                        <b class="caret"></b>
                    </a>
                    <ul class="dropdown-menu">
                        <li><a href="<?=yii\helpers\Url::to(['manage/changeemail']);?>">修改邮箱</a></li>
                        <li><a href="<?=yii\helpers\Url::to(['manage/changepass']);?>">修改密码</a></li>
                        <li><a href="#">订单管理</a></li>
                    </ul>
                </li>
                <li class="settings hidden-phone">
                    <a href="personal-info.html" role="button">
                        <i class="icon-cog"></i>
                    </a>
                </li>
                <!-- 退出 -->
                <li class="settings hidden-phone">
                    <a href="<?=yii\helpers\Url::to(['public/logout']);?>" role="button">
                        <i class="icon-share-alt"></i>
                    </a>
                </li>
            </ul>            
        </div>
    </div>
    <!-- end navbar -->

    <!-- sidebar -->
    <div id="sidebar-nav">
        <ul id="dashboard-menu">

            <?php 
                // 获取控制器的名称
                $controller=Yii::$app->controller->id;
                // 获取action的名称
                $action=Yii::$app->controller->action->id;

                foreach(Yii::$app->params['adminmenu'] as $menu)
                  {
                    //默认是不显示

                    $show="hidden";
                    // 判断如果该用户拥有访问该控制器的所有权限
              
                    if(Yii::$app->admin->can($menu['module'].'/*'))
                    {
                        $show='show';
                    }
                    else
                    {
                        // 判断是否有下拉分类 和该用户是否有权访问该控制器
                        if(empty($menu['submenu']) && !Yii::$app->admin->can($menu['url']))
                        {
                            // 不可以就直接跳过了
                            continue;
                        }
                        else
                        {
                            foreach ($menu['submenu'] as $sub) {
                                # 判断当前节点是否被访问
                                if(Yii::$app->admin->can($menu['module'].'/'.$sub['url']))
                                {
                                    $show='show';
                                }
                            }
                        }
                    }
            ?>
            
            <li class="<?php echo $controller==$menu['module'] ? 'active ' : '';  echo $show; ?>">
                <a <?php echo !empty($menu['submenu']) ? 'class="dropdown-toggle"' : ''; ?> href="<?php echo $menu['url']=='#' ? '#' : yii\helpers\Url::to([$menu['url']]); ?>">
                    <i class="<?php echo $menu['icon'];?>"></i>
                    <span><?php echo $menu['label'];?></span>
                    <?php if(!empty($menu['submenu'])): ?>
                    <i class="icon-chevron-down"></i>
                <?php endif;?>
                </a>
                <!-- 判断当前是否是当前控制器 -->
                <ul class="submenu <?php echo $controller==$menu['module'] && !empty($menu['submenu']) ? 'active ' : '' ; ?>">
                    <?php foreach($menu['submenu'] as $sub):?>
                        <!-- 判断是否有访问权限 -->
                        <?php if(!Yii::$app->admin->can($menu['module'].'/*') && !Yii::$app->admin->can($menu['module'].'/'.$sub['url'])) continue; ?>
                    <li><a href="<?=yii\helpers\Url::to([$menu['module'].'/'.$sub['url']])?>"><?php echo $sub['label']; ?></a></li>
                      
                <?php endforeach;?>
                </ul>
            </li>            

            <?php } ?>
         
            
        </ul>
    </div>
    <!-- end sidebar -->
    <div class="content">
        <?php 
            /**
             * @homeLink 面包屑的第一个链接,默认是Yii::$app->homeUrl;
             */
            echo Breadcrumbs::widget([
                'homeLink'=>['label'=>'首页 >>','url'=>'/admin/default/index/'],
                'links'=>isset($this->params['breadcrumbs']) ? $this->params['breadcrumbs'] : '',
            ]);
        ?>
        <?=$content;?>
    </div>

<?php $this->endBody();?>
</body>
</html>
<?php $this->endPage();?>
