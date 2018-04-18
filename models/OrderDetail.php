<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%order_detail}}".
 *
 * @property string $detailid
 * @property string $productid
 * @property string $price
 * @property string $productnum
 * @property string $orderid
 * @property string $createtime
 */
class OrderDetail extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%order_detail}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['productid', 'productnum', 'orderid', 'createtime'], 'integer'],
            [['price'], 'number'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'detailid' => 'Detailid',
            'productid' => 'Productid',
            'price' => 'Price',
            'productnum' => 'Productnum',
            'orderid' => 'Orderid',
            'createtime' => 'Createtime',
        ];
    }

    /**
     * [add 添加方法]
     *
     * @DateTime 2017-12-20
     *
     * @param    [type] $data
     */
    public function add($data)
    {
        if ($this->load($data) && $this->save()) {
            return true;
        }
     
        return false;
    }
}
