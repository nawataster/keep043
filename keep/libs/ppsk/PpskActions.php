<?php
// define('_IS_SHOW_DBG_BUFF', true);  //  true / false

/**
 *	Class for ajax actions (handlers) and utils to serve them.
 * @author Constantine Nawata (nawataster@gmail.com)
 */
class PpskActions{

/**
 * controls
 * @param	string $handleResource - string in format `filler:handle_name:class_name:filler`.
 * 			Lenth of `filler:handle_name:class_name` string is 50 symbols.
 * 			Max length of `handle_name:class_name` string is 48 symbols.
 * @param	mixed $value
 * @return	object xajax response
 */


//TODO: Think to present $handleResource and $value as syhpered JSON array. (or maybe as syhpered serialized  PHP array)

	public function onHandler( $handleResource = NULL, $value = NULL ){
		global $log_obj, $gl_PpskPath;
		$objResponse = new xajaxResponse();

		$err_access	= "location.href='".$gl_PpskPath."access.php'";

		if( !(bool)$handleResource ){
			$objResponse->script( $err_access );
			return $objResponse;
		}

		if( _PPSK_IS_CIPHER ){
			$cipher_obj	= new sipherManager( $_SESSION['cipher_base'], $_SESSION['cipher_key'] );
			$handleResource	= $cipher_obj->decipherString( $handleResource );
		}

		$res_arr	= explode( ':', $handleResource );
		if( !( $res_arr && ( count( $res_arr ) == 4 ) ) ){
			$objResponse->script( $err_access );
			return $objResponse;
		}

		list( $filler1, $hndlName, $className, $filler2 ) = $res_arr;
		if( !( $filler1 == $filler2 && $className && class_exists( $className ) ) ){
			$objResponse->script( $err_access );
			return $objResponse;
		}

		$obj = new $className( NULL );	//	Value NULL is set becouse of presense of $Owner paramenter
		if( !( $hndlName && method_exists( $obj, $hndlName ) ) ){
			$objResponse->script( $err_access );
			return $objResponse;
		}

		try{
			$obj->$hndlName( $objResponse, $value );  //  Parameter $objResponse is mandatory in handler and must be a reference (starts with &)
		}catch( Exception $e ){
			$log_obj->putLogInfo( $e->getMessage() );
		}
		return $objResponse;
	}
//______________________________________________________________________________

}//	Class end

// Example to put array data to debug buffer:
//$_SESSION[ 'debug_info' ]	= getArrContent( $dbg_data, 'dbg_data' );
