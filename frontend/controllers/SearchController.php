<?php
/**
 * Author: lf
 * Blog: https://blog.ittxx.cn
 * Email: job@ittxx.cn
 * Created at: 2016-04-05 13:08
 */

namespace frontend\controllers;

use common\models\meta\ArticleMetaTag;
use yii;
use frontend\models\Article;
use yii\web\Controller;
use yii\data\ActiveDataProvider;

class SearchController extends Controller
{

    /**
     * 搜索
     *
     * @return string
     */
    public function actionIndex()
    {
        $where = ['type' => Article::ARTICLE];
        $query = Article::find()->select([])->where($where);
        $keyword = htmlspecialchars(yii::$app->getRequest()->get('q'));
        $time = htmlspecialchars(yii::$app->getRequest()->get('time'));
        $newmonth_date_fisrt = strtotime(date('Y-m-01 H:i:s', strtotime($time)));
        $newmonth_date_last = strtotime(date('Y-m-31 H:i:s', strtotime($time)));
        if($time){
            $query->andWhere(['and',['>','created_at',$newmonth_date_fisrt], ['<','created_at',$newmonth_date_last]]);
            $key = str_replace('-','年',$time).'月';
			$type = yii::t('frontend', 'Publish on {keyword} results', ['keyword'=>'<span class=isearch>'.$key.'</span>']);
        }
        else{
            $query->andFilterWhere(['like', 'title', $keyword]);
            $key = $keyword;
			$type = yii::t('frontend', 'Search keyword {keyword} results', ['keyword'=>'<span class=isearch>'.$key.'</span>']);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'sort' => SORT_ASC,
                    'id' => SORT_DESC,
                ]
            ]
        ]);
        return $this->render('/article/index', [
            'dataProvider' => $dataProvider,
            'type' => $type,
			'cat' => '',
        ]);
    }

    public function actionTag($tag='')
    {
        $metaTagModel = new ArticleMetaTag();
        $aids = $metaTagModel->getAidsByTag($tag);
        $where = ['type' => Article::ARTICLE];
        $query = Article::find()->select([])->where($where)->where(['in', 'id', $aids]);
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort' => [
                'defaultOrder' => [
                    'sort' => SORT_ASC,
                    'id' => SORT_DESC,
                ]
            ]
        ]);
        return $this->render('/article/index', [
            'dataProvider' => $dataProvider,
            'type' => yii::t('frontend', 'Tag {tag} related articles', ['tag'=>'<span class=isearch>'.$tag.'</span>']),
			'cat' => '',
        ]);
    }

}
