<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "{{%user}}".
 *
 * @property string $userid
 * @property string $username
 * @property string $userpass
 * @property string $useremail
 * @property string $createtime
 */
class User extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    public $repass;
    public $loginname;
    public $rememberMe=true;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%user}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            ['loginname','required','message'=>'登录名字不能为空','on'=>['login']],
            ['username','required','message'=>'用户名不能为空','on'=>['reg','regbymail']],
            ['username','unique','message'=>'用户名已经被注册','on'=>['reg','regbymail']],
            ['useremail','required','message'=>'邮箱不能为空','on'=>['reg','regbymail']],
            ['useremail','unique','message'=>'该邮箱已被注册','on'=>['reg','regbymail']],
            ['useremail','email','message'=>'邮箱格式不正确','on'=>['reg','regbymail']],
            ['userpass','required','message'=>'密码不能为空','on'=>['reg','regbymail','login']],
            ['repass','required','message'=>'确认密码不能为空','on'=>['reg']],
            ['repass','compare','compareAttribute'=>'userpass','message'=>'两次密码不一样','on'=>['reg']],
            ['loginname','validatepass'],


        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'userid' => 'Userid',
            'username' => '用户名',
            'userpass' => '用户密码',
            'useremail' => '用户邮箱',
            'repass'=>'确认密码',
            'loginname'=>'用户名/邮箱',
        ];
    }

    /**
     * [reg 添加用户操作]
     *
     * @DateTime 2017-12-05
     *
     * @param    [type] $data
     * @param    string $scenario
     *
     * @return   [type]
     */
    public function reg($data,$scenario = 'reg')
    {
        $this->scenario=$scenario;

        if($this->load($data) && $this->validate())
        {
            // 创建时间
            $this->createtime=time();
            $this->userpass=Yii::$app->getSecurity()->generatePasswordHash($this->userpass);
            // $this->userpass=md5($this->userpass);
            // save 为false的时候，不验证直接插入数据库
            if($this->save(false))
            {
                return true;
            }
            return false;
        }
        return false;
    }

    /**
     * [getProfile 关联查询profile信息表的的内容]
     *
     * @DateTime 2017-12-05
     *
     * @return   [type]
     */
    public function getProfile()
    {
        // 一对一的关系， 数组条件 user表里面的userid对应profile表里面的userid
        return $this->hasOne(Profile::className(),['userid'=>'userid']);
    }
    /**
     * [regByEmail 通过邮箱创建用户]
     *
     * @DateTime 2017-12-06
     *
     * @param    [type] $data
     *
     * @return   [type]
     */
    public function regByEmail($data)
    {
        $data['User']['username']='Yii'.uniqid();
        $data['User']['userpass']=uniqid();
        $this->scenario='regbymail';

        if($this->load($data) && $this->validate())
        {
            $mailer=Yii::$app->mailer->compose('createuser',['username'=>$data['User']['username'],'userpass'=>$data['User']['userpass']]);
            $mailer->setFrom('1655585137@qq.com');
            $mailer->setTo($data['User']['useremail']);
            $mailer->setSubject('新建用户');
            // if($mailer->send() && $this->reg($data,'regbymail'))
            if($mailer->queue() && $this->reg($data,'regbymail'))
            {
                return true;
            }
        }
        return false;

    }
    /**
     * [getUser 获取用户的实例]
     *
     * @DateTime 2018-01-22
     *
     * @return   [type]
     */
    public function getUser()
    {
        return self::find()->where('username=:loginname or useremail=:loginname',[":loginname"=>$this->loginname])->one();
    }

    /**
     * [login 执行登录]
     *
     * @DateTime 2017-12-06
     *
     * @param    [type] $data
     *
     * @return   [type]
     */
    public function login($data)
    {
        // var_dump($data);
        $this->scenario='login';
        if($this->load($data) && $this->validate())
        {
            // 条件成功返回session
            // 已经验证的用户 ，保存的时间
            $lifetime= $this->rememberMe ? 24*3600 : 0;
            return Yii::$app->user->login($this->getUser(),$lifetime);          
            // $session=Yii::$app->session;
            // 设置登录的周期
            // session_set_cookie_params($lifetime);
            // $session['isLogin']='1';
            // $session['loginname']=$this->loginname;

            // return (bool)$session['isLogin'];

        }
    }

    /**
     * [validatepass 登录验证操作]
     *
     * @DateTime 2017-12-07
     *
     * @return   [type]
     */
    public function validatepass()
    {
        if(!$this->hasErrors())
        {
          
            // 判断登录的用户名类型
            $loginname='username';
            if(preg_match('/@/',$this->loginname))
            {
                $loginname='useremail';
            }
            // var_dump($loginname);
            // 根据用户名以及密码查询用户信息
            $data=self::find()->where($loginname.'=:loginname',[':loginname'=>$this->loginname])->one();
            // var_dump($data);die;
            // var_dump($data->userpass);die;
            if(is_null($data))
            {
                // echo 2221;die;
                $this->addError('userpass','用户名或者密码错误'); 
            }
            // 根据查询出来的用户的密码做验证
            // var_dump(Yii::$app->getSecurity()->validatePassword($this->userpass,$data->userpass));die;
            if(!Yii::$app->getSecurity()->validatePassword($this->userpass,$data->userpass))
            {
                // echo 222;die;
               $this->addError('userpass','用户名或者密码错误');  
            }
            // 存储用户的名字
            // Yii::$app->session['loginname']=$data->username;
            // var_dump(Yii::$app->session['loginname']);
            // die;
        }
    }
    // 通过ID 查询
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }  
    // 通过token查询  该方法实用于无状态的 RESTful 应用，
    public static function findIdentityByAccessToken($token,$type=null)
    {
        return null;
    }
    // 获取用户实例的主键ID
    public  function  getId()
    {
        // Yii::$app->end();
        return $this->userid;
    }

    // 当前用户的（cookie）认证密钥
    public  function  getAuthKey()
    {
       return '';
    }

    public function validateAuthKey($authkey)
    {
        return true;
    }
}

