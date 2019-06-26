<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\BarangDatang;

/**
 * BarangDatangSearch represents the model behind the search form of `app\models\BarangDatang`.
 */
class BarangDatangSearch extends BarangDatang
{

    public $namaBarang;
    public $namaShift;
    public $namaPerusahaan;
    public $noSo;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'shift_id', 'perusahaan_id', 'barang_id'], 'integer'],
            [['tanggal', 'created_at','updated_at','jam','namaBarang','namaPerusahaan','namaShift','noSo','no_lo','tanggal_lo'], 'safe'],
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
        $query = BarangDatang::find();
        $query->joinWith(['perusahaan as p','barang as b','shift as s','faktur as f']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['namaPerusahaan'] = [
            'asc' => ['p.nama'=>SORT_ASC],
            'desc' => ['p.nama'=>SORT_DESC]
        ];

        $dataProvider->sort->attributes['namaShift'] = [
            'asc' => ['s.nama'=>SORT_ASC],
            'desc' => ['s.nama'=>SORT_DESC]
        ];

        $dataProvider->sort->attributes['namaBarang'] = [
            'asc' => ['b.nama_barang'=>SORT_ASC],
            'desc' => ['b.nama_barang'=>SORT_DESC]
        ];

        $dataProvider->sort->attributes['noSo'] = [
            'asc' => ['f.no_so'=>SORT_ASC],
            'desc' => ['f.no_so'=>SORT_DESC]
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
            'tanggal' => $this->tanggal,
            'jumlah' => $this->jumlah,
            'shift_id' => $this->shift_id,
            'perusahaan_id' => $this->perusahaan_id,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'barang_id' => $this->barang_id,
        ]);

        $query->andFilterWhere(['like', 'b.nama_barang', $this->namaBarang])
            ->andFilterWhere(['like', 's.nama', $this->namaShift])
            ->andFilterWhere(['like', 'f.no_so', $this->noSo])
            ->andFilterWhere(['like', 'p.nama', $this->namaPerusahaan]);

        return $dataProvider;
    }
}
