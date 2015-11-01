<?php
class SelectedMetadataDAO extends AtomFeedDAO {
	// research parameters
	private $metadataIds;
	public function __construct($serviceAccess) {
		parent::__construct ( $serviceAccess );
		$this->metadataIds = array ();
	}
	public function __sleep() {
		return array (
				'metadataIds'
		);
	}
	protected function acquireXMLString() {
		return $this->serviceAccess->getMetadataList( null, null, staticFilters, 0, 100 );
	}
	public function getStaticFilters() {
		$filters = array ();
		foreach ( $this->metadataIds as $value ) {
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

