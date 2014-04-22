<?php
class PushScenarioTest extends ScenarioTesting
{
    public function testLinksDashboard()
    {
        foreach($this->models['users']['native'] as $user){//echo "UserId = {$user->id}\n";
            $this->setUser($user);
            $this->path(array('PushProjectController', $user->role.'Dashboard'))->page;
            $this->checkAllLinks();
        }
    }
}