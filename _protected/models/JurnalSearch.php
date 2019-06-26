<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Jurnal;

/**
 * JurnalSearch represents the model behind the search form of `app\models\Jurnal`.
 */
class JurnalSearch extends Jurnal
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'perkiraan_id', 'perusahaan_id'], 'integer'],
            [['no_bukti', 'created_at', 'updated_at'], 'safe'],
            [['debet', 'kredit'], 'number'],
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
        $query = Jurnal::find();

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
            'perkiraan_id' => $this->perkiraan_id,
            'debet' => $this->debet,
            'kredit' => $this->kredit,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'perusahaan_id' => $this->perusahaan_id,
        ]);

        $query->andFilterWhere(['like', 'no_bukti', $this->no_bukti]);

        return $dataProvider;
    }

    public function searchByTanggal($params)
    {
        $query = Jurnal::find();

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

        $this->tanggal_awal = date('Y-m-d',strtotime($params['Jurnal']['tanggal_awal']));
        $this->tanggal_akhir = date('Y-m-d',strtotime($params['Jurnal']['tanggal_akhir']));
        $query->where(['perusahaan_id'=>Yii::$app->user->identity->perusahaan_id]);
        $query->andFilterWhere(['between', 'tanggal', $this->tanggal_awal, $this->tanggal_akhir]);

        $query->orderBy(['tanggal'=>SORT_ASC]);
        return $dataProvider;
    }

    public function searchByTanggalAkun($params,$p = 'debet')
    {
        $query = Jurnal::find();
        
        $this->perkiraan_id = $params['Jurnal']['perkiraan_id'];
        $this->tanggal_awal = date('Y-m-d',strtotime($params['Jurnal']['tanggal_awal']));
        $this->tanggal_akhir = date('Y-m-d',strtotime($params['Jurnal']['tanggal_akhir']));
        $query->where([
            'perkiraan_id' => $this->perkiraan_id
        ]);
        $query->andFilterWhere(['between', 'tanggal', $this->tanggal_awal, $this->tanggal_akhir]);

        $query->orderBy(['tanggal'=>SORT_ASC]);
        if($p =='debet')
            return $query->sum('debet');
        else
            return $query->sum('kredit');
    }


}
