<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-06-19 14:46
 */
namespace frontend\widgets;

class ScrollPicView extends \yii\base\Widget
{

    public $banners;

    //public $template = "<ul class='slick centered-btns centered-btns1' style='max-width: 1309px;'>{lis}</ul>
                        //<a href='' class=\"centered-btns_nav centered-btns1_nav prev\">Previous</a>
                        //<a href='' class=\"centered-btns_nav centered-btns1_nav next\">Next</a>";

    //public $liTemplate = "<li id=\"centered-btns1_s0\" class=\"\" style=\"display: list-item; float: none; position: absolute; opacity: 0; z-index: 1; transition: opacity 700ms ease-in-out;\">
                            // <a target='{target}' href=\"{link_url}\"><img class=\"img_855x300\" src=\"{img_url}\" alt=\"\"><span></span></a>
                          //</li>";

    public $template = "<div class=\"ws_images\">
                            <ul>
                                {lis1}
                            </ul>
                        </div>
            
                        <div class=\"ws_thumbs\">
                            <div>
                                {lis2}
                            </div>
                        </div>";

    public $liTemplate1 = "<li><a target=\"{target}\" href=\"{link_url}\" title=\"{desc}\"><img src=\"{img_url}\" title=\"{desc}\" alt=\"{desc}\"/></a></li>";
    public $liTemplate2 = "<a target=\"{target}\" href=\"{link_url}\" title=\"{desc}\"><img src=\"{img_url}\" width='120' height='60'/></a>";

    /**
     * @inheritdoc
     */
    public function run()
    {
        parent::run();
        $lis1 = '';$lis2 = '';
        foreach ($this->banners as $banner) {
            $lis1 .= str_replace(['{link_url}', '{img_url}', '{target}', '{desc}'], [$banner['link'], $banner['img'], $banner['target'], $banner['desc']], $this->liTemplate1);
            $lis2 .= str_replace(['{link_url}', '{img_url}', '{target}', '{desc}'], [$banner['link'], $banner['img'], $banner['target'], $banner['desc']], $this->liTemplate2);
        }

        return str_replace(['{lis1}','{lis2}'], [$lis1,$lis2], $this->template);

    }

}