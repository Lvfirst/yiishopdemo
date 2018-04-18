<?php
namespace app\models;

use app\models\Order;
use app\models\OrderDetail;
use app\models\Product;
// 引入支付宝设置
use gerpayt\yii2_alipay\AlipayPay;
class Pay{

	// 
    public static function alipay($orderid)
    {
    	// 根据订单Id 查询总价，传递支付的金额用 price
        $amount = Order::find()->where('orderid = :oid', [':oid' => $orderid])->one()->amount; 
        if (!empty($amount)) {
            $alipay = new AlipayPay();
            $giftname = "慕课商城";  //  对应subject 商品名称
            //查询订单的详情,里面包含什么商品什么的
            $data = OrderDetail::find()->where('orderid = :oid', [':oid' => $orderid])->all();
            $body = "";
            foreach($data as $pro) {
                $body .= Product::find()->where('productid = :pid', [':pid' => $pro['productid']])->one()->title . " - ";
            }
            $body .= "等商品"; // 商品描述 body
            // 商品展示地址
            $showUrl = "http://shop.mr-jason.com";
            // 向平台发送请求
            $html = $alipay->requestPay($orderid, $giftname, $amount, $body, $showUrl);
            echo $html;
        }

        
    }
    /**
     * [notify 异步通知处理方式]
     *
     * @DateTime 2017-12-25
     *
     * @param    [type] $data
     *
     * @return   [type]
     */
    public static function notify($data)
    {
        $alipay = new AlipayPay();
        // 返回验证是否成功
        $verify_result = $alipay->verifyNotify();
        if ($verify_result) {
            // 传递过去的订单参数,orderid
            $out_trade_no = $data['extra_common_param'];
            // trade_no 支付宝交易号 
            $trade_no = $data['trade_no'];
            //  trade_status 交易状态
            $trade_status = $data['trade_status'];
            // 未成功支付前是支付失败
            $status = Order::PAYFAILED;
            if ($trade_status == 'TRADE_FINISHED' || $trade_status == 'TRADE_SUCCESS') {
                // 成功则设定成功状态
                $status = Order::PAYSUCCESS;
                // 查询订单ID是否真实存在
                $order_info = Order::find()->where('orderid = :oid', [':oid' => $out_trade_no])->one();
                if (!$order_info) {
                    return false;
                }
                // 判断他还是待支付的状态
                if ($order_info->status == Order::CHECKORDER) {
                    // 修改订单状态,以及支付宝的的交易信息 $data
                    Order::updateAll(['status' => $status, 'tradeno' => $trade_no, 'tradeext' => json_encode($data)], 'orderid = :oid', [':oid' => $order_info->orderid]);
                } else {
                    return false;
                }
            }
            return true;
        } else {
            return false;
        }
    }
    
}
