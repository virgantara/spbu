<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\BbmDropping;

/**
 * BbmDroppingSearch represents the model behind the search form of `app\models\BbmDropping`.
 */
class BbmDroppingSearch extends BbmDropping
{

    public $namaBarang;
    public $namaShift;
    public $namaTangki;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'bbm_faktur_id', 'barang_id'], 'integer'],
            [['no_lo', 'tanggal', 'jam', 'created_at', 'updated_at','namaBarang','namaShift','namaTangki'], 'safe'],
            [['jumlah'], 'number'],
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
        $query = BbmDropping::find();
        $query->joinWith(['barang as b','shift as s','tangki as t']);
        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['namaBarang'] = [
            'asc' => ['b.nama_barang'=>SORT_ASC],
            'desc' => ['b.nama_barang'=>SORT_DESC]
        ];

        $dataProvider->sort->attributes['namaShift'] = [
            'asc' => ['t.nama'=>SORT_ASC],
            'desc' => ['t.nama'=>SORT_DESC]
        ];

        $dataProvider->sort->attributes['namaTangki'] = [
            'asc' => ['t.nama'=>SORT_ASC],
            'desc' => ['t.nama'=>SORT_DESC]
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'bbm_faktur_id' => $this->bbm_faktur_id,
            'tanggal' => $this->tanggal,
            'jam' => $this->jam,
            'barang_id' => $this->barang_id,
            'jumlah' => $this->jumlah,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ]);

        $query->andFilterWhere(['like', 'no_lo', $this->no_lo]);

        return $dataProvider;
    }
}
