<?php

namespace app\controllers;
use Yii;

use app\models\Category;
use app\models\User;
use app\models\Cart;
use app\models\Product;
class CommonController extends \yii\web\Controller
{
	protected $actions=['*']; //针对哪些方法有效
	protected $except=[]; //除了xxx 控制器
	protected $mustlogin=[];  //必须登录的控制器
	protected $verbs=[]; //存储哪些控制器需要对http请求做控制
	public function behaviors()
	{
		return [
			'access'=>[
				'class'=>\yii\filters\AccessControl::className(),
				'only'=>$this->actions,
				'except'=>$this->except,
				'rules'=>[
					[
						'allow'=>false,
						'actions'=>empty($this->mustlogin) ? [] :$this->mustlogin,
						'roles'=>['?'],
					],
					[
						'allow'=>true,
						'actions'=>empty($this->mustlogin) ? [] : $this->mustlogin,
					],
				],
			],
			// 检查请求动作的HTTP请求方式是否允许执行， 如果不允许，会抛出HTTP 405异常
			'verbs'=>[
				'class'=>\yii\filters\VerbFilter::className(),
				'actions'=>$this->verbs,
			],
		];
	}

    public function init()
    {
    	// 菜单缓存
    	$cache = Yii::$app->cache;
    	$key='menu';
    	if(!$menu=$cache->get($key))
    	{
    		// 把获取的菜单放进来
    		$menu=Category::getMenu();
    		// $setMenu=serialize($menu);
    		// key  value ttl 
    		$cache->set($key,$menu,60);
    		// $cache->expire($key,60);
    	}


    	// 购物车
    	// 在redis里面的键值 
    	$CartKey='cart';
    	if(!$data=$cache->get($CartKey))
    	{
		 	$data = [];
	        $data['products'] = [];
	        $total = 0;
	        $userid = Yii::$app->user->id;
	        $carts = Cart::find()->where('userid = :uid', [':uid' => $userid])->asArray()->all();
	        foreach($carts as $k=>$pro) {
	            $product = Product::find()->where('productid = :pid', [':pid' => $pro['productid']])->one();
	            $data['products'][$k]['cover'] = $product->cover;
	            $data['products'][$k]['title'] = $product->title;
	            $data['products'][$k]['productnum'] = $pro['productnum'];
	            $data['products'][$k]['price'] = $pro['price'];
	            $data['products'][$k]['productid'] = $pro['productid'];
	            $data['products'][$k]['cartid'] = $pro['cartid'];
	            $total += $data['products'][$k]['price'] * $data['products'][$k]['productnum'];
	        }
	        $data['total'] = $total;

	        // 利用缓存的依赖查询来解决,添加了新数据即时显示
	        $dep=new \yii\caching\DbDependency([
	        	'sql'=>'select max(updatetime) from {{%cart}} where userid=:uid',
	        	'params'=>[':uid'=>Yii::$app->user->id],
	        ]);
	        // $dep 设置我们的依赖关系
	        $cache->set($CartKey,$data,60,$dep);
    	}

    	// var_dump();
    	// 对查询进行缓存，将查询进行缓存，判断如果缓存里面有同样结果则去除
    	// 
    	$dep=new \yii\caching\DbDependency([
    		'sql'=>'select max(updatetime) from {{%product}} where ison="1"',
    	]);

    	$tui=Product::getDb()->cache(function(){
    		 return Product::find()->where('istui = "1" and ison = "1"')->orderby('createtime desc')->limit(3)->all();
    		},60,$dep);

    	// var_dump($tui);die;
    
    	// $menu=Category::getMenu();
    	$this->view->params['menu']=$menu;

    	$this->view->params['cart'] = $data;
    }

    // Yii::app()->request->sendFile

}
