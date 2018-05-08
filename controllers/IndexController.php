<?php

namespace app\controllers;
use Yii;
use app\controllers\CommonController;
use app\models\Product;
class IndexController extends CommonController
{

	protected $except = ['index'];
	
	public function actionIndex()
	{

		Yii::$app->asyncLog->send(['testKafka'.date('Y-m-d H:i:s')]);
		die;
        // phpinfo();die;

		// Yii::error('this is a error');
		// Yii::warning('this is a warning');
		/*Yii::trace('this is a trace!'); 
		Yii::info('this is a info !');
		Yii::error('this is a error !');
		Yii::warning('this is a warning !');*/

		// Yii::beginProfile('myIndex'); 做性能剖析
		// var_dump(Yii::$app->cache);
		// die;
		// Yii::$app->redis->set('predis','120');
		// $redis=Yii::$app->redis;
		// $a=$redis->get('predis');
		// var_dump($a);
		// var_dump(Yii::$app->redis->keys('*'));
		// die;
		// var_dump(Yii::$app->user->identity);
		// die;
		$this->layout='layout1';
		$data['tui'] = Product::find()->where('istui = "1" and ison = "1"')->orderby('createtime desc')->limit(4)->all();
        $data['new'] = Product::find()->where('ison = "1"')->orderby('createtime desc')->limit(4)->all();
        $data['hot'] = Product::find()->where('ison = "1" and ishot = "1"')->orderby('createtime desc')->limit(4)->all();
        $data['all'] = Product::find()->where('ison = "1"')->orderby('createtime desc')->limit(7)->all();
        // Yii::endProfile('endIndex'); 结束性能剖析  上下一定一致
		return $this->render('index',['data'=>$data]);
		
	}
}

?>
