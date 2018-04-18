<?php

namespace app\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use \yii\db\ActiveRecord;
/**
 * This is the model class for table "{{%product}}".
 *
 * @property string $productid
 * @property string $cateid
 * @property string $title
 * @property string $descr
 * @property string $num
 * @property string $price
 * @property string $cover
 * @property string $pics
 * @property string $issale
 * @property string $ishot
 * @property string $istui
 * @property string $saleprice
 * @property string $ison
 * @property string $createtime
 */
class Product extends ActiveRecord
{
    // 七牛云 AK SK  DOMAIN BUCKET
    const AK='f7619x4CK0AYFyBPYEJS4pPt0sbz88_pgz8u-qrR';
    const SK='502YeDo7w3Lcc9podGmNQqSoLEywgeMdOBs2s__u';
    const DOMAIN='p0vo22knc.bkt.clouddn.com';
    const BUCKET='lone';
    public $cate;


    public function behaviors()
    {
        return [
            [
                'class'=>TimestampBehavior::className(),
                'createdAtAttribute'=>'createtime',
                'updatedAtAttribute'=>'updatetime',
                //添加的时机
                'attributes'=>[
                    ActiveRecord::EVENT_BEFORE_INSERT=>['createtime','updatetime'],//插入的时候要添加的字段
                    ActiveRecord::EVENT_BEFORE_UPDATE=>['updatetime'],//
                ],
            ]
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%product}}';
    }



    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['title', 'required', 'message' => '标题不能为空'],
            ['descr', 'required', 'message' => '描述不能为空'],
            ['cateid', 'required', 'message' => '分类不能为空'],
            ['price', 'required', 'message' => '单价不能为空'],                                           
            [['price','saleprice'], 'number', 'min' => 0.01, 'message' => '价格必须是数字'],
            ['saleprice','required','message'=>'未填写促销价格'],
            ['num', 'integer', 'min' => 0, 'message' => '库存必须是数字'],
            [['issale','ishot', 'pics', 'istui'],'safe'],
            [['cover'], 'required'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'cateid' => '分类名称',
            'title'  => '商品名称',
            'descr'  => '商品描述',
            'price'  => '商品价格',
            'ishot'  => '是否热卖',
            'issale' => '是否促销',
            'saleprice' => '促销价格',
            'num'    => '库存',
            'cover'  => '图片封面',
            'pics'   => '商品图片',
            'ison'   => '是否上架',
            'istui'   => '是否推荐',
        ];
    }

    /**
     * [add 添加商品信息]
     *
     * @DateTime 2017-12-12
     */
    
    public  function add($data)
    {
        if($this->load($data) && $this->save())
        {
            return true;
        }
        
        return false;
    }
}
