<?php 
	
namespace app\modules\controllers;

use app\modules\models\Admin;
use app\modules\models\Rbac;
use app\models\User;
use Yii;


use \yii\data\ActiveDataProvider;
use \yii\db\Query;
class RbacController extends CommonController
{
	public $layout='layout1';
	protected $mustlogin=['createrule','createrole','roles','assignitem'];
	/**
	 * [actionCreaterole 创建角色]
	 *
	 * @DateTime 2018-02-24
	 *
	 * @return   [type]
	 */
	public function actionCreaterole()
	{
		
		if(Yii::$app->request->isPost)
		{
			$auth=Yii::$app->authManager;
			// 创建角色
			$role=$auth->createRole(null);
			$post=Yii::$app->request->post();
			if(empty($post['name']) || empty($post['description']))
			{
				throw \Exception('参数错误');
			}

			$role->name=$post['name'];
			$role->description=$post['description'];
			$role->ruleName=empty($post['rule_name']) ? null : $post['rule_name'];
			$role->data=empty($post['data']) ? null :$post['data'];
			// 向auth_item表写入数据
			if($auth->add($role))
			{
				Yii::$app->session->setFlash('info','create success');
			}

		}

		return $this->render('_createitem');
	}
	/**
	 * [actionRoles 角色列表]
	 *
	 * @DateTime 2018-02-24
	 *
	 * @return   [type]
	 */
	public function actionRoles()
	{
		$auth=Yii::$app->authManager;
		$data=new ActiveDataProvider(
			[
				'query'=>(new Query)->from($auth->itemTable)->where('type=1')->orderBy('created_at desc'),
				'pagination'=>['pageSize'=>5],
			]
		);
		
		return $this->render('_items',['dataProvider'=>$data]);
	}
	/**
	 * [assignitem 分配权限]
	 *
	 * @DateTime 2018-03-19
	 *
	 * @return   [type]
	 */
	public function actionAssignitem($name)
	{
		$name=Yii::$app->request->get('name');
		// 传递过来name值，给角色分配名称
		$name=htmlspecialchars($name);
		$auth=Yii::$app->authManager;
		$parent=$auth->getRole($name);

		//获取该角色的权限节点，角色节点 
		$children=Rbac::getChildrenByName($name);

		// var_dump($children);die;
		// 获取所有角色
		$roles=Rbac::getOptions($auth->getRoles(),$parent);
		// var_dump($roles);
		// 获取所有的权限列表
		// $permissions=$auth->getPermissions();
		$permissions=Rbac::getOptions($auth->getPermissions(),$parent);
		// var_dump($permissions);die;
		/*接下来实例化参数*/
		if(Yii::$app->request->post())
		{
			$post=Yii::$app->request->post();
			if(Rbac::addChild($post['children'],$name))
			{
				// echo '成功';die;
				Yii::$app->session->setFlash('info','分配成功');
			}
			
		}

		return $this->render('_assignitem',['parent'=>$name,'roles'=>$roles,'permissions'=>$permissions,'children'=>$children]);
	}

	/**
	 * [actionCreaterule 创建规则]
	 *
	 * @DateTime 2018-04-08
	 *
	 * @return   [type]
	 */
	public function actionCreaterule()
	{

		if(Yii::$app->request->isPost)
		{
			$post=Yii::$app->request->post();
			if(empty($post['class_name']))
			{
				throw new \Exception('参数错误');
			}

			// 拼接类名
			$className='app\\models\\'.$post['class_name'];
			// 判断规则类是否存在
			if(!class_exists($className))
			{
				throw new \Exception('规则类不存在');
			}
			
			$rule=new $className;

			if(Yii::$app->authManager->add($rule))
			{
				Yii::$app->session->setFlash('info','添加规则成功');
			}						
		}

		return $this->render('_createrule');
	}
}

?>