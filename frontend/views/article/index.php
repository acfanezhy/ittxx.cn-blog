<?php
/**
 * Author: lf
 * Blog: https://blog.ittxx.cn
 * Email: job@ittxx.cn
 * Created at: 2017-03-15 21:16
 */

/**
 * @var $this yii\web\View
 * @var $dataProvider yii\data\ActiveDataProvider
 * @var $type string
 */

use common\models\Options;
use yii\helpers\ArrayHelper;
use frontend\models\Article;
use common\models\Category;
use frontend\widgets\ArticleListView;
use frontend\widgets\ScrollPicView;
use common\widgets\JsBlock;
use frontend\assets\IndexAsset;
use yii\data\ArrayDataProvider;

IndexAsset::register($this);
$this->title = yii::$app->feehi->website_title;
?>
<div class="content-wrap">
    <div class="content">
        <?php if($cat !==''){ ?>
        <div class="top_recommonds">
            <div class="relates">
                <?php				
                $category = Category::findOne(['alias' => $cat]);
                $descendants = Category::getDescendants($category['id']);
                if( empty($descendants) ) {
                    $where['cid'] = $category['id'];
                    $limts = 4;
                }else{
                    $cids = ArrayHelper::getColumn($descendants, 'id');
                    $cids[] = $category['id'];
                    $where['cid'] = $cids;
                    $limts = 8;
                }
                ?>
            <?= ArticleListView::widget([
                'dataProvider' => new ArrayDataProvider([
                    'allModels' => Article::find()->limit(1)->where(['flag_headline'=>1])->andWhere($where)->limit($limts)->with('category')->orderBy("sort asc")->all(),
                ]),
                'layout' => "<h2><i class='fa fa-th-list'></i>&nbsp;&nbsp;" . yii::t('frontend', 'Well-choosen') . "</h2>
                                <ul class=\"related_img cont\">
                                    {items}
                                </ul>
                             ",
                'template' => "<i class='fa fa-minus'></i><a rel='bookmark' title='{title}' href='{article_url}' class='tit' target='_blank'>{title_list}</a>",
                'itemOptions' => ['tag'=>'li', 'class' =>''],
                //'thumbWidth' => 168,
                //'thumbHeight' => 112,
            ]) ?>
            </div>
        </div>
		<?php } ?>
        <header class="archive-header">
            <h1><i class="fa fa-folder-open"></i>&nbsp;&nbsp;<?=$type?>&nbsp;&nbsp;<a title="订阅<?=$type?>" target="_blank" href="" style="display: none"><i class="rss fa fa-rss"></i></a></h1>
        </header>

        <?= ArticleListView::widget([
            'dataProvider' => $dataProvider,
            /*'layout' => "<div class='list-views'>
                                    {items}
                                </div>
                             ",*/
        ]) ?>
    </div>
</div>
<?= $this->render('/widgets/_sidebar') ?>
<?php JsBlock::begin() ?>
<script>
    $(function () {
        var mx = document.body.clientWidth;
        $(".slick").responsiveSlides({
            auto: true,
            pager: true,
            nav: true,
            speed: 700,
            timeout: 7000,
            maxwidth: mx,
            namespace: "centered-btns"
        });
    });
</script>
<?php JsBlock::end() ?>
