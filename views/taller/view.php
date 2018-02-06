<?php
use yii\helpers\Html;
use yii\widgets\DetailView;
$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Tallers', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?= Yii::$app->session->getFlash('msg') ?>

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

    <img src="https://nosenose4.nyc3.digitaloceanspaces.com/raichu31-01-2018/61wxghabxdl._sl1000__large.jpg">

    <?php if (empty($model->url_bucket)) { ?>

    <?php } else { ?>

        <?php if ($error == false) { ?>

            
<div class="container">
     


            <div class="row">
                <h3>jQuery Ajax file upload with percentage progress bar</h3>
                <form id="myform" method="post">

                    <div class="form-group">
                        <label>Enter the file name: </label>
                        <input class="form-control" type="text" id="filename" /> 
                    </div>
                    <div class="form-group">
                        <label>Select file: </label>
                        <input class="form-control" type="file" id="myfile" data-taller-id="<?=$model->id?>"/>
                    </div>
                    <div class="form-group">
                        <div class="progress">
                            <div class="progress-bar progress-bar-success myprogress" role="progressbar" style="width:0%">0%</div>
                        </div>

                        <div class="msg"></div>
                    </div>

                    <input type="button" id="btn" class="btn-success" value="Upload" />
                </form>
            </div>
        </div>
        <hr>
            <input  name="subir-archivo" accept="*" id="subir-archivo" class ="btn btn-primary" data-taller-id = "<?=$model->id ?>" type="file" value="Subir Archivo" >
            <hr>
        
            <div>
                <h1 id="nose"></h1>
            </div>

            <div class="progress">
              <div class="progress-bar progress-bar-success progress-bar-striped" role="progressbar"
              aria-valuenow="40" aria-valuemin="0" aria-valuemax="100" style="width:40%">
                40% Complete (success)
              </div>
            </div>
            <hr>

            <table class="table-bordered table">
                <thead>
                    <tr>
                        <th><h4>Elemento</h4></th>
                        <th><h4>Size</h4></th>
                          <th><h4>Permiso</h4></th>
                        <th><h4>Last Modified</h4></th>
                        <th><h4>Opciones</h4></th>
                          <th><h4>cambiar permisos</h4></th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($lista_objetos['result'] as $objeto => $detalles) { ?>
                        <tr>

                            <td>
                                <?= $detalles['nombre'] ?>
                            </td>

                            <td>
                                <?= $detalles['size'] ?>
                            </td>

                                                        <td>
                                <?= $detalles['permiso'] ?>
                            </td>

                            <td>
                                <?= $detalles['last'] ?>
                            </td>
                                

                            <td>
                                <h4><a href="<?=$model->url_bucket.$detalles['nombre'] ?>" target="_blank">ver</a></h4>
                                <?= Html::a('Eliminar', ['eliminar', 'id' => $model->id, 'objeto' => $detalles['nombre']], ['class' => 'btn btn-danger']) ?>
                            </td>
                            <td>

                                <?= 
                                    Html::a('Privado', ['private', 'id' => $model->id, 'objeto' => $detalles['nombre']], ['class' => 'btn btn-success']).'<br><br>'.

                                    Html::a('Publico', ['public', 'id' => $model->id, 'objeto' => $detalles['nombre']], ['class' => 'btn btn-success']) ?>
                            </td>
                           
                        </tr>
                    <?php  } ?>
                </tbody>
            </table>
            <hr>
        <?php } ?>
    <?php } ?>

</div>

<?php 
    $this->registerJsFile(
        '@web/js/upload.js',
        ['depends' => [\yii\web\JqueryAsset::className()]]
    );
?>
