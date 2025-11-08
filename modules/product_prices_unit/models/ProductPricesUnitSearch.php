<?php

namespace app\modules\product_prices_unit\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\product_prices_unit\models\ProductPricesUnitForm;

/**
 * ProductPricesUnitSearch represents the model behind the search form about `app\modules\product_prices_unit\models\ProductPricesUnitForm`.
 */
class ProductPricesUnitSearch extends ProductPricesUnitForm
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'product_id', 'unit_id'], 'integer'],
            [['price'], 'number'],
            [['datetime', 'created_at', 'updated_at'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params, $cusomSearch=NULL)
    {
        $query = ProductPricesUnitForm::find()
                                    ->joinWith(['product','unit'])
                                    ->orderBy([
                                        'products.name' => SORT_ASC,
                                        'units.name' => SORT_ASC,
                                        'product_prices_unit.datetime' => SORT_DESC,
                                    ]);   

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }
		if($cusomSearch != NULL){
			 
		} else {
        	$query->andFilterWhere([
            'id' => $this->id,
            'product_id' => $this->product_id,
            'unit_id' => $this->unit_id,
            'price' => $this->price,
            'datetime' => $this->datetime,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);
		}
        return $dataProvider;
    }
}
