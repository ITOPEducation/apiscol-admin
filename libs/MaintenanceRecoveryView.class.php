<?php
class MaintenanceRecoveryView extends AbstractView implements IView {
	public function MaintenanceRecoveryView($model, $prefix, $mainController) {
		parent::__construct ( $model, $prefix, $mainController );
	}
	public function build() {
		$this->createHiddenInputs ();
		$this->addContent ();
	}
	private function addContent() {
		$this->render .= HTMLLoader::load ( 'maintenance-recovery' );
		$metadataRecoveryArea = '<br/>Vos droits sont insuffisants pour accéder à ces fonctionnalités.';
		$writeMetadataArea = '<br/>Vos droits sont insuffisants pour accéder à ces fonctionnalités.';
		$importMetadataResultArea = '';
		$urlRegistrationResultArea = '';
		if ($this->mainController->userIsAllowedToWrite ()) {
			$metadataRecoveryArea = $this->getMetadataRecoveryArea ();
		}
		$this->render = str_replace ( "[METADATA-RECOVERY]", $metadataRecoveryArea, $this->render );
	}
	private function getMetadataRecoveryArea() {
		$area = '<div class="recovery-control ui-helper-clearfix"><form action="[PREFIX]/resources/detail/[MDID]/refresh" method="POST"	id="metadata-recovery"> <input type="submit" value="recovery" />Reconstruire le dépôt de métadonnées</form>	<div class="display-result"></div></div>';
		return $area;
	}
}

