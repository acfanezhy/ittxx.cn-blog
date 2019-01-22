<?php
/**
 * Author: lf
 * Blog: https://blog.ittxx.cn
 * Email: job@ittxx.cn
 * Created at: 2016-04-02 22:48
 */

namespace frontend\controllers;

use yii;
use yii\web\Controller;
use frontend\models\Article;
use yii\web\NotFoundHttpException;

class PageController extends Controller
{

    /**
     * 单页
     *
     * @param string $name
     * @return string
     * @throws \yii\web\NotFoundHttpException
     */
    public function actionView($name = '')
    {
        if ($name == '') {
            $name = yii::$app->getRequest()->getPathInfo();
        }
        $model = Article::findOne(['type' => Article::SINGLE_PAGE, 'sub_title' => $name]);
        if (empty($model)) {
            throw new NotFoundHttpException('None page named ' . $name);
        }
		Article::updateAllCounters(['scan_count' => 1], ['sub_title' => $name]);  //更新浏览次数
        return $this->render('view', [
            'model' => $model,
        ]);
    }

}