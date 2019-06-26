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
}
