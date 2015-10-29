<?php
class ResourcesStructureView extends AbstractView implements IView {
	private $resourcesList;
	private $controls;
	public function build() {
		$this->createControls ();
		$this->createHiddenInputs ();
		$this->addContent ();
	}
	private function createControls() {
	}
	private function addContent() {
		$this->render .= HTMLLoader::load ( 'resources-structure' );
	}
}
?>