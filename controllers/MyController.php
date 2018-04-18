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
class MyController extends Controller
{
	public function actionLv($arr=123)
	{
		echo $arr;
	}

	public function actionTran()
	{
		$n=10;
		for($i=0;$i<=$n;$i++)
		{
			for($j=0;$j<=$i;$j++)
			{
				if($j==0 || $i==$j)
				{
					$arr[$i][$j]=1;
				}
				else
				{
					$arr[$i][$j]=$arr[$i][$j-1]+$arr[$i-1][$j-1];
				}
				echo $arr[$i][$j]."\t";
			}
			echo "<br>";
		}
	}
}