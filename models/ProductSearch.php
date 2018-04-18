<?php 
namespace app\models;

use \yii\elasticsearch\ActiveRecord;

class ProductSearch extends ActiveRecord
{
	public function attributes()
	{
		return ["productid","title","descr"];
	}

	/**
	 * [index 指定索引]
	 *
	 * @DateTime 2018-04-13
	 *
	 * @return   [type]
	 */
	public static function index()
	{
		return "imooc_shop";
	}
	/**
	 * [type description]
	 *
	 * @DateTime 2018-04-13
	 *
	 * @return   [type]
	 */
	public static function type()
	{
		return "products";
	}
}