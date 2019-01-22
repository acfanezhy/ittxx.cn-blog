<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-06-19 00:21
 */

namespace frontend\widgets;

use common\models\Article;
use yii;
use yii\helpers\Url;
use yii\helpers\ArrayHelper;
use yii\widgets\LinkPager;
use yii\helpers\StringHelper;
use frontend\models\Comment;
use common\libs\Constants;
use common\models\meta\ArticleMetaLike;

class ArticleListView extends \yii\widgets\ListView
{

    /**
     * @var string 布局
     */
    public $layout = "{items}\n<div class=\"pagination\">{pager}</div>";

    /**
     * @var int 标题截取长度
     */
    public $titleLength = 40;
    public $titlelistLength = 20;

    /**
     * @var int summary截取长度
     */
    public $summaryLength = 110;

    /**
     * @var int 缩率图宽
     */
    public $thumbWidth = 220;

    /**
     * @var int 缩略图高
     */
    public $thumbHeight = 150;

    public $itemOptions = [
        'tag' => 'article',
        'class' => 'excerpt'
    ];

    public $pagerOptions = [
        'firstPageLabel' => '首页',
        'lastPageLabel' => '尾页',
        'prevPageLabel' => '上一页',
        'nextPageLabel' => '下一页',
        'options' => [
            'class' => '',
        ],
    ];

    /**
     * @var string 模板
     */
    public $template = "<div class='focus' style='display: none'>
                                   <!--<a target='_blank' href='{article_url}'>
                                        <img width='186px' height='112px' class='thumb' src='{img_url}' alt='{title}'></a>-->
                               </div>
                               <header>
                                   <a class='label {labelclass} label-j' href='{category_url}?ctype={ctype}'>{ctypeName}<i class='label-arrow'></i></a>
                                   <h2><a target='_blank' href='{article_url}' title='{title}'>{title}</a></h2>
                               </header>
                               <p class='auth-span'>
                                   <span id='mute-category' class='muted'><i class='fa fa-list-alt'></i><a href='{category_url}'> {category}</a></span>
                                   <span class='muted' style='display:none'><i class='fa fa-user'></i> <a href=''>{auth_name}</a></span>
                                   <span class='muted'><i class='fa fa-clock-o'></i> {pub_date}</span>
                                   <span class='muted'><i class='fa fa-eye'></i> {scan_count}</span>
                                   <span class='muted'><i class='fa fa-comments-o'></i> <a target='_blank' href='{comment_url}'>{comment_count}评论</a></span>
                                   <span class='muted'><a href='javascript:;' data-action='ding' data-id='{article_id}' like-url='/article/like' id='Addlike' class='action'><i class='fa fa-heart-o'></i> <span class='count'>{like_count}</span>喜欢</a></span>

                               </p>
                               <span class='note' style='display: {visible}'> {summary}</span>";

    /**
     * @inheritdoc
     */
    public function init()
    {
        parent::init();
        $this->pagerOptions = [
            'firstPageLabel' => yii::t('app', 'first'),
            'lastPageLabel' => yii::t('app', 'last'),
            'prevPageLabel' => yii::t('app', 'previous'),
            'nextPageLabel' => yii::t('app', 'next'),
            'options' => [
                'class' => 'pagination',
            ]
        ];
        if( empty($this->itemView) ) {
            $this->itemView = function ($model, $key, $index) {
                /** @var $model \frontend\models\Article */
                $categoryName = $model->category ? $model->category->name : yii::t('app', 'uncategoried');
                $categoryUrl = Url::to(['article/index', 'cat' => $model->category->alias]);
                //$imgUrl = $model->getThumbUrlBySize($this->thumbWidth, $this->thumbHeight);
                $articleUrl = Url::to(['article/view', 'id' => $model->id]);
                $summary = StringHelper::truncate($model->summary, $this->summaryLength);
                if(!$summary) $visible='none'; else $visible='';
                $title = StringHelper::truncate($model->title, $this->titleLength);
                $title_list = StringHelper::truncate($model->title, $this->titlelistLength);
                $comment_count = Comment::getCommentCount($model->id);
                $like_count = $model->getArticleLikeCount();
                if($model->ctype == 1) $ctypeclass = 'label-info';
                elseif($model->ctype == 2) $ctypeclass = 'label-inverse';
                elseif($model->ctype == 3) $ctypeclass = 'label-success';
                else $ctypeclass = 'label-important';
                $ctypeName = Constants::getArticleCtype($model->ctype);
                if($model->flag_recommend == 1) $title_list = "<span class='reds'>".$title_list."</span>";

                return str_replace([
                    '{article_url}',
                    '{article_id}',
                    //'{img_url}',
                    '{auth_name}',
                    '{category_url}',
                    '{title}',
                    '{title_list}',
                    '{visible}',
                    //'{title_full}',
                    '{summary}',
                    '{pub_date}',
                    '{date}',
                    '{scan_count}',
                    '{comment_count}',
                    '{like_count}',
                    '{category}',
                    '{ctype}',
                    '{ctypeName}',
                    '{labelclass}',
                    '{comment_url}'
                ], [
                    $articleUrl,
                    $model->id,
                    //$imgUrl,
                    $model->author_name,
                    $categoryUrl,
                    $title,
                    $title_list,
                    $visible,
                    //$model->title,
                    $summary,
                    date('Y-m-d', $model->created_at),
                    date('m-d', $model->created_at),
                    $model->scan_count,
                    $comment_count,
                    $like_count,
                    $categoryName,
                    $model->ctype,
                    $ctypeName,
                    $ctypeclass,
                    $articleUrl . "#comments"
                ], $this->template);
            };
        }
    }

    /**
     * @inheritdoc
     */
    public function renderPager()
    {
        $pagination = $this->dataProvider->getPagination();
        if ($pagination === false || $this->dataProvider->getCount() <= 0) {
            return '';
        }
        /* @var $class LinkPager */
        $pager = $this->pager;
        $class = ArrayHelper::remove($pager, 'class', LinkPager::className());
        $pager['pagination'] = $pagination;
        $pager['view'] = $this->getView();
        $pager = array_merge($pager, $this->pagerOptions);
        return $class::widget($pager);
    }

}
