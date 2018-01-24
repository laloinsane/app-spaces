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

    <table>
        <thead>
            <tr>
                <th><h4>Objetos</h4></th>
                <th><h4>Size</h4></th>
                <th><h4>Last Modified</h4></th>
                <th><h4>Opciones</h4></th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($lista_objetos as $objeto1) { ?>
                <?php foreach ($objeto1 as $key => $objeto) { ?>
                <tr>
                    <td>
                        <h4><a href="<?=$model->url_bucket.$objeto ?>" target="_blank"><?= $objeto ?></a></h4>
                    </td>
                    <td>
                        <h4><?= $objeto ?></h4>
                    </td>
                    <td>
                        <h4><?= $objeto ?></h4>
                    </td>
                    <td>
                        <h4><a href="<?=$model->url_bucket.$objeto ?>" target="_blank">ver</a></h4>
                        <?= Html::a('Eliminar', ['eliminar', 'id' => $model->id, 'objeto' => $objeto], ['class' => 'btn btn-danger']) ?>
                    </td>
                </tr>
                <?php  } ?>
            <?php  } ?>

        
        </tbody>
    </table>
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
