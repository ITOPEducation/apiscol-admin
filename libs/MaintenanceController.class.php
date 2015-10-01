<?php
class MaintenanceController implements IController {
	public function __construct($mainController, $model, $prefix) {
		$this->mainController = $mainController;
		$this->model = $model;
		$this->prefix = $prefix;
	}
	public function completeScripts() {
		if ($_SESSION ['action'] == 'recovery') {
			$this->mainController->addScript ( 'layout' );
			$this->mainController->addScript ( 'form' );
			$this->mainController->addScript ( 'cookie' );
			$this->mainController->addScript ( 'init_maintenance_recovery' );
		}
		$this->mainController->addScript ( 'init' );
	}
	public function defineView() {
		switch ($_SESSION ['action']) {
			case 'recovery' :
				$this->view = new MaintenanceRecoveryView ( $this->model, $this->prefix, $this->mainController );
				break;
		}
	}
	public function processSyncRequest() {
		if (isset ( Security::$_CLEAN ['import-metadata'] )) {
			$this->processMetadataImport ();
		}
		if (isset ( Security::$_CLEAN ['url'] ) && isset ( Security::$_CLEAN ['resid'] ) && isset ( Security::$_CLEAN ['etag'] )) {
			$this->registerMetadataId ();
			$this->acquireContent ();
			$this->registerUrl ();
		}
	}
	public function processAsyncRequest() {
		print_r(Security::$_CLEAN);
		if (isset ( Security::$_CLEAN ['import-metadata'] )) {
			$this->processMetadataImport ();
			$this->defineView ();
			if (! is_null ( $this->getView () ))
				echo $this->getView ()->getImportOfMetadataResultArea ();
			else {
				echo MainController::xmlErrorMessage ( "Erreur inconnue", 0, "Une erreur est survenue" );
			}
		} else if (isset ( Security::$_CLEAN ['url'] ) && isset ( Security::$_CLEAN ['metadata-id'] )) {
			$this->registerMetadataId ();
			if (! $this->mainController->isInError ())
				$this->acquireContent ();
			if (! $this->mainController->isInError ())
				$this->registerUrl ();
			$this->defineView ();
			if (! $this->mainController->isInError ())
				echo $this->getView ()->getUrlRegistrationResultArea ();
			else {
				// TODO écrire du html
				$errors = $this->mainController->getErrorMessage ();
				echo MainController::xmlErrorMessage ( $errors ['private'], 0, $errors ['public'] );
			}
		} else if (isset ( Security::$_CLEAN ['url-parsing-report'] )) {
			$url = RequestUtils::restoreProtocole ( Security::$_CLEAN ['url-parsing-report'] );
			// TODO catch bad url
			echo $this->model->getUrlParsingReport ( $url );
		}
	}
	public function getView() {
		return $this->view;
	}
}
?>