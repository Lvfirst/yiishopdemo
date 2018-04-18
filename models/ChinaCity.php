<?php 
namespace app\models;

use yii\db\ActiveRecord;
class ChinaCity extends ActiveRecord
{
	/**
	 * [tableName 设定表名]
	 *
	 * @DateTime 2017-11-20
	 *
	 * @return   [type]
	 */
	public static function tableName()
	{
		return 'chinacity';// return 表名
		// 假设你有表前缀，
		// 可以在 /config/db.php 数组里面添加 'tablePrefix'=>'表前缀的名字'
		// return '{{%test}}'; //相当于 表前缀_test, 
	}
}
?>