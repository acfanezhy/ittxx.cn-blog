<?php
/**
 * Author: lf
 * Blog: https://blog.ittxx.cn
 * Email: job@ittxx.cn
 * Created at: 2016-06-21 14:26
 */
use common\models\meta\ArticleMetaTag;
use common\models\Options;
use frontend\models\Article;
use yii\helpers\Url;
use frontend\models\Comment;
use frontend\models\User;
use common\helpers\CutStr;
use frontend\models\FriendlyLink;
use yii\data\ActiveDataProvider;


?>
<aside class="sidebar">
    <div class="widget widget_text">
        <div class="textwidget">
            <div class="social">
                <a href="<?= yii::$app->feehi->github ?>" rel="external nofollow" title="" target="_blank" data-original-title="github"><i class="tencentweibo fa fa-github"></i></a>
                <a href="<?= yii::$app->feehi->weibo ?>" rel="external nofollow" title="" target="_blank" data-original-title="新浪微博"><i class="sinaweibo fa fa-weibo"></i></a>
                <a class="weixin" data-original-title="" title=""><i class="weixins fa fa-weixin"></i>
                    <div class="weixin-popover">
                        <div class="popover bottom in">
                            <div class="arrow"></div>
                            <div class="popover-title"><?=yii::t('frontend', 'Wechat')?>“<?= yii::$app->feehi->wechat ?>”</div>
                            <div class="popover-content"><img src="<?=yii::$app->getRequest()->getBaseUrl()?>/frontend/web/static/images/weixin.jpg"></div>
                        </div>
                    </div>
                </a>
                <a href="mailto:<?= yii::$app->feehi->email ?>" rel="external nofollow" title="" target="_blank" data-original-title="Email"><i class="email fa fa-envelope-o"></i></a>
                <a href="http://wpa.qq.com/msgrd?v=3&amp;uin=<?= yii::$app->feehi->qq ?>&amp;site=qq&amp;menu=yes" rel="external nofollow" title="" target="_blank" data-original-title="联系QQ"><i class="qq fa fa-qq"></i></a>
                <a href="<?= Url::to(['article/rss'])?>" rel="external nofollow" target="_blank" title="" data-original-title="订阅本站"><i class="rss fa fa-rss"></i></a>
            </div>
        </div>
    </div>
    
    <div class="widget widget_text"><div class="title"><h2><sapn class="title_span">欢迎光临</span></h2></div>			
        <div class="textwidget"><p style='padding:20px'>欢迎您的光临，本博所发布之文章皆为作者亲测通过，如有错误，欢迎通过各种方式指正。内容持续更新，可保留关注！<br><br>
        E-mail: &nbsp;&nbsp;&nbsp;admin@ittxx.cn<br><br>
        QQ：&nbsp;164800697<br>
        </p></div>
	</div>
        
    <div class="widget d_postlist">
        <div class="title">
            <h2>
                <sapn class="title_span"><?= yii::t('frontend', 'Article Groupon') ?></sapn>
            </h2>
        </div>
        <ul class="guidang">
            <?php
            $starttime = Article::find()->min('created_at');
            $endtime = Article::find()->max('created_at');
            /* while( ($starttime = strtotime('+1 month', $starttime)) <= $endtime) {
                $month_arr[] = date('Y-m', $endtime); // 取得递增月;
            }*/
            $i = false;
            while( $starttime < $endtime ) {
                //$newmonth = !$i ? date('Y-n', strtotime('+0 Month', $starttime)) : date('Y-n', strtotime('+1 Month', $starttime));
				$newmonth = !$i ? date('Y-n', strtotime('-0 Month', $endtime)) : date('Y-n', strtotime('-1 Month', $endtime));
                //$starttime = strtotime( $newmonth );
			    $endtime = strtotime( $newmonth );
                $i = true;
                //$month_arr[]= $newmonth;
                $newmonth_date_fisrt = strtotime(date('Y-m-01 H:i:s', strtotime($newmonth)));
                $newmonth_date_last = strtotime(date('Y-m-31 H:i:s', strtotime($newmonth)));
				$where = ['type' => Article::ARTICLE];
                $query = Article::find()->where($where)->andWhere(['and',['>','created_at',$newmonth_date_fisrt], ['<','created_at',$newmonth_date_last]])->all();
                if(count($query) > 0){
                  echo "<a href=".Url::to(['/search', 'time' => $newmonth])." style='font-size:115%; float:left; margin:5px 15px; display:block'>".str_replace('-','年',$newmonth)."月 <span style='color:#999'> ( ".count($query)." )</span></a>  ";
                }
            }

            ?>
        </ul>
    </div>
    <div class="widget d_postlist">
        <div class="title">
            <h2>
                <sapn class="title_span"><?= yii::t('frontend', 'Hot Recommends') ?></sapn>
            </h2>
        </div>
        <ul>
            <?php
            $articles = Article::find()->where(['flag_special_recommend' => 1])->limit(6)->orderBy("sort asc")->all();
            foreach ($articles as $article) {
                /** @var $article \frontend\models\Article */
                $url = Url::to(['article/view', 'id' => $article->id]);
                //$imgUrl = $article->getThumbUrlBySize(125, 86);
                $article->title= CutStr::truncate_utf8_string($article->title, 20);
                $article->created_at = yii::$app->formatter->asDate($article->created_at);
                echo "<li>
                    <a href=\"{$url}\" title=\"{$article->title}\" target=\"_blank\">
                        <span class=\"thumbnail\" style='display: none'><img src=\"\" alt=\"\"></span>
                        <span class=\"text\">{$article->title}</span>
                        <span class=\"muted\">{$article->created_at}</span><span class=\"muted_1\">{$article->comment_count}" . yii::t('frontend', ' Comments') . "</span>
                    </a>
                </li>";
            }
            ?>
        </ul>
    </div>

    <div class="widget d_tag">
        <div class="title">
            <h2>
                <sapn class="title_span"><?= yii::t('frontend', 'Clound Tags') ?></sapn>
            </h2>
        </div>
        <div class="d_tags">
            <?php
            $tagsModel = new ArticleMetaTag();
            foreach ($tagsModel->getHotestTags() as $k => $v) {
                echo "<a title='' href='" . Url::to(['search/tag', 'tag' => urlencode($k)]) . "' data-original-title='{$v}" . yii::t('frontend', ' Topics') . "'>{$k} ({$v})</a>";
            }
            ?>
        </div>
    </div>

    <div class="widget d_comment">
        <div class="title">
            <h2>
                <sapn class="title_span"><?= yii::t('frontend', 'Latest Comments') ?></sapn>
            </h2>
        </div>
        <ul>
            <?php
            $comments = Comment::find()->orderBy("id desc")->limit(6)->all();
            foreach ($comments as $v) {
                //$urlimg  = User::findByUsername($v['nickname'])['avatar'];
                //$userAvatar = yii::$app->params['site']['url'].$urlimg;
                //if(!$urlimg){
                    $userAvatar = '/frontend/web/admin/static/img/default.png';
                //}
                ?>
                <li>
                    <a href="<?= Url::to(['article/view', 'id' => $v['aid'], '#' => 'comment-' . $v['id']]) ?>" title="">
                        <img data-original="<?=$userAvatar ?>" class="avatar avatar-72" height="50" width="50" src="<?=$userAvatar ?>" style="display: block;">
                        <div class="muted">
                            <i><?= $v['nickname'] ?></i>&nbsp;&nbsp;<?= yii::$app->formatter->asRelativeTime($v['created_at']) ?>
                            (<?= yii::$app->formatter->asTime($v['created_at']) ?>)<?= yii::t('frontend', ' said') ?>
                            ：<br><span><?= $v['content'] ?></span></div>
                    </a>
                </li>
            <?php } ?>
        </ul>
    </div>
    <div class="widget widget_text">
        <div class="title">
            <h2>
                <sapn class="title_span"><?= yii::t('frontend', 'Friendly Links') ?></sapn>
            </h2>
        </div>
        <div class="textwidget">
            <div class="d_tags_1">
                <?php
                $links = FriendlyLink::find()->where(['status' => FriendlyLink::DISPLAY_YES])->orderBy("sort asc, id asc")->asArray()->all();
                foreach ($links as $link) {
                    echo "<a target='_blank' href='{$link['url']}'>{$link['name']}</a>";
               }
                ?>
            </div>
        </div>
    </div>
</aside>
