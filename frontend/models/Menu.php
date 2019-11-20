<?php

namespace frontend\models;

use Yii;
use frontend\models\Restaurants;
use common\models\Order;
use yii\db\ActiveQuery;
use yii2tech\ar\softdelete\SoftDeleteBehavior;

/**
 * This is the model class for table "menu".
 *
 * @property integer $id
 * @property integer $restaurantId
 * @property string $foodName
 * @property string $foodInfo
 * @property double|string $foodPrice
 * @method softDelete
 */
class Menu extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'menu';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['restaurantId', 'foodName', 'foodInfo', 'foodPrice'], 'required'],
            [['restaurantId'], 'integer'],
            [['foodInfo'], 'string'],
            [['foodPrice'], 'number', 'min' => 0.01, 'max' => 999.99],
            [['foodName'], 'string', 'max' => 200],
        ];
    }

    public function behaviors()
    {
        return [
            'softDeleteBehavior' => [
                'class' => SoftDeleteBehavior::class,
                'softDeleteAttributeValues' => [
                    'deletedAt' => function ($model) {
                        return date('Y-m-d');
                    }
                ],
            ],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'restaurantId' => 'Restaurant ID',
            'foodName' => 'Nazwa Żarcia',
            'foodInfo' => 'Info o Żarciu',
            'foodPrice' => 'Cena Żarcia',
        ];
    }

    public function getRestaurants(): ActiveQuery
    {
        return $this->hasMany(Restaurants::className(), ['id' => 'restaurantId']);
    }


    public function getOrder(): ActiveQuery
    {
        return $this->hasMany(Order::className(), ['foodId'=>'id']);
    }
}
