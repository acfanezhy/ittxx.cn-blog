<?php
/**
 * Author: lf
 * Blog: https://blog.ittxx.cn
 * Email: job@ittxx.cn
 * Created at: 2016-04-02 22:55
 */

/**
 * @var $this yii\web\View
 * @var $model frontend\models\Article
 * @var $commentModel frontend\models\Comment
 * @var $prev frontend\models\Article
 * @var $next frontend\models\Article
 * @var $recommends array
 * @var $commentList array
 */

use frontend\widgets\ArticleListView;
use yii\data\ArrayDataProvider;
use yii\helpers\Url;
use frontend\assets\ViewAsset;
use common\widgets\JsBlock;
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use frontend\models\Comment;
use frontend\models\User;
use frontend\models\Menu;
use common\models\Category;
use common\libs\Constants;

$this->title = $model->title;

$this->registerMetaTag(['name' => 'keywords', 'content' => $model->seo_keywords], 'keywords');
$this->registerMetaTag(['name' => 'description', 'content' => $model->seo_description], 'description');
$this->registerMetaTag(['name' => 'tags', 'content' => call_user_func(function()use($model) {
    $tags = '';
    foreach ($model->articleTags as $tag) {
        $tags .= $tag->value . ',';
    }
    return rtrim($tags, ',');
    }
)], 'tags');
$this->registerMetaTag(['property' => 'article:author', 'content' => $model->author_name]);
$categoryName = $model->category ? $model->category->name : yii::t('app', 'uncategoried');
$countComment = Comment::getCommentCount($model->id);

$thisParentid = Menu::findOne(['name' => $categoryName])['parent_id'];
$thisParentName = Menu::findOne(['id' => $thisParentid])['name'];
$thisCatParentid = Category::findOne(['alias' => $model->category->alias])['parent_id'];
$thisParentAlias = Category::findOne(['id' => $thisCatParentid])['alias'];
echo "<style>#menu-item-{$thisParentid}>a,#menu-item-{$thisParentid}>a:hover{background:#1ABC9C;}</style>";

if($thisParentName) $categoryNames = $categoryName.'-'.$thisParentName;
else $categoryNames = $categoryName;
if($model->seo_title) $this->title = $model->seo_title. '-' .$categoryNames.'-' .yii::$app->feehi->website_title;
else $this->title=$model->title. '-' .$categoryNames.'-' .yii::$app->feehi->website_title;

ViewAsset::register($this);

?>
<div class="content-wrap">
    <div class="content">
        <div class="breadcrumbs">
            <a title="<?=yii::t('frontend', 'Return Home')?>" href="<?= yii::$app->getHomeUrl() ?>"><i class="fa fa-home"></i></a>
            <?php if($thisParentName){ ?>
                <small>&gt;</small>
                <a href="<?= Url::to(['article/index', 'cat' => $thisParentAlias]) ?>"><?= $thisParentName ?></a>
            <?php } ?>
            <small>&gt;</small>
            <a href="<?= Url::to(['article/index', 'cat' => $model->category->alias]) ?>"><?= $categoryName ?></a>
            <small>&gt;</small>
            <span class="muted"><?=yii::t('frontend', 'Contents')?></span>
            <span style="float: right"><a id="fullscreem"  href="javascript:">宽屏浏览》</a></span>
        </div>
        <header class="article-header">
            <h1 class="article-title"><span class='label label-inverse label-j label-f'><?=Constants::getArticleCtype($model->ctype)?><i class='label-arrow'></i></span>&nbsp;&nbsp;<?= $model->title ?></h1>
            <div class="meta">
                <span id="mute-category" class="muted"><i class="fa fa-list-alt"></i>
                    <a href="<?= Url::to(['article/index','cat' => $model->category->alias]) ?>"> <?= $categoryName ?></a>
                </span>
                <span class="muted" style="display: none"><i class="fa fa-user"></i> admin</span>
                <time class="muted"><i class="fa fa-clock-o"></i> <?= yii::$app->getFormatter()->asDate($model->created_at) ?></time>
				<span class="muted"><i class="fa fa-leaf"></i> <?= $model->from ?></span>
                <span class="muted"><i class="fa fa-eye"></i> <span id="scanCount"><?= $model->scan_count ?></span></span>
                <span class="muted"><i class="fa fa-comments-o"></i>
                    <a href="<?= Url::to([
                        'article/view',
                        'id' => $model->id
                    ]) ?>#comments">
                        <?= $countComment ?><?=yii::t('frontend', 'Comment')?>
                    </a>
                </span>
            </div>
        </header>

        <article class="article-content">
            <?= $model->articleContent->content ?>
            <p style="text-indent: 0px;">
                <?= yii::t('frontend', 'Reproduced please indicate the source') ?>：
                <a href="<?= yii::$app->homeUrl ?>" data-original-title="" title=""><?= yii::$app->feehi->website_title ?></a>
                »
                <a href="<?= Url::to(['article/view', 'id' => $model->id]) ?>" data-original-title="" title=""><?= $model->title ?></a>
            </p>
			<p style="text-indent: 0px;">
			最后更新：<?= date('Y-m-d H:i:s',$model->updated_at) ?>
			</p>
			
            <div class="article-social">
                <a href="javascript:;" data-action="ding" data-id="<?=$model->id?>" like-url="<?=Url::to(['article/like'])?>" id="Addlike" class="action"><i class="fa fa-thumbs-up"></i><?=yii::t('frontend', 'Like')?> (<span class="count"><?= $model->getArticleLikeCount() ?></span>)</a>
                <span class="or">or</span>
                <span class="action action-share bdsharebuttonbox"><i class="fa fa-share-alt"></i><?=yii::t('frontend', 'Share')?> (<span class="share_count"><?= $model->getArticleShareCount() ?></span>)
                    <div class="action-popover">
                        <div class="popover top in"><div class="arrow"></div>
                            <div class="popover-content">
                                <a href="javascript:;" data-action="share" data-id="<?=$model->id?>" share-url="<?=Url::to(['article/share'])?>" id="Addshare" class="sinaweibo fa fa-weibo" data-cmd="tsina" title="分享到新浪微博"></a>
                                <a href="javascript:;" data-action="share" data-id="<?=$model->id?>" share-url="<?=Url::to(['article/share'])?>" id="Addshare" class="bds_qzone fa fa-star" data-cmd="qzone" title="分享到QQ空间"></a>
                                <a href="javascript:;" data-action="share" data-id="<?=$model->id?>" share-url="<?=Url::to(['article/share'])?>" id="Addshare" class="tencentweibo fa fa-tencent-weibo" data-cmd="tqq" title="分享到腾讯微博"></a>
                                <a href="javascript:;" data-action="share" data-id="<?=$model->id?>" share-url="<?=Url::to(['article/share'])?>" id="Addshare" class="qq fa fa-qq" data-cmd="sqq" title="分享到QQ好友"></a>
                                <a href="javascript:;" data-action="share" data-id="<?=$model->id?>" share-url="<?=Url::to(['article/share'])?>" id="Addshare" class="bds_renren fa fa-renren" data-cmd="renren" title="分享到人人网"></a>
                                <a href="javascript:;" data-action="share" data-id="<?=$model->id?>" share-url="<?=Url::to(['article/share'])?>" id="Addshare" class="bds_weixin fa fa-weixin" data-cmd="weixin" title="分享到微信"></a>
                                <a href="javascript:;" data-action="share" data-id="<?=$model->id?>" share-url="<?=Url::to(['article/share'])?>" id="Addshare" class="bds_more fa fa-ellipsis-h" data-cmd="more"></a>
                            </div>
                        </div>
                    </div>
                </span>
            </div>
        </article>
        <footer class="article-footer">
            <div class="article-tags">
                <i class="fa fa-tags"></i>
                <?php foreach ($model->articleTags as $tag){ ?>
                    <a href="<?=Url::to(['search/tag', 'tag'=>urlencode($tag->value)])?>" rel="tag" data-original-title="" title=""><?=$tag->value?></a>
                <?php } ?>
            </div>
        </footer>
        <nav class="article-nav">
            <?php
                if ($prev !== null) {
            ?>
                <span class="article-nav-prev">
                    <i class="fa fa-angle-double-left"></i><a href='<?= Url::to(['article/view', 'id' => $prev->id]) ?>' rel="prev"><?= $prev->title ?></a>
                </span>
            <?php } ?>
            <?php
                if ($next != null) {
            ?>
                <span class="article-nav-next">
                    <a href="<?= Url::to(['article/view', 'id' => $next->id]) ?>" rel="next"><?= $next->title ?></a><i class="fa fa-angle-double-right"></i>
                </span>
            <?php } ?>
        </nav>
        <div class="related_posts">
            <div class="relates">
                <?= ArticleListView::widget([
                    'dataProvider' => new ArrayDataProvider([
                        'allModels' => $recommends,
                    ]),
                    'layout' => "<ul class='related_img'><h2>" . yii::t('frontend', 'Related Recommends') . "</h2>{items}</ul>",
                    'template' => "<i class='fa fa-minus'></i><a href='{article_url}' title='{title}' target='_blank'>{title_list}</a>",
                    'itemOptions' => ['tag'=>'li', 'class'=>''],
                ]) ?>
            </div>
        </div>

        <div id="respond" class="no_webshot">
            <form action="" method="post" id="commentform">
                <?php $form = ActiveForm::begin(); ?>
                <?= Html::activeHiddenInput($commentModel, 'aid', ['value' => $model->id]) ?>
                <div class="comt-title" style="display: block;">
                    <?php if (yii::$app->getUser()->getIsGuest()) {
                        $user= yii::t('frontend', 'Guest');
                        $userAvatar = '/frontend/web/admin/static/img/default.png';
                    } else {
                        $user= yii::$app->getUser()->getIdentity()->username;
                        //$userAvatar = User::findByUsername($user)['avatar'];
                        $userAvatar = '/frontend/web/admin/static/img/default.png';
                        //userAvatar = yii::$app->getRequest()->getBaseUrl()/static/images/comment-user-avatar.png
                    } ?>
                    <div class="comt-avatar pull-left">
                        <img src="<?=$userAvatar ?>" class="avatar avatar-72" height="50" width="50">
                    </div>
                    <div class="comt-author pull-left">
                        <?= $user ?>
                        <span><?= yii::t('frontend', 'Post my comment') ?></span> &nbsp;
                        <a class="switch-author" href="javascript:void(0)" data-type="switch-author" style="font-size:12px;"><?= yii::t('frontend', 'Change account') ?></a>
                    </div>
                    <a id="cancel-comment-reply-link" class="pull-right" href="javascript:;"><?= yii::t('frontend', 'Cancel comment') ?></a>
                </div>

                <div class="comt">
                    <div class="comt-box">
                        <?= $form->field($commentModel, 'content', ['template' => '{input}'])->textarea([
                            'class' => 'input-block-level comt-area',
                            'cols' => '100%',
                            'rows' => '3',
                            'tabindex' => 1,
                            'placeholder' => yii::t('frontend', 'Writing some...'),
                            "id" => "comment"
                        ]) ?>
                        <div class="comt-ctrl">
                            <button class="btn btn-primary pull-right" type="submit" name="submit" id="submit" tabindex="5">
                                <i class="fa fa-check-square-o"></i> <?= yii::t('frontend', 'Submit comment') ?>
                            </button>
                            <div class="comt-tips pull-right">
                                <div class="comt-tip comt-error" style="display: none;"></div>
                                <input type='hidden' name='comment_post_ID' value='114' id='comment_post_ID'/>
                                <?= $form->field($commentModel, 'reply_to', ['template' => '{input}'])->hiddenInput(['value' => 0, 'id' => 'comment_parent']) ?>
                                <p style="display: none;"><input type="hidden" id="akismet_comment_nonce" name="akismet_comment_nonce" value="32920dc775"/></p>
                                <p style="display: none;"><input type="hidden" id="ak_js" name="ak_js" value="101"/></p>
                            </div>
                            <span data-type="comment-insert-smilie" class="muted comt-smilie"><i class="fa fa-smile-o"></i> <?= yii::t('frontend', 'emoj') ?></span>
                            <span class="muted comt-mailme"><label for="comment_mail_notify" class="checkbox inline" style="padding-top:0">
                                <input type="checkbox" name="comment_mail_notify" id="comment_mail_notify" value="comment_mail_notify" checked="checked"><?=yii::t('frontend', 'Send email at someone replied')?></label>
                            </span>
                        </div>
                    </div>
                    <div class="comt-comterinfo" id="comment-author-info" style="display:none">
                        <h4><?= yii::t('frontend', 'Hi, Please fill') ?></h4>
                        <ul>
                            <li class="form-inline">
                                <label class="hide" for="author"><?= yii::t('app', 'Nickname') ?></label>
                                <?php if (yii::$app->getUser()->getIsGuest()) {
                                    $defaultNickname = yii::t('frontend', 'Guest');
                                } else {
                                    $defaultNickname = yii::$app->getUser()->getIdentity()->username;
                                } ?>
                                <?= $form->field($commentModel, 'nickname', ['template' => '{input}<span class="help-inline">' . yii::t('app', 'Nickname') . ' (' . yii::t('frontend', 'required') . ')</span>'])->textInput(['value' => $defaultNickname]) ?>
                            </li>
                            <li class="form-inline"><?= $form->field($commentModel, 'email', ['template' => '{input}<span class="help-inline">' . yii::t('app', 'Email') . ' </span>'])->textInput() ?></li>
                            <li class="form-inline"><?= $form->field($commentModel, 'website_url', ['template' => '{input}<span class="help-inline">' . yii::t('frontend', 'Website') . '</span>'])->textInput() ?></li>
                        </ul>
                    </div>
                </div>
                <?php ActiveForm::end() ?>
        </div>
        <div id="postcomments">
            <div id="comments">
                <i class="fa fa-comments-o"></i> <b> (<?= $countComment ?>)</b><?= yii::t('frontend', 'person posted') ?>
            </div>
            <ol class="commentlist">
                <?php
                foreach ($commentList as $v) {
                    //$userAvatar = User::findByUsername($v['nickname'])['avatar'];
                    //if(!$userAvatar){
                        $userAvatar = '/frontend/web/admin/static/img/default.png';
                    //} //$userAvatar = 'https://secure.gravatar.com/avatar/'.md5($v['nickname']);
                    ?>
                    <li class="comment even thread-even depth-1 byuser comment-author-admin bypostauthor"
                        id="comment-<?= $v['id'] ?>">
                        <div class="c-avatar">
                            <img class="avatar avatar-72" height="50" width="50" src="<?= $userAvatar ?>" style="display: block;">
                            <div class="c-main" id="div-comment-<?= $v['id'] ?>">
                                <?= $v['content'] ?><br>
                                <div class="c-meta">
                                    <span class="c-author"><?= empty($v['nickname']) ? '游客' : $v['nickname'] ?></span><?= yii::$app->formatter->asDate($v['created_at']) ?>
                                    (<?= yii::$app->getFormatter()->asRelativeTime($v['created_at']) ?>)
                                    <a rel="nofollow" class="comment-reply-link" href="" onclick="return addComment.moveForm('div-comment-<?= $v['id'] ?>', '<?= $v['id'] ?>', 'respond','0' )" aria-label="回复给admin">回复</a>
                                </div>
                            </div>
                        </div>
                        <?php
                        if (! empty($v['sub'])) {
                            ?>
                            <ul class="children">
                                <?php
                                    foreach ($v['sub'] as $value) {
                                        //$userAvatar = User::findByUsername($value['nickname'])['avatar'];
                                        //if(!$userAvatar){
                                            $userAvatar = '/frontend/web/admin/static/img/default.png';
                                        //}
                                ?>
                                    <li class="comment odd alt depth-2" id="comment-<?= $value['id'] ?>">
                                        <div class="c-avatar">
                                            <img class="avatar avatar-72" height="50" width="50" src="<?= $userAvatar ?>" style="display: block;">
                                            <div class="c-main" id="div-comment-<?= $value['id'] ?>"><?= $value['content'] ?><br>
                                                <div class="c-meta">
                                                    <span class="c-author">
                                                        <?= empty($value['nickname']) ? yii::t('frontend', "Guest") : $value['nickname'] ?>
                                                    </span>
                                                    <?= yii::$app->getFormatter()->asDate($value['created_at']) ?>(<?= yii::$app->getFormatter()->asRelativeTime($value['created_at']) ?>)
                                                </div>
                                            </div>
                                        </div>
                                    </li><!-- #comment-## -->
                                <?php } ?>
                            </ul>
                        <?php } ?>
                    </li><!-- #comment-## -->
                <?php } ?>
            </ol>
            <div class="commentnav">
            </div>
        </div>
    </div>
</div>

<?= $this->render('/widgets/_sidebar') ?>

<?php JsBlock::begin(); ?>
<script type="text/javascript">
    SyntaxHighlighter.all();
    $(document).ready(function () {
        $.ajax({
            url:"<?=Url::to(['article/view-ajax'])?>",
            data:{id:<?=$model->id?>},
            success:function (data) {
                $("span.count").html(data.likeCount);
                $("span.share_count").html(data.shareCount);
                $("span#scanCount").html(data.scanCount);
                $("span#commentCount").html(data.commentCount);
            }
        });
    })
</script>
<script>with(document)0[(getElementsByTagName("head")[0]||body).appendChild(createElement("script")).src="http://bdimg.share.baidu.com/static/api/js/share.js?v=89860593.js?cdnversion="+~(-new Date()/36e5)];</script>
<?php JsBlock::end(); ?>
