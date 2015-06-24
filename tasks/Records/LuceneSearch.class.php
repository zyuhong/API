<?php
require_once 'tasks/Records/Record.class.php';

class LuceneSearch extends Record
{	
	public $width;
	public $height;
	public $keyword;
	public function __construct()
	{	
		parent::__construct();
		$this->width 	= 0;
		$this->height 	= 0;
		$this->keyword  = '';
	}
	
	public function setRecord()
	{	
		$this->width 	= isset($_GET['width'])?$_GET['width']:720;
		$this->height 	= isset($_GET['height'])?$_GET['height']:1280;
		$this->type		= isset($_GET['type'])?$_GET['type']:0;
		$this->subtype  = (int)(isset($_GET['subtype'])?$_GET['subtype']:0);
		$this->keyword  = isset($_GET['keyword'])?$_GET['keyword']:'';
		parent::setParam();
	}
}