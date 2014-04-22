<?php
/**
 * FunctionalTesting class file.
 * @author Pavel Bariev <bariew@yandex.ru>
 * @copyright (c) 2013, Moqod
 * @license http://www.opensource.org/licenses/bsd-license.php
 */
Yii::import('application.tests.extensions.wunit.*');
/**
 * tests are running from protected/tests folder with "phpunit functional" command (or "phpunit functional functional/TestClass.php")
 * @package application.tests
 */
class FunctionalTesting extends WUnitTestCase
{
    public $path;
    public $page;
    public $client;
    public $user;
    public $error = array('code'=>'', 'message'=>'');
    /**
     * @var array denied to follow link patterns array
     */
    public $linkFilter = array('mailto:');
    
    
    /* STANDART TESTS */
    
    public function standartLinksTest($path, $accessLevel, $exclude = array())
    {
        $this->linkFilter = array_merge($this->linkFilter, $exclude);
        foreach($this->models['users']['native'] as $user){//echo "UserId = {$user->id}\n";
            $this->setUser($user);
            $access = Yii::app()->user->level($accessLevel);
            echo $path;
            $this->setPath($path)->checkAccess($access);
            if($access){
                $this->checkAllLinks();
            }
        }
    }
    /**
     * check if $string exists on this page inside H tag
     * @param string $string
     */
    protected function checkTitles($string, $role='')
    {
        $this->assertTrue(
            ($this->page->filter('h1:contains("' . $string . '")')->count() > 0)
            || ($this->page->filter('h2:contains("' . $string . '")')->count() > 0) 
            || ($this->page->filter('h3:contains("' . $string . '")')->count() > 0)
            || ($this->page->filter('h4:contains("' . $string . '")')->count() > 0),
            "View has no H1 with '{$string}' role '{$role}'"
        );
    }
    /**
     * checks if user action (such as form submit) is successfull (has div with .alert-success class)
     * @return boolean
     */
    public function checkSuccess()
    {
        return $this->assertTrue($this->page->filter('div.alert-success')->count() > 0);
    }
    /**
     * checks if links are accessable or not (depends on $mode)
     * @param boolean $mode
     */
    public function checkAllLinks($mode=true)
    {
        $links = $this->page->filter('a')->links();
        foreach($links as $link){
            if(!($uri = $link->getUri()) || !$this->allowedLink($uri)){
                continue;
            } ;
            $this->setPath($uri, $mode)->checkAccess($mode);
        }
    }
    /**
     * checks if uri is allowed to follow
     * @param string $uri url to check
     * @return boolean whether link is allowed
     */
    protected function allowedLink($uri)
    {
        foreach($this->linkFilter as $filter){
            if(strpos($uri, $filter)){
                return false;
            }
        }
        return true;
    }
    /**
     * checks of link is accessable
     * @param boolean $mode
     * @return \FunctionalTesting
     */
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

    
    /* CONSTRUCT */

    public function __construct()
    {
        Yii::setPathOfAlias('webroot', dirname(__FILE__).'/../../');
        $this->setUser(false);
        $this->client = static::createClient();
    }
    /**
     * sets path to this
     * @param string $path 
     * @param string $method // GET or POST etc
     * @return \FunctionalTesting
     */
    public function setPath($path, $method = 'GET')
    {
        $this->path = $path;
        try{
            $this->page = @$this->client->request($method, $this->path);
        }catch(Exception $e){
            $this->page = false;
            $this->error = "{$e->getCode()} {$e->getMessage()}";
        };
        return $this;
    }

    /* GETTERS AND SETTERS */
    
    /**
     * gets models fixtures 
     * @return array
     */
    public function getModels()
    {
        $s = DIRECTORY_SEPARATOR;
        return array(
            'users' => include_once(dirname(__FILE__) . "{$s}fixtures{$s}_users.php")
        );
    }
    /**
     * sets Yii user
     * @uses WebUser::setNodel()
     * @param User $user
     */
    public function setUser($user)
    {
        $this->user = $user;
        Yii::app()->user->setModel($user);
    }
    
    public function __get($attribute)
    {
        if(isset($this->$attribute)){
            return $this->$attribute;
        }
        $methodName = 'get' . ucfirst($attribute);
        if(method_exists($this, $methodName)){
            return $this->$methodName();
        }else{
            throw new Exception('Calling undefined method or attribute '. $attribute);
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
