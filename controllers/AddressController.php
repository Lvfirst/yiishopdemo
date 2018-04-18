<?php

namespace app\controllers;
use Yii;
use app\models\User;
use app\models\Address;
class AddressController extends CommonController
{

    protected $mustlogin=['add','del'];
    protected $verbs=[
        'add'=>['post'],
        'del'=>['get'],
    ];
    /**
     * [actionAdd 添加收货地址]
     *
     * @DateTime 2017-12-22
     *
     * @return   [type]
     */
    public function actionAdd()
    {
    	// if(Yii::$app->session['isLogin']!=1)
    	// {
    	// 	return $this->redirect(['member/auth']);
    	// }
    	// $loginname=Yii::$app->session['loginname'];
        // $userid=User::find()->where("username=:name or useremail=:email",[':name'=>$loginname,':email'=>$loginname])->one()->userid;
        $userid=Yii::$app->user->id;
        if(Yii::$app->request->isPost)
        {
        	// 接受传递过来的数据
        	$post=Yii::$app->request->post();
        	$post['userid']=$userid;
        	$post['address']=$post['address1'].$post['address2'];
        	// 向模型载入数据
        	$data['Address']=$post;
            // var_dump($data);die;
        	$model=new Address;
        	$model->load($data);
        	$model->save();
        }
        //1802 	
        return $this->redirect($_SERVER['HTTP_REFERER']);
        
    }

    /**
     * [actionDel 删除地址操作]
     *
     * @DateTime 2017-12-22
     *
     * @return   [type]
     */
    public  function actionDel()
    {

        
        // 判断登录
        // if(Yii::$app->session['isLogin']!=1)
        // {
        //     return $this->redirect(['member/auth']);
        // }
        // 判断用户是否存在
        // $loginname = Yii::$app->session['loginname'];
        // $userid = User::find()->where('username = :name or useremail = :email', [':name' => $loginname, ':email' => $loginname])->one()->userid;
        $userid=Yii::$app->user->id;
        // 判断用户匹配订单
        $addressid=Yii::$app->request->get('addressid');
        if(!Address::find()->where("userid=:uid and addressid=:aid",[':aid'=>$addressid,':uid'=>$userid])->one())
        {
           return $this->redirect($_SERVER['HTTP_REFERER']);
        }
        // 通过aid成功删除
        Address::deleteAll('addressid=:id',[':id'=>$addressid]);

        // 
        return $this->redirect($_SERVER['HTTP_REFERER']);        
    }

}
