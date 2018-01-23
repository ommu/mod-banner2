<?php
/**
 * Banner Click Histories (banner-click-history)
 * @var $this ClickController
 * @var $model BannerClickHistory
 * @var $form CActiveForm
 *
 * @author Putra Sudaryanto <putra@sudaryanto.id>
 * @contact (+62)856-299-4114
 * @copyright Copyright (c) 2017 Ommu Platform (opensource.ommu.co)
 * @created date 8 January 2017, 21:21 WIB
 * @modified date 23 January 2018, 07:09 WIB
 * @link https://github.com/ommu/ommu-banner
 *
 */
?>

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>
	<ul>
		<li>
			<?php echo $model->getAttributeLabel('category_search'); ?>
			<?php $category = BannerCategory::getCategory();
			if($category != null)
				echo $form->dropDownList($model, 'category_search', $category, array('prompt'=>'', 'class'=>'form-control'));
			else
				echo $form->dropDownList($model, 'category_search', array('prompt'=>'Select Category'), array('class'=>'form-control')); ?>
		</li>

		<li>
			<?php echo $model->getAttributeLabel('banner_search'); ?>
			<?php echo $form->textField($model, 'banner_search', array('class'=>'form-control')); ?>
		</li>

		<li>
			<?php echo $model->getAttributeLabel('user_search'); ?>
			<?php echo $form->textField($model, 'user_search', array('class'=>'form-control')); ?>
		</li>

		<li>
			<?php echo $model->getAttributeLabel('click_date'); ?>
			<?php //echo $form->textField($model, 'click_date', array('class'=>'form-control'));
			$this->widget('application.libraries.core.components.system.CJuiDatePicker',array(
				'model'=>$model,
				'attribute'=>'click_date',
				//'mode'=>'datetime',
				'options'=>array(
					'dateFormat' => 'dd-mm-yy',
				),
				'htmlOptions'=>array(
					'class' => 'form-control',
				 ),
			)); ?>
		</li>

		<li>
			<?php echo $model->getAttributeLabel('click_ip'); ?>
			<?php echo $form->textField($model, 'click_ip', array('class'=>'form-control')); ?>
		</li>

		<li class="submit">
			<?php echo CHtml::submitButton(Yii::t('phrase', 'Search')); ?>
		</li>
	</ul>
<?php $this->endWidget(); ?>
