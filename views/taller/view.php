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

<input  name="subir-archivo" accept="image/*" id="subir-archivo" 
    class ="btn btn-primary" data-taller-id = "<?=$model->id ?>" type="file" value="Subir Archivo" >
    
<div class="progress">
  <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar"
  aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:40%">
    40% Complete (success)
  </div>
</div>
    <hr>

    <table>
        <thead>
            <tr>
                <th><h4>Elemento</h4></th>
                <th><h4>Size</h4></th>
                <th><h4>Last Modified</h4></th>
                <th><h4>Opciones</h4></th>
            </tr>
        </thead>
        <tbody>

            <?php
      // print_r($lista_objetos);
            foreach ($lista_objetos as $objeto) { ?>
                <tr>
                    <td>
                        <h4><a href="<?=$model->url_bucket.$objeto ?>" target="_blank"><?= $objeto[0]['nombre'] ?></a></h4>
                    </td>
                    <td>
                        <h4><?= $objeto['size'] ?></h4>
                    </td>
                    <td>
                        <h4><?= $objeto['last'] ?></h4>
                    </td>
                    <td>
                        <h4><a href="<?=$model->url_bucket.$objeto ?>" target="_blank">ver</a></h4>
                        <?= Html::a('Eliminar', ['eliminar', 'id' => $model->id, 'objeto' => $objeto], ['class' => 'btn btn-danger']) ?>
                    </td>
                </tr>
            <?php  } ?>

        
        </tbody>
    </table>
    <hr>
 

</div>

<?php 
    $this->registerJsFile(
        '@web/js/upload.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]
    );
?>
