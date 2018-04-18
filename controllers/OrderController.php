<?php

namespace app\controllers;
use Yii;

use app\models\Product;
use app\models\Order;
use app\models\OrderDetail;
use app\models\User;
use app\models\Cart;
use app\models\Address;
use app\models\Pay;
use dzer\express\Express;
use app\controllers\CommonController;

use yii\filters\AccessControl;

class OrderController extends CommonController
{

  // 设置必须登录才可以访问的控制器
  protected $mustlogin=['index','check','add','confirm','pay','getexpress','received'];
  protected $verbs=[
    'confirm'=>['post'],

  ];
  // public function behaviors()
  // {
  //     return [
  //       'access'=>[
  //         'class'=>AccessControl::classname(),
  //         'only'=>['*'], //针对那些方法有效
  //         // 'except'=>[],除了xxx控制器
  //         'rules'=>[ 
  //           [
  //             'allow'=>false,
  //             'actions'=>['index','check'],
  //             'roles'=>['?'],// guest->? authenticated->@
  //           ],
  //           [
  //              'allow'=>true,
  //              'actions'=>['index','check'],
  //              'roles'=>['@'],
  //           ],
  //         ],
  //       ],    
  //     ];
  // }

  /**
   * [actionIndex 前台订单列表]
   *
   * @DateTime 2017-12-27
   *
   * @return   [type]
   */
    public function actionIndex()
    {
    		// 么有页面
       $this->layout='layout2';
      // 判断是否登录 获取当前用户的信息
        // if(Yii::$app->user->isGuest)
        // {
        //    return $this->redirect(['member/auth']);
        // }
        // if (Yii::$app->session['isLogin'] != 1) {
        //     return $this->redirect(['member/auth']);
        // }
        // $loginname = Yii::$app->session['loginname'];
        // // 获取用户的ID
        // $userid = User::find()->where('username = :name or useremail = :email', [':name' => $loginname, ':email' => $loginname])->one()->userid;
        $userid=Yii::$app->user->id;
        //查询该用户的订单
        $data=Order::getProducts($userid);

       return $this->render('index',['orders'=>$data]);
    }
	// 3-5 收银台界面   
    public function actionCheck()
    {
        // if(Yii::$app->session['isLogin']!=1)
        // {
        //     return $this->redirect(['member/auth']);
        // }
        // 订单的ID
        $orderid=Yii::$app->request->get('orderid');
        // 获取订单状态
        $status=Order::find()->where('orderid=:id',[':id'=>$orderid])->one()->status;
        if($status!=Order::CREATEORDER && $status!=Order::CHECKORDER)
        {
            return $this->redirect(['order/index']);
        }
        // 获取用户ID
        // $userid=User::find()->where("username=:name or useremail=:email",[':name'=>Yii::$app->session['loginname'],':email'=>Yii::$app->session['loginname']])->one()->userid;
        $userid=Yii::$app->user->userid;
        // 查询地址
        $addresses=Address::find()->where('userid=:uid',[':uid'=>$userid])->asArray()->all();
        // 获取订单信息
        $details=OrderDetail::find()->where('orderid=:oid',[':oid'=>$orderid])->asArray()->all();
        // 返回前台的数据信息
        $data=[];
        foreach ($details as $key => $value) {
            // 查询出该商品的信息
            $product=Product::find()->where('productid=:id',[':id'=>$value['productid']])->one();
            $value['title']=$product->title;
            $value['cover']=$product->cover;
            $data[]=$value;
        }
        // 快递邮寄快递价格
        $express = Yii::$app->params['express'];
        $expressPrice = Yii::$app->params['expressPrice'];        
    	$this->layout='layout1';
        return $this->render("check", ['express' => $express, 'expressPrice' => $expressPrice, 'addresses' => $addresses, 'products' => $data]);
    }

    /**
     * [actionAdd 订单详情]
     *
     * @DateTime 2017-12-20
     *
     * @return   [type]
     */
    public function actionAdd()
    {
    	// if(Yii::$app->session['isLogin']!=1)
    	// {
    	// 	return $this->redirect(['member/auth']);
    	// }
    	$Transaction=Yii::$app->db->beginTransaction();
    	try {
    		if(Yii::$app->request->isPost)
    		{
                //接受post数据
    			$post=Yii::$app->request->post();
                $orderModel=new Order;
                // $usermodel=User::find()->where("username=:name or useremail=:email",[':name'=>Yii::$app->session['loginname'],':email'=>Yii::$app->session['loginname']])->one();
                // var_dump($usermodel);
                // die;
               // if(Yii::$app->user->isGuest)
               // {
               //   throw new \Exception();
               // }
               // 获取userid
               // $userid=$usermodel->userid;
               $userid=Yii::$app->user->id;
               // var_dump($userid);die;
               // 成功则写入数据
               $orderModel->userid=$userid;
               $orderModel->status=Order::CREATEORDER; // 创建订单状态 0
               $orderModel->createtime=time();
               if(!$orderModel->save())
               {
                    throw new \Exception();
               }
               //获取插入ID
               $orderid=$orderModel->getPrimaryKey();
               // var_dump($orderid);
               // die();
               // 遍历购物车的post内容
               foreach ($post['OrderDetail'] as $key => $value) {
                    // 订单详情
                    $model=new OrderDetail;
                    $value['orderid']=$orderid;//订单的ID
                    $value['createtime']=time();
                    $data['OrderDetail']=$value;
                    if(!$model->add($data))
                    {
                        throw new \Exception();
                    }
                    // 删除购物车中的商品 productid
                    Cart::deleteAll("productid=:id",[':id'=>$value['productid']]);
                    // 更新库存
                    Product::updateAllCounters(['num'=>-$value['productnum']],'productid=:id',[':id'=>$value['productid']]);
               }
    		
    		}
            // 提交事务
            $Transaction->commit();
    	} catch (\Exception $e) {
    	   
           $Transaction->rollback();
           var_dump($e->getMessage());
           echo "<hr>";
           var_dump($e->getFile());
           echo "<hr>";
           var_dump($e->getLine());
           // return $this->redirect(['cart/index']);
    	}
        // 成功了传入订单的ID
        return $this->redirect(['order/check','orderid'=>$orderid]);
    }

    /**
     * [actionConfirm 确认订单]
     *
     * @DateTime 2017-12-23
     *
     * @return   [type]
     */
    public function actionConfirm()
    {
        // addressid ,expressid,status,amount 
        try {
           // if (Yii::$app->session['isLogin'] != 1) {
           //      return $this->redirect(['member/auth']);
           //  }
            if(!Yii::$app->request->isPost)
            {
              throw new \Exception();
            }
            // 查询是否为有效用户
            // $loginname = Yii::$app->session['loginname'];
            // $usermodel = User::find()->where('username = :name or useremail = :email', [':name' => $loginname, ':email' => $loginname])->one();            
            // if(empty($usermodel))
            // {
            //   throw new \Exception();
            // }
            // 获取用户ID
            // $userid=$usermodel->userid;
            $userid=Yii::$app->user->id;
            // 查询是否为真实用户
            $post=Yii::$app->request->post();
            // 查询该用户于该订单的关系
            $model=Order::find()->where("orderid=:oid and userid=:uid",[':uid'=>$userid,':oid'=>$post['orderid']])->one();
            if(empty($model))
            {
              throw new \Exception();
            }
            // 开始写入数据的操作
            $model->scenario = "update"; //指定scenario
            $post['status']=Order::CHECKORDER;//待支付状态
            // 该订单的订单详情
            $odetails=OrderDetail::find()->where('orderid=:orid',[':orid'=>$post['orderid']])->all();
            // 声明订单的总额是多少
            $amount=0;
            // 计算该订单对应商品的总价
            foreach ($odetails as $k => $val) {
              $amount+=$val['productnum']*$val['price'];
            }
            // 加上快递的价格
            $express = Yii::$app->params['expressPrice'][$post['expressid']];
            if ($express < 0) {
                throw new \Exception();
            }
            $amount += $express;  
            $post['amount']=$amount;
            $data['Order']=$post;
            // 写入数据
            if($model->load($data) && $model->save())
            {
                // 参数  paymethod orderid 
                return $this->redirect(['order/pay','orderid'=>$post['orderid'],'paymethod'=>$post['paymethod']]);
            }  

        } catch (\Exception $e) {
            var_dump($e->getMessage());
            var_dump($e->getLine());
        }
        // $post=Yii::$app->request->post();
        
    }

  /**
   * [actionPay description]
   *
   * @DateTime 2017-12-23
   *
   * @return   [type]
   */
   public function actionPay()
   {
      try{
            if (Yii::$app->user->isGuest) {
                throw new \Exception();
            }
            // 接受订单id 以及 支付方式
            $orderid = Yii::$app->request->get('orderid');
            $paymethod = Yii::$app->request->get('paymethod');
            // 有一个为空则抛出异常
            if (empty($orderid) || empty($paymethod)) {
                throw new \Exception();
            }
            // 判断支付方式选择处理方法, 调用model/alipay
            if ($paymethod == 'alipay') {
                return Pay::alipay($orderid);
            }
        }catch(\Exception $e) {}
        return $this->redirect(['order/index']);  
   }
   /**
    * [actionGetexpress 获取快递信息]
    *
    * @DateTime 2017-12-28
    *
    * @return   [type]
    */
   public function actionGetexpress()
   {
      $expressno=Yii::$app->request->get('expressno');
      $rs = Express::search($expressno);
      echo $rs;
   }

   /**
    * [actionReceived 确认收货]
    *
    * @DateTime 2017-12-28
    *
    * @return   [type]
    */
   public function actionReceived()
   {

      // 这块应该判断登录用户的
      $orderid=Yii::$app->request->get('orderid');
      $model=Order::find()->where("orderid=:oid",[':oid'=>$orderid])->one();
      // 如果是已经发货了
      if(!empty($model) && $model->status==Order::SENDED)
      {
          $model->status=Order::RECEIVED;
          $model->save();
      }

      return $this->redirect(['order/index']);
   }


  public function actionTest()
  {
    $url= Yii::$app->urlManager->createAbsoluteUrl(['pay/return']);
    echo $url;
  }
}
