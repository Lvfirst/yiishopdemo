<?php 
namespace app\models;
use yii\rbac\Rule; // rule yii自带的rule方法,实现里面的 excute
use Yii;
use app\models\Category;

class AuthorRule extends Rule
{
	// 指定一个名字
	public $name='isAuthor';
	/**
	 * [execute description]
	 *
	 * @DateTime 2018-04-08
	 *
	 * @param    [type] $user  当前登录用户的uid
	 * @param    [type] $item  所属规则rule，也就是我们后面要进行的新增规则
	 * @param    [type] $params 请求带过来的参数
	 *
	 * @return   [type]  返回boolean ,可以访问返回true,不可以返回false
	 */
	public function execute($user,$item,$params)
	{
		// 获取控制器的action 操作名称
		$action=Yii::$app->controller->action->id;
		// 判断是不是删除操作
		if($action=='delete')
		{
			// 接收传递过来的 cateid
			$cateid=Yii::$app->request->get('id');
			$cate=Category::findOne($cateid);
			// 判断用户ID 是否相同
			return $cate->adminid==$user;
		}

		return true;

	}

}