<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

namespace common\models\meta;

use yii;

class ArticleMetaShare extends \common\models\ArticleMeta
{

    public $keyName = "share";


    /**
     * @param $aid
     * @return bool
     */
    public function setShare($aid)
    {
        $this->aid = $aid;
        $this->key = $this->keyName;
        $this->value = yii::$app->getRequest()->getUserIP();
        return $this->save(false);
    }

    /**
     * @param $aid
     * @return int|string
     */
    public function getShareCount($aid)
    {
        return $this->find()->where(['aid' => $aid, 'key' => $this->keyName])->count("aid");
    }

}
