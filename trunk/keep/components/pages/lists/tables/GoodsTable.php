<?php
// require_once( $gl_pagesPath.'managers/AddEditManagerPane.php' );

class GoodsTable extends PTable{

	public function __construct( $Owner=NULL, $isHndl=FALSE, $isInitView=TRUE ){
		$this->setProperties();
		parent::__construct( $Owner );
		if( $isInitView ){ $this->initHtmlView( $isHndl ); }
	}
//______________________________________________________________________________

	/**
	 * sets properties of table in constructor
	 * @param	string $instName - uniq name for object instance.
	 * @return void
	 */
	private function setProperties(){
		$this->mLevels			= array( 'manager' );
		$this->mSourceDbTable	= 'goods_view';
		$this->mTargetDbTable	= 'goods';
		$this->mSelectorColor	= '#EDD3EA';
		$this->mPgLen			= 17;
		$this->mMaxGrPg			= 10;

		$this->mIsFixHeight		= TRUE;

		$this->mColumns	= array(
			array(
				'field'		=> 'name',
				'name'		=> _PNAME1,
				'ttl_css'	=> 'goodsTblNameTtlTd',
				'sll_css'	=> 'goodsTblNameClTd',
				'bg_clr'	=> '#FFF7E2',
				'is_sort'	=> TRUE
			),

			array(
				'field'		=> 'cku',
				'name'		=> _GOOD_ARTICLE,
				'ttl_css'	=> 'goodsTblArticleTtlTd',
				'sll_css'	=> 'goodsTblArticleClTd',
				'bg_clr'	=> '#FFF7E2',
				'is_sort'	=> TRUE
			),

			array(
				'field'		=> 'cats',
				'name'		=> _GOOD_CATS,
				'ttl_css'	=> 'goodsTblCatsTtlTd',
				'sll_css'	=> 'goodsTblCatsClTd',
				'bg_clr'	=> '#FFF7E2',
				'is_sort'	=> FALSE
			)

		);

		$this->setSearchFields( array( 'name', 'cku' ));
		$this->mPaneClassName	= 'AddEditGoodsPane';
	}
//______________________________________________________________________________

	public function deleteRowHandler( &$objResponse, $nullValue ){
		$auth_obj = new Authentication();

		if( $auth_obj->isGrantAccess( $this->mLevels ) ){
			$class	= get_class( $this );
			$db_obj	= new KeepDbl( $this );
			$result	= $db_obj->deleteDbViewsForManager( $_SESSION[ 'tables' ][ $class ][ 'line_id' ] );
			if( $result['is_error'] ){
				$this->showAlertHandler( $objResponse, array( 'message' => $result[ 'description' ], 'focus' => $result[ 'focus_id' ] ) );
			}else{
				parent::deleteRowHandler( $objResponse, $nullValue );
			}
		}
	}
//______________________________________________________________________________

	public function __destruct(){
		parent::__destruct();
	}
//______________________________________________________________________________

}//	Class end
