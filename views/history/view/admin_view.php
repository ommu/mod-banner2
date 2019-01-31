<?php
/**
 * Banner Views (banner-views)
 * @var $this app\components\View
 * @var $this ommu\banner\controllers\history\ViewController
 * @var $model ommu\banner\models\BannerViews
 *
 * @author Putra Sudaryanto <putra@sudaryanto.id>
 * @contact (+62)856-299-4114
 * @copyright Copyright (c) 2017 OMMU (www.ommu.co)
 * @created date 6 October 2017, 13:24 WIB
 * @modified date 24 January 2019, 17:54 WIB
 * @link https://github.com/ommu/mod-banner
 *
 */

use yii\helpers\Html;
use yii\helpers\Url;
use yii\widgets\DetailView;

$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Views'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $model->banner->title;

$this->params['menu']['content'] = [
	['label' => Yii::t('app', 'Back To Manage'), 'url' => Url::to(['index']), 'icon' => 'table'],
	['label' => Yii::t('app', 'Delete'), 'url' => Url::to(['delete', 'id'=>$model->view_id]), 'htmlOptions' => ['data-confirm'=>Yii::t('app', 'Are you sure you want to delete this item?'), 'data-method'=>'post'], 'icon' => 'trash'],
];
?>

<div class="banner-views-view">

<?php echo DetailView::widget([
	'model' => $model,
	'options' => [
		'class'=>'table table-striped detail-view',
	],
	'attributes' => [
		'view_id',
		[
			'attribute' => 'categoryId',
			'value' => isset($model->banner->category) ? $model->banner->category->title->message : '-',
		],
		[
			'attribute' => 'bannerTitle',
			'value' => isset($model->banner) ? $model->banner->title : '-',
		],
		[
			'attribute' => 'userDisplayname',
			'value' => isset($model->user) ? $model->user->displayname : '-',
		],
		[
			'attribute' => 'view_date',
			'value' => Yii::$app->formatter->asDatetime($model->view_date, 'medium'),
		],
		'view_ip',
		[
			'attribute' => 'views',
			'value' => Html::a($model->views ? $model->views : 0, ['history/manage', 'view'=>$model->primaryKey], ['title'=>Yii::t('app', '{count} histories', ['count'=>$model->views])]),
			'format' => 'html',
		],
	],
]) ?>

</div>