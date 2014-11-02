<?php
class BaseJob {

	protected $name = null;
	protected $description = null;
	protected $title = null;
	protected $parameters = array();

	public function getName() { return $this->name;}
	public function getTitle() { return $this->title;}
	public function getDescription() { return $this->description;}
	public function runJob() {}

	public function getParameters(){

		return array();
	}

	public function getHtmlOfParameters()
	{

		$this->parameters = $this->getParameters();


		$html = '';
		if (count($this->parameters)) {
			foreach ($this->parameters as $name => $parameter) {
				if ($parameter['type'] == "select") {
					$html .= "{$parameter['name']} <select name='$name'>";
					if (array_key_exists('options', $parameter)) {
						foreach ($parameter['options'] as $option) {
							$html .= "<option>$option</option>";
						}
					}
					
					$html .= "</select><br/>";
				} else {
					$html .= "{$parameter['name']} <input type='text' name='$name'/><br/>";
				}
			}
		}

		return $html;
		
	}
}