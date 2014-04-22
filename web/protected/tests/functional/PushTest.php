<?php
class PushTest extends FunctionalTesting
{
    public function testDashboard()
    {
        foreach($this->models['users']['native'] as $user){//echo "UserId = {$user->id}\n";
            $this->setUser($user);
            $this->setPath('/project/pushProject/index')->checkAccess();
            $this->checkAllLinks();
        }
    }
    
    public function testStatistics()
    {
        //$this->standartLinksTest('/project/projectStatistics/devicesBar?project_id=3', 'company');
    }
}