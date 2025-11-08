<?php

namespace app\modules\invoice\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\invoice\models\InvoiceForm;

/**
 * InvoiceSearch represents the model behind the search form about `app\modules\invoice\models\InvoiceForm`.
 */
class InvoiceSearch extends InvoiceForm
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'customer_id', 'created_by'], 'integer'],
            [['invoice_number', 'issue_date', 'due_date', 'status', 'payment_method', 'notes', 'created_at', 'updated_at'], 'safe'],
            [['subtotal', 'discount_total', 'tax_total', 'total_amount'], 'number'],
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
        $query = InvoiceForm::find();

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
			$query->andFilterWhere ( [ 'OR' ,['like', 'invoice_number', $cusomSearch],
            ['like', 'status', $cusomSearch],
            ['like', 'payment_method', $cusomSearch],
            ['like', 'notes', $cusomSearch]] );
 
		} else {
        	$query->andFilterWhere([
            'id' => $this->id,
            'customer_id' => $this->customer_id,
            'issue_date' => $this->issue_date,
            'due_date' => $this->due_date,
            'subtotal' => $this->subtotal,
            'discount_total' => $this->discount_total,
            'tax_total' => $this->tax_total,
            'total_amount' => $this->total_amount,
            'created_by' => $this->created_by,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'invoice_number', $this->invoice_number])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'payment_method', $this->payment_method])
            ->andFilterWhere(['like', 'notes', $this->notes]);
		}
        return $dataProvider;
    }
}
