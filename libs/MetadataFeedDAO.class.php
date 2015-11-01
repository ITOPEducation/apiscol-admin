<?php
class MetadataFeedDAO extends AtomFeedDAO {
	private $start;
	private $rows;
	private $query;
	// research parameters
	private $staticFilters;
	private $dynamicFilters;
	private $metadataIds;
	public function __construct($serviceAccess) {
		parent::__construct ( $serviceAccess );
		$this->start = 0;
		$this->rows = 10000;
		$this->query = "";
		$this->staticFilters = array ();
		$this->dynamicFilters = array ();
		$this->metadataIds = array ();
	}
	public function __sleep() {
		return array (
				'staticFilters',
				'dynamicFilters',
				'metadataIds',
				'serviceAccess' 
		);
	}
	protected function acquireXMLString() {
		return $this->serviceAccess->getMetadataList ( isset ( $this->query ) ? $this->query : null, $this->dynamicFilters, $this->staticFilters, $this->metadataIds, $this->start, $this->rows );
	}
	public function getStaticFilters() {
		$filters = array ();
		foreach ( $this->staticFilters as $value ) {
			$filters [$value] = str_replace ( "::", "=", $value );
		}
		return $filters;
	}
	public function addStaticFilter($filter) {
		if (! in_array ( $filter, $this->staticFilters ))
			$this->staticFilters [] = $filter;
	}
	public function getDynamicFilters() {
		$filters = array ();
		foreach ( $this->dynamicFilters as $value ) {
			$filters [$value] = substr ( $value, strripos ( $value, ':' ) + 1 );
		}
		return $filters;
	}
	public function addDynamicFilter($filter) {
		if (! in_array ( $filter, $this->dynamicFilters ))
			$this->dynamicFilters [] = $filter;
	}
	public function setMetadataIds(array $metadataIds) {
		$this->metadataIds = $metadataIds;
	}
	public function clearFilters($filter) {
		if ($filter == "all") {
			$this->staticFilters = array ();
			$this->dynamicFilters = array ();
		} else {
			if (($key = array_search ( $filter, $this->staticFilters )) !== false) {
				array_splice ( $this->staticFilters, $key, 1 );
			}
			if (($key = array_search ( $filter, $this->dynamicFilters )) !== false) {
				array_splice ( $this->dynamicFilters, $key, 1 );
			}
		}
	}
	public function setQuery($query) {
		if ($query == "~")
			unset ( $this->query );
		else
			$this->query = $query;
	}
	public function getQuery() {
		if (isset ( $this->query ))
			return $this->query;
		else
			return "";
	}
	public function setStart($start) {
		$this->start = $start;
	}
	public function getStart() {
		return $this->start;
	}
	public function setRows($rows) {
		$this->rows = $rows;
	}
	public function getRows() {
		return $this->rows;
	}
	public function getCount() {
		assert ( $this->isBuilt );
		return $this->xpath->query ( "/atom:feed/apiscol:length" )->item ( 0 )->textContent;
	}
	public function markSelectedMetadata(array $selectedMetadataIds) {
		foreach ( $this->document->childNodes as $feedNode ) {
			if ($feedNode instanceof DOMElement) {
				foreach ( $feedNode->childNodes as $entryNode ) {
					if ($entryNode instanceof DOMElement && $entryNode->nodeName == "entry") {
						$idNodes = $entryNode->getElementsByTagName ( "id" );
						if ($idNodes->length > 0)
							$idNode = $idNodes->item ( 0 );
						$uri = $idNode->nodeValue;
						$rawId = array_pop ( explode ( ':', $uri ) );
						if (in_array ( $rawId, $selectedMetadataIds ))
							$entryNode->setAttribute ( "selected", true );
					}
				}
			}
		}
	}
}

