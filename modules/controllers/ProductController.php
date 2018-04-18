<?php

namespace app\modules\controllers;
use Yii;
use app\models\Product;
// 七牛云存储
use crazyfd\qiniu\Qiniu;
use app\models\Category;
// 分页类
use yii\data\Pagination;
// markdown 
// use yii\helpers\Markdown;
use yii\helpers\VarDumper;
// use yii\helpers\ArrayHelper;

class ProductController extends CommonController
{	
    protected $mustlogin=['list','add','on','off','del','mod'];
	/**
	 * [actionList 商品列表]
	 *
	 * @DateTime 2017-12-12
	 *
	 * @return   [type]
	 */
    public function actionList()
    {

        $this->layout='layout1';
        $model=Product::find();
        $count=$model->count();
        $pageSize=Yii::$app->params['pageSize']['product'];
        $pager=new Pagination(['totalCount'=>$count,'pageSize'=>$pageSize]);
        $product=$model->offset($pager->offset)->limit($pager->limit)->all();
        // var_dump($count);
        return $this->render('list',['products'=>$product,'pager'=>$pager]);
    }

    /**
     * [actionAdd 商品添加]
     *
     * @DateTime 2017-12-12
     *
     * @return   [type]
     */
    public function actionAdd()
    {
    	$this->layout='layout1';
    	// 商品类
    	$model=new Product;
    	$cates=new Category;
    	$list=$cates->getOptions();
    	// 释放掉默认的 0 -> 添加默认的分类
    	unset($list['0']);
    	if(Yii::$app->request->isPost)
    	{
    		$post=Yii::$app->request->post();
    		$pics=$this->upload();
    		// 判断是否有图片传入
    		if(!$pics)
    		{
    			$model->addError('cover','封面不能为空');
    		}
            else
            {
                // 存储到$_POST
                $post['Product']['cover']=$pics['cover'];
                $post['Product']['pics']=$pics['pics'];
            }

            if($pics && $model->add($post))
            {
                Yii::$app->session->setFlash('info','添加成功!');
            }
            else
            {
                Yii::$app->session->setFlash('info','添加失败!');
            }
    	}

    	return $this->render('add',['opts'=>$list,'model'=>$model]);
    }
    /**
     * [actionOn 上架]
     *
     * @DateTime 2017-12-14
     *
     * @return   [type]
     */
    public  function actionOn()
    {
       $productid=Yii::$app->request->get('productid');
       Product::updateAll(['ison'=>1],'productid=:id',[':id'=>$productid]);
       $refer=Yii::$app->request->getReferrer();
       return  $this->redirect($refer);
    }
    /**
     * [actionOff 下架]
     *
     * @DateTime 2017-12-14
     *
     * @return   [type]
     */
    public function actionOff()
    {
        $productid=Yii::$app->request->get('productid');
       Product::updateAll(['ison'=>0],'productid=:id',[':id'=>$productid]);
        $refer=Yii::$app->request->getReferrer();
        return $this->redirect($refer);
    }
    /**
     * [actionDel 删除]
     *
     * @DateTime 2017-12-14
     *
     * @return   [type]
     */
    public function actionDel()
    {   
        $productid=Yii::$app->request->get('productid');
        $model=Product::findOne($productid);
        $qiniu=new Qiniu(Product::AK,Product::SK,Product::DOMAIN,Product::BUCKET);
        // $qiniu->delete(basename($model->cover));
        $pics=json_decode($model->pics,true);
        // var_dump($pics);die;
        foreach ($pics as $key => $value) {
            $qiniu->delete($key);
        }
        Product::deleteAll('productid=:id',[':id'=>$productid]);

        return $this->redirect(['product/list']);
    }
    /**
     * [actionMod 编辑]
     *
     * @DateTime 2017-12-14
     *
     * @return   [type]
     */
    public function actionMod()
    {
        $this->layout='layout1';

        $cates=new Category;
        $model=new Product;
        $list=$cates->getOptions();
        unset($list[0]);
        $productid=Yii::$app->request->get('productid');
        $products=$model->find()->where("productid=:id",[":id"=>$productid])->one();
        if(Yii::$app->request->isPost)
        {
            $post=Yii::$app->request->post();
            $qiniu=new Qiniu(Product::AK,Product::SK,Product::DOMAIN,Product::BUCKET);
            // 对图片不做操作时还是原来的地址
            $post['Product']['cover']=$products->cover;
            // 判断是否传入新的封面图
            if($_FILES['Product']['error']['cover']==0)
            {
                $coverkey=uniqid();
                $qiniu->uploadFile($_FILES['Product']['tmp_name']['cover'],$coverkey);
                $post['Product']['cover']=$qiniu->getLink($coverkey);
                // 删除七牛云服务器上的旧图片  用basename获取key
                $qiniu->delete(basename($products->cover));

            }   
            $pics=[];
            foreach ($_FILES['Product']['tmp_name']['pics'] as $key => $value) {
                if($_FILES['Product']['error']['pics'][$key]>0)
                {
                    continue;
                }
                $pickey=uniqid();
                $qiniu->uploadFile($value,$pickey);

                $pics[$pickey]=$qiniu->getLink($pickey);
            }
            $post['Product']['pics']=json_encode(array_merge((array)json_decode($products->pics,true),$pics));
            if($products->load($post) && $products->save())
            {
                Yii::$app->session->setFlash('info','修改成功');
            }
        }

        return $this->render('add',['opts'=>$list,'model'=>$products]);

    }

    public  function  actionRemovepic()
    {
        // 获取删除的条件
        $delInfo=Yii::$app->request->get();
        $model=Product::findOne($delInfo['productid']);
        $pics=json_decode($model->pics,true);
        $qiniu=new Qiniu(Product::AK,Product::SK,Product::DOMAIN,Product::BUCKET);
        $qiniu->delete($delInfo['key']);
        unset($pics[$delInfo['key']]);
        $model->pics=json_encode($pics);
        $model->update(['pics']);
        return $this->redirect(['product/mod', 'productid' => $delInfo['productid']]);
        // $qiniu->        
    }

    public function actionTest()
    {
        $info=Category::getMenu();
        var_dump($info);
    }

    /**
     * [upload  上传图片]
     *
     * @DateTime 2017-12-14
     *
     * @return   [type]
     */
    private function upload()
    {

    	if($_FILES['Product']['error']['cover']>0)
    	{
    		return false;
    	}

        // 实例化七牛上传类 
        $qiniu=new Qiniu(Product::AK,Product::SK,Product::DOMAIN,Product::BUCKET);
        // 生成个唯一的名字
        $key=uniqid();
        // 传入文件 $key
        // $qiniu->uploadFile($_FILES['Product']['tmp_name'],$key);
        // 获取图片的地址
        $cover=$qiniu->getLink($key);
        // 存储缩略图地址
        $pics=[];
        // 遍历取出上传的多张文件
        foreach ($_FILES['Product']['tmp_name']['pics'] as $k=> $value) {
            // 上传失败就跳出循环
           if($_FILES['Product']['error']['pics'][$k]>0)
           {
                continue;
           }
            // 同理声明key 
           $pkey=uniqid();
           // 
           $qiniu->uploadFile($value,$pkey);
           $pics[$pkey]=$qiniu->getLink($pkey);
        }
        //这些都有了返回数据
        return ['cover'=>$cover,'pics'=>json_encode($pics)]; 
    }

//  cloudUrl 
//  http://p0vo22knc.bkt.clouddn.com/5a331fa14d9eb

}
