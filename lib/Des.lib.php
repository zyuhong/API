<?php
/************************************************************
 *  @Author: yangdong 
 *  @Create Time: 2013-07-08
 *************************************************************/
class Des {
	public static function encrypt($str, $key) {
		$size = mcrypt_get_block_size( MCRYPT_DES, MCRYPT_MODE_ECB );
		$str = self::pkcs5Pad( $str, $size );
        $td = mcrypt_module_open(MCRYPT_DES, '', MCRYPT_MODE_ECB, '');
        $iv = mcrypt_create_iv(mcrypt_enc_get_iv_size($td));
        mcrypt_generic_init($td, $key, $iv);
        $data = mcrypt_generic($td, $str);
        mcrypt_generic_deinit($td);
        mcrypt_module_close($td);

		return $data;
	}

	public static function decrypt($str, $key) {
        $decrypted = mcrypt_decrypt(MCRYPT_DES, $key, $str, MCRYPT_MODE_ECB);
        $decrypted = self::pkcs5Unpad($decrypted);

		return $decrypted;
	}

    public static function decrypt_cbc($str, $key) {
        //自定义初始化向量
        $ivArray=array(1, 2, 3, 4, 5, 6, 7, 8);
        $iv=null;
        foreach ($ivArray as $element)
            $iv.=CHR($element);

        $result =  mcrypt_decrypt(MCRYPT_DES, $key, $str, MCRYPT_MODE_CBC, $iv);
        $result = self::pkcs5Unpad( $result );

        return $result;
    }

	private static function pkcs5Pad($text, $blocksize) {
		$pad = $blocksize - (strlen( $text ) % $blocksize);
		return $text . str_repeat( chr($pad), $pad );
	}

	private static function pkcs5Unpad($text) {
		$pad = ord( $text {strlen( $text ) - 1} );
		if($pad > strlen( $text ))
			return false;
		if(strspn( $text, chr( $pad ), strlen( $text ) - $pad ) != $pad)
			return false;
		return substr( $text, 0, - 1 * $pad );
	}
}

