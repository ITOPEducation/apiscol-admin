<?php
class ResourcesStructureController extends AbstractResourcesController {
	public function completeScripts() {
		$this->mainController->addScript ( 'layout' );
		$this->mainController->addScript ( 'nested_sortable' );
		$this->mainController->addScript ( 'perfect_scrollbar' );
		$this->mainController->addScript ( 'init_resources_structure' );
		$this->mainController->addScript ( 'init' );
		$this->mainController->addCss ( 'perfect_scrollbar' );
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
		if (isset ( Security::$_CLEAN ['edit-struture-metadata-id'] )) {
			$response = $this->processResourceSelectionForStructureView ( Security::$_CLEAN ['edit-struture-metadata-id'] );
			if (isset ( $response ['content'] )) {
				return $this->metadataArrayToXml ( $response ['content'] );
			}
			
			echo MainController::xmlErrorMessage ( "Problème lors du choix de ressources pour la vue 'structure'", 500, "Erreur d'origine inconnue" );
		}
		if (isset ( Security::$_CLEAN ['hierarchy-data'] )) {
			$resourceIdForStructureView = $this->model->getResourceIdForStructureView ();
			if (empty ( $resourceIdForStructureView )) {
				echo MainController::xmlErrorMessage ( "Aucune racine sélectionnée", 412, "Impossible d'enregistrer la hiérarchie" );
				return;
			}
			$resourceEtagForStructureView = $this->model->getResourceEtagForStructureView ();
			if (empty ( $resourceIdForStructureView )) {
				echo MainController::xmlErrorMessage ( "Le jeton de fraicheur est vide", 500, "Impossible d'enregistrer la hiérarchie" );
				return;
			}
			$response = $this->model->registerHierarchyData ( $resourceIdForStructureView, Security::$_CLEAN ['hierarchy-data'], $resourceEtagForStructureView );
			if (false === $response) {
				echo MainController::xmlErrorMessage ( "Envoi d'une hiérarchie invalide", 412, "Erreur d'origine inconnue" );
			}
		}
	}
	public function processSyncRequest() {
		// Root resource to be edited
		$resourceIdForStructureView = $this->model->getResourceIdForStructureView ();
		if (empty ( $resourceIdForStructureView )) {
			$this->mainController->setInError ( true );
			$this->mainController->setErrorMessage ( "Veuillez sélectionner la ressource à éditer dans les listes de ressources." );
			return;
		}
		$this->registerMetadataId ( $resourceIdForStructureView, true );
		if ($this->mainController->isInError ())
			return;
			// optimistic concurrency : save the etag for freshness control
		$this->model->setResourceEtagForStructureView ( $this->model->getMetadata ()->getEtag () );
		// List of selected resources
		$this->model->prepareSearchQuery ();
		$this->model->addSelectedMetadataIdsToMetadataList ();
		$this->model->getMetadataList ()->setListOfMetadataIsForced ( true );
		$metadataInEditedResourceHierarchy = $this->model->getMetadata ()->getMetadataInResourceHierarchy ();
		$this->model->getMetadataList ()->disableMetadataIds ( $metadataInEditedResourceHierarchy );
		try {
			$this->model->launchSearchQuery ();
		} catch ( BadUrlRequestException $e ) {
			$this->mainController->setInError ( true );
			$this->mainController->setErrorMessage ( "Impossible de consulter les ressources. Le service est peut-être arrêté.", $e->getMessage () );
		} catch ( HttpRequestException $e ) {
			$this->mainController->setInError ( true );
			$this->mainController->setErrorMessage ( "Le service ApiScol Seek n'a pas répondu ou a dysfonctionné (erreur " . $e->getCode () . ").", $e->getContent () );
		} catch ( CorruptedXMLStringException $e ) {
			$this->mainController->setInError ( true );
			$this->mainController->setErrorMessage ( "Le service ApiScol Seek a renvoyé des données illisibles.", $e->getMessage () );
		}
		
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
		$counter = 0;
		foreach ( $metadataIds as $key => $metadataId ) {
			$this->model->setMetadataIdSelected ( $metadataId, $selecteds [$counter] );
			$counter ++;
		}
		return array (
				"content" => $this->model->getSelectedMetadataList () 
		);
	}
	private function processResourceSelectionForStructureView($metadataId) {
		$this->model->setResourceIdForStructureView ( $metadataId );
		
		return array (
				"content" => $this->model->getResourceIdForStructureView () 
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