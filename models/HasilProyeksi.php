<?php

namespace app\models;

use Yii;

/**
 * This is the model class for table "hasil_proyeksi".
 *
 * @property string $provinsi
 * @property string $kab_kota
 * @property int $p2015
 * @property int $p2016
 * @property int $p2017
 * @property int $p2018
 * @property int $p2019
 * @property int $p2020
 * @property int $p2021
 * @property int $p2022
 * @property int $p2023
 * @property int $p2024
 * @property int $p2025
 */
class HasilProyeksi extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'hasil_proyeksi';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['provinsi', 'kab_kota', 'p2015', 'p2016', 'p2017', 'p2018', 'p2019', 'p2020', 'p2021', 'p2022', 'p2023', 'p2024', 'p2025'], 'required'],
            [['p2015', 'p2016', 'p2017', 'p2018', 'p2019', 'p2020', 'p2021', 'p2022', 'p2023', 'p2024', 'p2025'], 'integer'],
            [['provinsi', 'kab_kota'], 'string', 'max' => 100],
            [['kab_kota'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'provinsi' => 'Provinsi',
            'kab_kota' => 'Kabupaten/kota',
            'p2015' => '2015',
            'p2016' => '2016',
            'p2017' => '2017',
            'p2018' => '2018',
            'p2019' => '2019',
            'p2020' => '2020',
            'p2021' => '2021',
            'p2022' => '2022',
            'p2023' => '2023',
            'p2024' => '2024',
            'p2025' => '2025',
        ];
    }
}
