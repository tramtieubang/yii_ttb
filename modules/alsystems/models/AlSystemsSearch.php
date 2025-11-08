<?php

namespace app\modules\alsystems\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\alsystems\models\AlSystemsForm;

/**
 * AlSystemsSearch represents the model behind the search form about `app\modules\alsystems\models\AlSystemsForm`.
 */
class AlSystemsSearch extends AlSystemsForm
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id'], 'integer'],
            [['code', 'name', 'brand', 'origin', 'color', 'surface_type', 'description', 'status', 'created_at', 'updated_at'], 'safe'],
            [['thickness'], 'number'],
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
        $query = AlSystemsForm::find();

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
            ['like', 'brand', $cusomSearch],
            ['like', 'origin', $cusomSearch],
            ['like', 'color', $cusomSearch],
            ['like', 'surface_type', $cusomSearch],
            ['like', 'description', $cusomSearch],
            ['like', 'status', $cusomSearch]] );
 
		} else {
        	$query->andFilterWhere([
            'id' => $this->id,
            'thickness' => $this->thickness,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'code', $this->code])
            ->andFilterWhere(['like', 'name', $this->name])
            ->andFilterWhere(['like', 'brand', $this->brand])
            ->andFilterWhere(['like', 'origin', $this->origin])
            ->andFilterWhere(['like', 'color', $this->color])
            ->andFilterWhere(['like', 'surface_type', $this->surface_type])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'status', $this->status]);
		}
        return $dataProvider;
    }
}
