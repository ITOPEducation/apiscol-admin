<?php
class MaintenanceController implements IController {
	public function __construct($mainController, $model, $prefix) {
		$this->mainController = $mainController;
		/**
		 *
		 * @var Model
		 */
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
	}
	public function processAsyncRequest() {
		switch (Security::$_CLEAN ['action']) {
			case 'recovery' :
				if (isset ( Security::$_CLEAN ['target-repository'] ))
					switch (Security::$_CLEAN ['target-repository']) {
						case 'metadata' :
							try {
								$response = $this->model->askForRecoveryMaintenance ( 'metadata' );
								echo $response ['content'];
							} catch ( HttpRequestException $e ) {
								echo MainController::xmlErrorMessage ( $e->getMessage (), 500, "Le service ne semble pas répondre" );
							} catch ( BadUrlRequestException $e2 ) {
								echo MainController::xmlErrorMessage ( $e2->getMessage (), 404, "L'url appelée ne renvoie pas de réponse" );
							}
							break;
						case 'content' :
							try {
								$response = $this->model->askForRecoveryMaintenance ( 'content' );
								echo $response ['content'];
							} catch ( HttpRequestException $e ) {
								echo MainController::xmlErrorMessage ( $e->getMessage (), 500, "Le service ne semble pas répondre" );
							} catch ( BadUrlRequestException $e2 ) {
								echo MainController::xmlErrorMessage ( $e2->getMessage (), 404, "L'url appelée ne renvoie pas de réponse" );
							}
							break;
					}
				else if (isset ( Security::$_CLEAN ['maintenance-process-report'] )) {
					$url = RequestUtils::restoreProtocole ( Security::$_CLEAN ['maintenance-process-report'] );
					$nbLines = isset ( Security::$_CLEAN ['nb-lines'] ) ? Security::$_CLEAN ['nb-lines'] : 0;
					try {
						echo $this->model->getMaintenanceProcessReport ( $url, $nbLines );
					} catch ( HttpRequestException $e ) {
						echo MainController::xmlErrorMessage ( $url . "  " . $e->getMessage (), 404, "Le service ne semble pas répondre" );
					} catch ( BadUrlRequestException $e2 ) {
						echo MainController::xmlErrorMessage ( $url . "  " . $e2->getMessage (), 404, "L'url appelée ne renvoie pas de réponse" );
					} catch ( ConnexionFailureException $e3 ) {
						echo MainController::xmlErrorMessage ( $e3->getMessage (), 500, "Impossible de se conencer à ApiScol" );
					}
				}
				break;
			case 'optimization' :
				if (isset ( Security::$_CLEAN ['target-repository'] ))
					switch (Security::$_CLEAN ['target-repository']) {
						case 'metadata' :
							try {
								$response = $this->model->askForOptimizationMaintenance ( 'metadata' );
								echo $response ['content'];
							} catch ( HttpRequestException $e ) {
								echo MainController::xmlErrorMessage ( $e->getMessage (), 500, "Le service ne semble pas répondre" );
							} catch ( BadUrlRequestException $e2 ) {
								echo MainController::xmlErrorMessage ( $e2->getMessage (), 404, "L'url appelée ne renvoie pas de réponse" );
							} catch ( ConnexionFailureException $e3 ) {
								echo MainController::xmlErrorMessage ( $e3->getMessage (), 500, "Impossible de se conencer à ApiScol" );
							}
							break;
						case 'content' :
							try {
								$response = $this->model->askForOptimizationMaintenance ( 'content' );
								echo $response ['content'];
							} catch ( HttpRequestException $e ) {
								echo MainController::xmlErrorMessage ( $e->getMessage (), 500, "Le service ne semble pas répondre" );
							} catch ( BadUrlRequestException $e2 ) {
								echo MainController::xmlErrorMessage ( $e2->getMessage (), 404, "L'url appelée ne renvoie pas de réponse" );
							} catch ( ConnexionFailureException $e3 ) {
								echo MainController::xmlErrorMessage ( $e3->getMessage (), 500, "Impossible de se conencer à ApiScol" );
							}
							break;
					}
				
				break;
		}
	}
	public function getView() {
		return $this->view;
	}
}
?>