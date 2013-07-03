<?php
	require('../../../../includes/configuration/prepend.inc.php');

	class ExampleForm extends QForm {
		/** @var QSimpleTable */
		protected $objFileUpload;

		protected function Form_Create() {
			// Define the DataGrid
			$this->objFileUpload = new QJqFileUpload($this);
		}
	}

	ExampleForm::Run('ExampleForm');
?>