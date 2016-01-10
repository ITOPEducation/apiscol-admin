<?php
class ResourcesDetailDisplayView extends AbstractResourceDetailView {
	public function ResourcesDetailDisplayView($model, $prefix, $mainController) {
		parent::__construct ( $model, $prefix, $mainController );
	}
	protected function addContent() {
		parent::addContent ();
		$this->render = str_replace ( "[PANEL]", HTMLLoader::load ( 'resources-detail-display' ), $this->render );
		$displaySnippet = '<br/>Vos droits sont insuffisants pour accéder à ces fonctionnalités.';
		$customThumbsArea = '';
		$thumbsChoiceArea = 'Vos droits sont insuffisants pour accéder à ces fonctionnalités.';
		if ($this->mainController->userIsAllowedToRead ()) {
			$displaySnippet = $this->getDisplaySnippet ();
		}
		if ($this->mainController->userIsAllowedToWrite ()) {
			$action = $this->getAction ();
		}
		$noContentIndicator = $this->getNoContentIndicator ();
		$this->render = str_replace ( "[DISPLAY]", $displaySnippet, $this->render );
		$this->render = str_replace ( "[DISPLAY-MODE-LABEL]", $this->model->getDisplayModeLabel (), $this->render );
		$this->render = str_replace ( "[DISPLAY-DEVICE-LABEL]", $this->model->getDisplayDeviceLabel (), $this->render );
		$this->render = str_replace ( "[ACTION]", $action, $this->render );
		$this->render = str_replace ( "[NO-CONTENT-INDICATOR]", $noContentIndicator, $this->render );
	}
	private function getDisplaySnippet() {
		if ($this->mainController->isInError ())
			return '';
		return '<a data-mode="' . $this->model->getDisplayMode () . '" data-style="inherit" href="' . $this->model->getMetadata ()->getLink () . '">' . $this->model->getMetadata ()->getTitle () . '</a>';
	}
	private function transformXMLThumbsSuggestions(DOMDocument $XMLsuggestions) {
		$this->proc = $this->getXSLTProcessor ( 'xsl/thumbsSuggestionsList.xsl' );
		$this->proc->setParameter ( '', 'prefix', $this->prefix );
		$this->proc->setParameter ( '', 'random', rand ( 0, 1000 ) );
		$this->proc->setParameter ( '', 'url', $this->prefix . '/resources/detail/' . $this->model->getMetadata ()->getId () . '/display' );
		return $this->proc->transformToXML ( $XMLsuggestions );
	}
	public function getThumbsChoiceArea() {
		$area = '';
		if ($this->mainController->isInError ())
			return $area;
		try {
			// TODO change to async
			$thumbsSuggestions = $this->model->getThumbsSuggestions ();
			$area .= $this->transformXMLThumbsSuggestions ( $thumbsSuggestions->getDocument () );
		} catch ( HttpRequestException $e ) {
			$area .= "Donnee Indisponible";
			$this->mainController->setInError ( true );
			$this->mainController->setErrorMessage ( "Le choix de miniatures semble indisponible.", $e->getContent () );
		}
		if ($this->mainController->isInError ()) {
			$errors = $this->mainController->getErrorMessage ();
			$area .= '<div class="ui-state-error"><strong>' . $errors ["private"] . '</strong><br/>' . $errors ["public"] . '</strong></div>';
		}
		
		$area .= '</div>';
		return $area;
	}
	public function getNoContentIndicator() {
		$indicator = '';
		if (! $this->mainController->isInError ()) {
			$link = $this->model->getMetadata ()->getContentLink ();
			if (empty ( $link ) || $link == Model::NO_ANSWER)
				$indicator = 'data-no-content="true" disabled="disabled"';
		}
		
		return $indicator;
	}
	private function getRefreshArea() {
		$area = '';
		return $area;
	}
	private function getAction() {
		if ($this->mainController->isInError ())
			return '';
		$action = $this->prefix . '/resources/detail/' . $this->model->getMetadata ()->getId () . '/display';
		return $action;
	}
}
?>