<?php
class ResourcesStructureView extends AbstractView implements IView {
	public function build() {
		$this->createControls ();
		$this->createHiddenInputs ();
		$this->addContent ();
	}
	private function createControls() {
	}
	private function addContent() {
		$this->render .= HTMLLoader::load ( 'resources-structure' );
		$this->render = str_replace ( "[EDITED-RESOURCE]", $this->getEditedResourceHierarchy (), $this->render );
		$availableResources = '';
		if ($this->mainController->userIsAllowedToRead ()) {
			if (! (is_null ( $this->model->getMetadataList () )) && $this->model->getMetadataList ()->isbuilt ())
				$availableResources = $this->transformXMLResults ();
		} else {
			$this->mainController->setInError ( true );
			$this->mainController->setErrorMessage ( "{RIGHTS-IMPOSSIBLE-BROWSE-RESOURCES}" );
		}
		
		$this->render = str_replace ( "[AVAILABLE-RESOURCES]", $availableResources, $this->render );
	}
	private function transformXMLResults() {
		$this->proc = $this->getXSLTProcessor ( 'xsl/structureViewAvailableMetadataList.xsl' );
		$this->proc->setParameter ( '', 'prefix', $this->prefix );
		$this->proc->setParameter ( '', 'write_permission', $this->mainController->userIsAllowedToWrite () );
		try {
			
			$resourcesListXml = $this->model->getMetadataList ( false );
			$resourcesList = $resourcesListXml->getDocumentAsString ();
			$doc = new DOMDocument ();
			$doc->loadXML ( $resourcesList );
			return $this->proc->transformToXML ( $doc );
		} catch ( HttpRequestException $e ) {
			$this->mainController->setInError ( true );
			// TODO traduire
			$this->mainController->setErrorMessage ( "{ERROR-IMPOSSIBLE-CONNECT-META}", $e->getContent () );
			return "";
		} catch ( BadUrlRequestException $e ) {
			$this->mainController->setInError ( true );
			// TODO traduire
			$this->mainController->setErrorMessage ( "{ERROR-IMPOSSIBLE-CONNECT-META}", $e->getMessage () );
		}
	}
	private function getEditedResourceHierarchy() {
		$hierarchy = '';
		if (null !== $this->model->getMetadata ()) {
			$hierarchy = $this->getListTemplate ();
			
			$rootItem = $this->getMetadataItem ( $this->model->getMetadata () );
			$hierarchy = str_replace ( '[ITEMS]', $rootItem, $hierarchy );
		}
		
		return $hierarchy;
	}
	private function getMetadataItem(MetadataDAO $metadata) {
		$item = $this->getListItemTemplate ();
		$title = $metadata->getTitle ();
		$desc = $metadata->getSummary ();
		$id = $metadata->getId ();
		$item = str_replace ( '[TITLE]', $title, $item );
		$item = str_replace ( '[DESC]', $desc, $item );
		$item = str_replace ( '[ID]', $id, $item );
		$children = $metadata->getChildren ();
		$childItems = '';
		if (! is_null ( $children ) && is_array ( $children )) {
			$nbChildren = count ( $children );
			for($i = 0; $i < $nbChildren; $i ++) {
				$childItems .= $this->getMetadataItem ( $children [$i] );
			}
		}
		$item = str_replace ( '[CHILDREN]', $childItems, $item );
		return $item;
	}
	private function getListTemplate() {
		return '<ol id="resource-hierarchy" class="sortable ui-sortable mjs-nestedSortable-branch mjs-nestedSortable-expanded">[ITEMS]</ol>';
	}
	private function getListItemTemplate() {
		return '<li class="mjs-nestedSortable-leaf" id="[ID]">
					<div class="menuDiv">
					
					<span title="Click to show/hide item details" data-id="[ID]" class="expandEditor ui-icon ui-icon-triangle-1-n">
						<span></span>
					</span>
					<span title="Click to show/hide children" class="disclose ui-icon ui-icon-minusthick">
						<span></span>
					</span>
					<span title="Click to delete item." data-id="[ID]" class="deleteMenu ui-icon ui-icon-closethick">
						<span></span>
					</span>
					<span>
						<span class="itemTitle">
							[TITLE]
							<a href="/resources/detail/[ID]/display" 
								class="ui-icon ui-icon-extlink">
							Accéder
						</a>
					</span>
					
					</span>
						<div id="menuEdit[ID]" class="menuEdit hidden">
							<p>
							[DESC]
							</p>
						</div>
					</div>
				<ol>[CHILDREN]</ol>
				</li>';
	}
}

?>