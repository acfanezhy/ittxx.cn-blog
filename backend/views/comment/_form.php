<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-23 15:49
 */

/**
 * @var $this yii\web\View
 * @var $model backend\models\Comment
 */

use backend\widgets\ActiveForm;
use common\libs\Constants;

$this->title = "Comments";
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox float-e-margins">
            <?=$this->render('/widgets/_ibox-title')?>
            <div class="ibox-content">
                <?php $form = ActiveForm::begin(); ?>
                <?= $form->field($model, 'nickname') ?>
                <?= $form->field($model, 'content')->textarea() ?>
                <?= $form->field($model, 'website_url') ?>
                <?= $form->field($model, 'ip') ?>
                <?= $form->field($model, 'status')->radioList(Constants::getCommentStatusItems()) ?>
                <?= $form->defaultButtons() ?>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
</div>