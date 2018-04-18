<?php

namespace app\controllers;

use Yii;
use yii\filters\AccessControl;
use yii\web\Controller;
use yii\web\Response;
use yii\filters\VerbFilter;
use app\models\LoginForm;
use app\models\ContactForm;
use app\models\EntryForm;
use app\models\Country;
class SiteController extends Controller
{
    /**
     * @inheritdoc
     */
    // public function behaviors()
    // {
    //     return [
    //         'access' => [
    //             'class' => AccessControl::className(),
    //             'only' => ['logout'],
    //             'rules' => [
    //                 [
    //                     'actions' => ['logout'],
    //                     'allow' => true,
    //                     'roles' => ['@'],
    //                 ],
    //             ],
    //         ],
    //         'verbs' => [
    //             'class' => VerbFilter::className(),
    //             'actions' => [
    //                 'logout' => ['post'],
    //             ],
    //         ],
    //     ];
    // }

    /**
     * @inheritdoc
     */
    public function actions()
    {
        return [
            'error' => [
                'class' => 'yii\web\ErrorAction',
            ],
            'captcha' => [
                'class' => 'yii\captcha\CaptchaAction',
                'fixedVerifyCode' => YII_ENV_TEST ? 'testme' : null,
            ],
        'auth' => [
          'class' => 'yii\authclient\AuthAction',
          // 'successCallback' => [$this, 'successCallback'],
          'successUrl'=>\yii\helpers\Url::to(['site/callback']),
          ],            
        ];
    }

    /**
     * Displays homepage.
     *
     * @return string
     */
    public function actionIndex()
    {
        return $this->render('index');
    }

    /**
     * Login action.
     *
     * @return Response|string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }
        return $this->render('login', [
            'model' => $model,
        ]);
    }

    /**
     * Logout action.
     *
     * @return Response
     */
    public function actionLogout()
    {
        Yii::$app->user->logout();

        return $this->goHome();
    }

    /**
     * Displays contact page.
     *
     * @return Response|string
     */
    public function actionContact()
    {
        $model = new ContactForm();
        if ($model->load(Yii::$app->request->post()) && $model->contact(Yii::$app->params['adminEmail'])) {
            Yii::$app->session->setFlash('contactFormSubmitted');

            return $this->refresh();
        }
        return $this->render('contact', [
            'model' => $model,
        ]);
    }

    /**
     * Displays about page.
     *
     * @return string
     */
    public function actionAbout()
    {
        return $this->render('about');
    }
    /**
     * [actionsay 测试]
     *
     * @DateTime 2017-07-28
     *
     * @param    string $message
     *
     * @return   [type]
     */
    public function actionSay($message='hello')
    {
        // var_dump($_GET);die;
        return $this->render('say',['message'=>$message]);
    }

    public function actionEntry()
    {
        //实例化表单的模型 
        $models=new EntryForm;
        // var_dump($model);
        if($models->load(Yii::$app->request->post()) && $models->validate())
        {
            // $this->dd($models);
            return $this->render('entry-confirm',['model'=>$models]);
        }
        else
        {
            return $this->render('entry',['model'=>$models]);
        }
    }
    /**
     * [actionMydb 测试数据库连接]
     *
     * @DateTime 2017-07-29
     *
     * @return   [type]
     */
    public function actionMydb()
    {
        // $countries=Country::find()->orderBy('name')->all();
        
        $country=Country::findOne('US'); 
             
        // echo $country->name;
        $country->name='USA';
        $country->save();
        // $this->dd($countries);
    }



    public function actionCallback()
    {
        echo '2222';
        // $attributes = $client->getUserAttributes();
        // echo 2222;
    }



    public function successCallback($client)
    {
         $attributes = $client->getUserAttributes();

         var_dump($attributes);
         // user login or signup comes here
    }
}

