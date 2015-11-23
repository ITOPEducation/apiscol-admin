<?php
abstract class AbstractDAO implements IDAO {
	/**
	 *
	 * @var array
	 */
	private $options;
	/**
	 *
	 * @var DOMDocument
	 */
	protected $document;
	protected $xpath;
	protected $xmlString;
	/**
	 *
	 * @var ServiceAccess
	 */
	protected $serviceAccess;
	protected $id;
	protected $url;
	protected $isBuilt;
	public function __construct($serviceAccess) {
		$this->serviceAccess = $serviceAccess;
		$this->isBuilt = false;
		$this->options = array ();
	}
	public function getDocument() {
		return $this->document;
	}
	public function getXPath() {
		return $this->xpath;
	}
	public function build() {
		$document = new DOMDocument ();
		if (! isset ( $this->xmlString ))
			$this->xmlString = $this->acquireXMLString ();
		$document->preserveWhiteSpace = false;
		$success = @$document->loadXML ( $this->xmlString );
		if (! $success) {
			$error = error_get_last ();
			throw new CorruptedXMLStringException ( $error ["message"] . " // Données brutes : " . strip_tags ( $this->xmlString ) );
		}
		$this->setDocument ( $document );
	}
	public function setDocument(DOMDocument $document) {
		$this->document = $document;
		$this->xpath = new DOMXpath ( $this->document );
		$this->setXPathNameSpace ();
		$this->isBuilt = true;
	}
	public function setId($id) {
		$this->id = $id;
	}
	public function getId() {
		return $this->id;
	}
	abstract protected function acquireXMLString();
	abstract protected function getDefaultNameSpace();
	protected function setXPathNameSpace() {
		$rootNamespace = $this->document->lookupNamespaceUri ( $this->document->namespaceURI );
		$this->xpath->registerNamespace ( $this->getDefaultNameSpace (), $rootNamespace );
	}
	public function getDocumentAsString() {
		assert ( $this->isBuilt );
		$this->document->formatOutput = true;
		return $this->document->saveXML ();
	}
	// devrait être abstraite
	// Fatal error: Can't inherit abstract function IDAO::getEtag() (previously declared abstract in AbstractDAO) in /web/c/h/joachim-dornbusch/http/aps_adm/libs/AbstractDAO.class.php on line 2
	public function getEtag() {
		return "";
	}
	public function setXMLString($xmlString) {
		$this->xmlString = $xmlString;
	}
	public function setUrl($url) {
		$this->url = $url;
	}
	public function isBuilt() {
		return $this->isBuilt;
	}
	public function setOption($key, $value) {
		$this->options [$key] = $value;
	}
	protected function getOption($key) {
		if (array_key_exists ( $key, $this->options ))
			return $this->options [$key];
		return null;
	}
}
?>