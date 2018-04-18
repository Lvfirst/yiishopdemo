<?php

namespace app\controllers;
use app\models\Product;
use app\models\ProductSearch;
use Yii;
use yii\data\Pagination;
class ProductController extends \yii\web\Controller
{
    protected $except=['index','detail','search'];
  

  	// public $layout=false;
  	// 商品分类
    public function actionIndex()
    {
    	// 去掉默认的头部 
        $this->layout='layout2';
        $cid=Yii::$app->request->get('cateid');        
        // 声明where条件
       $where="cateid = :cid and ison='1'"; 
       $params=[':cid'=>$cid];
       $model=Product::find()->where($where,$params);
       // 查询出所有的
       $all=$model->asArray()->all();

       $count=$model->count();
       //实例化分页类
       $pageSize=Yii::$app->params['pageSize']['frontproduct'];
       $pager=new Pagination(['totalCount'=>$count,'pageSize'=>$pageSize]);

       //all
       $all=$model->offset($pager->offset)->limit($pager->limit)->asArray()->all(); 
       // var_dump($all);die;
       // 显示推荐的
       $tui=$model->where($where.' and istui=\'1\'',$params)->offset($pager->offset)->limit($pager->limit)->asArray()->all();
       // 热卖
       $hot = $model->Where($where . ' and ishot = \'1\'', $params)->orderby('createtime desc')->limit(5)->asArray()->all();
       // 促销
       $sale = $model->Where($where . ' and issale = \'1\'', $params)->orderby('createtime desc')->limit(5)->asArray()->all();
        return $this->render("index", ['sale' => $sale, 'tui' => $tui, 'hot' => $hot, 'all' => $all, 'pager' => $pager, 'count' => $count]);
    }

    // 商品详情
    public function actionDetail()
    {
    	$this->layout='layout2';
        $productid=Yii::$app->request->get('productid');
        $product=Product::find()->where("productid=:id",[':id'=>$productid])->asArray()->one();
        $data['all'] = Product::find()->where('ison = "1"')->orderby('createtime desc')->limit(7)->all();
    	return $this->render('detail',['product'=>$product,'data'=>$data]);
    }

    /**
     * [actionSearch 执行搜索]
     *
     * @DateTime 2018-04-13
     *
     * @return   [type]
     */
    public function actionSearch()
    {
        $keywords=htmlspecialchars(Yii::$app->request->get('keywords'));
        // 设置检索出来的高亮
        $highlight=[
          "pre_tags"=>["<em>"],
          "post_tags"=>["</em>"],
          "fields"=>[
              "title"=>new \stdClass(),
              "descr"=>new \stdClass(),
          ]
        ];
        $searchModel=ProductSearch::find()->query([
            "multi_match"=>[
              "query"=>$keywords,
              "fields"=>["title","descr"],
            ],
          ]);
        // 总数
        $count=$searchModel->count();
        $pageSize=Yii::$app->params['pageSize']['frontproduct'];
        $pager=new Pagination(['totalCount'=>$count,'pageSize'=>$pageSize]);
        $result=$searchModel->highlight($highlight)->offset($pager->offset)->limit($pager->limit)->all();  //我....忘了写all.....
        // 存储组合后的信息
        $products=[];
        foreach ($result as  $res) {
          // 查询出该商品的信息
            $product=Product::findOne($res->productid);
            // 把高亮的字段替换出来
            $product->title=!empty($res->highlight['title'][0]) ? $res->highlight['title'][0] : $product->title;
            $product->descr=!empty($res->highlight['descr'][0]) ? $res->highlight['descr'][0] : $product->descr;
            $products[]=$product;
        }

        $model=Product::find();
        $where="ison='1' ";
        $tui=$model->where($where.' and istui=\'1\'')->limit(5)->orderby('createtime desc')->asArray()->all();
       // 热卖
       $hot = $model->Where($where . ' and ishot = \'1\'')->orderby('createtime desc')->limit(5)->asArray()->all();
       // 促销
       $sale = $model->Where($where . ' and issale = \'1\'')->orderby('createtime desc')->limit(5)->asArray()->all();
       return $this->render("index", ['sale' => $sale, 'tui' => $tui, 'hot' => $hot, 'all' => $products, 'pager' => $pager, 'count' => $count]);
    }

}
