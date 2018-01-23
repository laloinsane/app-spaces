<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Tallers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="taller-view">
    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'nombre',
            'url_bucket:url',
        ],
    ]) ?>
    <h4>Listado de buckets: </h4>
    <?=json_encode($buckets);?>
    <hr>

    <input  name="subir-archivo" accept="image/*" id="subir-archivo" 
    class ="btn btn-primary" data-taller-id = "<?=$model->id ?>" type="file" value="Subir Archivo" >
    

</div>

<?php 
    $this->registerJsFile(
        '@web/js/upload.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]
    );
?>
