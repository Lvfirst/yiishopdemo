<?php

namespace app\modules\controllers;

use yii\web\Controller;

/**
 * Default control0ler for the `admin` module
 */
class DefaultController extends CommonController
{
	protected $mustlogin=['index'];
    /**
     * Renders the index view for the module
     * @return string
     */
    public function actionIndex()
    {
    	$this->layout='layout1';
        return $this->render('index');
    }
}
