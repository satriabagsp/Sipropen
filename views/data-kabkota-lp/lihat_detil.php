<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\DataKabkota2;
use yii\grid\GridView;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;
use app\models\DataKabKota;
use fedemotta\datatables\DataTables;
use miloschuman\highcharts\Highcharts;
use miloschuman\highcharts\SeriesDataHelper;

/* @var $this yii\web\View */
/* @var $model app\models\DataKabKota */

//Untuk menampilkan detail view data master kabupaten/kota tahun 2015 saja.
    $model_kabkota = DataKabkota2::findOne($model->kabkota);

//fungsi transpose array
    function transpose($array) {
        array_unshift($array, null);
        return call_user_func_array('array_map', $array);
    }

$this->title = 'Data Master ' . $model->kabkota;
$this->params['breadcrumbs'][] = ['label' => 'Data Kabupaten Kota', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="data-kab-kota-view">
    <div class="panel panel-info">
        <div class="panel-heading"><h1><?= Html::encode($this->title) ?></h1></div>
        <div class="panel-body">
    
                <?php
                    //buat tabel untuk ngeliatin data.
                        //Buat koneksi ke DB
                            include "koneksi.php";
                            $sql_AmbilData = "SELECT *
                                              FROM data_kabkota
                                              WHERE id_kabkota = '" . $model->id_kabkota . "'";
                            $query_AmbilData = mysqli_query($host,$sql_AmbilData) or die(mysqli_error());

                        //Memasukan data dari database dalam bentuk ARRAY
                            $i = '';
                            $ku[] = '0-4'; $ku[] = '5-9'; $ku[] = '10-14'; $ku[] = '15-19'; $ku[] = '20-24';
                            $ku[] = '25-29'; $ku[] = '30-34'; $ku[] = '35-39'; $ku[] = '40-44'; $ku[] = '45-49';
                            $ku[] = '50-54'; $ku[] = '55-59'; $ku[] = '60-64'; $ku[] = '65-69'; $ku[] = '70-74';
                            $ku[] = '75-79'; $ku[] = '80-84'; $ku[] = '85-89'; $ku[] = '90-94'; $ku[] = '95+'; 
                            while($row = mysqli_fetch_array($query_AmbilData)){ 
                                $i++;
                                $id_prov[] = $row['id_prov'];
                                $provinsi[] = $row['provinsi'];
                                $id_kabkota[] = $row['id_kabkota'];
                                $kabkota[] = $row['kabkota'];
                                for($umur=5;$umur<101;$umur=$umur+5){
                                    ${'tot_'.$umur.'_2010'} = (int)$row['tot_'.$umur.'_2010'];
                                    ${'tot_'.$umur.'_2015'} = (int)$row['tot_'.$umur.'_2015'];
                                    $data_laki2_2010[] = $row['l_'.$umur.'_2010'];
                                    $data_perempuan_2010[] = $row['p_'.$umur.'_2010'];
                                    $data_laki2_2015[] = $row['l_'.$umur.'_2015'];
                                    $data_perempuan_2015[] = $row['p_'.$umur.'_2015'];
                                    $data_total_2010[] = $row['tot_'.$umur.'_2010'];
                                    $data_total_2015[] = $row['tot_'.$umur.'_2015'];
                                };
                            };
                    ?>
 
                <div class="col-md-6">
                        <div>
                            <?= Highcharts::widget([ //buat grafik pie
                                'options' => [
                                    'title' => ['text' => 'Jumlah Penduduk Kabupaten/kota Menurut Kelompok Umur dan Jenis Kelamin'],
                                    'subtitle' => [
                                        'text' => 'Hasil SENSUS 2010'  
                                    ],
                                    'chart' => [
                                        'type' => 'pie',
                                    ],
                                    'plotOptions' => [
                                        'pie' => [
                                            'allowPointSelect' => 'true',
                                            'cursor' => 'pointer',
                                            'dataLabels' => [
                                                'enabled' => 'false'
                                            ],
                                        ],
                                    ],
                                    'series' => [
                                        [
                                            'name' => 'Jumlah Penduduk',
                                            'data' => [
                                                ['Usia 0-4 tahun' , $tot_5_2010],
                                                ['Usia 5-9 tahun' , $tot_10_2010],
                                                ['Usia 10-14 tahun' , $tot_15_2010],
                                                ['Usia 15-19 tahun' , $tot_20_2010],
                                                ['Usia 20-24 tahun' , $tot_25_2010],
                                                ['Usia 25-29 tahun' , $tot_30_2010],
                                                ['Usia 30-34 tahun' , $tot_35_2010],
                                                ['Usia 35-39 tahun' , $tot_40_2010],
                                                ['Usia 40-44 tahun' , $tot_45_2010],
                                                ['Usia 45-49 tahun' , $tot_50_2010],
                                                ['Usia 50-54 tahun' , $tot_55_2010],
                                                ['Usia 55-59 tahun' , $tot_60_2010],
                                                ['Usia 60-64 tahun' , $tot_65_2010],
                                                ['Usia 65-69 tahun' , $tot_70_2010],
                                                ['Usia 70-74 tahun' , $tot_75_2010],
                                                ['Usia 75-79 tahun' , $tot_80_2010],
                                                ['Usia 80-84 tahun' , $tot_85_2010],
                                                ['Usia 85-89 tahun' , $tot_90_2010],
                                                ['Usia 90-94 tahun' , $tot_95_2010],
                                                ['Usia 95+ tahun' , $tot_100_2010],
                                            ],
                                        ],

                                    ],
                                ]
                            ]); ?> 
                            </div>
                        <br>
                        <table rules="row" style="text-align:center;" border="2" class="table">
                            <tr style="background-color: #9ca7ba;" border="2">
                                <th style="text-align:center">Kelompok Umur</th>
                                <th style="text-align:center">Laki-laki</th>
                                <th style="text-align:center">Perempuan</th>
                                <th style="text-align:center">Jumlah</th> 
                            </tr>
                            <?php 
                                for($a=0;$a<count($data_laki2_2010);$a++){
                                    $jumjum = $data_laki2_2010[$a] + $data_perempuan_2010[$a];
                                ?>
                                <tr>
                                    <td><?php echo $ku[$a]; ?></td>
                                    <td><?php echo number_format($data_laki2_2010[$a]); ?></td>
                                    <td><?php echo number_format($data_perempuan_2010[$a]); ?></td>
                                    <td><?php echo number_format($jumjum); ?></td>
                                </tr>
                            <?php } ?>
                            <tr>
                                <th style="background-color: #9ca7ba;text-align:center">Total</th>
                                <th style="background-color: #9ca7ba;text-align:center"><?php echo number_format(array_sum($data_laki2_2010)); ?></th>
                                <th style="background-color: #9ca7ba;text-align:center"><?php echo number_format(array_sum($data_perempuan_2010)); ?></th>
                                    <?php $jumjum = array_sum($data_laki2_2010) + array_sum($data_perempuan_2010); ?>
                                <th style="background-color: #9ca7ba;text-align:center"><?php echo number_format($jumjum); ?></th>
                            </tr>
                        </table>

                  </div>

                  <div class="col-md-6">
                        <div>
                            <?= Highcharts::widget([ //buat grafik pie
                                'options' => [
                                    'title' => ['text' => 'Jumlah Penduduk Kabupaten/kota Menurut Kelompok Umur dan Jenis Kelamin'],
                                    'subtitle' => [
                                        'text' => 'Hasil SUPAS 2015'  
                                    ],
                                    'chart' => [
                                        'type' => 'pie',
                                    ],
                                    'plotOptions' => [
                                        'pie' => [
                                            'allowPointSelect' => 'true',
                                            'cursor' => 'pointer',
                                            'dataLabels' => [
                                                'enabled' => 'false'
                                            ],
                                        ],
                                    ],
                                    'series' => [
                                        [
                                            'name' => 'Jumlah Penduduk',
                                            'data' => [
                                                ['Usia 0-4 tahun' , $tot_5_2015],
                                                ['Usia 5-9 tahun' , $tot_10_2015],
                                                ['Usia 10-14 tahun' , $tot_15_2015],
                                                ['Usia 15-19 tahun' , $tot_20_2015],
                                                ['Usia 20-24 tahun' , $tot_25_2015],
                                                ['Usia 25-29 tahun' , $tot_30_2015],
                                                ['Usia 30-34 tahun' , $tot_35_2015],
                                                ['Usia 35-39 tahun' , $tot_40_2015],
                                                ['Usia 40-44 tahun' , $tot_45_2015],
                                                ['Usia 45-49 tahun' , $tot_50_2015],
                                                ['Usia 50-54 tahun' , $tot_55_2015],
                                                ['Usia 55-59 tahun' , $tot_60_2015],
                                                ['Usia 60-64 tahun' , $tot_65_2015],
                                                ['Usia 65-69 tahun' , $tot_70_2015],
                                                ['Usia 70-74 tahun' , $tot_75_2015],
                                                ['Usia 75-79 tahun' , $tot_80_2015],
                                                ['Usia 80-84 tahun' , $tot_85_2015],
                                                ['Usia 85-89 tahun' , $tot_90_2015],
                                                ['Usia 90-94 tahun' , $tot_95_2015],
                                                ['Usia 95+ tahun' , $tot_100_2015],
                                            ],
                                        ],

                                    ],
                                ]
                            ]); ?> 
                        </div>
                        <br>
                        <table style="text-align:center" border="2" class="table">
                            <tr style="background-color: #9ca7ba;" border="2">
                                <th style="text-align:center">Kelompok Umur</th>
                                <th style="text-align:center">Laki-laki</th>
                                <th style="text-align:center">Perempuan</th>
                                <th style="text-align:center">Jumlah</th>    
                            </tr>
                            <?php 
                                //Jumlah laki2 dan perempuan
                                    for($a=0;$a<count($data_laki2_2015);$a++){
                                         $jumjum = $data_laki2_2015[$a] + $data_perempuan_2015[$a];
                                    ?>
                                <tr>
                                    <td><?php echo $ku[$a]; ?></td>
                                    <td><?php echo number_format($data_laki2_2015[$a]); ?></td>
                                    <td><?php echo number_format($data_perempuan_2015[$a]); ?></td>
                                    <td><?php echo number_format($jumjum); ?></td>
                                </tr>
                            <?php } ?>
                            <tr>
                                <th style="background-color: #9ca7ba;text-align:center">Total</th>
                                <th style="background-color: #9ca7ba;text-align:center"><?php echo number_format(array_sum($data_laki2_2015)); ?></th>
                                <th style="background-color: #9ca7ba;text-align:center"><?php echo number_format(array_sum($data_perempuan_2015)); ?></th>
                                    <?php $jumjum = array_sum($data_laki2_2015) + array_sum($data_perempuan_2015); ?>
                                <th style="background-color: #9ca7ba;text-align:center"><?php echo number_format($jumjum); ?></th>
                            </tr>
                        </table>
                  </div>

            <?php if (Yii::$app->user->identity->role == 'provinsi' || Yii::$app->user->identity->role == 'pusat'): ?>
                <span class="pull-right">
                    <?= Html::a('Sunting', ['update', 'id' => $model->kabkota], ['class' => 'btn btn-primary']) ?>
                    <?= Html::a('Hapus', ['delete', 'id' => $model->kabkota], [
                        'class' => 'btn btn-danger',
                        'data' => [
                            'confirm' => 'Apakah akan menghapus data demografi ' . $this->title . '?',
                            'method' => 'post',
                        ],
                    ]) ?>
                </span> 

            <?php elseif (Yii::$app->user->identity->role == 'kab_kota'): ?> 
                

            <?php endif; ?>
            
        </div>    
    </div>
</div>
