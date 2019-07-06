<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "erp_transaksi".
 *
 * @property int $id
 * @property int $perkiraan_id
 * @property double $jumlah
 * @property string $keterangan
 * @property string $tanggal
 * @property string $created_at
 * @property string $updated_at
 * @property int $perusahaan_id
 *
 * @property Perkiraan $perkiraan
 * @property Perusahaan $perusahaan
 */
class Transaksi extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'erp_transaksi';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['perkiraan_id', 'keterangan', 'tanggal', 'perusahaan_id','no_bukti'], 'required'],
            [['perkiraan_id', 'perusahaan_id'], 'integer'],
            [['jumlah'], 'number'],
            [['tanggal', 'created_at', 'updated_at'], 'safe'],
            [['keterangan'], 'string', 'max' => 255],
            [['perkiraan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Perkiraan::className(), 'targetAttribute' => ['perkiraan_id' => 'id']],
            [['perkiraan_lawan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Perkiraan::className(), 'targetAttribute' => ['perkiraan_lawan_id' => 'id']],
            [['perusahaan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Perusahaan::className(), 'targetAttribute' => ['perusahaan_id' => 'id_perusahaan']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'perkiraan_id' => 'Akun',
            'perkiraan_lawan_id' => 'Akun Sumber',
            'jumlah' => 'Jumlah',
            'keterangan' => 'Keterangan',
            'tanggal' => 'Tanggal',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'perusahaan_id' => 'Perusahaan ID',
        ];
    }

    public function beforeSave($insert)
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        $this->tanggal = date('Y-m-d', strtotime($this->tanggal));
       
        return true;
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPerkiraan()
    {
        return $this->hasOne(Perkiraan::className(), ['id' => 'perkiraan_id']);
    }

    public function getPerkiraanLawan()
    {
        return $this->hasOne(Perkiraan::className(), ['id' => 'perkiraan_lawan_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPerusahaan()
    {
        return $this->hasOne(Perusahaan::className(), ['id_perusahaan' => 'perusahaan_id']);
    }

    public static function insertTransaksi($pars)
    {
        $trx = new Transaksi();
        $trx->perusahaan_id = Yii::$app->user->identity->perusahaan_id;

        $akun_lawan = Perkiraan::find()->where([
            'kode' => $pars['kode_akun_lawan'],
            'perusahaan_id' => Yii::$app->user->identity->perusahaan_id
        ]);

        $akun_lawan = $akun_lawan->one();

        if(!empty($akun_lawan))
        {
            $perkiraan_lawan_id = $akun_lawan->id;
            $trx->perkiraan_id = $pars['perkiraan_id'];
            $trx->no_bukti = $pars['no_bukti'];
            $trx->keterangan = $pars['keterangan'];
            $trx->tanggal = $pars['tanggal'];    
            $trx->jumlah = $pars['jumlah'];//$model->qty * $model->barang->harga_jual;
            $trx->perkiraan_lawan_id = $perkiraan_lawan_id;
            if($trx->save())
            {
                $params = [
                    'perkiraan_id' => $trx->perkiraan_id,
                    'transaksi_id' => $trx->id,
                    'no_bukti' => $trx->no_bukti,
                    'jumlah' => $trx->jumlah,
                    'keterangan' => $trx->keterangan,
                    'keterangan_lawan' => $akun_lawan->nama,
                    'tanggal' => $pars['tanggal']
                ];


                $kodeAsal = $trx->perkiraan->kode;
                if(
                    \app\helpers\MyHelper::startsWith($kodeAsal, '1') ||
                    \app\helpers\MyHelper::startsWith($kodeAsal, '5') ||
                    \app\helpers\MyHelper::startsWith($kodeAsal, '6') ||
                    \app\helpers\MyHelper::startsWith($kodeAsal, '8')
                )
                {
                    $jurnal = new Jurnal;
                    $jurnal->perkiraan_id = $params['perkiraan_id'];
                    $jurnal->debet = $params['jumlah'];
                    $jurnal->transaksi_id = $params['transaksi_id'];
                    $jurnal->no_bukti = $params['no_bukti'];
                    $jurnal->keterangan = $params['keterangan'];
                    $jurnal->perusahaan_id = Yii::$app->user->identity->perusahaan_id;
                    $jurnal->tanggal = date('Y-m-d',strtotime($params['tanggal']));
                    if(!$jurnal->save())
                    {
                        print_r($jurnal->getErrors());exit;
                    }

                    if(!empty($perkiraan_lawan_id))
                    {
                        $kodeLawan = $trx->perkiraanLawan->kode;
                        
                        $jurnal = new Jurnal;
                        $jurnal->perkiraan_id = $trx->perkiraan_lawan_id;
                        
                        $jurnal->transaksi_id = $params['transaksi_id'];
                        $jurnal->no_bukti = $params['no_bukti'];
                        $jurnal->kredit = $params['jumlah'];
                        $jurnal->keterangan = $params['keterangan_lawan'];
                        $jurnal->perusahaan_id = Yii::$app->user->identity->perusahaan_id;
                        $jurnal->tanggal = date('Y-m-d',strtotime($params['tanggal']));
                        if(!$jurnal->save())
                        {
                            print_r($jurnal->getErrors());exit;
                        }

                    }
                }

                else if(
                    \app\helpers\MyHelper::startsWith($kodeAsal, '2') ||
                    \app\helpers\MyHelper::startsWith($kodeAsal, '3') ||
                    \app\helpers\MyHelper::startsWith($kodeAsal, '4') ||
                    \app\helpers\MyHelper::startsWith($kodeAsal, '7')
                )
                {
                    $jurnal = new Jurnal;
                    $jurnal->perkiraan_id = $params['perkiraan_id'];
                    $jurnal->debet = 0;
                    $jurnal->transaksi_id = $params['transaksi_id'];
                    $jurnal->no_bukti = $params['no_bukti'];
                    $jurnal->kredit = $params['jumlah'];
                    $jurnal->keterangan = $params['keterangan'];
                    $jurnal->perusahaan_id = Yii::$app->user->identity->perusahaan_id;
                    $jurnal->tanggal = date('Y-m-d',strtotime($params['tanggal']));
                    if(!$jurnal->save())
                    {
                        echo 'Journal';
                        print_r($jurnal->getErrors());exit;
                    }

                    if(!empty($perkiraan_lawan_id))
                    {
                        $kodeLawan = $trx->perkiraanLawan->kode;
                        
                        $jurnal = new Jurnal;
                        $jurnal->perkiraan_id = $trx->perkiraan_lawan_id;
                        $jurnal->debet = $params['jumlah'];
                        $jurnal->transaksi_id = $params['transaksi_id'];
                        $jurnal->no_bukti = $params['no_bukti'];
                       
                        $jurnal->keterangan = $params['keterangan_lawan'];
                        $jurnal->perusahaan_id = Yii::$app->user->identity->perusahaan_id;
                        $jurnal->tanggal = date('Y-m-d',strtotime($params['tanggal']));
                        if(!$jurnal->save())
                        {
                            echo 'Journal lawan';
                            print_r($jurnal->getErrors());exit;
                        }

                    }
                }
            }

            else{
                echo 'Trx Error';
                print_r($trx->getErrors());exit;
            }
        }
    }
}
