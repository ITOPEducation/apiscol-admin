<?php
class ResourcesStructureController extends AbstractResourcesController {
	public function completeScripts() {
		$this->mainController->addScript ( 'layout' );
		$this->mainController->addScript ( 'init_resources_structure' );
		$this->mainController->addScript ( 'init' );
	}
	public function defineView() {
		$this->view = new ResourcesStructureView ( $this->model, $this->prefix, $this->mainController );
		
		$this->mainController->setTitle ( 'Ressources pédagogiques - structure' );
	}
	public function processAsyncRequest() {
		if (isset ( Security::$_CLEAN ['select-metadata-id'] ) && isset ( Security::$_CLEAN ['select-metadata'] )) {
			$response = $this->processMetadataSelection ( Security::$_CLEAN ['select-metadata-id'], Security::$_CLEAN ['select-metadata'] );
			if (isset ( $response ['content'] )) {
				
				return $this->metadataArrayToXml ( $response ['content'] );
			}
			
			echo MainController::xmlErrorMessage ( "Problème lors de la selection", 500, "Erreur d'origine inconnue" );
		}
	}
	public function processSyncRequest() {
		$resetStart = false;
		
		if (isset ( Security::$_CLEAN ['active-tab'] )) {
			$this->model->setDisplayParameter ( 'active-tab', Security::$_CLEAN ['active-tab'] );
		} else if ($_SESSION ['action'] == "detail") {
			$this->model->setDisplayParameter ( 'active-tab', 'main_menu_item_display' );
		}
		if (isset ( Security::$_CLEAN ['north-pane'] )) {
			$this->model->setDisplayParameter ( 'north-pane', Security::$_CLEAN ['north-pane'] );
		}
		if (isset ( Security::$_CLEAN ['west-pane'] )) {
			$this->model->setDisplayParameter ( 'west-pane', Security::$_CLEAN ['west-pane'] );
		}
		if (isset ( Security::$_CLEAN ['south-pane'] )) {
			$this->model->setDisplayParameter ( 'south-pane', Security::$_CLEAN ['south-pane'] );
		}
	}
	public function getView() {
		return $this->view;
	}
	private function processMetadataSelection(array $metadataIds, array $selecteds) {
		$success = true;
		$counter = 0;
		foreach ( $metadataIds as $key => $metadataId ) {
			$this->model->setMetadataIdSelected ( $metadataId, $selecteds [$counter] ) && $success;
			$counter ++;
		}
		
		return array (
				"content" => $this->model->getSelectedMetadataList () 
		);
	}
	function metadataArrayToXml($array, $xml = false) {
		if ($xml === false) {
			$xml = new SimpleXMLElement ( '<data/>' );
		}
		foreach ( $array as $key => $value ) {
			
			$xml->addChild ( 'mdid', $value );
		}
		return $xml->asXML ();
	}
}
?>