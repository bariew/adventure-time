<?php
class FrontendController extends Controller
{
    public function actions()
    {
        return array(
            'read'      => 'application.modules.base.controllers.actions.admin.ReadAction',
            'index'     => 'application.modules.base.controllers.actions.admin.IndexAction',
        );
    } 
    public function actionCron($key)
    {
        if($key != 'myKey987'){
            return;
        }
    }
}
//* * * * * wget http://topnotes.dev/frontend/cron?key=myKey987