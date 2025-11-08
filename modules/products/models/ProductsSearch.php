<?php

namespace app\modules\products\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\products\models\ProductsForm;

/**
 * ProductsSearch represents the model behind the search form about `app\modules\products\models\ProductsForm`.
 */
class ProductsSearch extends ProductsForm
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'category_id'], 'integer'],
            [['name', 'datetime', 'created_at', 'updated_at'], 'safe'],
            [['price'], 'number'],
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
        $query = ProductsForm::find()
                            ->joinWith(['units', 'productPricesUnits'])
                            ->orderBy([
                                'products.name' => SORT_ASC,
                                'units.name' => SORT_ASC,
                                'product_prices_unit.datetime' => SORT_ASC,
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
			$query->andFilterWhere ( [ 'OR' ,['like', 'name', $cusomSearch]] );
 
		} else {
        	$query->andFilterWhere([
            'id' => $this->id,
            'category_id' => $this->category_id,
            'price' => $this->price,
            'datetime' => $this->datetime,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'name', $this->name]);
		}
        return $dataProvider;
    }
}
