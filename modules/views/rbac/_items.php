<?php  
    $this->title='角色列表';
    $this->registerCssFile('/admin/css/compiled/user-list.css');
    use yii\grid\GridView;
    use yii\helpers\Html;
?>
    <!-- <link rel="stylesheet" href="assets/admin/css/compiled/user-list.css" type="text/css" media="screen" /> -->
    <!-- main container -->
    <!-- <div class="content"> -->
        
        <div class="container-fluid">
            <div id="pad-wrapper" class="users-list">
                <div class="row-fluid header">
                    <h3>角色列表</h3>
                    <div class="span10 pull-right">
                       <!--  <a href="<?php// echo yii\helpers\Url::to(['rbac/createrole']) ?>" class="btn-flat success pull-right">
                            <span>&#43;</span>
                            添加新角色
                        </a> -->
                    </div>
                </div>

                <!-- Users table -->
              <?=GridView::widget([
                'dataProvider'=>$dataProvider,
                'columns'=>[
                  [
                    'class'=>'yii\grid\SerialColumn',
                  ], 
                  'description:text:名称', 
                  'name:text:标识',
                  'rule_name:text:规则名称',
                  'created_at:datetime:创建时间',
                  'updated_at:datetime:更新时间',
                  [
                    'class'=>'yii\grid\ActionColumn',
                    'header'=>'操作',
                    'template'=>'{assign}{update}{delete}',
                    'buttons'=>[
                        'assign'=>function($url,$model,$key){
                            return Html::a('分配权限',['assignitem','name'=>$model['name']]);
                        },
                        'update'=>function($url,$model,$key){
                            return Html::a('更新  ',['updateitem','name'=>$model['name']]);
                        },
                        'delete'=>function($url,$model,$key)
                        {
                          return Html::a('删除',['deleteitem','name'=>$model['name']]);
                        },
                      
                    ],
                  ],
                ],
                // 设定布局的 items(表格数据) summary(描述) pager(分页)
                'layout'=>"{items}\n{summary}\n<div class='pagination pull-right'>{pager}</div>",
                ]);?>
                <!-- end users table -->
            </div>
        </div>
    <!-- </div> -->
    <!-- end main container -->
