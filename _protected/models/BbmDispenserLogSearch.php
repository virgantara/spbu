<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\BbmDispenserLog;

/**
 * BbmDispenserLogSearch represents the model behind the search form of `app\models\BbmDispenserLog`.
 */
class BbmDispenserLogSearch extends BbmDispenserLog
{

    public $namaDispenser;
    public $namaShift;
    public $namaBarang;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'dispenser_id', 'shift_id', 'perusahaan_id'], 'integer'],
            [['jumlah'], 'number'],
            [['tanggal', 'created_at','namaShift','namaDispenser','namaBarang'], 'safe'],
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
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = BbmDispenserLog::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $query->joinWith(['dispenser as d','shift as s','barang as b']);

        $dataProvider->sort->attributes['namaShift'] = [
            'asc' => ['s.nama'=>SORT_ASC],
            'desc' => ['s.nama'=>SORT_DESC]
        ];

        $dataProvider->sort->attributes['namaDispenser'] = [
            'asc' => ['d.nama'=>SORT_ASC],
            'desc' => ['d.nama'=>SORT_DESC]
        ];

        $dataProvider->sort->attributes['namaBarang'] = [
            'asc' => ['b.nama_barang'=>SORT_ASC],
            'desc' => ['b.nama_barang'=>SORT_DESC]
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere(['like', 's.nama', $this->namaShift])
        ->andFilterWhere(['like', 'd.nama', $this->namaDispenser])
        ->andFilterWhere(['like', 'b.nama_barang', $this->namaBarang]);

        return $dataProvider;
    }
}
