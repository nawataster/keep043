<?php
define('_IS_SHOW_DBG_BUFF', true);  //  true / false

/**
 *	Class for ajax actions (handlers) and utils to serve them.
 * @author Constantine Nawata (nawataster@gmail.com)
 */
class PpskActions{
	/**
	 * @param	string $handleResource - string in format `filler:handle_name:class_name:filler`.
	 * 			Lenth of `filler:handle_name:class_name` string is 50 symbols.
	 * 			Max length of `handle_name:class_name` string is 48 symbols.
	 * @param	mixed $value
	 * @return	object xajax response
	 */
	public function corePpskHandler( $handleResource = NULL, $value = NULL ){
		global $log_obj, $gl_PpskPath;
		$objResponse = new xajaxResponse();

		if( !( $handleResource	!= NULL && $handleResource != _EMPTY ) ){
			$objResponse->addScript( "location.href = '".$gl_PpskPath."access.php'" );
			return $objResponse;
		}

		if( _PPSK_IS_CIPHER ){
			$cipher_obj	= new sipherManager( $_SESSION[ 'cipher_base' ], $_SESSION[ 'cipher_key' ] );
			$handleResource	= $cipher_obj->decipherString( $handleResource );
		}

		$res_arr	= explode( ":", $handleResource );
		if( !( $res_arr && ( count( $res_arr ) == 4 ) ) ){
			$objResponse->addScript( "location.href = '".$gl_PpskPath."access.php'" );
			return $objResponse;
		}

		list( $filler1, $hndlName, $className, $filler2 ) = $res_arr;
		if( !( $filler1 == $filler2 && $className && class_exists( $className ) ) ){
			$objResponse->addScript( "location.href = '".$gl_PpskPath."access.php'" );
			return $objResponse;
		}

		$obj = new $className( NULL );	//	Value NULL is set becouse of presense of $Owner paramenter
		if( !( $hndlName && method_exists( $obj, $hndlName ) ) ){
			$objResponse->addScript( "location.href = '".$gl_PpskPath."access.php'" );
			return $objResponse;
		}

		try{
			$obj->$hndlName( $objResponse, $value );  //  Parameter $objResponse is mandatory in handler and must be a reference (starts with &)

			if( _IS_SHOW_DBG_BUFF && isset( $_SESSION[ 'debug_info' ] ) ){
				$objResponse->addAssign( 'debug_buffer', 'innerHTML', $_SESSION[ 'debug_info' ] );
				unset( $_SESSION[ 'debug_info' ] );
			}else{
				$objResponse->addAssign( 'debug_buffer', 'innerHTML', '' );
			}

		}catch( Exception $e ){
			$log_obj->putLogInfo( $e->getMessage() );
		}
		return $objResponse;
	}
	//--------------------------------------------------------------------------------------------------

}//	Class end

// Example to put array data to debug buffer:
//$_SESSION[ 'debug_info' ]	= getArrContent( $dbg_data, 'dbg_data' );
?>