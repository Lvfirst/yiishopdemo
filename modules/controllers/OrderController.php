<?php

namespace app\modules\controllers;
use app\models\Order;
use app\models\OrderDetail;
use app\models\Product;
use app\models\User;
use app\models\Address;
use yii\web\Controller;
use yii\data\Pagination;
use Yii;
// use app\modules\controllers\CommonController;

class OrderController extends CommonController
{
	protected $mustlogin=['list','detail','send'];
	/**
	 * [actionList 订单列表]
	 *
	 * @DateTime 2017-12-26
	 *
	 * @return   [type]
	 */
	public function actionList()
	{
		// 订单列表
		$this->layout='layout1';
		$model=Order::find();
		$count=$model->count();
		// 获取pagesize
		$pageSize=Yii::$app->params['pageSize']['order'];
		$pager=new Pagination(['totalCount'=>$count,'pageSize'=>$pageSize]);
		// 获取数据
		$data=$model->offset($pager->offset)->limit($pager->limit)->all();
		// 获取订单详情
		$data=Order::getDetail($data);
		// var_dump($data);die;
		return $this->render('list', ['pager' => $pager, 'orders' => $data]);		
	}
	/**
	 * [actionDetail 订单详情]
	 *
	 * @DateTime 2017-12-26
	 *
	 * @return   [type]
	 */
	public function actionDetail()
	{
		$this->layout='layout1';
		$orderid=(int)Yii::$app->request->get('orderid');
		$data=Order::find()->where("orderid=:oid",[':oid'=>$orderid])->one();
		$orders=Order::getData($data);
		return $this->render('detail',['order'=>$orders]);
	}
	/**
	 * [actionSend 发货处理]
	 *
	 * @DateTime 2017-12-26
	 *
	 * @return   [type]
	 */
	public function actionSend()
	{
		$this->layout='layout1';
		$orderid=(int)Yii::$app->request->get('orderid');
		$model=Order::find()->where("orderid=:oid",[':oid'=>$orderid])->one();
		$model->scenario='send';
		// 接受数据修改订单状态
		if(Yii::$app->request->isPost)
		{
			$post=Yii::$app->request->post();
			$model->status=Order::SENDED;
			if($model->load($post) && $model->save())
			{
				Yii::$app->session->setFlash('info','发货成功');
			}
		}
		return $this->render('send',['model'=>$model]);
	}
}