<?php

namespace app\modules\models;

use Yii;

/**
 * This is the model class for table "{{%admin}}".
 *
 * @property string $adminid
 * @property string $adminuser
 * @property string $adminpass
 * @property string $adminemail
 * @property string $logintime
 * @property string $loginip
 * @property string $createtime
 */
class Admin extends \yii\db\ActiveRecord implements \yii\web\IdentityInterface
{
    public $remeberMe=true; //声明一个记住我的属性
    public $repass;
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        // 使用表前缀
        return '{{%admin}}';

    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
            // [['logintime', 'loginip', 'createtime'], 'integer'],
            // [['adminuser', 'adminpass'], 'string', 'max' => 32],
            // [['adminemail'], 'string', 'max' => 50],
            // [['adminuser', 'adminpass'], 'unique', 'targetAttribute' => ['adminuser', 'adminpass'], 'message' => 'The combination of Adminuser and Adminpass has already been taken.'],
            // [['adminuser', 'adminemail'], 'unique', 'targetAttribute' => ['adminuser', 'adminemail'], 'message' => 'The combination of Adminuser and Adminemail has already been taken.'],        
        return [
            ['adminuser','required','message'=>'管理员账号不能为空','on'=>['login','seekpass','changepass','adminadd','changeemail']],
            ['adminpass','required','message'=>'管理员密码不能为空','on'=>['login','changepass','adminadd','changeemail']],
            ['remeberMe','boolean','on'=>'login'],
            ['adminpass','validatePass','on'=>['login','changeemail']],
            ['adminemail','required','message'=>'电子邮箱不能为空','on'=>['seekpass','adminadd','changeemail']],
            ['adminemail','email','message'=>'电子邮箱的格式不正确','on'=>['seekpass','adminadd','changeemail']],
            ['adminemail','unique','message'=>'电子邮箱已经被注册','on'=>['adminadd','-p']],
            ['adminuser','unique','message'=>'该账号已经被注册','on'=>'adminadd'],
            ['adminemail','validatEmail','on'=>'seekpass'],
            ['repass','required','message'=>'确认密码不能为空','on'=>['changepass','adminadd']],
            ['repass','compare','compareAttribute'=>'adminpass','message'=>'两次密码不一致','on'=>['changepass','adminadd']],
        ];
    }
    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'adminid' => 'Adminid',
            'adminuser' => '管理员账号',
            'adminpass' => '管理员密码',
            'adminemail' => '管理员邮箱',
            'logintime' => 'Logintime',
            'loginip' => 'Loginip',
            'createtime' => 'Createtime',
            'repass'=>'确认密码',
        ];
    }    
    // 验证密码
    public function validatePass()
    {
        if(!$this->hasErrors())
        {
            // where 条件的占位符  :user 对应后面数组的值
            $data=self::find()->where('adminuser=:user',[':user'=>$this->adminuser])->one();
            if(is_null($data))
            {
                // 返回错误信息
                $this->addError('adminpass','用户名字或者密码错误');
            } 
            if(!Yii::$app->getSecurity()->validatePassword($this->adminpass,$data->adminpass))
            {
                $this->addError('adminpass','用户名字或者密码错误');
            }  
        }
    }

    /**
     * [validatEmail 验证电子邮箱]
     *
     * @DateTime 2017-11-24
     *
     * @return   [boolean]
     */
    public function  validatEmail()
    {
        if(!$this->hasErrors())
        {
            $data=self::find()->where('adminuser=:user and adminemail=:email',[':user'=>$this->adminuser,':email'=>$this->adminemail])->one();
            if(is_null($data))
            {
                $this->addError('adminemail','用户名或者邮箱不匹配');
            }
        }
    }
    /**
     * [getAdmin 获取后台登录用户的实例]
     *
     * @DateTime 2018-01-25
     *
     * @return   [type]
     */
    public function getAdmin()
    {
        return self::find()->where('adminuser=:user',[':user'=>$this->adminuser])->one();
    }

    // 执行登录
    public function login($data)
    {
        // 指定那一个场景使用规则    在rules下用 on
        $this->scenario='login';
        // 判断函数是否值是否加载进来
        if($this->load($data) && $this->validate())
        {
            // 写入用户登录信息
            $lifetime=$this->remeberMe ? 24*3600 : 0;

            return Yii::$app->admin->login($this->getAdmin(),$lifetime);
            // // 调用session  php手册查看该函数,声明周期,路径,作用域 …………
            // // session_set_cookie_params('lifetime','path','domain',....);
            // session_set_cookie_params($lifetime);
            // // 自带封装session
            // $session=Yii::$app->session;
            // $session['admin']=[
            //     'adminuser'=>$this->adminuser,
            //     'isLogin'=>1,
            // ];
            // // Yii:$app->request->userIP 获取用户IP
            // // 执行更新操作  更新字段 更新条件(:user 占位符) 绑定更新参数
            // $this->updateAll(['logintime'=>time(),'loginip'=>ip2long(Yii::$app->request->userIP)],'adminuser=:user',[':user'=>$this->adminuser]);            
            // return (bool)$session['admin']['isLogin'];
        }
        return false;
    }
    /**
     * [seekPass 执行找回密码]
     *
     * @DateTime 2017-11-24
     *
     * @param    [type] $data
     *
     * @return   boolean
     */
    public function seekPass($data)
    {
        $this->scenario='seekpass';
        // $this->validate()  系统封装的 
        // 对我们传递过来的表单做验证  对应上面生成的rules规则
        // http://blog.csdn.net/wujiangwei567/article/details/46446537 表单验证 api 
        if($this->load($data) && $this->validate())
        {
            // 获取传递过来的参数
            $adminuser=$data['Admin']['adminuser'];
            $adminemail=$data['Admin']['adminemail'];
            // 创建一个token
            $time=time();
            $token=$this->createToken($adminuser,$time);
            // 渲染我们的邮件模板并且传递参数
            $mailer=Yii::$app->mailer->compose('seekpass',['adminuser'=>$adminuser,'time'=>$time,'token'=>$token]);
            $mailer->setFrom('1655585137@qq.com');
            $mailer->setTo($adminemail);
            $mailer->setSubject('找回密码');
            if($mailer->send())
            {
                return true;
            }
            // Yii::$app->mailer->compose()
            //     ->setFrom('1655585137@qq.com')
            //     ->setTo($adminemail)
            //     ->setSubject('Message subject')
            //     ->setTextBody('Plain text content')
            //     ->setHtmlBody('<b>1125Test mail</b>')
            //     ->send();
        }
        return false;
    }
    /**
     * [createToken 创建token]
     *  加密方式： MD5用户名拼接base64编码的用户IP再次拼接MD5的时间戳，之后把拼接的字符串再次MD5了
     * @DateTime 2017-11-25
     *
     * @param    [type] $adminuser
     * @param    [type] $time
     *
     * @return   [type]
     */
    public  function createToken($adminuser,$time)
    {
        return md5(md5($adminuser).base64_encode(Yii::$app->request->userIP).md5($time));
    }
    /**
     * [changepass 修改密码操作]
     *
     * @DateTime 2017-11-25
     *
     * @param    [type] $data
     *
     * @return   [type]
     */
    public function changepass($data)
    {
        $this->scenario='changepass';
        if($this->load($data) && $this->validate())
        {
            // var_dump($this->adminpass);
            // var_dump($this->adminuser);
            // var_dump($data);die;
            return (bool)$this->updateAll(['adminpass'=>md5($this->adminpass)],'adminuser=:user',[':user'=>$this->adminuser]);
        }
        return false;
    }

    /**
     * [reg 添加管理员]
     *
     * @DateTime 2017-11-28
     *
     * @param    [type] $data
     *
     * @return   [type]
     */
    public function reg($data)
    {
        $this->scenario='adminadd';
        // $data['Admin']['adminpass']=md5($data['Admin']['adminpass']);
        // $data['Admin']['repass']=md5($data['Admin']['repass']);
        //save  执行插入操作
        if($this->load($data) && $this->validate())
        {
            $this->adminpass=Yii::$app->getSecurity()->generatePasswordHash($this->adminpass);
            // $this->adminpass=md5($this->adminpass);
            // 如果save直接传递false 就会省略验证直接插入
            if($this->save(false))
            {
                return true;
            }
            return false;
        }
        return false;
    }


    /**
     * [changeemail 修改邮箱操作]
     *
     * @DateTime 2017-12-04
     *
     * @param    [type] $data
     *
     * @return   [type]
     */
    public function changeemail($data)
    {
        $this->scenario='changeemail';
        if($this->load($data) && $this->validate())
        {
            return (bool)$this->updateAll(['adminemail'=>$this->adminemail],'adminuser=:user',[':user'=>$this->adminuser]);
        }
        return false;
    }   


    // 实现接口的方法
    public static function findIdentity($id)
    {
        return static::findOne($id);
    }

    public static function findIdentityByAccessToken($token,$type=null)
    {
        return null;
    }

    public function getId()
    {
        return $this->adminid;
    }

    public function getAuthKey()
    {
        return '';
    }
    
    public function validateAuthKey($authkey)
    {
        return true;
    }
}
