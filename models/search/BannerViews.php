<?php
/**
 * BannerViews
 *
 * BannerViews represents the model behind the search form about `ommu\banner\models\BannerViews`.
 *
 * @author Aziz Masruhan <aziz.masruhan@gmail.com>
 * @contact (+62)857-4115-5177
 * @copyright Copyright (c) 2018 Ommu Platform (www.ommu.co)
 * @created date 6 October 2017, 13:16 WIB
 * @modified date 1 May 2018, 20:44 WIB
 * @modified by Putra Sudaryanto <putra@sudaryanto.id>
 * @contact (+62)856-299-4114
 * @link https://github.com/ommu/mod-banner
 *
 */

namespace ommu\banner\models\search;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use ommu\banner\models\BannerViews as BannerViewsModel;

class BannerViews extends BannerViewsModel
{
	/**
	 * {@inheritdoc}
	 */
	public function rules()
	{
		return [
			[['view_id', 'banner_id', 'user_id', 'views'], 'integer'],
			[['view_date', 'view_ip',
				'category_search', 'banner_search', 'user_search'], 'safe'],
		];
	}

	/**
	 * {@inheritdoc}
	 */
	public function scenarios()
	{
		// bypass scenarios() implementation in the parent class
		return Model::scenarios();
	}

	/**
	 * Tambahkan fungsi beforeValidate ini pada model search untuk menumpuk validasi pd model induk. 
	 * dan "jangan" tambahkan parent::beforeValidate, cukup "return true" saja.
	 * maka validasi yg akan dipakai hanya pd model ini, semua script yg ditaruh di beforeValidate pada model induk
	 * tidak akan dijalankan.
	 */
	public function beforeValidate() {
		return true;
	}

	/**
	 * Creates data provider instance with search query applied
	 *
	 * @param array $params
	 * @return ActiveDataProvider
	 */
	public function search($params)
	{
		$query = BannerViewsModel::find()->alias('t');
		$query->joinWith([
			'banner banner',
			'banner.category.title category',
			'user user'
		]);

		// add conditions that should always apply here
		$dataProvider = new ActiveDataProvider([
			'query' => $query,
		]);

		$attributes = array_keys($this->getTableSchema()->columns);
		$attributes['category_search'] = [
			'asc' => ['category.message' => SORT_ASC],
			'desc' => ['category.message' => SORT_DESC],
		];
		$attributes['banner_search'] = [
			'asc' => ['banner.title' => SORT_ASC],
			'desc' => ['banner.title' => SORT_DESC],
		];
		$attributes['user_search'] = [
			'asc' => ['user.displayname' => SORT_ASC],
			'desc' => ['user.displayname' => SORT_DESC],
		];
		$dataProvider->setSort([
			'attributes' => $attributes,
			'defaultOrder' => ['view_id' => SORT_DESC],
		]);

		$this->load($params);

		if(!$this->validate()) {
			// uncomment the following line if you do not want to return any records when validation fails
			// $query->where('0=1');
			return $dataProvider;
		}

		// grid filtering conditions
		$query->andFilterWhere([
			't.view_id' => $this->view_id,
			't.banner_id' => isset($params['banner']) ? $params['banner'] : $this->banner_id,
			't.user_id' => isset($params['user']) ? $params['user'] : $this->user_id,
			't.views' => $this->views,
			'cast(t.view_date as date)' => $this->view_date,
			'banner.cat_id' => isset($params['category']) ? $params['category'] : $this->category_search,
		]);

		$query->andFilterWhere(['like', 't.view_ip', $this->view_ip])
			->andFilterWhere(['like', 'banner.title', $this->banner_search])
			->andFilterWhere(['like', 'user.displayname', $this->user_search]);

		return $dataProvider;
	}
}
