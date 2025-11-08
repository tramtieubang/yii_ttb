<?php

namespace app\modules\alprofiles\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\alprofiles\models\AlProfilesForm;

/**
 * AlProfilesSearch represents the model behind the search form about `app\modules\alprofiles\models\AlProfilesForm`.
 */
class AlProfilesSearch extends AlProfilesForm
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'system_id', 'length'], 'integer'],
            [['code', 'name', 'door_types', 'image_url', 'note', 'status', 'created_at', 'updated_at'], 'safe'],
            [['weight_per_meter', 'unit_price'], 'number'],
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
        $query = AlProfilesForm::find();

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
            ['like', 'door_types', $cusomSearch],
            ['like', 'image_url', $cusomSearch],
            ['like', 'note', $cusomSearch],
            ['like', 'status', $cusomSearch]] );
 
		} else {
        	$query->andFilterWhere([
            'id' => $this->id,
            'system_id' => $this->system_id,
            'length' => $this->length,
            'weight_per_meter' => $this->weight_per_meter,
            'unit_price' => $this->unit_price,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'door_types', $this->door_types])
            ->andFilterWhere(['like', 'image_url', $this->image_url])
            ->andFilterWhere(['like', 'note', $this->note])
            ->andFilterWhere(['like', 'status', $this->status]);
		}
        return $dataProvider;
    }
}
