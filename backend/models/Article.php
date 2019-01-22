<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-04-11 09:53
 */

namespace backend\models;

use yii;
use common\helpers\Util;
use common\libs\Constants;
use common\models\meta\ArticleMetaTag;

class Article extends \common\models\Article
{
    /**
     * @var string
     */
    public $tag = '';

    /**
     * @var null|string
     */
    public $content = null;

    /**
     * @inheritdoc
     */
    public function afterValidate()
    {
        parent::afterValidate();
        if($this->visibility == Constants::ARTICLE_VISIBILITY_SECRET){//加密文章需要设置密码
            if( empty( $this->password ) ){
                $this->addError('password', yii::t('app', "Secret article must set a password"));
            }
        }
    }

    /**
     * @inheritdoc
     */
    public function beforeSave($insert)
    {
        Util::handleModelSingleFileUpload($this, 'thumb', $insert, '@thumb', ['thumbSizes'=>self::$thumbSizes]);
        $this->seo_keywords = str_replace('，', ',', $this->seo_keywords);
        if ($insert) {
            $this->author_id = yii::$app->getUser()->getIdentity()->getId();
            $this->author_name = yii::$app->getUser()->getIdentity()->username;
        }
        if($this->scan_count < 10) $this->scan_count = rand(100, rand(200,300));

        if(!$this->created_at || $this->created_at==0) $this->created_at = time();
        else $this->created_at = strtotime($this->created_at);
        //echo $this->updated_at; exit;
        if(!$this->summary){ $this->summary = $this->title;}

        if(!$this->seo_keywords){
            $newkeyword='';
            $define_keywords = yii::$app->feehi->keword_dictionary;
            $keywords = explode(",", $define_keywords);
            foreach ($keywords as $keyword){
                if(strpos($this->content, $keyword)) $newkeyword .= $keyword.',';
            }
            $this->seo_keywords = $newkeyword;
        }
        //var_dump($newkeyword); exit;
        if(!$this->seo_title){ $this->seo_title = $this->title;}
        if(!$this->seo_description){ $this->seo_description = $this->summary;}

        return parent::beforeSave($insert);
    }

    /**
     * @inheritdoc
     */
    public function afterSave($insert, $changedAttributes)
    {
        $articleMetaTag = new ArticleMetaTag();
        $articleMetaTag->setArticleTags($this->id, $this->tag);
        if ($insert) {
            $contentModel = new ArticleContent();
            $contentModel->aid = $this->id;
        } else {
            if ($this->content === null) {
                return;
            }
            $contentModel = ArticleContent::findOne(['aid' => $this->id]);
            if ($contentModel == null) {
                $contentModel = new ArticleContent();
                $contentModel->aid = $this->id;
            }
        }
        $contentModel->content = $this->content;
        $contentModel->save();
        parent::afterSave($insert, $changedAttributes);
    }

    /**
     * @inheritdoc
     */
    public function beforeDelete()
    {
        Comment::deleteAll(['aid' => $this->id]);
        if (($articleContentModel = ArticleContent::find()->where(['aid' => $this->id])->one()) != null) {
            $articleContentModel->delete();
        }
        return true;
    }

    /**
     * @inheritdoc
     */
    public function afterFind()
    {
        parent::afterFind();
        $this->tag = call_user_func(function(){
            $tags = '';
            foreach ($this->articleTags as $tag) {
                $tags .= $tag->value . ',';
            }
            return rtrim($tags, ',');
        });
        $this->created_at = date('Y-m-d H:i:s',$this->created_at);
        $this->content = ArticleContent::findOne(['aid' => $this->id])['content'];
    }

}