<?php

namespace app\controllers;
use Yii;
use app\models\User;
use app\models\Product;
use app\models\Cart;

class CartController extends CommonController
{	
    // 定义必须登录的控制器
    protected $mustlogin=['index','add','mod','del'];
	// 3-4 购物车
	// public $layout=false;
    public function actionIndex()
    {
    	$this->layout='layout1';
        // if(Yii::$app->session['isLogin']!=1)
        // {
        //     return $this->redirect(['member/auth']);
        // }
        // 获取当前的用户ID
        // $userid=User::find()->where('username=:name',[':name'=>Yii::$app->session['loginname']])->one()->userid;
        $userid=Yii::$app->user->id;
        // 查询当前登录用户的购物车
        $carts=Cart::find()->where("userid=:id",[':id'=>$userid])->asArray()->all();
        // 显示商品的信息
        foreach ($carts as $k => $value) {
            $product=Product::find()->where('productid=:pid',[':pid'=>$value['productid']])->one();
             //显示要返回的数据 
             $data[$k]['cover']=$product->cover;
             $data[$k]['title']=$product->title;
             $data[$k]['productnum']=$value['productnum'];
             $data[$k]['price']=$value['price'];
             $data[$k]['productid'] = $value['productid'];
             $data[$k]['cartid'] = $value['cartid'];
        }

        // var_dump($data);die;

        // bug  为空会出现错误
        return $this->render('index',['data'=>$data]);
    }

    /**
     * [actionAdd 购物车添加]
     *
     * @DateTime 2017-12-19
     *
     * @return   [type]
     */
    public function  actionAdd()
    {
    	// 判断是否登录
    	// if(Yii::$app->session['isLogin']!=1)
    	// {
    	// 	return $this->redirect(['member/auth']);
    	// }
    	// 获取当前用户的ID
    	// $userid=User::find()->where('username=:username',[':username'=>Yii::$app->session['loginname']])->one()->userid;
        $userid=Yii::$app->user->id;
    	// 判断请求类型
    	if(Yii::$app->request->isPost)
    	{
    		$info=Yii::$app->request->post();
    		// 提取出来产品的数量
    		$num=Yii::$app->request->post()['productnum'];
    		$data['Cart']=$info;
    		// 绑定 userid
    		$data['Cart']['userid']=$userid;
    	}
        // 处理get 请求的过来的参数
    	if(Yii::$app->request->isGet)
    	{
    		$productid=Yii::$app->request->get('productid');
    		$model=Product::find()->where('productid=:productid',[':productid'=>$productid])->one();
    		// 判断该商品是否促销
    		$price=$model->issale ? $model->saleprice : $model->price;
    		// 未进入详情页的时候默认是 1
    		$num=1;
    		// 整合需要插入的数据
    		$data['Cart']=['productid'=>$productid,'price'=>$price,'productnum'=>$num,'userid'=>$userid];
    	}
    	// 查询购物车里面是否存在
    	if(!$model=Cart::find()->where('userid=:id and productid=:pid',[':pid'=>$data['Cart']['userid'],':id'=>$data['Cart']['productid']])->one())
    	{
    		$model=new Cart;
    	}
    	else
    	{
    		// 查询有结果的话在基础上增加数量
    		$data['Cart']['productnum']=$model->productnum + $num;
    	}
    	
    	// $data['Cart']['Product']['createnum']=time();

    	// 加载进去数据
    	$model->load($data);
    	$model->save();
    	
    	return $this->redirect(['cart/index']);
    }

    /**
     * [actionMod 修改订单商品]
     *
     * @DateTime 2017-12-20
     *
     * @return   [type]
     */
    public function actionMod()
    {
        if(Yii::$app->request->isAjax)
        {
           $cartid=Yii::$app->request->get('cartid');
           $productnum=Yii::$app->request->get('productnum');
           Cart::updateAll(['productnum' => $productnum], 'cartid = :cid', [':cid' => $cartid]);
        }
    }

    /**
     * [actionDel 删除订单操作]
     *
     * @DateTime 2017-12-20
     *
     * @return   [type]
     */
    public function actionDel()
    {
        $cartid = Yii::$app->request->get("cartid");
        Cart::deleteAll('cartid = :cid', [':cid' => $cartid]);
        return $this->redirect(['cart/index']);
    }
}
