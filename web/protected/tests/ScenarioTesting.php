<?php
/**
 * ClassName class file.
 * @author Pavel Bariev <bariew@yandex.ru>
 * @copyright (c) 2013, Moqod
 * @license http://www.opensource.org/licenses/bsd-license.php
 */

/**
 * tests are running from protected/tests folder with "phpunit functional" command (or "phpunit functional functional/TestClass.php")
 * @package application.modules.mymodule
 */
class ScenarioTesting extends WUnitTestCase
{
    public $path;
    public $page;
    public $client;
    public $user;
    
    public $error = array('code'=>'', 'message'=>'');
    public $moduleName, $modelName, $controllerName, $actionName;
    
    /* STANDART TESTS */
    
    protected function getTitle()
    {
        ucfirst($this->actionName) . lcfirst($this->getModelName());
    }
    
    protected function checkTitles($string=false)
    {
        if($string === false){
            $string = $this->getTitle();
        }
        $this->assertTrue(
            ($this->page->filter('h1:contains("' . $string . '")')->count() > 0)
            || ($this->page->filter('h2:contains("' . $string . '")')->count() > 0) 
            || ($this->page->filter('h3:contains("' . $string . '")')->count() > 0)
            || ($this->page->filter('h4:contains("' . $string . '")')->count() > 0),
            "{$this->getView()} view has no H1 with '$string'"
        );
    }
    
    public function checkSuccess()
    {
        return $this->assertTrue($this->page->filter('div.alert-success')->count() > 0);
    }
    
    public function checkAllLinks($mode=true)
    {
        $links = $this->page->filter('a')->links();
        foreach($links as $link){
            $this->path($link->getUri(), $mode);
        }
    }
    
    /* CONSTRUCT */
    
    protected function getView()
    {
        return "{$this->controllerName}->{$this->actionName}";
    }
    
    public function __construct()
    {
        Yii::setPathOfAlias('webroot', dirname(__FILE__).'/../../');
        $this->setUser(false);
        $this->client = static::createClient();
    }
    
    public function setUser($user)
    {
        $this->user = $user;
        Yii::app()->user->setModel($user);
    }
    
    public function path($options, $mode=true, $method = 'GET')
    {
        $this->createPath($options);
        try{
            $this->page = @$this->client->request($method, $this->path);
        }catch(Exception $e){
            $this->page = false;
            $this->error = "{$e->getCode()} {$e->getMessage()}";
        };
        $this->checkAccess($mode);
        return $this;
    }
    
    public function checkAccess($mode=true)
    {
        if($mode === true){
            $this->assertTrue(is_object($this->page),
                "Can not access page on {$this->path} for " . @$this->user['role'] . " {$this->error}");
        }else{
            $this->assertFalse(is_object($this->page), "Can access unaccessable page on {$this->path} for " . @$this->user['role']);
        }
        return $this;
    }
    
    protected function createPath($options)
    {
        if(is_string($options)){
            $path = explode('/', $options);
            $this->moduleName       = @$path[0];
            $this->modelName        = ucfirst(@$path[1]);
            $this->controllerName   = $this->modelName .'Controller';
            $this->actionName       = @$path[2];
            return $this->path = $options;
        }else{
            $this->controllerName   = @$options[0];
            $this->actionName       = @$options[1];
            $this->modelName        = str_replace('Controller', '', $this->controllerName);
            $this->moduleName       = lcfirst(preg_replace('/([A-Z][a-z]+).*/', '$1', $this->modelName));
            return $this->path = "/{$this->moduleName}/" . lcfirst($this->modelName) . "/{$this->actionName}/" . @$options[2];
        }
    }
    
    public function getModels()
    {
        $s = DIRECTORY_SEPARATOR;
        return array(
            'users' => include_once(dirname(__FILE__) . "{$s}fixtures{$s}_users.php")
        );
    }
    
    public function __get($attribute)
    {
        if(isset($this->$attribute)){
            return $this->$attribute;
        }
        $methodName = 'get' . ucfirst($attribute);
        if(method_exists($this, $methodName)){
            return $this->$methodName();
        }
    }
}

/**
 * @expectedException CHttpException
 */

/* WUNIT EXAMPLES 
 * http://www.yiiframework.com/extension/wunit
 * 
 * SELECT LINK
 * $crawler->selectLink('Go elsewhere...')->link();
 * SELECT BY CLASS
 * $this->assertTrue($crawler->filter('h2.subtitle')->count() > 0);
 * CHECK COUNT
 * $this->assertEquals(4, $crawler->filter('h2')->count());
 * CHECK HEADER
 * $this->assertTrue($client->getResponse()->headers->contains('Content-Type', 'application/json'));
 * REGEXP
 * $this->assertRegExp('/foo/', $client->getResponse()->getContent());
 * STATUS CODE
 * $this->assertEquals(200, $client->getResponse()->getStatusCode());

 * SELECT FORM
 * $form = $crawler->selectButton('validate')->form();
 * SUBMIT FORM
 * $crawler = $crawler->selectButton('validate')->submit();
 * $crawler = $client->submit($form, array('name' => 'Chris'));
 * FORM FIELDS
 * $form['name'] = 'Chris';                         // fill input
 * $form['country']->select('France');              // Select an option or a radio
 * $form['like_weavora']->tick();                   // Tick a checkbox
 * $form['photo']->upload('/path/to/lucas.jpg');    // Upload a file
 * 
 * 
    Last but not least, you can force each request to be executed in its own PHP process to avoid any side-effects when working with several clients in the same script:
    $client->insulate();

    $client->back();
    $client->forward();
    $client->reload();

    # Clears all cookies and the history
    $client->restart();
 * 
   Method Description
 
   filter('h1.title') 	Nodes that match the CSS selector
   filterXpath('h1') 	Nodes that match the XPath expression
   eq(1)                Node for the specified index
   first()              First node
   last()               Last node
   siblings()           Siblings
   nextAll()            All following siblings
   previousAll()        All preceding siblings
   parents()            Parent nodes
   children()           Children
   reduce($lambda)      Nodes for which the callable does not return false
 
 * 
 * EXAMPLES
 * 
    $newCrawler = $crawler->filter('input[type=submit]')
       ->last()
       ->parents()
       ->first()
 * 
 * 
    $foo = get_me_my_foo();
    $this->assertInstanceOf("MyObject", $foo);
    $this->assertTrue($foo->doStuff());
 */
