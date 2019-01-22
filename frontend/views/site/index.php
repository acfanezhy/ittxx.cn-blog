<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
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
use frontend\widgets\ArticleListView;
use frontend\widgets\ScrollPicView;
use common\models\Category;
use common\widgets\JsBlock;
use frontend\assets\IndexAsset;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;

IndexAsset::register($this);
$this->title = yii::$app->feehi->website_title;
?>
<?php ?>
<div class="content-wrap">
    <div class="content">
        <div id="wowslider-container1">
            <?= ScrollPicView::widget([
                'banners' => Options::getBannersByType('index'),
            ]) ?>
            <div class="ws_shadow"></div>
        </div>

        <?php
        $categorys = Category::find()->where(['parent_id' => 0])->limit(10)->orderBy("sort asc,id asc")->all();
        foreach ($categorys as $category) {
            $category = Category::findOne(['alias' => $category->alias]);
            $descendants = Category::getDescendants($category['id']);
            if( empty($descendants) ) {
                $where['cid'] = $category['id'];
            }else{
                $cids = ArrayHelper::getColumn($descendants, 'id');
                $cids[] = $category['id'];
                $where['cid'] = $cids;
            }
        ?>
            <div class="top_recommonds">
                <div class="relates">
                <?= ArticleListView::widget([
                    'dataProvider' => new ArrayDataProvider([
                        'allModels' => Article::find()->limit(1)->where($where)->limit(10)->with('category')->orderBy("sort asc,created_at desc,id desc")->all(),
                    ]),
                    'layout' => "<h2 class='fontgreen'><i class='fa fa-th-list'></i>&nbsp;&nbsp;" . $category->name . " <span class='more'><a href='".Url::to(['article/index', 'cat' => $category->alias])."' title='查看更多'>更多>></a></span></h2>
                                    <ul class=\"related_img cont\">
                                        {items}
                                    </ul>
                                 ",
                    'template' => "<i class='fa fa-minus'></i><a rel='bookmark' title='{title}' href='{article_url}' class='titt' target='_blank'>{title_list}</a><em>{scan_count}</em>",
                    'itemOptions' => ['tag'=>'li', 'class' =>''],
                ]) ?>
                </div>
            </div>
        <?php } ?>
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
