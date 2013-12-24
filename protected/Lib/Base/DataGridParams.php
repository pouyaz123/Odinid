<?php

namespace Base;

/**
 * Description of DataGridParams
 *
 * @author Abbas Hashemian <tondarweb@gmail.com>
 */
class DataGridParams {

	/**
	 * @var DataGrid relative datagrid to this datagrid params
	 */
	public $DataGrid = NULL;

	/**
	 * @var string sort SQL part. default 1 ASC
	 */
	public $Sort = ' 1 ASC ';
	public $SortColumn = '1';
	public $SortOrder = 'ASC ';
	#
	public $HasFilter = false;

	/**
	 * @var string this is the SQL where clause. default 1=1
	 */
	public $SQLWhereClause = ' 1=1 ';
	#
	public $AllRowsCount = NULL;
	public $RowsPerPage = NULL;
	public $TotalPages = NULL;
	public $PageNo = NULL;
	#
	public $RowID = NULL;
//	public $SubGridID = NULL;
//	public $NPage = NULL;
//	public $TotalRows = NULL;
	/**
	 * @var int/str the time passed to the request by jQGrid (for IE browsers not to cache the request) (default value nd)
	 */
	public $nd = NULL;

	/**
	 * @param int &$LimitStartIdx
	 * @param int &$LimitLength
	 * @return str paging mysql limit str WITHOUT LIMIT keyword (e.g. 12, 50)
	 */
	public function QueryLimitParams($AllRowsCount, &$LimitStartIdx = NULL, &$LimitLength = NULL) {
		$PN = &$this->PageNo;
		$RPP = &$this->RowsPerPage;
		$TP = &$this->TotalPages;
		$this->AllRowsCount = $AllRowsCount = intval($AllRowsCount);
		if (!$RPP)
			return $AllRowsCount;
		$TP = ceil($AllRowsCount / $RPP);
		if ($PN > $TP)
			$PN = $TP;
		$LimitLength = $RPP < $AllRowsCount ? $RPP : $AllRowsCount;
		$LimitStartIdx = ($PN * $RPP) - $RPP;
		$LimitStartIdx = $LimitStartIdx < 0 ? 0 : $LimitStartIdx;
		return ' ' . ($LimitStartIdx ? "$LimitStartIdx, " : '') . "$LimitLength ";
	}

}
