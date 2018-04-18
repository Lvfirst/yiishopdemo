<?php

namespace app\models;

use Yii;
use app\models\OrderDetail;
use app\models\Product;
use app\models\Address;
use app\models\User;
use app\models\Category;

/**
 * This is the model class for table "{{%order}}".
 *
 * @property string $orderid
 * @property string $userid
 * @property string $addressid
 * @property string $amount
 * @property string $status
 * @property string $expressid
 * @property string $expressno
 * @property string $tradeno
 * @property string $tradeext
 * @property string $createtime
 * @property string $updatetime
 */
class Order extends \yii\ db\ActiveRecord
{
    
    const CREATEORDER = 0;
    const CHECKORDER = 100;
    const PAYFAILED = 201;
    const PAYSUCCESS = 202;
    const SENDED = 220;
    const RECEIVED = 260;
    
    public $products;//存储商品列表
    public $zhstatus;// 存储中文状态的说明
    public $username;//存储用户名字
    public $address;//存储地址

    public static $status = [
        self::CREATEORDER => '订单初始化',
        self::CHECKORDER  => '待支付',
        self::PAYFAILED   => '支付失败',
        self::PAYSUCCESS  => '等待发货',
        self::SENDED      => '已发货',
        self::RECEIVED    => '订单完成',
    ];

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
              [['userid', 'status'], 'required', 'on' => ['add']],
              [['addressid', 'expressid', 'amount', 'status'], 'required', 'on' => ['update']],
              ['expressno','required','message'=>'快递单号不能为空','on'=>['send']],
               ['createtime', 'safe', 'on' => ['add']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'orderid' => 'Orderid',
            'userid' => 'Userid',
            'addressid' => 'Addressid',
            'amount' => 'Amount',
            'status' => 'Status',
            'expressid' => 'Expressid',
            'expressno' => '快递单号',
            'tradeno' => 'Tradeno',
            'tradeext' => 'Tradeext',
            'createtime' => 'Createtime',
            'updatetime' => 'Updatetime',
        ];
    }


    /**
     * [getDetail 订单的详情]
     *
     * @DateTime 2017-12-25
     *
     * @param    [type] $data
     *
     * @return   [type]
     */
    public function getDetail($order)
    {
        foreach ($order as $value) {
           $value=self::getData($value); 
        }
        return $order;        
    }


    public static function getData($value)
    {
        $details=OrderDetail::find()->where('orderid=:oid',[':oid'=>$value->orderid])->all();

        $products=[];//
        foreach ($details as $detail) {
            // 查询出来详情表里的商品信息
            $product=Product::find()->where('productid=:pid',[':pid'=>$detail->productid])->one();
            // 在这里把商品的库存替换成订单的数量
            $product->num=$detail->productnum;
            // 压入products数组
            $products[]=$product;
        }
        $value->products=$products;
        // var_dump($value);
        $value->username=User::find()->where('userid=:uid',[':uid'=>$value->userid])->one()->username;
        $value['address']=Address::find()->where('userid=:uid',[':uid'=>$value->userid])->one()->address;
        if(empty($value->address))
        {
            $value->address='';
        }

        $value->zhstatus=self::$status[$value->status];     
        return $value;
    }

    /**
     * [getProducts 根据用户ID查询订单及详情]
     *
     * @DateTime 2017-12-27
     *
     * @return   [type]
     */
    public static function getProducts($userid)
    {
        // 获取该用户的订单
        $orders=Order::find()->where('status > 0 and userid=:uid',[':uid'=>$userid])->orderBy('createtime desc')->all();
        // 查询订单的详情
        
        foreach ($orders as $order) {
            $details=OrderDetail::find()->where("orderid=:oid",[":oid"=>$order->orderid])->all();
            // 在这块把订单的商品信息查询出来
            $products=[];
            foreach ($details as $detail) {
                $product=Product::find()->where('productid=:pid',[":pid"=>$detail->productid])->one();
                if(empty($product))
                {
                    continue;
                }
                $product->price=$detail->price;
                $product->num=$detail->productnum;
                $product->cate=Category::find()->where("cateid=:cid",[':cid'=>$product->cateid])->one()->title;
                // 把结果压入进去
                $products[]=$product;   
            }
            $order->zhstatus=self::$status[$order->status];
            $order->products=$products;
        }
        return $orders;
    }
}
