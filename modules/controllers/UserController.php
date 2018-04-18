<?php

namespace app\modules\controllers;
// 引入User的Model
use app\models\User;
use app\models\Profile;
use Yii;
use yii\data\Pagination;

class UserController extends \yii\web\Controller
{	
  /**
   * [actionUsers 后台显示会员列表]
   *
   * @DateTime 2017-12-05
   *
   * @return   [type]
   */
  public function actionUsers()
  {
      $this->layout='layout1';
      $model=User::find()->joinWith('profile');
      // 调取分页页数
      $pageSize=Yii::$app->params['pageSize']['user'];
     // 查询总数
      $count=$model->count();
      // 实例化分页对象
      $pager=new Pagination(['totalCount'=>$count,'pageSize'=>$pageSize]);
      
      $users=$model->offset($pager->offset)->limit($pager->limit)->all();

      return $this->render('users',['users'=>$users,'pager'=>$pager]);
  }

   /**
    * [actionReg description]
    *
    * @DateTime 2017-12-05
    *
    * @return   [type]
    */
   public function actionReg()
   {
   		$this->layout='layout1';
   		$model=new User;
   		// 判断是否为post请求
   		if(Yii::$app->request->isPost)
   		{
   			$post=Yii::$app->request->post();
   			if($model->reg($post))
   			{
           Yii::$app->session->setFlash('info','用户添加成功!');
   			}
   		}
      $model->userpass='';
      $model->repass='';
   		return $this->render('reg',['model'=>$model]);
   }

   /**
    * [actionDel 用户删除操作]
    *
    * @DateTime 2017-12-06
    *
    * @return   [type]
    */
   public function actionDel()
   {
      // 开启事务操作
      $transaction=Yii::$app->db->beginTransaction();

      try {
        // 获取传递过来的ID
        $userid=(int)Yii::$app->request->get('userid');
        if(empty($userid))
        {
          throw new \Exception();
        }
        // 查询详情表是否信息
        if($obj=Profile::find()->where('userid=:userid',[':userid'=>$userid])->one())
        {
            $res=Profile::deleteAll('userid=:id',[':id'=>$userid]);
            if(empty($res))
            {
              throw new \Exception();
            }
        }
        if(!User::deleteAll('userid=:id',[':id'=>$userid]))
        {
            throw new \Exception();
        }
        // 以上条件都没有则提交事务,执行操作
        $transaction->commit();
        // 捕获异常也要用根空间的 Exception
      } catch (\Exception $e) {
        if(Yii::$app->db->getTransaction())
        {
          $transaction->rollback();
        }
          // $this->redirect(['user/users']);
      }

      $this->redirect(['user/users']);
   }

}
