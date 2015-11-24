<?php
class MetadataDAO extends AtomDAO {
	const FETCH_CHILDREN = 'FETCH_CHILDREN';
	private $children;
	protected function acquireXMLString() {
		$fetchChildren = (true === $this->getOption ( self::FETCH_CHILDREN ));
		return $this->serviceAccess->getMetadata ( $this->getId (), $fetchChildren );
	}
	public function build() {
		parent::build ();
		$this->rootNameSpaceuri = $this->document->lookupNamespaceUri ( $this->document->namespaceURI );
	}
	public function getContentLink() {
		assert ( $this->isBuilt );
		$contentLink = $this->xpath->query ( "atom:link[@rel='describes'][@type='text/html']/@href" );
		if ($contentLink->length == 0)
			return Model::NO_ANSWER;
		$link = $contentLink->item ( 0 )->value;
		if (strlen ( trim ( $link ) ) == 0)
			return Model::NO_ANSWER;
		return $link;
	}
	public function getIconLink() {
		assert ( $this->isBuilt );
		$iconLink = $this->xpath->query ( "atom:link[@rel='icon']/@href" );
		if ($iconLink->length == 0)
			return "";
		$link = $iconLink->item ( 0 )->value;
		return $link;
	}
	public function getScoLomFrLink() {
		assert ( $this->isBuilt );
		return $this->xpath->query ( "atom:link[@rel='describedby'][@type='application/lom+xml']/@href" )->item ( 0 )->value;
	}
	public function getSnippetLink() {
		assert ( $this->isBuilt );
		return $this->xpath->query ( "apiscol:code-snippet/@href" )->item ( 0 )->value;
	}
	public function delete() {
		assert ( $this->isBuilt );
		return $this->serviceAccess->deleteMetadata ( $this->id, $this->getEtag () );
	}
	public function registerHierarchyData(array $hierarchyData) {
		return $this->serviceAccess->registerHierarchyData ( $this->id, $hierarchyData, $this->getEtag () );
	}
	public function sendRefreshRequest($target) {
		return $this->serviceAccess->createMetadataRefreshRequest ( $target, $this->id, $this->getEtag () );
	}
	public function getChildren() {
		assert ( $this->isBuilt );
		if (! is_array ( $this->children )) {
			$this->buildChildren ();
		}
		return $this->children;
	}
	private function buildChildren() {
		$this->children == array ();
		$this->xpath->registerNamespace ( "apiscol", $this->document->lookupNamespaceUri ( "apiscol" ) );
		$childrenNode = $this->xpath->query ( "/atom:entry/apiscol:children/atom:entry" );
		$nbChildren = $childrenNode->length;
		for($i = 0; $i < $nbChildren; $i ++) {
			$childNode = $childrenNode->item ( $i );
			
			$cloned = $childNode->cloneNode ( true );
			$cloned->setAttribute ( "xmlns", $this->document->lookupNamespaceUri ( "atom" ) );
			$cloned->setAttribute ( "xmlns:apiscol", $this->document->lookupNamespaceUri ( "apiscol" ) );
			$childDocument = new DOMDocument ();
			$childDocument->appendChild ( $childDocument->importNode ( $cloned, true ) );
			$child = new MetadataDAO ( $this->serviceAccess );
			
			$child->setDocument ( $childDocument );
			$child->setId ( RequestUtils::extractIdFromRestUri ( $child->getLink () ) );
			
			$this->children [$i] = $child;
		}
	}
	public function getMetadataInResourceHierarchy() {
		$metadataInResourceHierarchy = array ();
		$this->addMetadataFromHierarchy ( $this, $metadataInResourceHierarchy );
		return $metadataInResourceHierarchy;
	}
	private function addMetadataFromHierarchy(MetadataDAO $metadata, array & $metadataInResourceHierarchy) {
		$metadataInResourceHierarchy [] = $metadata->getId ();
		$children = $metadata->getChildren ();
		if (is_null ( $children ) || count ( $children ) == 0)
			return;
		foreach ( $children as $child ) {
			$this->addMetadataFromHierarchy ( $child, $metadataInResourceHierarchy );
		}
	}
}

