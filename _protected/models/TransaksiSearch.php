<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Transaksi;

/**
 * TransaksiSearch represents the model behind the search form of `app\models\Transaksi`.
 */
class TransaksiSearch extends Transaksi
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'perkiraan_id', 'perusahaan_id'], 'integer'],
            [['jumlah'], 'number'],
            [['keterangan', 'tanggal', 'created_at', 'updated_at','no_bukti'], 'safe'],
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
        $query = Transaksi::find();

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

        $query->where(['perusahaan_id'=>Yii::$app->user->identity->perusahaan_id]);

        // grid filtering conditions
        $query->andFilterWhere([
            'perkiraan_id' => $this->perkiraan_id,
            'jumlah' => $this->jumlah,
            'tanggal' => $this->tanggal,
            // 'no_bukti' => $this->no_bukti,
        ]);

        $query->andFilterWhere(['like', 'keterangan', $this->keterangan])
            ->andFilterWhere(['like', 'no_bukti', $this->no_bukti]);

        return $dataProvider;
    }
}
