<?php

namespace app\controllers;
use app\models\User;
use Yii;
use app\controllers\CommonController;
class MemberController extends CommonController
{
    // 我们的 behaviors 行为控制器不对这些控制器组认证
    protected $except=['auth','logout','reg','qqreg','qqlogin','qqcallback'];
	/**
	 * [actionAuth 执行登录操作]
	 *
	 * @DateTime 2017-12-06
	 *
	 * @return   [type]
	 */
    public function actionAuth()
    {
    	$this->layout='layout2';

    	if(Yii::$app->request->isGet)
    	{
    		$url=Yii::$app->request->referrer;
    		if(empty($url))
    		{
    			$url='/';
    		}
    		Yii::$app->session->setFlash('referrer',$url);
    	}

    	$model=new User;
    	if(Yii::$app->request->isPost)
    	{
    		$post=Yii::$app->request->post();
    		// 如果登录成功获取登录前的页面进行跳转
    		if($model->login($post))
    		{
    			// 获取写入的url
    			$url=Yii::$app->session->getFlash('referrer');

    			$this->redirect($url);
    		}
    	}
        return $this->render('auth',['model'=>$model]);
    }
    /**
     * [actionReg 执行注册操作]
     *
     * @DateTime 2017-12-06
     *
     * @return   [type]
     */
    public function actionReg()
    {
    	$model=new User;
    	
    	if(Yii::$app->request->isPost)
    	{
    		$post=Yii::$app->request->post();
    		if($model->regByEmail($post))
    		{
    			Yii::$app->session->setFlash('info','电子邮件发送成功');
    		}
    	}

    	$this->layout='layout2';
    	$model->userpass='';
    	return $this->render('auth',['model'=>$model]);
    }

    /**
     * [actionLogout 执行退出登录]
     *
     * @DateTime 2017-12-07
     *
     * @return   [type]
     */
    public function actionLogout()
    {
    	// Yii::$app->session->remove('isLogin');
    	// Yii::$app->session->remove('loginname');

    	// if(!isset(Yii::$app->session['isLogin']))
    	// {
    		// $this->redirect(Yii::$app->request->referrer);
    	// }
        // 如果需要保存会话的信息可以传递 false
        Yii::$app->user->logout(false); 
        return  $this->redirect(Yii::$app->request->referrer);
    }
    // 接下来是QQ登录操作

    /**
     * [actionQqreg 绑定信息]
     *
     * @DateTime 2017-12-07
     *
     * @return   [type]
     */
    public function actionQqreg()
    {
    	$this->layout='layout1';
    	$model=new User;
    	return $this->render('qqreg',['model'=>$model]);
    }

    /**
     * [actionQqlogin qq登录]
     *
     * @DateTime 2017-12-08
     *
     * @return   [type]
     */
    public function actionQqlogin()
    {
    	// error_reporting(7);
		require_once('../vendor/qqapi/qqConnectAPI.php');
    	$qc=new \QC();
    	$qc->qq_login();
    	
    }

    /**
     * [actionQqcallback QQ回调地址]
     *
     * @DateTime 2017-12-08
     *
     * @return   [type]
     */
    public function actionQqcallback()
    {
    	require_once('../vendor/qqapi/qqConnectAPI.php');
        $auth = new \OAuth();
        $accessToken = $auth->qq_callback();
        $openid = $auth->get_openid();
        $qc = new \QC($accessToken, $openid);
        $userinfo = $qc->get_user_info();
        echo '<pre>'; 
        // 接受QQ返回来的用户信息
        var_dump($userinfo);
    }

}
