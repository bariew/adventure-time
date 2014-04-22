<?php
class PageItem extends ActiveRecord
{
    /* ATTRIBUTES AND LISTS */
    
    public $descendants = array();
    public static $currentPage = false;
	
    public function getSeoAttributes()
    {
        return array(
            'title'             => $this->title,
            'page_title'        => $this->page_title,
            'page_description'  => $this->page_description,
            'page_keywords'     => $this->page_keywords,
        );
    }

    public function rules()
    {
        return array(
            array('title', 'required'),
            array('brief, content, page_description', 'safe'),
            array('visible', 'numerical', 'integerOnly' => true),
            array('name, label, layout, page_keywords, page_title', 'length', 'max' => 255),
        );
    }
    
    public function relations()
    {
        return array(
            'parent'    => array(self::BELONGS_TO, 'PageItem', 'pid'),
            'children'  => array(self::HAS_MANY, 'PageItem', 'pid'),
        );
    }
    
    public function behaviors()
    {
        return array(
            'tree' => array(
                'class'         => 'ext.artree.ARTreeBehavior',
                'actionPath'    => '/page/pageItem/update'
            ),
        );
    }
    
    public function scopes()
    {
        return array(
            'visible'           => array('condition'=>"visible = 1", "order"=>"rank"),
            'visibleChildren'   => array('condition'=>"visible = 1 AND pid = $this->id", "order"=>"rank"),
        );
    }
	
    public static function getCurrentPage()
    {
        return (self::$currentPage !== false)
            ? self::$currentPage
            : self::$currentPage = self::model()->findByAttributes(
                    array('url'=>$_SERVER['REQUEST_URI']), 
                    array('order'=>'id DESC')
            );
    }
}