<?php

namespace rade901;

use app\models\File;
use yii\base\Widget;
use yii\web\UploadedFile;
use Yii;

class UploadImageWidget extends Widget
{
    public $model;
    public $form;

    public function run()
    {
        if ($this->model->load(Yii::$app->request->post())) {
            $this->model->imageFile = UploadedFile::getInstance($this->model, 'imageFile');
            if ($this->model->validate()) {
                $this->saveImage();
            }
        }

        return $this->form->field($this->model, 'imageFile')->fileInput();
    }

    protected function saveImage()
    {
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // Delete the old image file if it exists
            if ($this->model->file_id) {
                $this->deleteOldImage($this->model->file_id);
            }

            $file = new File();
            $file->name = uniqid(true) . '.' . $this->model->imageFile->extension;
            $file->path_url = Yii::$app->params['uploads']['profile'];
            $file->base_url = Yii::$app->urlManager->createAbsoluteUrl($file->path_url);
            $file->mime_type = mime_content_type($this->model->imageFile->tempName);
            if (!$file->save()) {
                throw new \Exception('Image save failed');
            }

            // Set the file_id on the current image instance
            $this->model->file_id = $file->id;
            if (!$this->model->save()) {
                throw new \Exception('Image save failed');
            }

            if (!$this->model->imageFile->saveAs(Yii::getAlias('@webroot') . '/' . Yii::$app->params['uploads']['profile'] . '/' . $file->name)) {
                throw new \Exception('Image save failed');
            }

            $transaction->commit();
            Yii::$app->session->setFlash('success', 'Image uploaded successfully');
            return Yii::$app->controller->redirect(['view', 'id' => $this->model->id]);

        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
    }

    protected function deleteOldImage($fileId)
    {
        $file = File::findOne($fileId);
        if ($file) {
            $filePath = Yii::getAlias('@webroot') . '/' . Yii::$app->params['uploads']['profile'] . '/' . $file->name;
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $file->delete();
        }
    }
}