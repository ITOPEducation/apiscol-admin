<?php
class ResourcesDetailDisplayController extends AbstractResourcesDetailController {
	public function __construct($mainController, $model, $prefix) {
		parent::__construct ( $mainController, $model, $prefix );
	}
	public function completeScripts() {
		$this->mainController->addScript ( 'apiscol' );
		$this->mainController->addScript ( 'layout' );
		$this->mainController->addScript ( 'rcarousel' );
		$this->mainController->addScript ( 'form' );
		$this->mainController->addCss ( 'rcarousel' );
		$this->mainController->addScript ( 'init_resources_detail_view' );
		$this->mainController->addScript ( 'init' );
	}
	public function defineView() {
		$this->view = new ResourcesDetailDisplayView ( $this->model, $this->prefix, $this->mainController );
	}
	public function processSyncRequest() {
		$this->registerMetadataId ();
		
		if ($this->mainController->isInError ())
			return;
	}
	public function processAsyncRequest() {
		$this->registerMetadataId ();
		if ($this->mainController->isInError ())
			return;
		if (isset ( Security::$_CLEAN ['custom-preview'] )) {
			try {
				$this->model->acquireContentRepresentation ();
				$this->processCustomPreview ();
				$this->defineView ();
				echo "ok";
			} catch ( HttpRequestException $e ) {
				echo "Impossible de récupérer les suggestions de miniatures :" . $e->getMessage ();
			}
		} else {
			try {
				$this->model->acquireThumbsSuggestions ();
				$changed = $this->processCustomThumb ();
				$changed = $this->processThumbChoice () && $changed;
				if (true === $changed)
					$this->model->acquireThumbsSuggestions ();
				
				$this->defineView ();
				echo $this->getView ()->getThumbsChoiceArea ();
			} catch ( HttpRequestException $e ) {
				echo "Impossible de récupérer les suggestions de miniatures :" . $e->getMessage ();
			}
		}
	}
	public function processThumbChoice($secondTry = false) {
		$changed = false;
		if (isset ( Security::$_CLEAN ['choose-thumb'] )) {
			$changed = true;
			// TODO cHECK authorizations
			try {
				$this->model->assignThumbToMetadata ( Security::$_CLEAN ['choose-thumb'] );
			} catch ( HttpRequestException $e ) {
				$intro = 'Il y a eu un problème... (erreur ' . $e->getCode () . ')';
				switch ($e->getCode ()) {
					case "412" :
						$intro = 'Quelqu\'un a modifé cette ressource en même temps que vous (erreur ' . $e->getCode () . ')';
						break;
					case "500" :
						$intro = 'Quelquechose s\'est mal passé de notre côté (erreur ' . $e->getCode () . ')';
						break;
				}
				$this->mainController->setInError ( true );
				$this->mainController->setErrorMessage ( $intro, $e->getContent () );
			}
		}
		return $changed;
	}
	public function processCustomThumb($secondTry = false) {
		$changed = false;
		if (isset ( Security::$_CLEAN ['custom-thumb'] )) {
			$changed = true;
			$error = Security::$_CLEAN ['custom-thumb'] ['error'];
			if ($error == 1 || $error == 2) {
				$this->mainController->setInError ( true );
				$this->mainController->setErrorMessage ( "L'envoi de la miniature a échoué en raison de sa taille excessive." );
			} else if ($error == 3) {
				$this->mainController->setInError ( true );
				$this->mainController->setErrorMessage ( "L'envoi de la miniature a échoué en raison d'un incident de transfert." );
			} else if ($error == 4) {
				$this->mainController->setInError ( true );
				$this->mainController->setErrorMessage ( "Vous avez envoyé une miniature de taille nulle." );
			} else {
				try {
					$this->model->assignCustomThumbToMetadata ( Security::$_CLEAN ['custom-thumb'] );
				} catch ( HttpRequestException $e ) {
					$intro = 'Il y a eu un problème... (erreur ' . $e->getCode () . ')';
					switch ($e->getCode ()) {
						case "412" :
							$intro = 'Quelqu\'un a modifé cette ressource en même temps que vous (erreur ' . $e->getCode () . ')';
							break;
						case "500" :
							$intro = 'Quelquechose s\'est mal passé de notre côté (erreur ' . $e->getCode () . ')';
							break;
					}
					$this->mainController->setInError ( true );
					$this->mainController->setErrorMessage ( $intro, $e->getContent () );
				}
			}
		}
		return $changed;
	}
	public function processCustomPreview($secondTry = false) {
		if (isset ( Security::$_CLEAN ['custom-preview'] )) {
			$error = Security::$_CLEAN ['custom-preview'] ['error'];
			if ($error == 1 || $error == 2) {
				$this->mainController->setInError ( true );
				$this->mainController->setErrorMessage ( "L'envoi de la previsualisation a échoué en raison de sa taille excessive." );
			} else if ($error == 3) {
				$this->mainController->setInError ( true );
				$this->mainController->setErrorMessage ( "L'envoi de la previsualisation a échoué en raison d'un incident de transfert." );
			} else if ($error == 4) {
				$this->mainController->setInError ( true );
				$this->mainController->setErrorMessage ( "Vous avez envoyé une previsualisation de taille nulle." );
			} else {
				try {
					$this->model->assignCustomPreviewToContent ( Security::$_CLEAN ['custom-preview'] );
				} catch ( HttpRequestException $e ) {
					$intro = 'Il y a eu un problème... (erreur ' . $e->getCode () . ')';
					switch ($e->getCode ()) {
						case "412" :
							$intro = 'Quelqu\'un a modifé cette ressource en même temps que vous (erreur ' . $e->getCode () . ')';
							break;
						case "500" :
							$intro = 'Quelquechose s\'est mal passé de notre côté (erreur ' . $e->getCode () . ')';
							break;
					}
					$this->mainController->setInError ( true );
					$this->mainController->setErrorMessage ( $intro, $e->getContent () );
				}
			}
		}
	}
}
?>