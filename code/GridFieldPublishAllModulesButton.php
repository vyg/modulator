<?php

class GridFieldPublishAllModulesButton implements GridField_HTMLProvider {

	protected $targetFragment;

	public function __construct($targetFragment = 'before') {
		$this->targetFragment = $targetFragment;
	}

	public function getHTMLFragments($gridField) {

		$data = new ArrayData(array(
			'PublishLink' => Controller::join_links($gridField->Link('item'), 'publishall'),
			'ButtonName' => "Publish All",
		));

		return array(
			$this->targetFragment => $data->renderWith("GridFieldPublishAllModulesButton"),
		);
	}

}
