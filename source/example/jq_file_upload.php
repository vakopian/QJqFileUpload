<?php
	require('../../../../includes/configuration/prepend.inc.php');

	class ExampleForm extends QForm {
		/** @var QSimpleTable */
		protected $objFileUpload;
		/** @var  QLabel */
		protected $lblStatus;

		protected function Form_Create() {
			// Define the DataGrid
			$this->objFileUpload = new QJqFileUpload($this);
			$this->objFileUpload->AddAction(new QJqFileUpload_doneEvent(), new QAjaxAction("uploadDone"));
			$this->lblStatus = new QLabel($this);
		}

		public function uploadDone($strFormId, $strControlId, $strParameter) {
			$this->lblStatus->Text = 'Upload Done!';
		}
	}

	ExampleForm::Run('ExampleForm');
?>