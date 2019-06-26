<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "erp_jurnal".
 *
 * @property int $id
 * @property int $perkiraan_id
 * @property string $kode_perkiraan
 * @property double $debet
 * @property double $kredit
 * @property string $created_at
 * @property string $updated_at
 * @property int $perusahaan_id
 *
 * @property Perkiraan $perkiraan
 * @property Perusahaan $perusahaan
 */
class Jurnal extends \yii\db\ActiveRecord
{

     public $tanggal_awal;
    public $tanggal_akhir;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'erp_jurnal';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['perkiraan_id', 'perusahaan_id','tanggal','no_bukti','transaksi_id'], 'required'],
            [['perkiraan_id', 'perusahaan_id'], 'integer'],
            [['debet', 'kredit'], 'number'],
            [['created_at', 'updated_at'], 'safe'],
            [['no_bukti'], 'string', 'max' => 100],
            [['perkiraan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Perkiraan::className(), 'targetAttribute' => ['perkiraan_id' => 'id']],
            [['perusahaan_id'], 'exist', 'skipOnError' => true, 'targetClass' => Perusahaan::className(), 'targetAttribute' => ['perusahaan_id' => 'id_perusahaan']],
            [['transaksi_id'], 'exist', 'skipOnError' => true, 'targetClass' => Transaksi::className(), 'targetAttribute' => ['transaksi_id' => 'id']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'perkiraan_id' => 'Perkiraan ID',
            'transaksi_id' => 'Trx ID',
            'no_bukti' => 'No Bukti',
            'debet' => 'Debet',
            'kredit' => 'Kredit',
            'created_at' => 'Created At',
            'updated_at' => 'Updated At',
            'perusahaan_id' => 'Perusahaan ID',
        ];
    }

    

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPerkiraan()
    {
        return $this->hasOne(Perkiraan::className(), ['id' => 'perkiraan_id']);
    }

    public function getPrevJurnal()
    {
        return $this->hasOne(Jurnal::className(), ['id' => 'prev_jurnal_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getPerusahaan()
    {
        return $this->hasOne(Perusahaan::className(), ['id_perusahaan' => 'perusahaan_id']);
    }
}
