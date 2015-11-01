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
		$availableResources='';
		if($this->mainController->userIsAllowedToRead()) {
 			if(!(is_null($this->model->getMetadataList())) && $this->model->getMetadataList()->isbuilt())
 				$availableResources=$this->transformXMLResults();
		} else {
			$this->mainController->setInError(true);
			//TODO traduire
			$this->mainController->setErrorMessage("{RIGHTS-IMPOSSIBLE-BROWSE-RESOURCES}");
		}
		$this->render=str_replace("[AVAILABLE-RESOURCES]", $availableResources, $this->render);
	}
	private function transformXMLResults() {
		$this->proc=$this->getXSLTProcessor('xsl/structureViewAvailableMetadataList.xsl');
		$this->proc->setParameter('', 'prefix', $this->prefix);
		$this->proc->setParameter('', 'write_permission', $this->mainController->userIsAllowedToWrite());
		try {
				
			$resourcesListXml=$this->model->getMetadataList(false);
				
			$resourcesList=$resourcesListXml->getDocumentAsString();
			$doc=new DOMDocument();
			$doc->loadXML($resourcesList);
			return $this->proc->transformToXML($doc);
		} catch (HttpRequestException $e) {
			$this->mainController->setInError(true);
			//TODO traduire
			$this->mainController->setErrorMessage("{ERROR-IMPOSSIBLE-CONNECT-META}", $e->getContent());
			return "";
		} catch (BadUrlRequestException $e) {
			$this->mainController->setInError(true);
			//TODO traduire
			$this->mainController->setErrorMessage("{ERROR-IMPOSSIBLE-CONNECT-META}", $e->getMessage());
		}
	}
}
?>