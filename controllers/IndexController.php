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
		return $this->render('index',['data'=>$data]);
		
	}
}

?>
