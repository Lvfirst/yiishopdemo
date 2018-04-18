<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%address}}".
 *
 * @property string $addressid
 * @property string $firstname
 * @property string $lastname
 * @property string $company
 * @property string $address
 * @property string $postcode
 * @property string $email
 * @property string $telephone
 * @property string $userid
 * @property string $createtime
 */
class Address extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%address}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
         return [
            [['userid', 'firstname', 'lastname', 'address', 'email', 'telephone'], 'required'],
            [['createtime', 'postcode'],'safe'],
         ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'addressid' => 'Addressid',
            'firstname' => 'Firstname',
            'lastname' => 'Lastname',
            'company' => 'Company',
            'address' => 'Address',
            'postcode' => 'Postcode',
            'email' => 'Email',
            'telephone' => 'Telephone',
            'userid' => 'Userid',
            'createtime' => 'Createtime',
        ];
    }
}
