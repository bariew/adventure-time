<?php
class TextPageTest extends FunctionalTesting
{
    public function testIndex()
    {
        return;
        $this->standartLinksTest('/textPage/textPageCategory/index', 'root', array('/delete'));
    }
}