<?php

namespace app\controllers;

use Yii;
use app\models\Cart;
use app\models\Penjualan;
use app\models\PenjualanItem;
use app\models\PenjualanSearch;
use app\models\Pasien;
use app\models\Pendaftaran;

use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use app\helpers\MyHelper;
use yii\data\ActiveDataProvider;
use kartik\mpdf\Pdf;
use yii\httpclient\Client;


/**
 * PenjualanController implements the CRUD actions for Penjualan model.
 */
class PenjualanController extends Controller
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

    public function actionAjaxCekResep()
    {
        $result = [];
        if(Yii::$app->request->isPost)
        {

            if(empty($_POST['customer_id']) || empty($_POST['tgl']))
            {
                echo json_encode($result);
                exit;
            }

            $no_rm = $_POST['customer_id'];
            $p = \app\models\Penjualan::find();
            $p->where(['tanggal'=>$_POST['tgl'],'customer_id'=>$no_rm]);
            $hasil = $p->one();

            $result['is_exist'] = !empty($hasil);
            echo json_encode($result);
        }
    }

    public function actionKomposisi($id)
    {
        $results = $this->loadItems($id);
        Yii::$app->layout = 'partial';
        return $this->render('komposisi',[
            'results' => $results
        ]);
    }

    public function actionShowAllHistory($cid)
    {
        $tanggal_awal = date('Y-m-d',strtotime('last 3 months'));
        $tanggal_akhir = date('Y-m-d');
        $list = \app\helpers\MyHelper::loadRiwayatObat($cid,$tanggal_awal, $tanggal_akhir, 1, 1000);
        Yii::$app->layout = 'partial';
        return $this->render('riwayat_obat',[
            'results' => $list
        ]);
    }

    public function actionAjaxLoadItemHistory(){
        if (Yii::$app->request->isPost) {
            $dataItem = $_POST['dataItem'];
            $tanggal_awal = date('Y-m-d',strtotime('last 3 months'));
            $tanggal_akhir = date('Y-m-d');
            $list = \app\helpers\MyHelper::loadRiwayatObat($dataItem['customer_id'],$tanggal_awal, $tanggal_akhir);
            
            echo json_encode($list);
        }
    }

  

    public function actionPrintBatchEtiket($id)
    {
        $model = $this->findModel($id);
        
        $reg = Pendaftaran::findOne($model->kode_daftar);

        $api_baseurl = Yii::$app->params['api_baseurl'];
        $client = new Client(['baseUrl' => $api_baseurl]);

        $response = $client->get('/pasien/rm', ['key' => $model->customer_id])->send();
        
        $out = [];


        
        if ($response->isOk) {
            $result = $response->data['values'];

            if(!empty($result))
            {
                foreach ($result as $d) {
                    $out[] = [
                        'id' => $d['NoMedrec'],
                        'label'=> $d['NAMA'],
                        'alamat' => $d['ALAMAT'],  
                        'ttl' => $d['TGLLAHIR']
                    ];
                }
            }
        }

        $pasien = $out;

        $pdf = new Pdf(['mode' => Pdf::MODE_UTF8, 'format' => [68, 48],
           'marginLeft'=>8,
            'marginRight'=>1,
            'marginTop'=>0,
            'marginBottom'=>0,
        ]);
        $mpdf = $pdf->api; // fetches mpdf api
        $mpdf->SetHeader(false); // call methods or set any properties

        foreach($model->penjualanItems as $item)
        {

        
            if(!$item->is_racikan)
            {
                $mpdf->AddPage();
                $content = $this->renderPartial('printEtiket', [
                    'model' => $item,
                    'reg' => $reg,
                    'pasien' => $pasien,
                    'is_racikan' => 0
                ]);

                $mpdf->WriteHtml($content); // call mpdf write html
            }
        }

        $items = PenjualanItem::find()->select('kode_racikan')->distinct()->where([
            'penjualan_id' => $model->id,
            'is_racikan' => 1
        ])->all();

        foreach($items as $item)
        {
            
            if($item->kode_racikan == '-') 
                continue;

            $item = PenjualanItem::find()->where(['kode_racikan'=>$item])->one();
            $mpdf->AddPage();
            $content = $this->renderPartial('printEtiket', [
                'model' => $item,
                'reg' => $reg,
                'pasien' => $pasien,
                'is_racikan' => 1
            ]);

            $mpdf->WriteHtml($content); // call mpdf write html
            
        }

        Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
        Yii::$app->response->headers->add('Content-Type', 'application/pdf');

        echo $mpdf->Output('filename', 'I'); // call the mpdf api output as needed
    }

    public function actionPrintEtiket($id)
    {
        $model = PenjualanItem::findOne($id);
        
      
        $reg = Pendaftaran::findOne($model->penjualan->kode_daftar);

        $api_baseurl = Yii::$app->params['api_baseurl'];
        $client = new Client(['baseUrl' => $api_baseurl]);

        $response = $client->get('/pasien/rm', ['key' => $model->penjualan->customer_id])->send();
        
        $out = [];


        
        if ($response->isOk) {
            $result = $response->data['values'];

            if(!empty($result))
            {
                foreach ($result as $d) {
                    $out[] = [
                        'id' => $d['NoMedrec'],
                        'label'=> $d['NAMA'],
                        'alamat' => $d['ALAMAT'],  
                        'ttl' => $d['TGLLAHIR']
                    ];
                }
            }
        }

        $pasien = $out;

        
        $content = $this->renderPartial('printEtiket', [
            'model' => $model,
            'reg' => $reg,
            'pasien' => $pasien,
            'is_racikan' => $model->is_racikan
        ]);

        $pdf = new Pdf(['mode' => 'utf-8', 'format' => [68, 43],
            'marginLeft'=>2,
            'marginRight'=>1,
            'marginTop'=>0,
            'marginBottom'=>0,
        ]);
        $mpdf = $pdf->api; // fetches mpdf api
        // $mpdf->defaultFont("Courier");
        $mpdf->SetHeader(false); // call methods or set any properties
        $mpdf->WriteHtml($content); // call mpdf write html
        echo $mpdf->Output('filename', 'I'); // call the mpdf api output as needed
    }

    public function actionBayar($id,$kode)
    {
        $connection = \Yii::$app->db;
        $transaction = $connection->beginTransaction();
        try 
        {
            $model = $this->findModel($id);
            $model->status_penjualan = $kode;

            $params = [];
            switch ($kode) {
                case 0:
                    $params = [
                        'kode_trx' => $model->kode_penjualan,
                        'terbayar' => Penjualan::getTotalSubtotal($model),
                        'status_bayar' => 0
                    ];
                    break;
                case 1:
                    $params = [
                        'kode_trx' => $model->kode_penjualan,
                        'terbayar' => Penjualan::getTotalSubtotal($model),
                        'status_bayar' => 1
                    ];
                    break;
            }

            $billingModule = \Yii::$app->getModule('billing');
            $result = $billingModule->updateTagihanObat($params);

            if($model->validate())
                $model->save();
            else{
                print_r($model->getErrors());exit;
            }

            $transaction->commit();
            Yii::$app->session->setFlash('success', "Data tersimpan");
            return $this->redirect(['view','id'=>$id]);
        } catch (\Exception $e) {
            $transaction->rollBack();
            throw $e;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    public function actionPrintBayar($id)
    {
        $model = $this->findModel($id);
        $searchModel = $model->getPenjualanItems();

        
        $dataProvider = new ActiveDataProvider([
            'query' => $searchModel,
        ]);

        $api_baseurl = Yii::$app->params['api_baseurl'];
        $client = new Client(['baseUrl' => $api_baseurl]);

        $response = $client->get('/pasien/rm', ['key' => $model->customer_id])->send();
        
        $out = [];


        
        if ($response->isOk) {
            $result = $response->data['values'];

            if(!empty($result))
            {
                foreach ($result as $d) {
                    $out[] = [
                        'id' => $d['NoMedrec'],
                        'label'=> $d['NAMA'],
                        'alamat' => $d['ALAMAT'],  
                    ];
                }
            }
        }

        $pasien = $out;

        $content = $this->renderPartial('printBayar', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'pasien' => $pasien
        ]);

        $pdf = new Pdf(['mode' => 'utf-8', 'format' => [215, 95],
           'marginLeft'=>8,
            'marginRight'=>1,
            'marginTop'=>0,
            'marginBottom'=>0,
        ]);
        $mpdf = $pdf->api; // fetches mpdf api
        $mpdf->SetHeader(false); // call methods or set any properties
        $mpdf->WriteHtml($content); // call mpdf write html
        echo $mpdf->Output('filename', 'I'); // call the mpdf api output as needed
    }

    public function actionIndexKasir()
    {
        $searchModel = new PenjualanSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index_kasir', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionPrintResep($id)
    {
        $model = $this->findModel($id);
        $searchModel = $model->getPenjualanItems();

        $reg = Pendaftaran::findOne($model->kode_daftar);

        $dataProvider = new ActiveDataProvider([
            'query' => $searchModel,
        ]);


        $content = $this->renderPartial('printResep', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'reg' => $reg
        ]);

        $size = count($dataProvider->getModels());

        $height = $size + 140;
        $pdf = new Pdf(['mode' => 'utf-8', 'format' => [75, $height],
            'marginLeft'=>8,
            'marginRight'=>1,
            'marginTop'=>0,
            'marginBottom'=>0,
        ]);
        $mpdf = $pdf->api; // fetches mpdf api
        $mpdf->SetHeader(false); // call methods or set any properties
        $mpdf->WriteHtml($content); // call mpdf write html
        echo $mpdf->Output('filename', 'I'); // call the mpdf api output as needed
    }

    public function actionPrintPengantar($id)
    {
        $model = $this->findModel($id);
        $searchModel = $model->getPenjualanItems();

        $reg = Pendaftaran::findOne($model->kode_daftar);

        $dataProvider = new ActiveDataProvider([
            'query' => $searchModel,
        ]);


        $content = $this->renderPartial('printPengantar', [
            'model' => $model,
            'dataProvider' => $dataProvider,
            'reg' => $reg
        ]);

        $pdf = new Pdf(['mode' => 'utf-8', 'format' => [75, 130],
           'marginLeft'=>8,
            'marginRight'=>1,
            'marginTop'=>0,
            'marginBottom'=>0,
        ]);
        $mpdf = $pdf->api; // fetches mpdf api
        $mpdf->SetHeader(false); // call methods or set any properties
        $mpdf->WriteHtml($content); // call mpdf write html
        echo $mpdf->Output('filename', 'I'); // call the mpdf api output as needed
    }

    private function loadItems($id)
    {
        $rows = PenjualanItem::find()->where(['penjualan_id'=>$id])->all();
        $items = [];
        
        $total = 0;
        foreach($rows as $row)
        {
            if($row->qty < 1)   
                $subtotal_bulat = round($row->harga) * $row->qty;
            else
                $subtotal_bulat = round($row->harga) * ceil($row->qty);
            $total += $subtotal_bulat;
            $results = [
                'id' => $row->id,
                'kode_barang' => $row->stok->barang->kode_barang,
                'nama_barang' => $row->stok->barang->nama_barang,
                'harga_jual' => \app\helpers\MyHelper::formatRupiah($row->stok->barang->harga_jual),
                'harga_beli' => \app\helpers\MyHelper::formatRupiah($row->stok->barang->harga_beli),
                'harga' => \app\helpers\MyHelper::formatRupiah(round($row->harga)),
                'subtotal' => \app\helpers\MyHelper::formatRupiah($row->subtotal),
                'signa1' =>$row->signa1,
                'signa2' =>$row->signa2,
                'is_racikan' =>$row->is_racikan,
                'kode_racikan'=>$row->kode_racikan,
                'dosis_minta' =>$row->dosis_minta,
                'qty' =>$row->qty,
                'qty_bulat' => ceil($row->qty),
                'subtotal_bulat' => \app\helpers\MyHelper::formatRupiah($subtotal_bulat),
            ];

            // $results = array_merge($results,$row->attributes);
            // $results = array_merge($results,$results);

            $items['rows'][] = $results;
            

        } 

        $items['total'] = \app\helpers\MyHelper::formatRupiah($total);


        return $items;
    }

    public function actionAjaxLoadItemJual()
    {
        if (Yii::$app->request->isPost) 
        {

            $id = $_POST['dataItem'];
            
            $result = [
                'code' => 200,
                'message' => 'success',
                'items'=>$this->loadItems($id)
            ];

            echo json_encode($result);
        }
    }

    

    public function actionAjaxInputItem()
    {
        if (Yii::$app->request->isPost) {
            $dataItem = $_POST['dataItem'];

            $model = Cart::find()->where([
                'kode_transaksi'=>$dataItem['kode_transaksi'],
                'departemen_stok_id' => $dataItem['departemen_stok_id']
            ])->one();

            if(!empty($model)){
                $model->qty += $dataItem['qty'];
            }

            else{
                $model = new Cart;
                $model->attributes = $dataItem;
            }
            
            
            // $model = new PenjualanItem;
            // $model->attributes = $dataItem;
            
            $result = [
                'code' => 'success',
                'message' => 'Data telah disimpan'
            ];
            if($model->validate())
            {
                $model->save();
            }

            else{

                $errors = '';
                foreach($model->getErrors() as $attribute){
                    foreach($attribute as $error){
                        $errors .= $error.' ';
                    }
                }
                        
                $result = [
                    'code' => 'danger',
                    'message' => $errors
                ];
                // print_r();exit;
            }

            $list_cart = Cart::find()->where(['kode_transaksi'=>$dataItem['kode_transaksi']])->all();
            $items = [];
            $total = 0;
            foreach($list_cart as $item){
                $subtotal = $item->departemenStok->barang->harga_jual * $item->qty;
                $items[] = [
                    'id' => $item->departemen_stok_id,
                    'qty' => $item->qty,
                    'kode' => $item->departemenStok->barang->kode_barang,
                    'nama' => $item->departemenStok->barang->nama_barang,
                    'harga' => MyHelper::formatRupiah($item->departemenStok->barang->harga_jual),
                    'subtotal' => MyHelper::formatRupiah($subtotal),
                    'kode_transaksi' => $item->kode_transaksi
                ];

                $total += $subtotal;
            }
            $result['items'] = $items;
            $result['total'] = MyHelper::formatRupiah($total);
            echo json_encode($result);
        }
    }

    /**
     * Lists all Penjualan models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PenjualanSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single Penjualan model.
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
     * Creates a new Penjualan model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($jenis_rawat)
    {
        $model = new Penjualan();
        
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
            'jenis_rawat' => $jenis_rawat
        ]);
    }

    /**
     * Updates an existing Penjualan model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        $query = Cart::find()->where(['kode_transaksi'=>$model->kode_transaksi]);
        $cart = $query->all();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
            'cart' => $cart
        ]);
    }

    /**
     * Deletes an existing Penjualan model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        $model->is_removed = 1;
        $model->save();

        return $this->redirect(['index']);
    }

    /**
     * Finds the Penjualan model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return Penjualan the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = Penjualan::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException('The requested page does not exist.');
    }
}
