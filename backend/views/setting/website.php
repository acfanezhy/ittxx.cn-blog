<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2017-03-15 21:16
 */

/**
 * @var $this \yii\web\View
 * @var $model common\models\Options
 */

use backend\widgets\ActiveForm;
use common\libs\Constants;

$this->title = yii::t('app', 'Website Setting');
$this->params['breadcrumbs'][] = yii::t('app', 'Website Setting');
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox float-e-margins">
            <?=$this->render('/widgets/_ibox-title')?>
            <div class="ibox-content">
                <?php $form = ActiveForm::begin(); ?>
                <?= $form->field($model, 'website_title') ?>
                <?= $form->field($model, 'website_url') ?>
                <?= $form->field($model, 'seo_keywords') ?>
                <?= $form->field($model, 'seo_description')->textInput(['style'=>"min-height:60px;"]) ?>
                <?= $form->field($model, 'website_language')->dropDownList([
                    'zh-CN' => '简体中文',
                    'zh-TW' => '繁体中文',
                    'en-US' => 'English'
                ]) ?>
                <?= $form->field($model, 'website_comment')->radioList(Constants::getYesNoItems()) ?>
                <?= $form->field($model, 'website_comment_need_verify')->radioList(Constants::getYesNoItems()) ?>
                <?php
                $temp = \DateTimeZone::listIdentifiers();
                $timezones = [];
                foreach ($temp as $v) {
                    $timezones[$v] = $v;
                }
                ?>
                <?= $form->field($model, 'website_timezone')->dropDownList($timezones) ?>
                <?= $form->field($model, 'website_icp') ?>
                <?= $form->field($model, 'website_statics_script')->textarea() ?>
                <?= $form->field($model, 'website_status')->radioList(Constants::getWebsiteStatusItems()) ?>
                <?= $form->defaultButtons() ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>
