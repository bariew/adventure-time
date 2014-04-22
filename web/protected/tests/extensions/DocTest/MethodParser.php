<?php

class MethodParser extends ReflectionMethod
{
    public $tags = array();
    /**
     * @author Qiang Xue <qiang.xue@gmail.com> 
     */    
	protected function processComment()
	{
		$comment=strtr(trim(preg_replace('/^\s*\**( |\t)?/m','',trim($this->getDocComment(),'/'))),"\r",'');
		if(preg_match('/^\s*@\w+/m',$comment,$matches,PREG_OFFSET_CAPTURE)){
			$meta=substr($comment,$matches[0][1]);
			$this->processTags($meta);
		}
	}
    /**
     * @author Qiang Xue <qiang.xue@gmail.com> 
     */
	protected function processTags($comment)
	{
		$tags=preg_split('/^\s*@/m',$comment,-1,PREG_SPLIT_NO_EMPTY);
		foreach($tags as $tag){
			$segs   = preg_split('/\s+/',trim($tag),2);
			$tagName= $segs[0];
			$param  = isset($segs[1])?trim($segs[1]):'';
            // explode parameter to its type and description parts
            if(in_array($tagName, array('param', 'return'))){
                $arrayParam = explode(' ', $param);
                $param = array(
                    'type'          => array_shift($arrayParam),
                    'description'   => implode(' ', $arrayParam)
                );
            }else if(in_array($tagName, array('example'))){
                $arrayParam = explode('//', $param);
                $param = array(
                    'code'          => trim(@$arrayParam[0]),
                    'result'        => trim(@$arrayParam[1])
                );
            }
            $this->tags[$tagName][] = $param;
		}
	}    
    /* PREPARE */
    
    public function __construct($class , $name)
    {
        parent::__construct($class, $name);
        $this->processComment();
    }
}