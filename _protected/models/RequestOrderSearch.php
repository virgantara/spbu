<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\RequestOrder;

/**
 * RequestOrderSearch represents the model behind the search form of `app\models\RequestOrder`.
 */
class RequestOrderSearch extends RequestOrder
{

    public $namaDeptTujuan;
    public $namaDeptAsal;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'petugas1', 'petugas2', 'perusahaan_id'], 'integer'],
            [['no_ro', 'tanggal_pengajuan', 'tanggal_penyetujuan', 'created','is_approved','namaDeptTujuan','namaDeptAsal'], 'safe'],
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
        $query = RequestOrder::find();

        if(!Yii::$app->user->can('kepalaCabang')){
            
            $query->where(['departemen_id'=>Yii::$app->user->identity->departemen]);
        }

        // print_r(Yii::$app->user->identity->departemen);

        $query->joinWith(['departemenTo as deptTo','departemen as deptAsal']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=> ['defaultOrder' => ['tanggal_pengajuan'=>SORT_DESC]]
        ]);

        // $query->orderBy(['id'=>SORT_DESC])

         $dataProvider->sort->attributes['namaDeptTujuan'] = [
            'asc' => ['deptTo.nama'=>SORT_ASC],
            'desc' => ['deptTo.nama'=>SORT_DESC]
        ];

        $dataProvider->sort->attributes['namaDeptAsal'] = [
            'asc' => ['deptAsal.nama'=>SORT_ASC],
            'desc' => ['deptAsal.nama'=>SORT_DESC]
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
            'petugas1' => $this->petugas1,
            'petugas2' => $this->petugas2,
            'tanggal_pengajuan' => $this->tanggal_pengajuan,
            'tanggal_penyetujuan' => $this->tanggal_penyetujuan,
            'perusahaan_id' => $this->perusahaan_id,
            'created' => $this->created,
            'is_approved' => $this->is_approved
        ]);



        $query->andFilterWhere(['like', 'deptTo.nama', $this->namaDeptTujuan])
            ->andFilterWhere(['like', 'deptAsal.nama', $this->namaDeptAsal])
        ;

        return $dataProvider;
    }

    public function searchByTanggal($params)
    {
        $query = RequestOrder::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'pagination' => ['pageSize' => 0]
        ]);

        $this->load($params);

        $this->tanggal_awal = date('Y-m-d',strtotime($params['RequestOrder']['tanggal_awal']));
        $this->tanggal_akhir = date('Y-m-d',strtotime($params['RequestOrder']['tanggal_akhir']));
            

        $query->where(['departemen_id_to'=>Yii::$app->user->identity->departemen]);
        $query->andWhere(['perusahaan_id'=>Yii::$app->user->identity->perusahaan_id]);
        $query->andFilterWhere(['between', 'tanggal_pengajuan', $this->tanggal_awal, $this->tanggal_akhir]);

        $query->orderBy(['tanggal_pengajuan'=>SORT_ASC]);
        

        return $dataProvider;
    }
}
