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
		$availableResources = '';
		if ($this->mainController->userIsAllowedToRead ()) {
			if (! (is_null ( $this->model->getMetadataList () )) && $this->model->getMetadataList ()->isbuilt ())
				$availableResources = $this->transformXMLResults ();
		} else {
			$this->mainController->setInError ( true );
			$this->mainController->setErrorMessage ( "{RIGHTS-IMPOSSIBLE-BROWSE-RESOURCES}" );
		}
		$this->render = str_replace ( "[AVAILABLE-RESOURCES]", $availableResources, $this->render );
		$this->render = str_replace ( "[EDITED-RESOURCE]", $this->getEditedResourceHierarchy (), $this->render );
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
			
			$rootItem = $this->getListItemTemplate ();
			
			$title = $this->model->getMetadata ()->getTitle ();
			$desc = $this->model->getMetadata ()->getSummary ();
			$id = $this->model->getMetadata ()->getId ();
			$rootItem = str_replace ( '[TITLE]', $title, $rootItem );
			$rootItem = str_replace ( '[DESC]', $desc, $rootItem );
			$rootItem = str_replace ( '[ID]', $id, $rootItem );
			$hierarchy = str_replace ( '[ITEMS]', $rootItem, $hierarchy );
		}
		
		return $hierarchy;
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
				<ol></ol>
				</li>';
	}
}

?>