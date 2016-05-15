<?php
class ScoLOMfrDAO extends AbstractDAO {
	const VOID_RESOURCE = "Contenu non disponible";
	public function ScoLOMfrDAO($serviceAccess) {
		parent::__construct ( $serviceAccess );
	}
	protected function acquireXMLString() {
		assert ( isset ( $this->url ) );
		$scolomfr = $this->serviceAccess->getScoLomFr ( $this->url );
		return $scolomfr;
	}
	function getDefaultNameSpace() {
		return 'lom';
	}
	function getEtag() {
		assert ( false );
	}
	public function updateTitle($title) {
		$this->getTitleElement ()->getElementsByTagName ( "string" )->item ( 0 )->nodeValue = $title;
	}
	public function updateDescription($title) {
		$this->getDescriptionElement ()->getElementsByTagName ( "string" )->item ( 0 )->nodeValue = $title;
	}
	public function updateCoverage($title) {
		$this->getCoverageElement ()->getElementsByTagName ( "string" )->item ( 0 )->nodeValue = $title;
	}
	public function updateKeywords($keywords) {
		$this->cleanKeyWords ();
		foreach ( $keywords as $keyword ) {
			$this->addKeyWord ( $keyword );
		}
	}
	public function updateGeneralResourceType($generalResourceTypes) {
		$this->cleanGeneralResourceTypes ();
		foreach ( $generalResourceTypes as $generalResourceType ) {
			if (is_array ( $generalResourceType ) && !empty ( $generalResourceType ['label'] ) && !empty ( $generalResourceType ['value'] ))
				$this->addGeneralResourceType ( $generalResourceType ['value'], $generalResourceType ['label'] );
		}
	}
	public function updateAggregationLevel($aggregationLevel) {
		$this->cleanAggregationLevel ();
		if (is_array ( $aggregationLevel ) && !empty ( $aggregationLevel ['label'] ) && !empty ( $aggregationLevel ['value'] )) {
			$this->addAggregationLevel ( $aggregationLevel ['value'], $aggregationLevel ['label'] );
		}
	}
	public function updateLearningResourceType($learningResourceTypes) {
		$this->cleanLearningResourceTypes ();
		foreach ( $learningResourceTypes as $learningResourceType ) {
			if (is_array ( $learningResourceType ) && !empty ( $learningResourceType ['label'] ) && !empty ( $learningResourceType ['value'] ))
				$this->addLearningResourceType ( $learningResourceType ['value'], $learningResourceType ['label'] );
		}
	}
	public function updateEducationalDescription($title) {
		$this->getEducationalDescriptionElement ()->getElementsByTagName ( "string" )->item ( 0 )->nodeValue = $title;
	}
	public function updatePlace($places) {
		$this->cleanPlaces ();
		foreach ( $places as $place ) {
			if (is_array ( $place ) && !empty ( $place ['label'] ) && !empty ( $place ['value'] ))
				$this->addPlace ( $place ['value'], $place ['label'] );
		}
	}
	public function updateEducationalMethod($educationalMethods) {
		$this->cleanEducationalMethods ();
		foreach ( $educationalMethods as $educationalMethod ) {
			if (is_array ( $educationalMethod ) && !empty ( $educationalMethod ['label'] ) && !empty ( $educationalMethod ['value'] ))
				$this->addEducationalMethod ( $educationalMethod ['value'], $educationalMethod ['label'] );
		}
	}
	public function updateActivity($activities) {
		$this->cleanActivities ();
		foreach ( $activities as $activity ) {
			if (is_array ( $activity ) && !empty ( $activity ['label'] ) && !empty ( $activity ['value'] ))
				$this->addActivity ( $activity ['value'], $activity ['label'] );
		}
	}
	public function updateIntendedEndUserRole($intendedEndUserRoles) {
		$this->cleanIntendedEndUserRoles ();
		foreach ( $intendedEndUserRoles as $intendedEndUserRole ) {
			if (is_array ( $intendedEndUserRole ) && !empty ( $intendedEndUserRole ['label'] ) && !empty ( $intendedEndUserRole ['value'] ))
				$this->addIntendedEndUserRole ( $intendedEndUserRole ['value'], $intendedEndUserRole ['label'] );
		}
	}
	public function updateDifficulty($difficulty) {
		$this->cleanDifficulty ();
		if (is_array ( $difficulty ) && !empty ( $difficulty ['label'] ) && !empty( $difficulty ['value'] )) {
			$this->addDifficulty ( $difficulty ['value'], $difficulty ['label'] );
		}
	}
	public function updateClassifications($classifications) {
		foreach ( $classifications as $key => $value ) {
			$this->cleanClassification ( $value ["standard"], $key );
			$classification = $this->createClassificationElement ( $value ["standard"], $key );
			while ( $classification->getElementsByTagName ( "taxonPath" )->length > 0 ) {
				$classification->removeChild ( $classification->getElementsByTagName ( "taxonPath" )->item ( 0 ) );
			}
			$paths = $value ["taxonPaths"];
			for($i = 0; $i < count ( $paths ); $i ++) {
				$taxonPath = $this->createTaxonPath ( $classification, $paths [$i] ["source"] );
				$taxons = $paths [$i] ["taxons"];
				for($j = 0; $j < count ( $taxons ); $j ++) {
					$this->createTaxon ( $taxonPath, $taxons [$j] ['id'], $taxons [$j] ['entry'] );
				}
			}
		}
		$this->removeVoidClassifications ();
	}
	public function updateContributors($vcards, $roleIds, $roleLabels, $dates) {
		$this->cleanContributes ();
		for($i = 0; $i < count ( $vcards ); $i ++) {
			$this->createContribute ( $vcards [$i], $roleIds [$i], $roleLabels [$i], $dates [$i] );
		}
	}
	private function cleanContributes() {
		while ( $this->getLifeCycleElement ()->getElementsByTagName ( "contribute" )->length > 0 ) {
			$this->getLifeCycleElement ()->removeChild ( $this->getLifeCycleElement ()->getElementsByTagName ( "contribute" )->item ( 0 ) );
		}
	}
	public function getGeneralElement() {
		return $this->document->getElementsByTagName ( "general" )->item ( 0 );
	}
	public function getLomElement() {
		return $this->document->getElementsByTagName ( "lom" )->item ( 0 );
	}
	public function getEducationalElement() {
		$educationalElements = $this->document->getElementsByTagName ( "educational" );
		if ($educationalElements->length == 0) {
			$educationalElement = $this->document->createElement ( "educational" );
			$this->getLomElement ()->appendChild ( $educationalElement );
		} else {
			$educationalElement = $educationalElements->item ( 0 );
		}
		return $educationalElement;
	}
	public function getEducationalDescriptionElement() {
		return $this->getEducationalElement ()->getElementsByTagName ( "description" )->item ( 0 );
	}
	public function cleanClassification($source, $value) {
		$classifications = $this->document->getElementsByTagName ( "classification" );
		$length = $classifications->length;
		$classificationsToRemove = array ();
		for($i = 0; $i < $length; $i ++) {
			$classification = $classifications->item ( $i );
			$purpose = $classification->getElementsByTagName ( "purpose" );
			if ($purpose->length == 0)
				continue;
			$purposeElem = $purpose->item ( 0 );
			$sources = $purposeElem->getElementsByTagName ( "source" );
			$values = $purposeElem->getElementsByTagName ( "value" );
			if ($sources->length == 0 || $values->length == 0)
				continue;
			if(/*$sources->item(0)->value==$source &&*/ $values->item ( 0 )->nodeValue == $value)
				array_push ( $classificationsToRemove, $classification );
		}
		foreach ( $classificationsToRemove as $classificationToRemove ) {
			$classificationToRemove->parentNode->removeChild ( $classificationToRemove );
		}
	}
	private function cleanKeyWords() {
		while ( $this->getGeneralElement ()->getElementsByTagName ( "keyword" )->length > 0 ) {
			$this->getGeneralElement ()->removeChild ( $this->getGeneralElement ()->getElementsByTagName ( "keyword" )->item ( 0 ) );
		}
	}
	private function cleanGeneralResourceTypes() {
		while ( $this->getGeneralElement ()->getElementsByTagNameNS ( "http://www.lom-fr.fr/xsd/SCOLOMFR", "generalResourceType" )->length > 0 ) {
			$this->getGeneralElement ()->removeChild ( $this->getGeneralElement ()->getElementsByTagNameNS ( "http://www.lom-fr.fr/xsd/SCOLOMFR", "generalResourceType" )->item ( 0 ) );
		}
	}
	private function cleanAggregationLevel() {
		while ( $this->getGeneralElement ()->getElementsByTagName ( "aggregationLevel" )->length > 0 ) {
			$this->getGeneralElement ()->removeChild ( $this->getGeneralElement ()->getElementsByTagName ( "aggregationLevel" )->item ( 0 ) );
		}
	}
	private function cleanLearningResourceTypes() {
		while ( $this->getEducationalElement ()->getElementsByTagName ( "learningResourceType" )->length > 0 ) {
			$this->getEducationalElement ()->removeChild ( $this->getEducationalElement ()->getElementsByTagName ( "learningResourceType" )->item ( 0 ) );
		}
	}
	private function cleanPlaces() {
		while ( $this->getEducationalElement ()->getElementsByTagNameNS ( "http://www.lom-fr.fr/xsd/SCOLOMFR", "place" )->length > 0 ) {
			$this->getEducationalElement ()->removeChild ( $this->getEducationalElement ()->getElementsByTagNameNS ( "http://www.lom-fr.fr/xsd/SCOLOMFR", "place" )->item ( 0 ) );
		}
	}
	private function cleanEducationalMethods() {
		while ( $this->getEducationalElement ()->getElementsByTagNameNS ( "http://www.lom-fr.fr/xsd/SCOLOMFR", "educationalMethod" )->length > 0 ) {
			$this->getEducationalElement ()->removeChild ( $this->getEducationalElement ()->getElementsByTagNameNS ( "http://www.lom-fr.fr/xsd/SCOLOMFR", "educationalMethod" )->item ( 0 ) );
		}
	}
	private function cleanActivities() {
		while ( $this->getEducationalElement ()->getElementsByTagNameNS ( "http://www.lom-fr.fr/xsd/LOMFR", "activity" )->length > 0 ) {
			$this->getEducationalElement ()->removeChild ( $this->getEducationalElement ()->getElementsByTagNameNS ( "http://www.lom-fr.fr/xsd/LOMFR", "activity" )->item ( 0 ) );
		}
	}
	private function cleanIntendedEndUserRoles() {
		while ( $this->getEducationalElement ()->getElementsByTagName ( "intendedEndUserRole" )->length > 0 ) {
			$this->getEducationalElement ()->removeChild ( $this->getEducationalElement ()->getElementsByTagName ( "intendedEndUserRole" )->item ( 0 ) );
		}
	}
	private function cleanDifficulty() {
		while ( $this->getEducationalElement ()->getElementsByTagName ( "difficulty" )->length > 0 ) {
			$this->getEducationalElement ()->removeChild ( $this->getEducationalElement ()->getElementsByTagName ( "difficulty" )->item ( 0 ) );
		}
	}
	private function addKeyWord($keyword) {
		$keywordElem = $this->document->createElement ( "keyword" );
		$string = $this->document->createElement ( "string" );
		$string->setAttribute ( 'language', 'fr' );
		$value = $this->document->createTextNode ( $keyword );
		$string->appendChild ( $value );
		$keywordElem->appendChild ( $string );
		$this->getGeneralElement ()->appendChild ( $keywordElem );
	}
	private function addGeneralResourceType($resourceTypeValue, $resourceTypeLabel) {
		$generalResourceTypeElem = $this->document->createElement ( "scolomfr:generalResourceType" );
		$source = $this->document->createElement ( "scolomfr:source", "SCOLOMFRv2.0" );
		$value = $this->document->createElement ( "scolomfr:value", $resourceTypeValue );
		$label = $this->document->createElement ( "scolomfr:label", $resourceTypeLabel );
		$generalResourceTypeElem->appendChild ( $source );
		$generalResourceTypeElem->appendChild ( $value );
		$generalResourceTypeElem->appendChild ( $label );
		$this->getGeneralElement ()->appendChild ( $generalResourceTypeElem );
	}
	private function addAggregationLevel($aggregationLevelValue, $aggregationLevelLabel) {
		$aggregationLevelElem = $this->document->createElement ( "aggregationLevel" );
		$source = $this->document->createElement ( "source", "LOMv1.0" );
		$value = $this->document->createElement ( "value", $aggregationLevelValue );
		$label = $this->document->createElement ( "label", $aggregationLevelLabel );
		$aggregationLevelElem->appendChild ( $source );
		$aggregationLevelElem->appendChild ( $value );
		$aggregationLevelElem->appendChild ( $label );
		$this->getGeneralElement ()->appendChild ( $aggregationLevelElem );
	}
	private function addLearningResourceType($resourceTypeValue, $resourceTypeLabel) {
		$learningResourceTypeElem = $this->document->createElement ( "learningResourceType" );
		$source = $this->document->createElement ( "source", "LOMFRv1.0" );
		$value = $this->document->createElement ( "value", $resourceTypeValue );
		$label = $this->document->createElement ( "label", $resourceTypeLabel );
		$learningResourceTypeElem->appendChild ( $source );
		$learningResourceTypeElem->appendChild ( $value );
		$learningResourceTypeElem->appendChild ( $label );
		$this->getEducationalElement ()->appendChild ( $learningResourceTypeElem );
	}
	private function addPlace($placeValue, $placeLabel) {
		$placeElem = $this->document->createElement ( "scolomfr:place" );
		$source = $this->document->createElement ( "scolomfr:source", "SCOLOMFRv1.0" );
		$value = $this->document->createElement ( "scolomfr:value", $placeValue );
		$label = $this->document->createElement ( "scolomfr:label", $placeLabel );
		$placeElem->appendChild ( $source );
		$placeElem->appendChild ( $value );
		$placeElem->appendChild ( $label );
		$this->getEducationalElement ()->appendChild ( $placeElem );
	}
	private function addEducationalMethod($educationalMethodValue, $educationalMethodLabel) {
		$educationalMethodElem = $this->document->createElement ( "scolomfr:educationalMethod" );
		$source = $this->document->createElement ( "scolomfr:source", "SCOLOMFRv1.0" );
		$value = $this->document->createElement ( "scolomfr:value", $educationalMethodValue );
		$label = $this->document->createElement ( "scolomfr:label", $educationalMethodLabel );
		$educationalMethodElem->appendChild ( $source );
		$educationalMethodElem->appendChild ( $value );
		$educationalMethodElem->appendChild ( $label );
		$this->getEducationalElement ()->appendChild ( $educationalMethodElem );
	}
	private function addActivity($activityValue, $activityLabel) {
		$activityElem = $this->document->createElement ( "lomfr:activity" );
		$source = $this->document->createElement ( "lomfr:source", "LOMFRv1.0" );
		$value = $this->document->createElement ( "lomfr:value", $activityValue );
		$label = $this->document->createElement ( "lomfr:label", $activityLabel );
		$activityElem->appendChild ( $source );
		$activityElem->appendChild ( $value );
		$activityElem->appendChild ( $label );
		$this->getEducationalElement ()->appendChild ( $activityElem );
	}
	private function addIntendedEndUserRole($intendedEndUserRoleValue, $intendedEndUserRoleLabel) {
		$intendedEndUserRoleElem = $this->document->createElement ( "intendedEndUserRole" );
		$source = $this->document->createElement ( "source", "LOMv1.0" );
		$value = $this->document->createElement ( "value", $intendedEndUserRoleValue );
		$label = $this->document->createElement ( "label", $intendedEndUserRoleLabel );
		$intendedEndUserRoleElem->appendChild ( $source );
		$intendedEndUserRoleElem->appendChild ( $value );
		$intendedEndUserRoleElem->appendChild ( $label );
		$this->getEducationalElement ()->appendChild ( $intendedEndUserRoleElem );
	}
	private function addDifficulty($difficultyValue, $difficultyLabel) {
		$difficultyElem = $this->document->createElement ( "difficulty" );
		$source = $this->document->createElement ( "source", "LOMv1.0" );
		$value = $this->document->createElement ( "value", $difficultyValue );
		$label = $this->document->createElement ( "label", $difficultyLabel );
		$difficultyElem->appendChild ( $source );
		$difficultyElem->appendChild ( $value );
		$difficultyElem->appendChild ( $label );
		$this->getEducationalElement ()->appendChild ( $difficultyElem );
	}
	public function removeVoidClassifications() {
		$classifications = $this->document->getElementsByTagName ( "classification" );
		$length = $classifications->length;
		for($i = $length - 1; $i >= 0; $i --) {
			$classification = $classifications->item ( $i );
			$taxon = $classification->getElementsByTagName ( "taxon" );
			if ($taxon->length == 0)
				$classification->parentNode->removeChild ( $classification );
		}
	}
	private function createContribute($vcard, $roleId, $roleLabel, $date) {
		$contribute = $this->document->createElement ( "contribute" );
		$roleElem = $this->document->createElement ( "role" );
		$entity = $this->document->createElement ( "entity" );
		$dateElem = $this->document->createElement ( "date" );
		$source = $this->document->createElement ( "source" );
		$value = $this->document->createElement ( "value" );
		$label = $this->document->createElement ( "label" );
		$dateTime = $this->document->createElement ( "dateTime" );
		$sourceValue = $this->document->createTextNode ( "LOMv1.0" );
		$valueValue = $this->document->createTextNode ( $roleId );
		$labelValue = $this->document->createTextNode ( $roleLabel );
		$entityValue = $this->document->createTextNode ( $vcard );
		$dateTimeValue = $this->document->createTextNode ( $date );
		$source->appendChild ( $sourceValue );
		$value->appendChild ( $valueValue );
		$label->appendChild ( $labelValue );
		$roleElem->appendChild ( $source );
		$roleElem->appendChild ( $value );
		$roleElem->appendChild ( $label );
		$contribute->appendChild ( $roleElem );
		$entity->appendChild ( $entityValue );
		$contribute->appendChild ( $entity );
		$dateTime->appendChild ( $dateTimeValue );
		$dateElem->appendChild ( $dateTime );
		$contribute->appendChild ( $dateElem );
		$this->getLifeCycleElement ()->appendChild ( $contribute );
	}
	public function createClassificationElement($source, $value) {
		$classification = $this->document->createElement ( "classification" );
		$this->document->documentElement->appendChild ( $classification );
		$purpose = $this->document->createElement ( "purpose" );
		$sourceElem = $this->document->createElement ( "source" );
		$valueElem = $this->document->createElement ( "value" );
		$sourceValue = $this->document->createTextNode ( $source );
		$valueValue = $this->document->createTextNode ( $value );
		$purpose->appendChild ( $sourceElem );
		$sourceElem->appendChild ( $sourceValue );
		$purpose->appendChild ( $valueElem );
		$valueElem->appendChild ( $valueValue );
		$classification->appendChild ( $purpose );
		return $classification;
	}
	private function createTaxonPath($classification, $source) {
		$taxonPath = $this->document->createElement ( "taxonPath" );
		$sourceElem = $this->document->createElement ( "source" );
		$string = $this->document->createElement ( "string" );
		$string->setAttribute ( 'language', 'fr' );
		$stringValue = $this->document->createTextNode ( $source );
		$string->appendChild ( $stringValue );
		$sourceElem->appendChild ( $string );
		$taxonPath->appendChild ( $sourceElem );
		$classification->appendChild ( $taxonPath );
		return $taxonPath;
	}
	private function createTaxon($taxonPath, $id, $entry) {
		$taxon = $this->document->createElement ( "taxon" );
		$idElem = $this->document->createElement ( "id" );
		$idValue = $this->document->createTextNode ( $id );
		$idElem->appendChild ( $idValue );
		$entryElem = $this->document->createElement ( "entry" );
		$string = $this->document->createElement ( "string" );
		$string->setAttribute ( 'language', 'fr' );
		$stringValue = $this->document->createTextNode ( $entry );
		$string->appendChild ( $stringValue );
		$entryElem->appendChild ( $string );
		$taxon->appendChild ( $idElem );
		$taxon->appendChild ( $entryElem );
		$taxonPath->appendChild ( $taxon );
		return $taxonPath;
	}
	public function getTitleElement() {
		return $this->getGeneralElement ()->getElementsByTagName ( "title" )->item ( 0 );
	}
	public function getCoverageElement() {
		return $this->getGeneralElement ()->getElementsByTagName ( "coverage" )->item ( 0 );
	}
	public function getDescriptionElement() {
		return $this->getGeneralElement ()->getElementsByTagName ( "description" )->item ( 0 );
	}
	public function getLifeCycleElement() {
		return $this->document->getElementsByTagName ( "lifeCycle" )->item ( 0 );
	}
	public function send($mdid, $ifMatch) {
		$this->document->formatOutput = false;
		$this->serviceAccess->sendMetadataFile ( $this->document->saveXML (), $mdid, $ifMatch );
	}
}

