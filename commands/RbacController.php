<?php 

namespace app\commands;
use Yii;
use yii\console\Controller;

class RbacController extends Controller
{

	public function actionInit()
	{
		// 开启事务
		$tran=Yii::$app->db->beginTransaction();
		try{  
			// 获取目录位置
			$dir=dirname(dirname(__FILE__)).'/modules/controllers';
			$controllers=glob($dir.'/*');
			// var_dump($controllers);
			$permissions=[];
			foreach ($controllers as $controller) {
				$content=file_get_contents($controller);
				preg_match('/class ([a-zA-Z]+)Controller/',$content,$match);
				// 获取控制器名称
				$cName=$match[1];

				$permissions[]=strtolower($cName.'/*');
				// var_dump($permissions);
				// 匹配所有的action名称
				preg_match_all('/public function action([a-zA-Z_]+)/',$content,$matches);
				
				foreach ($matches[1] as $aName) {
					$permissions[]=strtolower($cName.'/'.$aName);
					# code...
				}
			}
			// insert tables 通过authManager
			$auth=Yii::$app->authManager;
			foreach ($permissions as $permission) {
				// 判断是否存在该权限
				if(!$auth->getPermission($permission))
				{
					$obj=$auth->createPermission($permission);
					$obj->description=$permission;
					$auth->add($obj);
				}
			}
			// 成功提交事务
			$tran->commit();
			echo 'import success';
				// var_dump($permissions);
		}
		catch(\Exception $e){
			$tran->rollback;
			echo 'import failed';
		}

	}

}

