<?php

namespace app\modules\controllers;
use Yii;
use app\models\Category;
use yii\helpers\ArrayHelper;
use yii\web\Response;
class CategoryController extends CommonController
{
    protected $mustlogin = ['tree', 'list', 'add', 'mod', 'del', 'rename', 'delete'];
    /**
     * [actionList 版块列表]
     *
     * @DateTime 2017-12-09
     *
     * @return   [type]
     */
    public function actionList()
    {
        $this->layout='layout1';
        $model=new Category;
        // getPrimaryCate();
        // 获取page和每页per-page参数
        $page=(int)Yii::$app->request->get('page') ? (int)Yii::$app->request->get('page') : 1;
        $perpage=(int)Yii::$app->request->get('per-page') ? (int)Yii::$app->request->get('per-page') : 1;

        $data=$model->getPrimaryCate();
        // $cates=$model->getTreeList();
        return $this->render('list',['pager'=>$data['pages'],'page'=>$page,'perpage'=>$perpage]);
    }

    /**
     * [actionAdd 添加版块]
     *
     * @DateTime 2017-12-09
     *
     * @return   [type]
     */
    public function actionAdd()
    {
        $this->layout='layout1';
        $model=new Category;
        // 创建层级分类
        $list=$model->getOptions();

        // var_dump($list);die;
        if(Yii::$app->request->isPost)
        {
            $post=Yii::$app->request->post();
            if($model->add($post))
            {
                Yii::$app->session->setFlash('info','添加成功');
            }
        }
        return $this->render('add',['list'=>$list,'model'=>$model]);
    }

    /**
     * [actionMod 编辑分类]
     *
     * @DateTime 2017-12-11
     *
     * @return   [type]
     */
    public function actionMod()
    {
        // 修改的操作是根据传递过来cateid ,修改parentid 和 title
        $this->layout='layout1';
        $cateid=Yii::$app->request->get('cateid');
        $model=Category::find()->where('cateid=:id',[':id'=>$cateid])->one();
        // 判断提交修改
        if(Yii::$app->request->isPost)
        {
            $post=Yii::$app->request->post();
            if($model->load($post) && $model->save())
            {
                Yii::$app->session->setFlash('info','操作成功');
            }
        }   
        $list=$model->getOptions();

        return $this->render('add',['model'=>$model,'list'=>$list]);
    }
    /**
     * [actionDel 执行删除操作]
     *
     * @DateTime 2017-12-11
     *
     * @return   [type]
     */
    public function actionDel()
    {
        try {
            $cateid=Yii::$app->request->get('cateid');
            // 没有传递过来真实ID
            if(empty($cateid))
            {
                throw new \Exception('参数错误');
            }

            $data=Category::find()->where('parentid=:pid',[':pid'=>$cateid])->one();
            // 查询是否有子类
            if($data)
            {
                throw new \Exception('该分类下有子类!');
            }
            // 
            if(!Category::deleteAll('cateid=:id',[':id'=>$cateid]))
            {
                throw new \Exception('删除失败');
            }
        } catch (\Exception $e) {   
            Yii::$app->session->setFlash('info',$e->getMessage());
        }

        return $this->redirect(['category/list']);
    }

    /**
     * [actionTree description]
     *
     * @DateTime 2018-01-15
     *
     * @return   [type]
     */
    public function actionTree()
    {

        // 设置返回的数据类型 yii 的 reponse对象
        // 方法文件位置 yiisoft/yii2/web/Response.php
        Yii::$app->response->format=\yii\web\Response::FORMAT_JSON;
        $model=new Category;
        $data=$model->getPrimaryCate();
        if(!empty($data))
        {
            return $data['data'];
        }

        return [];
    }



    /**
     * [actionRename jstree rename]
     *
     * @DateTime 2018-04-07
     *
     * @return   [type]
     */
    public function actionRename()
    {
        Yii::$app->response->format=Response::FORMAT_JSON;
        if (!Yii::$app->request->isAjax) {
            throw new \yii\web\MethodNotAllowedHttpException('Access Denied');
        }
        $post = Yii::$app->request->post();
        $newtext = $post['new'];
        $old = $post['old'];
        $id = (int)$post['id'];
        if (empty($newtext) || empty($id)) {
            return ['code' => -1, 'message' => '参数错误', 'data' => []];
        }
        if ($old == $newtext) {
            return ['code' => 0, 'message' => 'ok', 'data' => []];
        }
        $model = Category::findOne($id);
        $model->scenario = 'rename';
        $model->title = $newtext;
        if ($model->save()) {
            return ['code' => 0, 'message' => 'ok', 'data' => []];
        }
        return ['code' => 1, 'message' => '更新失败', 'data' => []];
    }
    /**
     * [actionDelete jstree del]
     *
     * @DateTime 2018-04-07
     *
     * @return   [type]
     */
    public function actionDelete()
    {
        Yii::$app->response->format = Response::FORMAT_JSON;
        if (!Yii::$app->request->isAjax) {
            throw new \yii\web\MethodNotAllowedHttpException('Access Denied');
        }
        $id = (int)Yii::$app->request->get("id");
        if (empty($id)) {
            return ['code' => -1, 'message' => '参数错误', 'data' => []];
        }
        $cate = Category::findOne($id);
        if (empty($cate)) {
            return ['code' => -1, 'message' => '参数错误', 'data' => []];
        }
        $total = Category::find()->where("parentid = :pid", [":pid" => $id])->count();
        if ($total > 0) {
            return ['code' => 1, 'message' => '该分类下包含子类，不允许删除', 'data' => []];
        }
        if ($cate->delete()) {
            return ['code' => 0, 'message' => 'ok', 'data' => []];
        }
        return ['code' => 1, 'message' => '删除失败', 'data' => []];
    }



}
