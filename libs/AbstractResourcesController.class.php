<?php
abstract class AbstractResourcesController implements IController {
	protected $view;
	/**
	 *
	 * @var Model
	 */
	protected $model;
	protected $mainController;
	protected $prefix;
	public function __construct($mainController, $model, $prefix) {
		$this->mainController = $mainController;
		$this->model = $model;
		$this->prefix = $prefix;
	}
	public function registerMetadataId($metadataId = null) {
		if (is_null ( $metadataId ))
			$metadataId = Security::$_CLEAN ['metadata-id'];
		if (! empty ( $metadataId )) {
			try {
				$this->model->setMetadataId ( $metadataId );
			} catch ( HttpRequestException $e ) {
				$this->mainController->setInError ( true );
				// TODO traduire
				$this->mainController->setErrorMessage ( "Problèmes de connexion à ApiScol pour obtenir les détails de la ressource.", $e->getContent () );
			} catch ( BadUrlRequestException $e ) {
				$this->mainController->setInError ( true );
				// TODO traduire
				$this->mainController->setErrorMessage ( "Pas de réponse d'ApiScol pour les détails de la ressource. Le service est peut-être arrêté ou en panne.", $e->getMessage () );
			}
		}
	}
	public function acquireContent($throwErrorIfNoContent = true) {
		try {
			$this->model->acquireContentRepresentation ();
			
			try {
				$this->model->acquireContentThumbRepresentation ();
			} catch ( HttpRequestException $e ) {
				$this->mainController->setInError ( true );
				$this->mainController->setErrorMessage ( "Problèmes de connexion à ApiScol pour obtenir les suggestions de miniatures basées sur le contenu.", $e->getContent () );
			} catch ( BadUrlRequestException $e ) {
				$this->mainController->setInError ( true );
				$this->mainController->setErrorMessage ( "Problèmes de connexion à ApiScol pour obtenir les suggestions de miniatures basées sur le contenu.", $e->getMessage () );
			}
		} catch ( HttpRequestException $e ) {
			$this->mainController->setInError ( true );
			$this->mainController->setErrorMessage ( "Problèmes de connexion à ApiScol pour obtenir le contenu de la ressource.", $e->getContent () );
		} catch ( MetadataWithoutContentException $e ) {
			if (true === $throwErrorIfNoContent) {
				$this->mainController->setInError ( true );
				$this->mainController->setErrorMessage ( "Aucun contenu n'est associé à cette metadonnée.", $e->getMessage () );
			}
		} catch ( BadUrlRequestException $e ) {
			$this->mainController->setInError ( true );
			$this->mainController->setErrorMessage ( "Problèmes de connexion à ApiScol pour obtenir le contenu de la ressource.", $e->getMessage () );
		} catch ( CorruptedXMLStringException $e ) {
			$this->mainController->setInError ( true );
			$this->mainController->setErrorMessage ( "Le contenu de cette métadonnée est illisible.", $e->getMessage () );
		}
	}
	public function acquireFacetsTest() {
		$this->model->acquireFacetsTest ();
	}
	public function getView() {
		return $this->view;
	}
}
?>