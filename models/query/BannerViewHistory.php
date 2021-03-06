<?php
/**
 * BannerViewHistory
 *
 * This is the ActiveQuery class for [[\ommu\banner\models\BannerViewHistory]].
 * @see \ommu\banner\models\BannerViewHistory
 * 
 * @author Putra Sudaryanto <putra@ommu.id>
 * @contact (+62)856-299-4114
 * @copyright Copyright (c) 2018 OMMU (www.ommu.id)
 * @created date 30 April 2018, 12:42 WIB
 * @link https://github.com/ommu/mod-banner
 *
 */

namespace ommu\banner\models\query;

class BannerViewHistory extends \yii\db\ActiveQuery
{
	/*
	public function active()
	{
		return $this->andWhere('[[status]]=1');
	}
	*/

	/**
	 * {@inheritdoc}
	 * @return \ommu\banner\models\BannerViewHistory[]|array
	 */
	public function all($db = null)
	{
		return parent::all($db);
	}

	/**
	 * {@inheritdoc}
	 * @return \ommu\banner\models\BannerViewHistory|array|null
	 */
	public function one($db = null)
	{
		return parent::one($db);
	}
}
