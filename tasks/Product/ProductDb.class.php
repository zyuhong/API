<?php
require_once 'tasks/Product/Product.class.php';
require_once 'tasks/protocol/ProductProtocol.php';
require_once 'lib/DBManager.lib.php';
require_once 'lib/MemDb.lib.php';
require_once 'public/public.php';

class ProductDb extends DBManager
{
	public function __construct()
	{
		$this->_product = new Product();
		
		global $g_arr_db_config;
		$dbConfig = $g_arr_db_config['coolshow'];
		$this->connectMySqlPara($dbConfig);
		
		$this->_memcached = new MemDb();
		global $g_arr_memcache_config;
		$this->_memcached->connectMemcached($g_arr_memcache_config);
	}
	
	private function _getProducts($sql)
	{
		try{
			$rows = $this->executeQuery($sql);
			if($rows === false){
				Log::write("ProductDb::_getProducts():executeQuery()".$sql." error", "log");
				return false;
			}
				
			$count = $this->getQueryCount();
			if ($count == 0){
				return true;
			}
				
			$arrProduct = array();
			foreach($rows as $row){
				$product = new ProductProtocol();
				$product->setProductByDB($row);
				array_push($arrProduct, $product);
			}
			
			return $arrProduct;
		}catch (Exception $e){
			Log::write("ProductDb::_getProducts()exception ".$e->getMessage(), "log");
			return false;
		}
		
		return false;
	}

	private function _getProductTag($sql)
	{
		try{
			$rows = $this->executeQuery($sql);
			if($rows === false){
				Log::write("ProductDb::_getProducts():executeQuery()".$sql." error", "log");
				return false;
			}
				
			$count = $this->getQueryCount();
			if ($count == 0){
				return true;
			}
				
			$arrProduct = array();
			foreach($rows as $row){				
				array_push($arrProduct, $row);
			}
			
			return $arrProduct;
		}catch (Exception $e){
			Log::write("ProductDb::_getProducts()exception ".$e->getMessage(), "log");
			return false;
		}
		
		return false;
	}
	
	public function getProductList($nType)
	{
		try {
			$sql = $this->_product->getSelectProductSql($nType);
			$result = $this->_memcached->getSearchResult($sql);
//			if($result){
//				Log::write("ProductDb::getProductList():getSearchResult()".$sql, "log");
//				return json_encode($result);
//			}
			if(!$nType) $arrProduct = $this->_getProducts($sql);
			if($nType)  $arrProduct = $this->_getProductTag($sql);
			if(!$arrProduct && !$nType){
				Log::write("ProductDb::getProductList()():_getCoolXiusDetail() failed", "log");
				$count = -3; //搜索结果为错误
				return get_rsp_result(false, 'get product list false');
			}
			if(intval($nType) == 1){
				$tempArr = array();
				foreach($arrProduct as $tdata){
					$tag = intval($tdata['tag']);
					if($tag > 0){
						$tagSql  = $this->_product->getProductByTagSql($tag);
						$tResult = $this->_memcached->getSearchResult($tagSql);
						if($tResult) $tempArr[] = $tResult;
						if(!$tResult){
							$tagproduct = $this->_getProducts($tagSql);
							$list = array('tag'=>$tag,'list'=>$tagproduct);
							$getresult = $this->_memcached->setSearchResult($tagSql, $list);
							if(!$getresult){
								Log::write("ProductDb::getProductTagList():setSearchResult() failed", "log");
							}							
							$tempArr[] = $list;
						}
					}
				}
				$arrProduct = $tempArr;
			}
			
			$json_rsp =  array(
				'result'  => true,
				'products' => $arrProduct,
			);
				
			$result = $this->_memcached->setSearchResult($sql, $json_rsp);
			if(!$result){
				Log::write("ProductDb::getProductList():setSearchResult() failed", "log");
			}
		}catch(Exception $e){
			Log::write("ProductDb::getProductList(): excepton error:".$e->getMessage(), "log");
			$count = -1;
			return $this->getFaultResult($count);
		}
		return json_encode($json_rsp);
	}
}