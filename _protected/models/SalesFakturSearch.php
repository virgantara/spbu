<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\SalesFaktur;

/**
 * SalesFakturSearch represents the model behind the search form of `app\models\SalesFaktur`.
 */
class SalesFakturSearch extends SalesFaktur
{

    public $namaSuplier;

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id_faktur', 'id_suplier', 'id_perusahaan'], 'integer'],
            [['no_faktur', 'created', 'tanggal_faktur','namaSuplier','created_at'], 'safe'],
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
    public function search($params)
    {
        $query = SalesFaktur::find();
        $query->where([self::tableName().'.id_perusahaan'=>Yii::$app->user->identity->perusahaan_id]);
        $query->joinWith(['suplier as s']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['created'=>SORT_DESC]]
        ]);

        $dataProvider->sort->attributes['namaSuplier'] = [
            'asc' => ['s.nama'=>SORT_ASC],
            'desc' => ['s.nama'=>SORT_DESC]
        ];

        // $query->orderBy(['created_at'=>SORT_DESC]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id_faktur' => $this->id_faktur,
            'id_suplier' => $this->id_suplier,
            'created' => $this->created,
            'tanggal_faktur' => $this->tanggal_faktur,
            'id_perusahaan' => $this->id_perusahaan,
        ]);

        $query->andFilterWhere(['like', 's.nama', $this->namaSuplier]);
        $query->andFilterWhere(['like', 'no_faktur', $this->no_faktur]);

        return $dataProvider;
    }

    public function searchByTanggal($params)
    {
        $query = SalesFaktur::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 0]
        ]);

        $this->load($params);

        $this->tanggal_awal = date('Y-m-d',strtotime($params['SalesFaktur']['tanggal_awal']));
        $this->tanggal_akhir = date('Y-m-d',strtotime($params['SalesFaktur']['tanggal_akhir']));
            


        $query->where(['id_perusahaan'=>Yii::$app->user->identity->perusahaan_id]);
        $query->andFilterWhere(['between', 'tanggal_dropping', $this->tanggal_awal, $this->tanggal_akhir]);

        $query->orderBy(['tanggal_faktur'=>SORT_ASC]);
        

        return $dataProvider;
    }
}
