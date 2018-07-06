<?php
/**
 * SettingController
 * @var $this SettingController
 * @var $model BannerSetting
 * @var $form CActiveForm
 *
 * Reference start
 * TOC :
 *	Index
 *	Edit
 *	Manual
 *
 *	LoadModel
 *	performAjaxValidation
 *
 * @author Putra Sudaryanto <putra@sudaryanto.id>
 * @contact (+62)856-299-4114
 * @copyright Copyright (c) 2014 Ommu Platform (www.ommu.co)
 * @modified date 23 January 2018, 07:08 WIB
 * @link https://github.com/ommu/mod-banner
 *
 *----------------------------------------------------------------------------------------------------------
 */

class SettingController extends Controller
{
	/**
	 * @var string the default layout for the views. Defaults to '//layouts/column2', meaning
	 * using two-column layout. See 'protected/views/layouts/column2.php'.
	 */
	//public $layout='//layouts/column2';
	public $defaultAction = 'index';

	/**
	 * Initialize admin page theme
	 */
	public function init() 
	{
		if(!Yii::app()->user->isGuest) {
			if(in_array(Yii::app()->user->level, array(1,2))) {
				$arrThemes = $this->currentTemplate('admin');
				Yii::app()->theme = $arrThemes['folder'];
				$this->layout = $arrThemes['layout'];
			}
		} else
			$this->redirect(Yii::app()->createUrl('site/login'));
	}

	/**
	 * @return array action filters
	 */
	public function filters() 
	{
		return array(
			'accessControl', // perform access control for CRUD operations
			//'postOnly + delete', // we only allow deletion via POST request
		);
	}

	/**
	 * Specifies the access control rules.
	 * This method is used by the 'accessControl' filter.
	 * @return array access control rules
	 */
	public function accessRules() 
	{
		return array(
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('edit'),
				'users'=>array('@'),
				'expression'=>'$user->level == 1',
			),
			array('allow', // allow authenticated user to perform 'create' and 'update' actions
				'actions'=>array('index','manual'),
				'users'=>array('@'),
				'expression'=>'in_array($user->level, array(1,2))',
			),
			array('deny',  // deny all users
				'users'=>array('*'),
			),
		);
	}
	
	/**
	 * Lists all models.
	 */
	public function actionIndex() 
	{
		$this->redirect(array('edit'));
	}

	/**
	 * Updates a particular model.
	 * If update is successful, the browser will be redirected to the 'view' page.
	 * @param integer $id the ID of the model to be updated
	 */
	public function actionEdit() 
	{
		$category=new BannerCategory('search');
		$category->unsetAttributes();  // clear any default values
		if(Yii::app()->getRequest()->getParam('BannerCategory')) {
			$category->attributes=Yii::app()->getRequest()->getParam('BannerCategory');
		}

		$gridColumn = Yii::app()->getRequest()->getParam('GridColumn');
		$columnTemp = array();
		if($gridColumn) {
			foreach($gridColumn as $key => $val) {
				if($gridColumn[$key] == 1)
					$columnTemp[] = $key;
			}
		}
		$columns = $category->getGridColumn($columnTemp);
		
		$model = BannerSetting::model()->findByPk(1);
		if($model == null)
			$model=new BannerSetting;

		// Uncomment the following line if AJAX validation is needed
		$this->performAjaxValidation($model);

		if(isset($_POST['BannerSetting'])) {
			$model->attributes=$_POST['BannerSetting'];

			$jsonError = CActiveForm::validate($model);
			if(strlen($jsonError) > 2) {
				$errors = $model->getErrors();
				$summary['msg'] = "<div class='errorSummary'><strong>".Yii::t('phrase', 'Please fix the following input errors:')."</strong>";
				$summary['msg'] .= "<ul>";
				foreach($errors as $key => $value) {
					$summary['msg'] .= "<li>{$value[0]}</li>";
				}
				$summary['msg'] .= "</ul></div>";

				$message = json_decode($jsonError, true);
				$merge = array_merge_recursive($summary, $message);
				$encode = json_encode($merge);
				echo $encode;

			} else {
				if(Yii::app()->getRequest()->getParam('enablesave') == 1) {
					if($model->save()) {
						echo CJSON::encode(array(
							'type' => 0,
							'msg' => '<div class="errorSummary success"><strong>'.Yii::t('phrase', 'Banner setting success updated.').'</strong></div>',
						));
						/*
						Yii::app()->user->setFlash('success', Yii::t('phrase', 'Banner setting success updated.'));
						$this->redirect(array('edit'));
						*/
					} else
						print_r($model->getErrors());
				}
			}
			Yii::app()->end();
		}

		$this->pageTitle = Yii::t('phrase', 'Banner Settings');
		$this->pageDescription = '';
		$this->pageMeta = '';
		$this->render('admin_edit', array(
			'model'=>$model,
			'category'=>$category,
			'columns' => $columns,
		));
	}
	
	/**
	 * Lists all models.
	 */
	public function actionManual() 
	{
		$manual_path = $this->module->basePath.'/assets/manual';
		
		$this->dialogDetail = true;
		$this->dialogGroundUrl = Yii::app()->controller->createUrl('o/admin/manage');
		$this->dialogWidth = 400;
		
		$this->pageTitle = Yii::t('phrase', 'Banner Manual Book');
		$this->pageDescription = '';
		$this->pageMeta = '';
		$this->render('admin_manual', array(
			'manual_path'=>$manual_path,
		));
	}

	/**
	 * Returns the data model based on the primary key given in the GET variable.
	 * If the data model is not found, an HTTP exception will be raised.
	 * @param integer the ID of the model to be loaded
	 */
	public function loadModel($id) 
	{
		$model = BannerSetting::model()->findByPk($id);
		if($model===null)
			throw new CHttpException(404, Yii::t('phrase', 'The requested page does not exist.'));
		return $model;
	}

	/**
	 * Performs the AJAX validation.
	 * @param CModel the model to be validated
	 */
	protected function performAjaxValidation($model) 
	{
		if(isset($_POST['ajax']) && $_POST['ajax']==='banner-setting-form') {
			echo CActiveForm::validate($model);
			Yii::app()->end();
		}
	}
}