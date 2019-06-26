<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\StokAwal;

/**
 * StokAwalSearch represents the model behind the search form of `app\models\StokAwal`.
 */
class StokAwalSearch extends StokAwal
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'barang_id', 'gudang_id', 'perusahaan_id', 'tahun'], 'integer'],
            [['tanggal', 'bulan', 'created'], 'safe'],
            [['stok'], 'number'],
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
        $query = StokAwal::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'barang_id' => $this->barang_id,
            'gudang_id' => $this->gudang_id,
            'perusahaan_id' => $this->perusahaan_id,
            'tanggal' => $this->tanggal,
            'tahun' => $this->tahun,
            'created' => $this->created,
            'stok' => $this->stok,
        ]);

        $query->andFilterWhere(['like', 'bulan', $this->bulan]);

        return $dataProvider;
    }
}
