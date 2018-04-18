<?php

namespace app\modules\controllers;
use Yii;
use app\modules\models\Admin;
// 分页
use yii\data\Pagination;
use app\modules\models\Rbac;

class ManageController extends \yii\web\Controller
{   
    // protected $mustlogin=[''];
    /**
     * [actionMailchangepass 邮箱找回密码]
     *
     * @DateTime 2017-11-26
     *
     * @return   [type]
     */
    public $layout='layout1';
    public function actionMailchangepass()
    {
    	$this->layout=false;
    	// $get=Yii::$app->request->get();

    	// var_dump($get);die;
    	// 接受GET传递的参数
    	$time=Yii::$app->request->get('timestamp');
    	$adminuser=Yii::$app->request->get('adminuser');
    	$token=Yii::$app->request->get('token');

    	$model=new Admin;
    	$myToken=$model->createToken($adminuser,$time);
    	// 判断token是否一致
    	if($token!=$myToken)
    	{
    		$this->redirect(['public/login']);
    		Yii::$app->end();
    	}
    	// 判断时间相差是否大于5分钟
    	if(time()-$time >300)
    	{
			$this->redirect(['public/login']);
    		Yii::$app->end();    		
    	}

    	// 判断是不是post提交
    	if(Yii::$app->request->isPost)
    	{
    		$post=Yii::$app->request->post();
    		if($model->changePass($post))
    		{
    			Yii::$app->session->setFlash('info','密码修改成功!');
    			// $this->redirect(['public/login']);
    		}
    	}
    	$model->adminuser=$adminuser;
    	// var_dump($model->adminuser);
        return $this->render('mailchangepass',['model'=>$model]);
    }
    /**
     * [actionManagers 管理员列表]
     *
     * @DateTime 2017-11-26
     *
     * @return   [type]
     */
    public function actionManagers()
    {
        $this->layout='layout1';
        $model=Admin::find();
        // 每页显示条数
        $pageSize=Yii::$app->params['pageSize']['manage'];
        // 总数
        $count=$model->count();
        //分页对象
        $pager=new Pagination(['totalCount'=>$count,'pageSize'=>$pageSize]);
        // 进行分页操作的查询,传递我们的offset & limit
        $managers=$model->offset($pager->offset)->limit($pager->limit)->all();
        // $managers=Admin::find()->all();
        return $this->render('managers',['managers'=>$managers,'pager'=>$pager]);
    }

    /**
     * [actionReg 添加管理员]
     *
     * @DateTime 2017-11-27
     *
     * @return   [type]
     */
    public function actionReg()
    {

        $this->layout='layout1';
        $model=new Admin;
        if(Yii::$app->request->isPost)
        {
            $post=Yii::$app->request->post();
            if($model->reg($post))
            {
                Yii::$app->session->setFlash('info','添加成功');
            }
            else
            {
                Yii::$app->session->setFlash('info','添加失败');
            }
        }
        $model->adminpass='';
        $model->repass='';
        return  $this->render('reg',['model'=>$model]);
    }

    /**
     * [actionDel 执行删除操作]
     *
     * @DateTime 2017-11-28
     *
     * @return   [type]
     */
    public function actionDel()
    {
        // 接受adminid
        $adminid=(int)Yii::$app->request->get('adminid');
        if(empty($adminid))
        {
            $this->redirect(['mamage/managers']);
        }
        $model=new Admin;
        if($model->deleteAll('adminid=:id',[':id'=>$adminid]))
        {
            Yii::$app->session->setFlash('info','删除成功');
            $this->redirect(['manage/managers']);
        }
        // var_dump($adminid);
    }

    /**
     * [changeEmail 修改邮箱]
     *
     * @DateTime 2017-11-29
     *
     * @return   [type]
     */
    public function actionChangeemail()
    {
        $this->layout='layout1';
        $model=Admin::find()->where('adminuser=:user',[':user'=>Yii::$app->session['admin']['adminuser']])->one();
        if(Yii::$app->request->isPost)
        {
            $post=Yii::$app->request->post();
            if($model->changeemail($post))
            {
                ##code
                Yii::$app->session->setFlash('info','修改成功');
            }
        }
        $model->adminpass='';
        // var_dump($model);
        // die;
        return $this->render('changeemail',['model'=>$model]);
    }

    /**
     * [changePass 修改密码]
     *
     * @DateTime 2017-12-04
     *
     * @return   [type]
     */
    public  function actionChangepass()
    {

        $this->layout='layout1';
        // 
        $model=Admin::findOne(Yii::$app->admin->id);
        // $model=Admin::find()->where('adminuser=:user',[':user'=>Yii::$app->session['admin']['adminuser']])->one();
        if(Yii::$app->request->isPost)
        {
            $post=Yii::$app->request->post();
            if($model->changepass($post))
            {
                Yii::$app->session->setFlash('info','修改成功');
            }
        }
        $model->adminpass='';
        $model->repass='';
        return $this->render('changepass',['model'=>$model]);
    }


    /**
     * [actionAssign 授权操作]
     *
     * @DateTime 2018-03-26
     *
     * @param    [type] $adminid
     *
     * @return   [type]
     */
    public function actionAssign($adminid)
    {
        $adminid=(int)$adminid;
       
        if(empty($adminid))
        {
            throw new \Expression('参数错误');
        }
        // 查询是否存在这条数据
        $admin=Admin::findOne($adminid);

        if(empty($admin))
        {
            // 抛出一个由于文件文件未找到的异常
            #一般模板找不见经常抛出这个异常
            throw new \yii\web\NotFoundHttpException('admin not exists');
        }

        if(Yii::$app->request->isPost)
        {
            $post=Yii::$app->request->post();
            // 为用户授权操作
            $children=!empty($post['children']) ? $post['children'] : [];
            if(Rbac::grant($adminid,$children))
            {
                Yii::$app->session->setFlash('info','分配成功');
            }
        }

        // 获取所有的角色和权限
        $auth=Yii::$app->authManager;
        $roles=Rbac::getOptions($auth->getRoles(),null);
        $permissions=Rbac::getOptions($auth->getPermissions(),null);
       // $children=Rbac::getChildrenByName($admin->adminuser);
        // $children=['roles'=>[],'permission'=>[]];
        $children=Rbac::getChildrenByUser($adminid);

        // var_dump($children);die;

        return $this->render('assign',['roles'=>$roles,'permissions'=>$permissions,'children'=>$children,'adminuser'=>$admin->adminuser]);

    }

    public function actionTrbac($id)
    {
        $auth=Yii::$app->authManager;
        // 获取角色的的所有授权,传入角色id
        $r=$auth->getAssignments($id);
        var_dump($r);
    }

}
