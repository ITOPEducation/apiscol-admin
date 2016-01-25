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
		$metadataOptimizationArea = '<br/>Vos droits sont insuffisants pour accéder à ces fonctionnalités.';
		$contentRecoveryArea = '<br/>Vos droits sont insuffisants pour accéder à ces fonctionnalités.';
		$contentOptimizationArea = '<br/>Vos droits sont insuffisants pour accéder à ces fonctionnalités.';
		$importMetadataResultArea = '';
		$urlRegistrationResultArea = '';
		if ($this->mainController->userIsAllowedToWrite ()) {
			$metadataRecoveryArea = $this->getMetadataRecoveryArea ();
			$metadataOptimizationArea = $this->getMetadataOptimizationArea ();
			$contentRecoveryArea = $this->getContentRecoveryArea ();
			$contentOptimizationArea = $this->getContentOptimizationArea ();
		}
		$this->render = str_replace ( "[METADATA-RECOVERY]", $metadataRecoveryArea, $this->render );
		$this->render = str_replace ( "[METADATA-OPTIMIZATION]", $metadataOptimizationArea, $this->render );
		$this->render = str_replace ( "[CONTENT-RECOVERY]", $contentRecoveryArea, $this->render );
		$this->render = str_replace ( "[CONTENT-OPTIMIZATION]", $contentOptimizationArea, $this->render );
	}
	private function getMetadataRecoveryArea() {
		$area = '<div class="recovery-control ui-helper-clearfix"><form action="[PREFIX]/maintenance/recovery" method="POST"	id="metadata-recovery"> <input type="submit" value="recovery" />Reconstruire le dépôt de métadonnées<input type="hidden" name="target-repository" value="metadata" /></form>	</div>';
		return $area;
	}
	private function getMetadataOptimizationArea() {
		$area = '<div class="recovery-control ui-helper-clearfix"><form action="[PREFIX]/maintenance/optimization" method="POST"	id="metadata-optimization"> <input type="submit" value="optimization" />Optimiser l\'index de recherche des  métadonnées<input type="hidden" name="target-repository" value="metadata" /></form>	</div>';
		return $area;
	}
	private function getContentRecoveryArea() {
		$area = '<div class="recovery-control ui-helper-clearfix"><form action="[PREFIX]/maintenance/recovery" method="POST"	id="content-recovery"> <input type="submit" value="recovery" />Reconstruire le dépôt de ressources<input type="hidden" name="target-repository" value="content" /></form>	</div>';
		return $area;
	}
	private function getContentOptimizationArea() {
		$area = '<div class="recovery-control ui-helper-clearfix"><form action="[PREFIX]/maintenance/optimization" method="POST"	id="content-optimization"> <input type="submit" value="optimization" />Optimiser l\'index de recherche des  ressources<input type="hidden" name="target-repository" value="content" /></form>	</div>';
		return $area;
	}
}

