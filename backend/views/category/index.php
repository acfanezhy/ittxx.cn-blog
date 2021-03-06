<?php
/**
 * Author: lf
 * Blog: https://blog.feehi.com
 * Email: job@feehi.com
 * Created at: 2016-03-21 14:14
 */

/**
 * @var $dataProvider yii\data\ArrayDataProvider
 * @var $model common\models\Category
 */

use backend\grid\DateColumn;
use backend\grid\GridView;
use backend\grid\SortColumn;
use backend\widgets\Bar;
use yii\helpers\Url;
use yii\helpers\Html;
use backend\grid\CheckboxColumn;
use backend\grid\ActionColumn;

$this->title = "Category";
$this->params['breadcrumbs'][] = yii::t('app', 'Category');
?>
<div class="row">
    <div class="col-sm-12">
        <div class="ibox">
            <?= $this->render('/widgets/_ibox-title') ?>
            <div class="ibox-content">
                <?= Bar::widget() ?>
                <?= GridView::widget([
                    'dataProvider' => $dataProvider,
                    'columns' => [
                        [
                            'class' => CheckboxColumn::className(),
                        ],
                        [
                            'class' => ActionColumn::className(),
                            'buttons' => [
                                'create' => function ($url, $model, $key) {
                                    return Html::a('<i class="fa  fa-plus" aria-hidden="true"></i> ', Url::to([
                                        'create',
                                        'parent_id' => $model['id']
                                    ]), [
                                        'title' => Yii::t('app', 'Create'),
                                        'data-pjax' => '0',
                                        'class' => 'btn btn-white btn-sm J_menuItem',
                                    ]);
                                }
                            ],
                            'width' => '160',
                            'template' => '{create} {view-layer} {update} {delete}',
                        ],
                        [
                            'attribute' => 'id',
                            'label' => yii::t('app', 'Id'),
                        ],
                        [
                            'attribute' => 'name',
                            'label' => yii::t('app', 'Name'),
                            'format' => 'html',
                            'value' => function ($model, $key, $index, $column) {
                                if($model['level'] > 1){
                                    return str_repeat('--', $model['level']) . $model['name'];
                                }
                                else{
                                    return str_repeat('', $model['level']) . "<B>".$model['name']."</B>";
                                }
                            }
                        ],
                        [
                            'attribute' => 'alias',
                            'label' => yii::t('app', 'Alias'),
                        ],
                        [
                            'class' => SortColumn::className(),
                            'label' => yii::t('app', 'Sort')
                        ],
                        [
                            'class' => DateColumn::className(),
                            'label' => yii::t('app', 'Created At'),
                            'attribute' => 'created_at',
                        ],
                        [
                            'class' => DateColumn::className(),
                            'label' => yii::t('app', 'Updated At'),
                            'attribute' => 'updated_at',
                        ],
                    ]
                ]) ?>
            </div>
        </div>
    </div>
</div>
