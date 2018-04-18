<?php

namespace app\modules\controllers;

// 引入创建的Admin模型
use app\modules\models\Admin;

use Yii;
class PublicController extends \yii\web\Controller
{
	/**
	 * [actionLogin 执行登录操作]
	 *
	 * @DateTime 2017-11-23
	 *
	 * @return   [type]
	 */
    public function actionLogin()
    {   
        // session_start();
        // var_dump($_SESSION);
        // die;
    	$this->layout=false;
    	$model=new Admin;
    	if(Yii::$app->request->isPost)
    	{
    		$post=Yii::$app->request->post();
    		// $get=Yii::$app->request->get();
    		// var_dump($get);die;
    		if($model->login($post))
    		{
    			$this->redirect(['default/index']);
    			Yii::$app->end();
    		}
    	}
        return $this->render('login',['model'=>$model]);
    }
    /**
     * [actionLogout 退出登录操作]
     *
     * @DateTime 2017-11-23
     *
     * @return   [type]
     */
    public function actionLogout()
    {
    	// 清除session中存储的登录信息
    	// Yii::$app->session->removeAll();
    	// 判断如果session中不存在就直接跳转login
    	// if(!isset(Yii::$app->session['admin']['isLogin']))
    	// {
    	// 	$this->redirect(['public/login']);
    	// // 表示终止程序；默认的表示正常终止，状态默认为0；其它状态均异常；
    	// 	Yii::$app->end();  //相当于exit
    	// }
        Yii::$app->admin->logout(false);
    	// 从哪里来到哪里去 
    	$this->goBack();
    }
    /**
     * [actionSeekpassword description]
     *
     * @DateTime 2017-11-24
     *
     * @return   [type]
     */
    public function actionSeekpassword()
    {
    	$this->layout=false;
    	$model=new Admin;

    	if(Yii::$app->request->isPost)
    	{
    		$post=Yii::$app->request->post();
    		// 执行发送邮件操作 并且验证密码
    		if($model->seekPass($post))
            {
                // 表单
               Yii::$app->session->setFlash('info','电子邮件已经发送成功，请查收'); 
            }
    	}
    	return $this->render('seekPassword',['model'=>$model]);
    }

}
