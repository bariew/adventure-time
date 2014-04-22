<?php
/**
 * behavior takes file from $model->{$this->field} fileUpload post
 * 1. creates urls and paths, using $this->path array or creates default path /files/$itemName/$modelId/$fileName
 * 2. processes images by params from $this->fileFields with PHPThumb (resize, crop etc)
 * 3. saves urls for related files and thumbs to owner Database table
 * 4. provides info about path to files by $this->field($field) (usefull if we do not use Database)
 * 5. removes files from disk and database on new file upload or owner delete
 */
class AttachedFileBehavior extends CActiveRecordBehavior
{
    public $field = false;
	public $path = false; // array('image'=>'/path/to/image.png', 'thumb1'=>'/path/to/thumb1.jpg'), 
	public $fileName;
    public $tempName;
	public $fileFields = array(); // like array('icon'    => array('resize', 164, 164)),
	public $db = true; // save fields to dbase
    
    
    /*    SAVE    */
    
    /**
	 * delete old files and save and process new if uploaded
	 */
    public function afterSave($event)
    {
        parent::afterSave($event);
        if($file = CUploadedFile::getInstance($this->owner, $this->field)){
           $this->fileName = $file->name;
           $this->tempName = $file->tempName;
        }else if(!$this->tempName || !$this->fileName){
            return true;
        }
		$this->deleteFiles();
		$attributes = $this->saveFiles();
		if($this->db)
	        $this->owner->findByPk($this->owner->primaryKey)->saveAttributes($attributes);
    }

    /**
	 * general function - creates paths, urls, processes images 
	 * and returns resulting array($attributeName1 => $url1, $attributeName2 => $url2, $attributeName3 => $url3, ... )
	 */
	private function saveFiles()
	{
		$path = $this->createPath();
		$result = array($this->field => substr($path, strcmp(Yii::getPathOfAlias('webroot'), $path)));
		if($this->fileFields)
			$result = array_merge($result, $this->processImages($path));
		// either save file and field to db or not
		if(!$this->db || $this->owner->hasAttribute($this->field) && !file_exists($path)){
			copy($this->tempName, $path);
		}
		return $result;
	}

    public function importUrl($url)
    {
        $this->tempName = $url;
        $pathArray = explode('/', $url);
        $this->fileName = urldecode(end($pathArray));
    }
    /* PROCESS IMAGES */
    
	/** processes file from $path with PHPThumb methods (resize, crop etc)
	 * written in $this->fileFields params,
	 * saves and returns $result - url for saving in db
	 */ 
    private function processImages($path)
    {
    	$result = array();
        require_once Yii::getPathOfAlias('webroot'). "/protected/vendors/phpthumb/ThumbLib.inc.php";
        foreach($this->fileFields as $attribute=>$allParams){
            $file = PhpThumbFactory::create($this->tempName);
			if(!is_array($allParams) || !is_array(reset($allParams)))
				$allParams = array($allParams);
			foreach($allParams as $params){
	            if(!$method = array_shift($params))
	                continue;
	            call_user_func_array(array($file, $method), $params);
	            $path = $this->createPath($attribute);
	            $file->save($path);
				$file = PhpThumbFactory::create($path);
			}

            $result[$attribute] = substr($path, strcmp(Yii::getPathOfAlias('webroot'), $path));
        }
		return $result;
    }
    
    /*  ATTRIBUTES AND LISTS  */
	/**
	 * returns attribute name for general file field (not thumbs). 
	 * If $this->field is not set in owner behavior attributes,
	 * we take behavior name as file attribute name
	 */
	public function getField()
	{
		if($this->field !== false)
			return $this->field;
		foreach(array_keys($this->owner->behaviors) as $name){
			if($this->owner->$name === $this)
				$this->field = $name;
		}
		return $this->field;
	}

	/** 
	 * creates an array from owner file attributes values 
	 */
	public function getAsArray()
	{
		$result = array($this->field => $this->field($this->field));
		foreach($this->fileFields as $field => $params)
			$result[$field] = $this->field($field);
		return $result;
	}
	
	// returns url for owner attribute $field
	public function field($field)
	{
		return $this->db 
			? (isset($this->owner->$field)  
				? $this->owner->$field : false
			)
			: $this->getPath($field);
	}
    
    
    
    /*    PATH     */
    
	/**
	 * creates path - folder by folder with rights 0777
	 */
	public function createPath($prefix='')
	{
		$s = DIRECTORY_SEPARATOR;
		$path = Yii::getPathOfAlias('webroot');
		foreach(explode($s, $this->getPath($prefix)) as $folder){
			if(!$folder)
				continue;
			$path .= $s . $folder;
			if(!strpos($folder, '.') && !file_exists($path))
				mkdir($path, 0777);
		}
		return $path;
	}
	/** actually returns 'url' part of path, without webroot
	 *  uses attribute name as $prefix to change original $this->fileName
	 *  if path is set in owner behavior attributes returns that path
	 */
	public function getPath($prefix = '')
	{
		if(@$this->path[$prefix])
			return $this->path[$prefix];
		if($prefix == $this->getField()) 
			$prefix = '';
		$s = DIRECTORY_SEPARATOR;
		return $s . 'files'
			. $s . get_class($this->owner) 
			. $s . $this->owner->primaryKey
			. $s . $prefix . $this->fileName;
	}
    
    /*    DELETE   */
    
    public function afterDelete($event)
    {
        parent::afterDelete($event);
		$this->deleteFolder();
    }
	
	public function deleteFolder()
	{
		$path = Yii::getPathOfAlias('webroot.files.'.get_class($this->owner).'.'.$this->owner->primaryKey);
		exec("rm -rf $path"); 
	}
	// removes all files related to fileFields (image, thumb1, thumb2 etc)
	public function deleteFiles()
	{
		$attributes = array();
		$fields = array($this->field);
		if($this->fileFields){
			$fields = array_merge($fields, array_keys($this->fileFields));
		}
		foreach($fields as $field){
			if(isset($this->owner->$field)){
				$attributes[$field] = '';
			}
			$this->deleteFile($field);
		}
		if($attributes && ($model = $this->owner->findByPk($this->owner->primaryKey))){
			$model->saveAttributes($attributes);
		}
		
		return true;
	}
	// removes file by its field name - $field ('thumb1, image etc')
	public function deleteFile($field)
	{
		$path = Yii::getPathOfAlias('webroot');
		if(!$url = $this->field($field))
			return false;
    	if(file_exists($path . $url))
			unlink($path . $url);
		return true;
	}
}