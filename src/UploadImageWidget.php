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
        try {
            // Begin transaction
            $transaction = Yii::$app->db->beginTransaction();
        
            // Delete the old image file if it exists
            if ($this->model->file_id) {
                $this->deleteOldImage($this->model->file_id);
            }
        
            if ($this->model->imageFile) {
                $file = new File();
                $file->name = uniqid(true) . '.' . $this->model->imageFile->extension;
                $file->path_url = Yii::$app->params['uploads']['image'];
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
        
                if (!$this->model->imageFile->saveAs(Yii::getAlias('@webroot') . '/' . Yii::$app->params['uploads']['image'] . '/' . $file->name)) {
                    throw new \Exception('Image save failed');
                }
        
                Yii::$app->session->setFlash('success', 'Image uploaded successfully');
            } else {
                // If no image is uploaded, set file_id to null and save the model
                $this->model->file_id = null;
                if (!$this->model->save()) {
                    throw new \Exception('Model save failed');
                }
                Yii::$app->session->setFlash('success', 'Model saved successfully without image');
            }
        
            $transaction->commit();
            return Yii::$app->controller->redirect(['view', 'id' => $this->model->id]);
        
        } catch (\Exception $e) {
            $transaction->rollBack();
            Yii::$app->session->setFlash('error', $e->getMessage());
        }
    }
    
        
    protected function deleteOldImage($file_id)
    {
        $file = File::findOne($file_id);
        if ($file) {
            if (file_exists(Yii::getAlias('@webroot') . '/' . $file->path_url . '/' . $file->name)) {
                unlink(Yii::getAlias('@webroot') . '/' . $file->path_url . '/' . $file->name);
            }
            $file->delete();
        }
    }
}