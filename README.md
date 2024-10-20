    Instalation

    composer require rade901/yii2-upload-image-widget

    start migrations:

    File-table

    php yii migrate --migrationPath=@vendor/rade901/yii2-upload-image-widget/src/migrations



    
     ```
     use yii\helpers\Html;
     use yii\widgets\ActiveForm;
     use Rade901\Yii2UploadImageWidget\UploadImageWidget;


    <div class="profile-form">

    <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]); ?>

    <?= UploadImageWidget::widget(['model' => $model, 'form' => $form]) ?>

    

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

    <?php if ($model->file_id): ?>
        <img src="<?= $model->file->base_url . '/' . $model->file->name ?>" alt="profile image" style="max-width: 200px;">
    <?php endif; ?>
    

     </div>
     ```

