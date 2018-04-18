<?php

namespace app\controllers;
use app\controllers\CommonController;
use app\models\Pay;
use Yii;

class PayController extends CommonController
{
    protected $except=['notify'];
    // 这个是必须登录才可以显示状态的
    protected $mustlogin=['return'];
	// 支付宝的这个不需要这个csrf验证
    public $enableCsrfValidation = false;
    /**
     * [actionNotify 处理处异步的方式]
     *
     * @DateTime 2017-12-25
     *
     * @return   boolean
     */
    public function actionNotify()
    {
        if (Yii::$app->request->isPost) {
            $post = Yii::$app->request->post();
            if (Pay::notify($post)) {
                echo "success";
                exit;
            }
            echo "fail";
            exit;
        }
    }
    /**
     * [actionReturn 处理同步的方式]
     *
     * @DateTime 2017-12-25
     *
     * @return   [type]
     */
    public function actionReturn()
    {
        $this->layout = 'layout1';
        // get 方式会传递过来订单状态
        $status = Yii::$app->request->get('trade_status');
        if ($status == 'TRADE_SUCCESS') {
            $s = 'ok';
        } else {
            $s = 'no';
        }
        return $this->render("status", ['status' => $s]);
    }
}
