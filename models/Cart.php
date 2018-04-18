<?php

namespace app\models;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveRecord;
use Yii;

/**
 * This is the model class for table "{{%cart}}".
 *
 * @property string $cartid
 * @property string $productid
 * @property string $productnum
 * @property string $price
 * @property string $userid
 * @property string $createtime
 */
class Cart extends ActiveRecord
{


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
        return '{{%cart}}';
    }

    /**
     * @inheritdoc
     */
 
    public function rules()
    {
        return [
            [['productid','productnum','userid','price'], 'required'],
            ['createtime', 'safe']
        ];
    }

   

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'cartid' => 'Cartid',
            'productid' => 'Productid',
            'productnum' => 'Productnum',
            'price' => 'Price',
            'userid' => 'Userid',
            'createtime' => 'Createtime',
        ];
    }
}
