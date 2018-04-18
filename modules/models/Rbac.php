<?php 

namespace app\modules\models;

use Yii;

class Rbac extends \yii\db\ActiveRecord
{
	/**
	 * [getOptions 获取角色或权限列表]
	 *
	 * @DateTime 2018-03-23
	 *
	 * @param    [type] $data
	 * @param    [type] $parent
	 *
	 * @return   [type]
	 */
	public static function getOptions($data,$parent)
	{
		$return=[];
		foreach ($data as $obj) {
			# code...
			# 判断当前的角色是否存在，
			# 判断当前分配的权限不和自己的一样
			# 判断当前用户能否被添加子节点
			if(!empty($parent) && $parent->name!=$obj->name && Yii::$app->authManager->canAddChild($parent,$obj))
			{
				$return[$obj->name]=$obj->description;
			}

			if(is_null($parent))
			{
				$return[$obj->name]=$obj->description;
			}
		}
		
		return $return;
	} 
	/**
	 * [addChild 为角色添加子节点和权限节点]
	 *
	 * @DateTime 2018-03-29
	 *
	 * @param    [type] $children
	 * @param    [type] $name
	 */
	public static function addChild($children,$name)
	{
		#实例化authManager
		#获取角色
		#判断角色是否存在
		#开启事务的操作
		$auth=Yii::$app->authManager;
		$itemObj=$auth->getRole($name);

		if(empty($itemObj))
		{
			return false;
		}

		// var_dump($itemObj);
		// die;
		// 开启事务
		$trans=Yii::$app->db->beginTransaction();
		try{
			// 移除该父节点的所有子节点
			$auth->removeChildren($itemObj);
			foreach ($children as $item) {
				// 判断存在的是角色列表还是权限节点
				$obj=empty($auth->getRole($item)) ? $auth->getPermission($item) : $auth->getRole($item);
				// 我竟然在这块把变量填写错了
				$auth->addChild($itemObj,$obj);
			}

			$trans->commit();
		}
		catch(\Exception $e)
		{	
			// var_dump($e->getLine());
			// var_dump($e->getMessage());die;
			$trans->rollback();
			return false;
		}

		return true;
	}

	/**
	 * [getChildrenByName 通过角色名称获取权限的子节点以及权限节点]
	 *
	 * @DateTime 2018-03-26
	 *
	 * @param    [type] $name
	 *
	 * @return   [type]
	 */
	public static function getChildrenByName($name)
	{
		if(empty($name))
		{
			return [];
		}

		$return=[];
		$return['roles']=[];
		$return['permission']=[];
		$auth=Yii::$app->authManager;
		// 获取素有的子节点
		$children=$auth->getChildren($name);
		// 没有任何子节点返回空数组
		if(empty($children))
		{
			return []; 
		}
		
		foreach ($children as $obj) {
			if($obj->type==1)
			{
				$return['roles'][]=$obj->name;
			}
			else
			{
				$return['permission'][]=$obj->name;
			}
		}

		return $return;
	}


	/**
	 * [grant 为用户分配权限]
	 *
	 * @DateTime 2018-03-29
	 *
	 * @param    [type] $adminid
	 * @param    [type] $children
	 *
	 * @return   [type]
	 */
	public  static function grant($adminid,$children)
	{
		$trans=Yii::$app->db->beginTransaction();
		try{
			$auth=Yii::$app->authManager;
			// 剥夺用户的权限
			$auth->revokeAll($adminid);
			foreach ($children as $item) {
				// 判断是权限节点还是角色节点
				$obj=empty($auth->getRole($item)) ? $auth->getPermission($item) : $auth->getRole($item);
				// 把这个权限分配给这个用户
				$auth->assign($obj,$adminid);
			}
			
			$trans->commit();			
		}
		catch(\Exception $e){
			$trans->rollback();
			return false;
		}

		return true;
	}

	/**
	 * [_getItemByUser 获得用户的角色和权限]
	 * 1 ->role 2->permission
	 * @DateTime 2018-04-03
	 *
	 * @param    [type] $adminid
	 * @param    [type] $type
	 *
	 * @return   [type]
	 */
	private static function _getItemByUser($adminid,$type)
	{
		$func='getPermissionsByUser';
		if($type==1) ### 有是判定写错了.....
		{
			// 1 为角色
			$func="getRolesByUser";
		}

		$data=[];
		$auth=Yii::$app->authManager;
		// 这传递adminid ###竟然传递了type ...
		$items=$auth->$func($adminid);

		foreach ($items as $item) {
			$data[]=$item->name;
		}

		return $data;
	}


	/**
	 * [getChildrenByUser 获取角色的权限和节点]
	 *
	 * @DateTime 2018-04-03
	 *
	 * @param    [type] $adminid
	 *
	 * @return   [type]
	 */
	public static function getChildrenByUser($adminid)
	{
		// 声明存储的数组
		$return=[];
		$return['roles']=self::_getItemByUser($adminid,1);
		$return['permission']=self::_getItemByUser($adminid,2);


		return $return;

	}
}

// 移除该父节点的子节点
# removeChildren($itemObj);
// 获得角色
# $auth->getRole('admin');
// 获得权限
# $auth->getPermission('adminlist');
// 获取所有角色  type=1 角色
# $auth->getRoles()
// 获得所有权限 type=2 权限节点
# $auth->getPermissions();
// arg1: checkbox的键名，arg2: checkbox的默认选中的 arg3:传递过来值的数组
#Html::checkboxList('children', $children['permission'],$permissions); 

//剥夺这个用户所有的权限
#$auth->revokeAll($adminid);