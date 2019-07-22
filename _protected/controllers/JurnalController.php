<?php

namespace app\controllers;

use Yii;
use app\models\Jurnal;
use app\models\JurnalSearch;
use app\models\Perkiraan;
use app\models\Transaksi;
use app\models\KartuStok;
use app\models\BbmDropping;
use app\models\DepartemenStok;


use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * JurnalController implements the CRUD actions for Jurnal model.
 */
class JurnalController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    public function actionNeraca()
    {
        $searchModel = new JurnalSearch();

        $model = new Jurnal;
        $tanggal_awal = !empty($_GET['Jurnal']['tanggal_awal']) ? date('Y-m-d',strtotime($_GET['Jurnal']['tanggal_awal'])) : date('Y-m-01');
        $tanggal_akhir = !empty($_GET['Jurnal']['tanggal_akhir']) ? date('Y-m-d',strtotime($_GET['Jurnal']['tanggal_akhir'])) : date('Y-m-d');
        $model->tanggal_awal = $tanggal_awal;
        $model->tanggal_akhir = $tanggal_akhir;
         $results = [];
        $aktiva_lancar = Perkiraan::find();
        $aktiva_lancar->where([
            'perusahaan_id'=>Yii::$app->user->identity->perusahaan_id
        ]);
        $params = Yii::$app->request->queryParams;
        $aktiva_lancar->andFilterWhere(['like','kode','1-1%',false]);
        $aktiva_lancar->orderBy(['kode'=>SORT_ASC]);       
        $aktiva_lancar = $aktiva_lancar->all();
        
        foreach($aktiva_lancar as $q1 => $m1)
        {
            $query = Transaksi::find()->where(['perkiraan_id'=>$m1->id]);
            $jumlah = $query->sum('jumlah');

            $params['Jurnal']['perkiraan_id'] = $m1->id;
            $results['aktiva_lancar'][$m1->id] = [
                'kode' => $m1->kode,
                'nama' => $m1->nama,
                'jumlah' => $jumlah,
            ];
        }
        $aktiva_tetap = Perkiraan::find();
        $aktiva_tetap->where([
            'perusahaan_id'=>Yii::$app->user->identity->perusahaan_id
        ]);
        $aktiva_tetap->andFilterWhere(['like','kode','1-2%',false]);
        $aktiva_tetap->orderBy(['kode'=>SORT_ASC]);       
        $aktiva_tetap = $aktiva_tetap->all();
        
        foreach($aktiva_tetap as $q1 => $m1)
        {
            $query = Transaksi::find()->where(['perkiraan_id'=>$m1->id]);
            $jumlah = $query->sum('jumlah');

            $params['Jurnal']['perkiraan_id'] = $m1->id;
            $results['aktiva_tetap'][$m1->id] = [
                'kode' => $m1->kode,
                'nama' => $m1->nama,
                'jumlah' => $jumlah,
            ];
        }

        $persediaan_akhir = KartuStok::find();
        $persediaan_akhir->joinWith(['barang as b']);
        $persediaan_akhir->where([
            'b.id_perusahaan'=>Yii::$app->user->identity->perusahaan_id
        ]);
        $persediaan_akhir->andFilterWhere(['like','kode_transaksi','JUAL_%',false]);
        $persediaan_akhir->andWhere(['between','tanggal',$tanggal_awal,$tanggal_akhir]);
        $persediaan_akhir = $persediaan_akhir->all();


        $hutang = Perkiraan::find();
        $hutang->where([
            'perusahaan_id'=>Yii::$app->user->identity->perusahaan_id
        ]);

        $hutang->andFilterWhere(['like','kode','2-%',false]);
        $hutang->orderBy(['kode'=>SORT_ASC]);
        $hutang = $hutang->all();
        
        foreach($hutang as $q1 => $m1)
        {
            $query = Transaksi::find()->where(['perkiraan_id'=>$m1->id]);
            $jumlah = $query->sum('jumlah');

            $params['Jurnal']['perkiraan_id'] = $m1->id;
            $results['hutang'][$m1->id] = [
                'kode' => $m1->kode,
                'nama' => $m1->nama,
                'jumlah' => $jumlah,
            ];
        }

        $modal = Perkiraan::find();
        $modal->where([
            'perusahaan_id'=>Yii::$app->user->identity->perusahaan_id
        ]);

        $modal->andFilterWhere(['like','kode','3-%',false]);
        $modal->orderBy(['kode'=>SORT_ASC]);
        $modal = $modal->all();
        
        foreach($modal as $q1 => $m1)
        {
            $query = Transaksi::find()->where(['perkiraan_id'=>$m1->id]);
            $jumlah = $query->sum('jumlah');

            $params['Jurnal']['perkiraan_id'] = $m1->id;
            $results['modal'][$m1->id] = [
                'kode' => $m1->kode,
                'nama' => $m1->nama,
                'jumlah' => $jumlah,
            ];
        }

        return $this->render('neraca', [
            'searchModel' => $searchModel,
            'model' => $model,
            'dataProvider' => $dataProvider,
            'aktiva_lancar' => $aktiva_lancar,
            'aktiva_tetap' => $aktiva_tetap,
            'hutang' => $hutang,
            'modal' => $modal,
            'persediaan_akhir' => $persediaan_akhir,
            'results'=>$results
        ]);
    }

    public function actionLabaRugi()
    {
        $searchModel = new JurnalSearch();

        $model = new Jurnal;
        $tanggal_awal = !empty($_GET['Jurnal']['tanggal_awal']) ? date('Y-m-d',strtotime($_GET['Jurnal']['tanggal_awal'])) : date('Y-m-01');
        $tanggal_akhir = !empty($_GET['Jurnal']['tanggal_akhir']) ? date('Y-m-d',strtotime($_GET['Jurnal']['tanggal_akhir'])) : date('Y-m-d');
        $model->tanggal_awal = $tanggal_awal;
        $model->tanggal_akhir = $tanggal_akhir;
        // print_r($model->attributes);exit;
        $pendapatan = Perkiraan::find();
        $pendapatan->where([
            'perusahaan_id'=>Yii::$app->user->identity->perusahaan_id
        ]);

        $pendapatan->andFilterWhere(['like','kode','4-%',false]);
        $pendapatan->orderBy(['kode'=>SORT_ASC]);

        $beban = Perkiraan::find();
        $beban->where([
            'perusahaan_id'=>Yii::$app->user->identity->perusahaan_id
        ]);

        $beban->andFilterWhere(['like','kode','5-%',false]);
        $beban->orderBy(['kode'=>SORT_ASC]);

        $bebanLain = Perkiraan::find();
        $bebanLain->where([
            'perusahaan_id'=>Yii::$app->user->identity->perusahaan_id
        ]);

        $bebanLain->andFilterWhere(['like','kode','8-%',false]);
        $bebanLain->orderBy(['kode'=>SORT_ASC]);

        $results = [];
        $params = Yii::$app->request->queryParams;
        $pendapatan = $pendapatan->all();
        
        foreach($pendapatan as $q1 => $m1)
        {
            $query = Transaksi::find()->where(['perkiraan_id'=>$m1->id]);
            $jumlah = $query->sum('jumlah');

            $params['Jurnal']['perkiraan_id'] = $m1->id;
            $results['pendapatan'][$m1->id] = [
                'kode' => $m1->kode,
                'nama' => $m1->nama,
                'jumlah' => $jumlah,
            ];
        }


        $persediaan_awal = KartuStok::find();
        $persediaan_awal->joinWith(['barang as b']);
        $persediaan_awal->where([
            'b.id_perusahaan'=>Yii::$app->user->identity->perusahaan_id
        ]);

        $persediaan_awal->andWhere(['tanggal'=>$model->tanggal_awal]);
        // $persediaan_awal->orderBy(['kode'=>SORT_ASC]);

        $persediaan_awal = $persediaan_awal->all();
        
        $pembelian = BbmDropping::find();
        $pembelian->joinWith(['barang as b']);
        $pembelian->where([
            'b.id_perusahaan'=>Yii::$app->user->identity->perusahaan_id
        ]);

        $pembelian->andWhere(['between','tanggal',$tanggal_awal,$tanggal_akhir]);
        $pembelian = $pembelian->all();
        // print_r($_GET);exit;
        $persediaan_akhir = KartuStok::find();
        $persediaan_akhir->joinWith(['barang as b']);
        $persediaan_akhir->where([
            'b.id_perusahaan'=>Yii::$app->user->identity->perusahaan_id
        ]);
        $persediaan_akhir->andFilterWhere(['like','kode_transaksi','JUAL_%',false]);
        $persediaan_akhir->andWhere(['between','tanggal',$tanggal_awal,$tanggal_akhir]);
        $persediaan_akhir = $persediaan_akhir->all();

        // print_r($results['pendapatan'][0]);exit;

        $beban = $beban->all();
        foreach($beban as $q1 => $m1)
        {
            // foreach($m1->perkiraans as $q2 => $m2)
            // {
            $query = Transaksi::find()->where(['perkiraan_id'=>$m1->id]);
            $jumlah = $query->sum('jumlah');

            $params['Jurnal']['perkiraan_id'] = $m1->id;
            $results['beban'][$m1->id] = [
                'kode' => $m1->kode,
                'nama' => $m1->nama,
                'jumlah' => $jumlah,
            ];
            // }
        }

        $bebanLain = $bebanLain->all();
        foreach($bebanLain as $q1 => $m1)
        {
            // foreach($m1->perkiraans as $q2 => $m2)
            // {
            $query = Transaksi::find()->where(['perkiraan_id'=>$m1->id]);
            $jumlah = $query->sum('jumlah');

            $params['Jurnal']['perkiraan_id'] = $m1->id;
            $results['bebanLain'][$m1->id] = [
                'kode' => $m1->kode,
                'nama' => $m1->nama,
                'jumlah' => $jumlah,
            ];
            // }
        }
        
        // print_r($results);exit;
        return $this->render('lb', [
            'searchModel' => $searchModel,
            'model' => $model,
            'dataProvider' => $dataProvider,
            'pendapatan' => $pendapatan,
            'persediaan_awal' => $persediaan_awal,
            'persediaan_akhir' => $persediaan_akhir,
            'pembelian' => $pembelian,
            'beban' => $beban,
            'bebanLain' => $bebanLain,
            'results'=>$results
        ]);
    }

    public function actionBukuBesar()
    {

        $akun = Perkiraan::findOne($_GET['Jurnal']['perkiraan_id']);
        $listakun = $akun;
        $searchModel = new JurnalSearch();

        
        $results = [];
        $params = Yii::$app->request->queryParams;
        if(!empty($listakun))
        foreach($listakun->perkiraans as $q1 => $m1)
        {

            $params['Jurnal']['perkiraan_id'] = $m1->id;
            $sumDebet = $searchModel->searchByTanggalAkun($params,'debet');
            $sumKredit = $searchModel->searchByTanggalAkun($params,'kredit');
            
            $results[$m1->id] = [
                'debet' => $sumDebet,
                'kredit' => $sumKredit
            ];
        }
        $model = new Jurnal;
        return $this->render('bb', [
            'searchModel' => $searchModel,
            'model' => $model,
            'dataProvider' => $dataProvider,
            'listakun' => $listakun,
            'results' => $results
        ]);
    }

    /**
     * Lists all Jurnal models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new JurnalSearch();
        $dataProvider = $searchModel->searchByTanggal(Yii::$app->request->queryParams);
        $model = new Jurnal;
        return $this->render('index', [
            'searchModel' => $searchModel,
            'model' => $model,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Jurnal model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new Jurnal model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new Jurnal();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing Jurnal model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing Jurnal model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Jurnal model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Jurnal the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Jurnal::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
