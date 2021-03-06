<?php
/**
 * BannerClicks
 * 
 * @author Putra Sudaryanto <putra@ommu.id>
 * @contact (+62)856-299-4114
 * @copyright Copyright (c) 2017 OMMU (www.ommu.id)
 * @created date 6 October 2017, 13:04 WIB
 * @modified date 12 February 2019, 22:52 WIB
 * @link https://github.com/ommu/mod-banner
 *
 * This is the model class for table "ommu_banner_clicks".
 *
 * The followings are the available columns in table "ommu_banner_clicks":
 * @property integer $click_id
 * @property integer $banner_id
 * @property integer $user_id
 * @property integer $clicks
 * @property string $click_date
 * @property string $click_ip
 *
 * The followings are the available model relations:
 * @property BannerClickHistory[] $histories
 * @property Users $user
 * @property Banners $banner
 *
 */

namespace ommu\banner\models;

use Yii;
use yii\helpers\Html;
use app\models\Users;

class BannerClicks extends \app\components\ActiveRecord
{
	public $gridForbiddenColumn = [];

	public $categoryId;
	public $bannerTitle;
	public $userDisplayname;

	/**
	 * @return string the associated database table name
	 */
	public static function tableName()
	{
		return 'ommu_banner_clicks';
	}

	/**
	 * @return array validation rules for model attributes.
	 */
	public function rules()
	{
		return [
			[['banner_id'], 'required'],
			[['banner_id', 'user_id', 'clicks'], 'integer'],
			[['click_ip'], 'string', 'max' => 20],
			[['user_id'], 'exist', 'skipOnError' => true, 'targetClass' => Users::className(), 'targetAttribute' => ['user_id' => 'user_id']],
			[['banner_id'], 'exist', 'skipOnError' => true, 'targetClass' => Banners::className(), 'targetAttribute' => ['banner_id' => 'banner_id']],
		];
	}

	/**
	 * @return array customized attribute labels (name=>label)
	 */
	public function attributeLabels()
	{
		return [
			'click_id' => Yii::t('app', 'Click'),
			'banner_id' => Yii::t('app', 'Banner'),
			'user_id' => Yii::t('app', 'User'),
			'clicks' => Yii::t('app', 'Clicks'),
			'click_date' => Yii::t('app', 'Click Date'),
			'click_ip' => Yii::t('app', 'Click IP'),
			'histories' => Yii::t('app', 'Histories'),
			'categoryId' => Yii::t('app', 'Category'),
			'bannerTitle' => Yii::t('app', 'Banner'),
			'userDisplayname' => Yii::t('app', 'User'),
		];
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getHistories($count=false)
	{
        if ($count == false) {
            return $this->hasMany(BannerClickHistory::className(), ['click_id' => 'click_id']);
        }

		$model = BannerClickHistory::find()
            ->alias('t')
            ->where(['t.click_id' => $this->click_id]);
		$histories = $model->count();

		return $histories ? $histories : 0;
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getUser()
	{
		return $this->hasOne(Users::className(), ['user_id' => 'user_id']);
	}

	/**
	 * @return \yii\db\ActiveQuery
	 */
	public function getBanner()
	{
		return $this->hasOne(Banners::className(), ['banner_id' => 'banner_id']);
	}

	/**
	 * {@inheritdoc}
	 * @return \ommu\banner\models\query\BannerClicks the active query used by this AR class.
	 */
	public static function find()
	{
		return new \ommu\banner\models\query\BannerClicks(get_called_class());
	}

	/**
	 * Set default columns to display
	 */
	public function init()
	{
        parent::init();

        if (!(Yii::$app instanceof \app\components\Application)) {
            return;
        }

        if (!$this->hasMethod('search')) {
            return;
        }

		$this->templateColumns['_no'] = [
			'header' => '#',
			'class' => 'app\components\grid\SerialColumn',
			'contentOptions' => ['class' => 'text-center'],
		];
		$this->templateColumns['categoryId'] = [
			'attribute' => 'categoryId',
			'value' => function($model, $key, $index, $column) {
				return isset($model->banner->category) ? $model->banner->category->title->message : '-';
				// return $model->categoryId;
			},
			'filter' => BannerCategory::getCategory(),
			'visible' => !Yii::$app->request->get('banner') ? true : false,
		];
		$this->templateColumns['bannerTitle'] = [
			'attribute' => 'bannerTitle',
			'value' => function($model, $key, $index, $column) {
				return isset($model->banner) ? $model->banner->title : '-';
				// return $model->bannerTitle;
			},
			'visible' => !Yii::$app->request->get('banner') ? true : false,
		];
		$this->templateColumns['userDisplayname'] = [
			'attribute' => 'userDisplayname',
			'value' => function($model, $key, $index, $column) {
				return isset($model->user) ? $model->user->displayname : '-';
				// return $model->userDisplayname;
			},
			'visible' => !Yii::$app->request->get('user') ? true : false,
		];
		$this->templateColumns['click_date'] = [
			'attribute' => 'click_date',
			'value' => function($model, $key, $index, $column) {
				return Yii::$app->formatter->asDatetime($model->click_date, 'medium');
			},
			'filter' => $this->filterDatepicker($this, 'click_date'),
		];
		$this->templateColumns['click_ip'] = [
			'attribute' => 'click_ip',
			'value' => function($model, $key, $index, $column) {
				return $model->click_ip;
			},
		];
		$this->templateColumns['clicks'] = [
			'attribute' => 'clicks',
			'value' => function($model, $key, $index, $column) {
				$clicks = $model->clicks;
				return Html::a($clicks, ['history/click/manage', 'click' => $model->primaryKey], ['title' => Yii::t('app', '{count} histories', ['count' => $clicks]), 'data-pjax' => 0]);
			},
			'filter' => false,
			'contentOptions' => ['class' => 'text-center'],
			'format' => 'raw',
		];
	}

	/**
	 * User get information
	 */
	public static function getInfo($id, $column=null)
	{
        if ($column != null) {
            $model = self::find();
            if (is_array($column)) {
                $model->select($column);
            } else {
                $model->select([$column]);
            }
            $model = $model->where(['click_id' => $id])->one();
            return is_array($column) ? $model : $model->$column;

        } else {
            $model = self::findOne($id);
            return $model;
        }
	}


	/**
	 * {@inheritdoc}
	 */
	public function insertCLick($banner_id, $user_id=null)
	{
        if ($user_id == null) {
            $user_id = !Yii::$app->user->isGuest ? Yii::$app->user->id : null;
        }
		
		$findClick = self::find()
			->select(['click_id', 'banner_id', 'user_id', 'clicks'])
			->where(['banner_id' => $banner_id]);
        if ($user_id != null) {
            $findClick->andWhere(['user_id' => $user_id]);
        } else {
            $findClick->andWhere(['is', 'user_id', null]);
        }
		$findClick = $findClick->one();
			
        if ($findClick !== null) {
            $findClick->updateAttributes(['clicks' => $findClick->clicks+1, 'click_ip' => $_SERVER['REMOTE_ADDR']]);
        } else {
			$click = new BannerClicks();
			$click->banner_id = $banner_id;
			$click->user_id = $user_id;
			$click->save();
		}
	}

	/**
	 * after find attributes
	 */
	public function afterFind()
	{
		parent::afterFind();

		// this->categoryId = isset($this->banner->category) ? $this->banner->category->title->message : '-';
		// $this->bannerTitle = isset($this->banner) ? $this->banner->title : '-';
		// $this->userDisplayname = isset($this->user) ? $this->user->displayname : '-';
	}

	/**
	 * before validate attributes
	 */
	public function beforeValidate()
	{
        if (parent::beforeValidate()) {
            if ($this->isNewRecord) {
                if ($this->user_id == null) {
                    $this->user_id = !Yii::$app->user->isGuest ? Yii::$app->user->id : null;
                }
            }
            $this->click_ip = $_SERVER['REMOTE_ADDR'];
        }
        return true;
	}
}
