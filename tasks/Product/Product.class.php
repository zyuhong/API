<?php
class Product
{
	const SQL_SELECT_ALL_PRODUCT = 'SELECT * FROM tb_yl_product WHERE tag = %d ORDER BY id desc';
	const SQL_SELECT_PRODUCT = 'SELECT * FROM tb_yl_product where type = %d ORDER BY id desc';
	const SQL_SELECT_ALL_TAG = 'SELECT tag FROM tb_yl_product GROUP BY tag';	
	
	public $strProduct;
	public $nWidth;
	public $nHeight;
	
	public function __construct()
	{
		$this->strProduct = '';
		$this->nWidth 	  = 0;
		$this->nHeight 	  = 0;		
	}
	
	public function setParam($strProduct, $nWidth, $nHeight)
	{
		$this->strProduct = $strProduct;
		$this->nWidth 	  = $nWidth;
		$this->nHeight 	  = $nHeight;
	}
	
	public function setProductByDb($row)
	{
		$this->strProduct = isset($row['product'])?$row['product']:'';
		$this->nWidth = (int)(isset($row['width'])?$row['width']:0);
		$this->nHeight 	  = (int)(isset($row['height'])?$row['height']:0);
	}
	
	public function getSelectProductSql($nType)
	{
		if($nType == 1){			
			return self::SQL_SELECT_ALL_TAG;
		}	
		$strSql = sprintf(self::SQL_SELECT_PRODUCT, $nType);
		return $strSql;
	}
	public function getProductByTagSql($tag)
	{
		return sprintf(self::SQL_SELECT_ALL_PRODUCT, $tag);
	}	
}