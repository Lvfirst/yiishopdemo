<?php 
namespace app\modules\controllers;

use yii\web\Controller;
use Yii;

class CommonController extends Controller 
{
	protected $actions=['*'];
	protected $except=[];
	protected $mustlogin=[];

	public function behaviors()
	{
		return [
			'access'=>[
				'class'=>\yii\filters\AccessControl::className(),
				'user'=>'admin',//指定user组件
				'only'=>$this->actions,
				'except'=>$this->except,
				'rules'=> [
					[
						'allow'=>true,
						'actions'=>empty($this->mustlogin) ? [] : $this->mustlogin,
						'roles'=>['@'],
					],
					[
						'allow'=>false,
						'actions'=>empty($this->mustlogin) ? [] : $this->mustlogin,
						'roles'=>['?'],
					],

				],
			],
		];
	}
	// $hash = Yii::$app->getSecurity()->validatePassword($password);
	/**
	 * [beforeAction 进行权限的认证]
	 *
	 * @DateTime 2018-04-07
	 *
	 * @return   [type]
	 */
	public function beforeAction($action)
	{
		if(!parent::beforeAction($action))
		{
			return false;
		}

		// 获取控制器名称
		$controller=$action->controller->id;
		// 获取方法名称
		$actionName=$action->id;
		// 通过user组件做rbac判断
		// 如果有 控制器下面的全部属性则直接放行
		if(Yii::$app->admin->can($controller.'/*'))
		{
			return true;
		}

		if(Yii::$app->admin->can($controller.'/'.$actionName))
		{
			return true;
		}
		// return true;
		throw new \yii\web\UnauthorizedHttpException('您没有访问'.$controller.'/'.$actionName.'的权限');

	}

	public function init()
	{

		// 获取当前用户要访问的控制器名称和方法名称
		// var_dump(222);die;
		// if(Yii::$app->session['admin']['isLogin']!=1)
		// {
		// 	return $this->redirect(['/admin/public/login']);
		// }
	}
}
