<?php

namespace app\models;
use yii\helpers\ArrayHelper;
use Yii;
use yii\behaviors\BlameableBehavior;
/**
 * This is the model class for table "{{%category}}".
 *
 * @property string $cateid
 * @property string $title
 * @property string $parentid
 * @property string $createtime
 */
class Category extends \yii\db\ActiveRecord
{

    public function behaviors()
    {
        return [
            [
                'class'=>BlameableBehavior::className(),
                'createdByAttribute'=>'adminid',
                'updatedByAttribute'=>null,
                'value'=>Yii::$app->admin->id,
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%category}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['parentid', 'createtime'], 'integer'],
            [['title'], 'string', 'max' => 32],
            ['parentid','required','message'=>'上级分类不能为空','except' => 'rename'],
            ['title','required','message'=>'标题不能为空'],
            ['title','unique','message'=>'已存在该分类'],
        ];
    }
    
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'cateid' => 'Cateid',
            'title' => '分类名称',
            'parentid' => '上级分类',
            'createtime' => '创建时间',
        ];
    }

    /**
     * [add 添加]
     *
     * @DateTime 2017-12-09
     */
    public function add($data)
    {
        $data['Category']['createtime']=time();
        if($this->load($data) && $this->save())
        {
            return true;
        }

        return false;
    }
    /**
     * [getData 获取所有的版块]
     *
     * @DateTime 2017-12-09
     *
     * @return   [type]
     */
    public function getData()
    {
        $cates=self::find()->all();
        $cates=ArrayHelper::toArray($cates);
        return $cates;
    }

    public function getTree($cates,$pid=0)
    {
        $tree=[];
        
        foreach ($cates as $key => $cate) {
            if($cate['parentid']==$pid)
            {
                $tree[]=$cate;
                $tree=array_merge($tree,$this->getTree($cates,$cate['cateid']));
            }
        }

        return $tree;
    }

    /**
     * [setPrefix 获取前缀]
     *
     * @DateTime 2017-12-11
     *
     * @param    [type] $data
     * @param    string $p
     */
    public function  setPrefix($data,$p="|-----")
    {
        $tree=[];
        $num=1;
        $prefix=[0=>1];
        while($val=current($data))
        {
            $key=key($data); //返回指针当前指向的 key
            // var_dump($val);
            // echo "<hr>";
            // var_dump($key); // 0 1 2 3 4
            // echo "<hr>";
            if($key>0)
            {
                //  key 1 2 3 4   vpid 1 0 2 2
                //key-1 0 1 2 3
                //dkp   0 1 0 2   
                // var_dump($data[$key-1]['parentid']);
                // echo "<hr>";
                if($data[$key-1]['parentid']!=$val['parentid'])
                {
                    $num++;
                }
            }

            if(array_key_exists($val['parentid'],$prefix))
            {
                // var_dump($val['parentid']);
                // echo "<hr>";
                // var_dump($prefix);
                // echo "<hr>";
                
                // val   num
                // 0      1
                // 0      1      
                // 2      2   
                $num=$prefix[$val['parentid']];
                // var_dump($num).'|';echo $val['parentid'];
                // echo "<hr>";
            }

            $val['title']=str_repeat($p,$num).$val['title'];
            $prefix[$val['parentid']]=$num;
            // var_dump($prefix);die;
            $tree[]=$val;
            next($data);

        }
        return $tree;
    }

    /**
     * [getOptions 获取option标签]
     *
     * @DateTime 2017-12-11
     *
     * @return   [type]
     */
    
    public  function getOptions()
    {   
        // 获取分类数据
        $data=$this->getData();
        // 获取我们的分类树
        $tree=$this->getTree($data);
        // 设定前缀
        $tree=$this->setPrefix($tree);
        $options=['添加顶级分类'];
        foreach ($tree as $value) {
            // 下标对应cateid
            $options[$value['cateid']]=$value['title'];
        }
        return $options;
    }

    /**
     * [getTreeList 获取层级的列表]
     *
     * @DateTime 2017-12-11
     *
     * @return   [type]
     */
    public function getTreeList()
    {
        $data=$this->getData();
        $tree=$this->getTree($data);

        return $this->setPrefix($tree);
    }

    /**
     * [getMenu 获取栏目以及二级栏目]
     *
     * @DateTime 2017-12-15
     *
     * @return   [type]
     */
    public   function getMenu()
    {
        // 获取所有的顶级分类
        $top=self::find()->where('parentid=:id',[':id'=>0])->limit(10)->orderby('createtime desc')->asArray()->all();
        $data=[];
        // 获取二级栏目
        foreach ($top as $key => $value) {
            $value['sub']=self::find()->where('parentid=:id',[':id'=>$value['cateid']])->limit(10)->asArray()->all();
            $data[$key]=$value;
        }

        return $data;
    }

    /**
     * [getPrimaryCate 获取全部的顶级分类]
     *
     * @DateTime 2018-01-15
     *
     * @return   [type]
     */
    public function getPrimaryCate()
    {
        $data=self::find()->where("parentid=:pid",[':pid'=>0]);
        // 分页对象
        $pages=new \yii\data\Pagination(['totalCount'=>$data->count(),'pageSize'=>'2']);
        $data=$data->orderBy('createtime desc')->limit($pages->limit)->offset($pages->offset)->all();
        if(empty($data))
        {
            return [];
        }
        // 拼接适合jstree的数据格式
        $primary=[];
        foreach ($data as $cate) {
            $primary[]=[
                'id'=>$cate->cateid,
                'text'=>$cate->title,
                'children'=>$this->getChild($cate->cateid),
            ];
        }
        
        return ['data'=>$primary,'pages'=>$pages];
    }
    /**
     * [getChild 拼接返回的子类]
     * FORMAT {'id':'1','text':'电商','children':[]}
     * @DateTime 2018-01-16
     *
     * @param    [type] $pid
     *
     * @return   [type]
     */
    public function getChild($pid)
    {
        $data=self::find()->where('parentid=:pid',[':pid'=>$pid])->all();
        if(empty($data))
        {
            return [];
        }   
        foreach ($data as  $value) {
            $children[]=[
                'id'=>$value->cateid,
                'text'=>$value->title,
                'children'=>$this->getChild($value->cateid),
            ];
        }

        return $children;
    }
}
