<?php
/**
 * @author Adhikari Shrawan
 * VERSION : 0.1
 * Email : shrawan.adh@gmail.com
 * Date : JUN 2 11:33
 * Copyright (c) Real Time Solutions
 * All rights reserved.
 *
 * Redistribution and use in source and binary forms, with or without modification,
 * are permitted provided must retain the above copyright notice .
 *
 * No Warranties
 *
 * The copyright owner or author will not be liable for any kind of damages.
 */

class XHBuilder{

	/*
	 * holds the html tags
	 */
	protected $__tags=array();


	/*
	 * holds the tags attributes
	 */
	protected $__attributes=array();


	/*
	 * holds the text of the tags
	 */
	protected $__texts=array();


	/*
	 * holds the array index of the tag which has been closed
	 */
	protected $__closes=array();


	/*
	 * holds the closing tags closed by using method close()
	 * used in close() and getHtml() method
	 */
	protected $__closingTag=array();



	/*
	 * holds the previous tag index of the current closed tag index
	 * use in close() text() __call()
	 */
	protected $__closeNow=null;



	/*
	 * holds the last array index of $this->__tags
	 */
	protected $__now=0;


	/*
	 *holds the last array index of $this->__tags
	 *
	 *when we close the tag $this->__now is set to previous tag index
	 *so to get the last $this->__tags index we set it
	 */
	protected $__current=null;


	/*
	 * used in close()
	 * holds the previous tag indx of closing tag
	 */
	protected $__previous=null;


	/*
	 * holds the tags which has been defined using insert() method
	 */
	protected $__forInsert=array();


	/*
	 * holds the array tag index that has been found searching
	 */
	protected $__foundindex=null;




	/**
	 * @param string $tag
	 * @return $this
	 */
	public function __construct($tag){
		$this->setTag($tag);
		return $this;
	}



	/**
	 * @param string $tag
	 * @return $this
	 */
	public function __get($tag){
		$aa=$tag;
		$this->setTag($tag);
		return $this;
	}





	/**
	 * sets the attributes of the tag
	 *
	 * @param string $method
	 * @param array $args
	 * @return $this
	 */
	public function __call($method,$args){

		if(count($args)===0){
			return $this;
		}

		/*
		 * index refers to the current tag being processed
		 */
		if($this->__foundindex!==null){
			$index=$this->__foundindex;
		}else{
			if($this->__closeNow===null){
				$index=$this->__now;
			}else{
				$index=$this->__closeNow;
				if($index<1){
					$index=1;
				}else{
					do{
						/*
						 * go up in the closes tag array and find the near tag not closed
						 */
						if($this->__closes[$index]===0){
							break;
						}
						$index--;
					}while($index>1);

					if($index<1)$index=1;
				}
			}
		}

		$method=trim(strtolower($method));

		if(is_array($index)){

			foreach($index as $arrayIndex){
				if(isset($this->__attributes[$arrayIndex][$method])){
					if($args[0]===NULL){
						unset($this->__attributes[$arrayIndex][$method]);
					}else{
						$this->__attributes[$arrayIndex][$method].=' '.$args[0];
					}
				}else{
					$this->__attributes[$arrayIndex][$method]=$args[0];
				}
			}

		}else{
			if(isset($this->__attributes[$index][$method])){
				if($args[0]===NULL){
					unset($this->__attributes[$index][$method]);
				}else{
					$this->__attributes[$index][$method].=' '.$args[0];
				}
			}else{
				$this->__attributes[$index][$method]=$args[0];
			}
		}

		return $this;
	}



	/**
	 * set tag
	 *
	 * @param string $tag
	 * @return $this
	 */
	final public function setTag($tag=null){

		if(!trim($tag)){
			return $this;
		}

		/*
		 * by default __current is null
		 * when we close the tag this is set to length of __tags
		 */
		if($this->__current!==null){
			$this->__now=$this->__current;
			$this->__current=null;
		}

		$this->__now=$this->__now+1;

		$this->__closes[$this->__now]=0;
		$this->__previous=null;
		$this->__closeNow=null;
		$this->__foundindex=null;

		$tag=trim(strtolower($tag));
		$this->__tags[]=array($this->__now=>$tag);
		return $this;
	}


	/**
	 * sets the text for the tag
	 * if $pureHtml is false $text is a string, if false text is html string
	 *
	 * @param string $text
	 * @param boolean $pureHtml
	 * @return $this
	 */
	final public function text($text=null,$pureHtml=FALSE){

		/*
		 * index refers to the current tag being processed
		 */
		if($this->__foundindex!==null){
			$index=$this->__foundindex;
		}else{
			if($this->__closeNow===null){
				$index=$this->__now;
			}else{
				$index=$this->__closeNow;
				if($index<1){
					$index=1;
				}else{
					do{
						/*
						 * go up in the closes tag array and find the near tag not closed
						 */
						if($this->__closes[$index]===0){
							break;
						}
						$index--;
					}while($index>1);
					if($index<1)$index=1;
				}
			}
		}


		/*if($text==null){
			$this->__texts[$index]='';
			return $this;
		}*/

		if($pureHtml===TRUE){
			$txt=(string)$text;
		}else{
			$txt=htmlentities((string)$text,ENT_QUOTES);
		}


		if(is_array($index)){
			foreach($index as $arrayIndex){
				if(isset($this->__texts[$arrayIndex])){
					if($text===null){
						unset($this->__texts[$arrayIndex]);
					}else{
						$this->__texts[$arrayIndex].=$txt;
					}
				}else{
					if($text===null){
						unset($this->__texts[$arrayIndex]);
					}else{
						$this->__texts[$arrayIndex]=$txt;
					}
				}
			}
		}else{
				if($text==null){
					$txt='';
				}
			if(isset($this->__texts[$index])){
				$this->__texts[$index].=$txt;
			}else{
				$this->__texts[$index]=$txt;
			}
		}


		/*if(isset($this->__texts[$index])){
			$this->__texts[$index].=$txt;
		}else{
			$this->__texts[$index]=$txt;
		}*/


		return $this;
	}






	/**
	 * sets the closing tag of the tag
	 *
	 * @return $this
	 */
	final public function close(){
		/*
		 * resert foundindex
		 */
		$this->__foundindex=null;



		/*
		 * store the tags array last in index
		 */
		$this->__current=count($this->__tags);



		/*
		 * previous is set to null when we close a tag
		 *
		 * previous is set when we close the tag
		 * it hold the previous tag index after current tag is closed
		 *
		 * previous is reset when we add a new tag
		 *
		 */
		if($this->__previous===null){
			$this->__previous=$this->__now;

			/*
			 * close tag lookup array
			 * 0 means its has not been closed with close()
			 * 1 means it has to be closed with close()
			 * 2 means it has been processed for close()
			 */
			$this->__closes[$this->__now]=1;
		}else{

			/*
			 * this is actually done to find the tag which has been closed
			 * this occures when we use repetative close()
			 * ie . ->close()->close()->close()
			 *
			 *
			 * loop until we find the tag index requested to be closed
			 */
			do{
				$this->__previous=$this->__previous-1;
				if($this->__previous<1){
					$this->__previous=0;
					break;
				}
			}while($this->__closes[$this->__previous]!==0);
		}

		$this->__closes[$this->__previous]=1;

		if($this->__previous===0){
			return $this;
		}

		/*
		 * now close all the tag requested to be closed
		 */
		if($this->__previous===1){


				if(isset($this->__closingTag[$this->__now])){
					array_push($this->__closingTag[$this->__now],1);

				}else{
					$this->__closingTag[$this->__now] =array();
					array_push($this->__closingTag[$this->__now],1);
				}



				/*
				* when a tag is closed set (processed tag) the close tag lookup array to 2
				* means its already processed
				*/

				$this->__closes[$this->__previous]=2;

				/*
				* points to previous tag of the current tag
				*/
				$this->__closeNow=$this->__previous;

		}else{
			$i=$this->__previous;

			/*
			 * loop until we find and process all the tag to be closed
			 */
			do{
				/*
				 * means this tag has be to closed
				 */
				if($this->__closes[$i]===1){
					if(isset($this->__closingTag[$this->__now])){

					   	array_push($this->__closingTag[$this->__now],$i);

						/*
						 * when a tag is closed set (processed tag) the close tag lookup array to 2
						 * means its already processed
						 */
						$this->__closes[$i]=2;

						/*
						 * points to previous tag of the current tag
			 			*/
						$this->__closeNow=$i-1;
					}else{

							$this->__closingTag[$this->__now] =array();
							array_push($this->__closingTag[$this->__now],$i);

						$this->__closes[$i]=2;

						/*
						 * points to previous tag of the current tag
			 			*/
						$this->__closeNow=$i-1;
					}
				}
			  $i--;
			}while($i!==0);
		}

		return $this;
	}



	/**
	 * checks if the tag has closing tag or not
	 *
	 * @param string $tag
	 * @return boolean
	 */
	final public function hasClosingTag($tag){
		$single=array('img','hr','input','area','br','base','col');
		if(in_array($tag,$single)){
			return FALSE;
		}else{
			return TRUE;
		}
	}



	/**
	 * insert another htmlobject inside a tag
	 *
	 *
	 * @param object $objOfHtml
	 * @return $this
	 */
	final public function insert($objOfHtml,$inEnd=FALSE,$hanyadikHelyre=null){
		if($objOfHtml instanceof HTML){

			/*
			 * index refers to the current tag being processed
			 */
			if($this->__foundindex!==null){
				$index=$this->__foundindex;
			}else{
				if($this->__closeNow===null){
					$index=$this->__now;
				}else{
					$index=$this->__closeNow;
					if($index<1){
						$index=1;
					}else{
						do{
							/*
							 * go up in the closes tag array and find the near tag not closed
							 */
							if($this->__closes[$index]===0){
								break;
							}
							$index--;
						}while($index>1);
						if($index<1)$index=1;
					}
				}
			}
			if(is_array($index)){
				foreach($index as $arrayIndex){
					if(isset($this->__forInsert[$arrayIndex])){
						array_push($this->__forInsert[$arrayIndex],array($objOfHtml,$inEnd));
					}else{
						$this->__forInsert[$arrayIndex]=array();
						array_push($this->__forInsert[$arrayIndex],array($objOfHtml,$inEnd));
					}
				}
			}else{
				if(isset($this->__forInsert[$index])){
					if($hanyadikHelyre !== null)
					{
						array_splice($this->__forInsert[$index],$hanyadikHelyre,0,array(array($objOfHtml,$inEnd)));
					}
					else
					{
						array_push($this->__forInsert[$index],array($objOfHtml,$inEnd));
					}
				}else{
					$this->__forInsert[$index]=array();
					if($hanyadikHelyre !== null)
					{
						array_splice($this->__forInsert[$index],$hanyadikHelyre,0,array(array($objOfHtml,$inEnd)));
					}
					else
					{
						array_push($this->__forInsert[$index],array($objOfHtml,$inEnd));
					}
				}
			}


			/*if(isset($this->__forInsert[$index])){
				array_push($this->__forInsert[$index],array($objOfHtml,$inEnd));
			}else{
				$this->__forInsert[$index]=array();
				array_push($this->__forInsert[$index],array($objOfHtml,$inEnd));
			}*/

		}
		return $this;
	}


	/**
	 * find the $findTagIdClass(tagname,id,class)
	 *
	 *  id=#[id]
	 *  class=.[class]
	 *  anyAttribute=&[attributeTyle]
	 *
	 *   find([attributeName],[attributeValue])
	 *
	 *   find(&[attributeType])
	 *
	 *   find(.[className])
	 *
	 *   find(#[idName])
	 *
	 *
	 * i.e: if <div>div1<div> <div>div2</div>
	 *      it would point to the llast div [div2]
	 *
	 * @param string $findTagIdClass
	 * @param string $findValue
	 * @return $this
	 */
	final public function find($findTagIdClass=null,$findValue=null){
		$this->__foundindex=null;

		if(!trim($findTagIdClass)){
			return $this;
		}

		$this->__current=count($this->__tags);


		if($findValue!==null){
			$this->__foundindex=array();
			foreach($this->__tags as $tKey => $tag){
				if(isset($this->__attributes[$tKey+1])){
					foreach($this->__attributes[$tKey+1] as $aType => $aValue){
						if($aType===$findTagIdClass){
							$values=explode(' ',$aValue);
							if(in_array($findValue,$values)){
								//$this->__foundindex=$tKey+1;
								array_push($this->__foundindex,$tKey+1);
							}
						}
					}
				}
			}


		}else{
			    $this->__foundindex=array();
				foreach($this->__tags as $tKey => $tag){

						if(strpos($findTagIdClass,'.')!==0 && strpos($findTagIdClass,'#')!==0 && strpos($findTagIdClass,'&')!==0){
							if($tag[$tKey+1]===strtolower($findTagIdClass)){
								array_push($this->__foundindex,$tKey+1);
							}
						}elseif(strpos($findTagIdClass,'#')===0){
								if(isset($this->__attributes[$tKey+1])){
									foreach($this->__attributes[$tKey+1] as $aType => $aValue){
										if($aType==='id'){
											$values=explode(' ',$aValue);

											/*remove hash(#)*/
											$id=substr($findTagIdClass,1);


											if(in_array($id,$values)){
												//$this->__foundindex=$tKey+1;
												array_push($this->__foundindex,$tKey+1);
											}
										}
									}
								}
						}elseif(strpos($findTagIdClass,'.')===0){
							if(isset($this->__attributes[$tKey+1])){
									foreach($this->__attributes[$tKey+1] as $aType => $aValue){
										if($aType==='class'){
											$values=explode(' ',$aValue);

											/*remove dot(.)*/
											$class=substr($findTagIdClass,1);


											if(in_array($class,$values)){
												//$this->__foundindex=$tKey+1;
												array_push($this->__foundindex,$tKey+1);
											}
										}
									}
								}

						}elseif(strpos($findTagIdClass,'&')===0){
							if(isset($this->__attributes[$tKey+1])){
								foreach($this->__attributes[$tKey+1] as $aType => $aValue){
									/*remove amp%(&)*/
									$attrName=substr($findTagIdClass,1);
									if($aType===$attrName){
										array_push($this->__foundindex,$tKey+1);
									}
								}
							}
						}
					}
				}

		if(is_array($this->__foundindex) && count($this->__foundindex)===0){
			$this->__foundindex=null;
		}

		return $this;
	}


	final public function alterTag($newTagName=null){

	   if(!trim($newTagName)){
			return $this;
		}
			/*
			 * index refers to the current tag being processed
			 */
			if($this->__foundindex!==null){
				$index=$this->__foundindex;
			}else{
				if($this->__closeNow===null){
					$index=$this->__now;
				}else{
					$index=$this->__closeNow;
					if($index<1){
						$index=1;
					}else{
						do{
							/*
							 * go up in the closes tag array and find the near tag not closed
							 */
							if($this->__closes[$index]===0){
								break;
							}
							$index--;
						}while($index>1);
						if($index<1)$index=1;
					}
				}
			}


			if(is_array($index)){
				foreach($index as $arrayIndex){
					$newTagName=strtolower(trim($newTagName));
					$this->__tags[$arrayIndex-1][$arrayIndex]=$newTagName;
				}
			}else{
				$newTagName=strtolower(trim($newTagName));
				$this->__tags[$index-1][$index]=$newTagName;
			}
			/*$newTagName=strtolower(trim($newTagName));
			$this->__tags[$index-1][$index]=$newTagName;*/
			return $this;
	}



	final public function clearAttribute($attributeName=null){

		if(!trim($attributeName)){
			return $this;
		}

		/*
		 * index refers to the current tag being processed
		 */
		if($this->__foundindex!==null){
			$index=$this->__foundindex;
		}else{
			if($this->__closeNow===null){
				$index=$this->__now;
			}else{
				$index=$this->__closeNow;
				if($index<1){
					$index=1;
				}else{
					do{
						/*
						 * go up in the closes tag array and find the near tag not closed
						 */
						if($this->__closes[$index]===0){
							break;
						}
						$index--;
					}while($index>1);

					if($index<1)$index=1;
				}
			}
		}

		$attributeName=trim(strtolower($attributeName));

		if(is_array($index)){

			foreach($index as $arrayIndex){
				if(isset($this->__attributes[$arrayIndex][$attributeName])){
					$this->__attributes[$arrayIndex][$attributeName]='';
				}
			}

		}else{
			if(isset($this->__attributes[$index][$attributeName])){
				$this->__attributes[$index][$attributeName]='';
			}
		}

		return $this;
	}



	/**
	 * return the generated html as string
	 *
	 * @return string
	 */
	final public function getHtml(){
		$__buildHtml='';
		$__lastSegHtml='';

		foreach($this->__tags as $tKey => $tag){

			if(isset($tag[$tKey+1])){


						$__buildHtml.='<'.$tag[$tKey+1];

						if(isset($this->__attributes[$tKey+1])){
							foreach($this->__attributes[$tKey+1] as $aType => $aValue){
								$__buildHtml .=' '.$aType.'="'.$aValue.'"';
							}
						}

						$__buildHtml.=$this->hasClosingTag($tag[$tKey+1])? '>':' />';

						if(isset($this->__texts[$tKey+1])){
							$__buildHtml .=$this->__texts[$tKey+1];
						}


						if(isset($this->__forInsert[$tKey+1])){
							foreach($this->__forInsert[$tKey+1] as $cKey => $value){
								    if($value[1]===FALSE){
								 		$__buildHtml.=$value[0];
								    }
								}
						}

						/*if current tag has been closed using close()*/
						if(isset($this->__closingTag[$tKey+1])){

							foreach($this->__closingTag[$tKey+1] as $closeKey => $closeTag){

								$__insertEnd='';
								if(isset($this->__forInsert[$closeTag])){
									foreach($this->__forInsert[$closeTag] as $cKey => $value){
										    if($value[1]===TRUE){
										 		$__insertEnd.=$value[0];
										    }
										}
								}
								if($this->hasClosingTag($this->__tags[$closeTag-1][$closeTag])===TRUE){
									$__buildHtml .=$__insertEnd.'</'.$this->__tags[$closeTag-1][$closeTag].'>';
								}else{
									$__buildHtml .=$__insertEnd;
								}
							}


						}elseif($this->__closes[$tKey+1]===0){

							$__insertEnd='';
							if(isset($this->__forInsert[$tKey+1])){
									foreach($this->__forInsert[$tKey+1] as $cKey => $value){
										    if($value[1]===TRUE){
										 		$__insertEnd.=$value[0];
										    }
										}
								}

							if($this->hasClosingTag($tag[$tKey+1])===TRUE){
								$__lastSegHtml.=strrev($__insertEnd.'</'.$tag[$tKey+1].'>');
							}else{
								$__lastSegHtml.=strrev($__insertEnd);
							}
					}
			}

		}

		return $__buildHtml.strrev($__lastSegHtml);
	}



	public function __toString(){
		return $this->getHtml();
	}

/* Egg Features*/
	public function findByTag($value)
	{
		$ret = array();

		foreach($this->__forInsert[1] as $val)
		{
			foreach($val[0]->getTag() as $_val)
			{
				if($value == $_val)
				{
					$ret[] = $val[0];
				}
			}
		}

		return $ret;
	}

	public function findByClass($value)
	{
		return $this->findByAttribute("class", $value);
	}

	public function findById($value)
	{
		return $this->findByAttribute("id", $value);
	}

	public function findByName($value)
	{
		return $this->findByAttribute("name", $value);
	}

	public function findByAttribute($attr,$value)
	{
		$ret = array();

		foreach($this->__forInsert[1] as $val)
		{
			foreach($val[0]->getAttributes() as $_key =>$_val)
			{
				if($_key == $attr && $value == $_val)
				{
					$ret[] = $val[0];
				}
			}
		}

		return $ret;
	}

	public function getForInsert()
	{
		return $this->__forInsert;
	}

	public function getAttributes()
	{
		return $this->__attributes[1];
	}

	public function getTag()
	{
		return $this->__tags[0];
	}

	public function addAttributeWithoutValue($name)
	{
		$this->__attributes[1][$name] = "";
	}

	function array_insert($array, $insert, $position = -1) {
		$position = ($position == -1) ? (count($array)) : $position ;
		if($position != (count($array))) {
			$ta = $array;
			for($i = $position; $i < (count($array)); $i++) {
				if(!isset($array[$i])) {
					die(print_r($array, 1)."\r\nInvalid array: All keys must be numerical and in sequence.");
				}
				$tmp[$i+1] = $array[$i];
				unset($ta[$i]);
			}
			$ta[$position] = $insert;
			$array = $ta + $tmp;
			//print_r($array);
		} else {
			$array[$position] = $insert;
		}

		ksort($array);
		return $array;
	}
}


class html extends XHBuilder{
	public function __construct($tagName){
		parent::__construct($tagName);
	}
}

class xml extends XHBuilder{
	public function __construct($tagName){
		parent::__construct($tagName);
	}
}




?>