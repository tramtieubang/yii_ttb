<?php

namespace app\modules\alaluminummaterials\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\alaluminummaterials\models\AlAluminumMaterialsForm;

/**
 * AlAluminumMaterialsSearch represents the model behind the search form about `app\modules\alaluminummaterials\models\AlAluminumMaterialsForm`.
 */
class AlAluminumMaterialsSearch extends AlAluminumMaterialsForm
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'profile_id', 'length', 'stock_quantity', 'stock_length'], 'integer'],
            [['code', 'name', 'note', 'created_at', 'updated_at'], 'safe'],
            [['unit_price'], 'number'],
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
        $query = AlAluminumMaterialsForm::find();

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
			$query->andFilterWhere ( [ 'OR' ,['like', 'code', $cusomSearch],
            ['like', 'name', $cusomSearch],
            ['like', 'note', $cusomSearch]] );
 
		} else {
        	$query->andFilterWhere([
            'id' => $this->id,
            'profile_id' => $this->profile_id,
            'length' => $this->length,
            'stock_quantity' => $this->stock_quantity,
            'stock_length' => $this->stock_length,
            'unit_price' => $this->unit_price,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'note', $this->note]);
		}
        return $dataProvider;
    }
}
