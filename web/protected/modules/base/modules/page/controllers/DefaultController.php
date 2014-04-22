<?php
class DefaultController extends FrontendController
{

    public function actionView()
    {
        if(!$this->page){
        	throw new CHttpException(404, 'Page not found');
        }
		if($this->page->layout){
			$this->layout = $this->page->layout;
		}
        $this->render('view', array(
            'model' => $this->page
        ));
    }
	
    /**
     * This is the action to handle external exceptions.
     */
    public function actionError($error=array('code'=>404, 'message'=>'Page not found.'))
    {
        $this->createTitles($error['code'].  ": " . $error['message']);
        if(Yii::app()->errorHandler->error){
            $error = Yii::app()->errorHandler->error;
        }
        if(Yii::app()->request->isAjaxRequest || in_array(Yii::app()->request->requestType, array('POST', 'PUT', 'DELETE', 'HEADER'))){
            echo json_encode(array(
                'code'      => $error['code'],
                'message'   => $error['message']
            ));
            Yii::app()->end();
        }else{
            $this->render('error', compact('error'));
        }
    }
    
    public function actionContacts()
    {
        $model = new ContactForm;
        if (isset($_POST['ContactForm'])){
            $model->attributes = $_POST['ContactForm'];
            if ($model->send()){
                Yii::app()->user->setFlash('contact', 'Thank you for your request!');
                $this->refresh();
            }
        }
        if(Yii::app()->request->isAjaxRequest)
            $this->renderPartial('_feedback', compact('model'));
        else
            $this->render('contacts', array('model' => $model, 'content' => $this->page['content']));
    }    
}
