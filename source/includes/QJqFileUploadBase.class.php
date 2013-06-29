<?php

	class QJqFileUploadHandler extends UploadHandler {
		/** @var  QJqFileUploadBase */
		private $objTarget;

		public function __construct(QJqFileUploadBase $objTarget, $options = null) {
			parent::__construct($options, false);
			$this->objTarget = $objTarget;
			/** @noinspection PhpUnusedLocalVariableInspection */
			foreach ($this->error_messages as $k => &$v) {
				$v = QApplication::Translate($v);
			}
		}

		protected function get_upload_path($file_name = null, $version = null) {
			return $this->objTarget->GetUploadPath($file_name, $version);
		}

		protected function get_user_id() {
			return $this->objTarget->GetUserId();
		}

		protected function get_user_path() {
			return $this->objTarget->GetUserPath();
		}

		public function HasOption($strName) {
			return array_key_exists($strName, $this->options);
		}

		public function SetOption($strName, $mixValue) {
			return $this->options[$strName] = $mixValue;
		}

		public function ParsePostData() {
			$this->initialize();
		}

		public function RenderAjax() {
			return $this->initialize();
		}
	}

	abstract class QJqFileUploadType {
		const BASIC = 0;
		const BASIC_PLUS = 1;
		const BASIC_PLUS_UI = 2;
	}

	/**
	 * Class QJqFileUploadBase
	 *
	 * @property string $UploadDir
	 * @property boolean $PerUserDirs
	 */
	class QJqFileUploadBase extends QJqFileUploadGen
	{
		/** @var  boolean */
		private $blnPerUserDirs;
		/** @var string */
		private $strUploadDir;
		/** @var \QJqFileUploadHandler */
		private $objUploadHandler;

		public function __construct($objParentObject, $strControlId = null, $intUiType = QJqFileUploadType::BASIC) {
			parent::__construct($objParentObject, $strControlId);

			if ($intUiType >= QJqFileUploadType::BASIC_PLUS) {
				$this->AddJavascriptFile('../../plugins/QJqFileUpload/js/load-image.min.js');
				$this->AddJavascriptFile('../../plugins/QJqFileUpload/js/canvas-to-blob.min.js');
				$this->AddCssFile('../../plugins/QJqFileUpload/css/bootstrap.min.css');
				$this->AddCssFile('../../plugins/QJqFileUpload/css/style.css');
				$this->AddCssFile('../../plugins/QJqFileUpload/css/bootstrap-responsive.min.css');
				$this->AddCssFile('../../plugins/QJqFileUpload/css/jquery.fileupload-ui.css');
			}
			$this->AddJavascriptFile('../../plugins/QJqFileUpload/js/jquery.iframe-transport.js');
			$this->AddJavascriptFile('../../plugins/QJqFileUpload/js/jquery.fileupload.js');
			if ($intUiType >= QJqFileUploadType::BASIC_PLUS) {
				$this->AddJavascriptFile('../../plugins/QJqFileUpload/js/jquery.fileupload-process.js');
				$this->AddJavascriptFile('../../plugins/QJqFileUpload/js/jquery.fileupload-image.js');
				$this->AddJavascriptFile('../../plugins/QJqFileUpload/js/jquery.fileupload-audio.js');
				$this->AddJavascriptFile('../../plugins/QJqFileUpload/js/jquery.fileupload-video.js');
				$this->AddJavascriptFile('../../plugins/QJqFileUpload/js/jquery.fileupload-validate.js');
			}

			if ($intUiType >= QJqFileUploadType::BASIC_PLUS_UI) {
				$this->AddCssFile('../../plugins/QJqFileUpload/css/blueimp-gallery.min.css');
			}

			$this->objUploadHandler = new QJqFileUploadHandler($this, array(
				'param_name' => $this->ControlId
			));
		}

		protected function makeJqOptions() {
			$strJqOptions = parent::makeJqOptions();
			if ($strJqOptions) {
				$strJqOptions .= ', ';
			}
			return $strJqOptions . 'url: ""';
		}

		public function ParsePostData() {
			if (array_key_exists($this->strControlId, $_FILES)) {
				$this->objUploadHandler->ParsePostData();
			}
		}

		public function RenderAjax($blnDisplayOutput = true) {
			return $this->objUploadHandler->RenderAjax();
		}

		public function Validate() {
			// do nothing
		}

		public function GetUserId() {
			@session_start();
			return session_id();
		}

		public function GetUserPath() {
			if ($this->blnPerUserDirs) {
				return $this->GetUserId().'/';
			}
			return '';
		}

		public function GetUploadPath($strFileName = null, $strVersion = null) {
			return $this->UploadDir. $this->GetUserPath() . ($strVersion ? $strVersion.'/' : '') . ($strFileName ? $strFileName : '');
		}

		/////////////////////////
		// Public Properties: GET
		/////////////////////////
		public function __get($strName) {
			switch ($strName) {
				// MISC
				case 'UploadDir': return $this->strUploadDir;
				case 'PerUserDirs': return $this->blnPerUserDirs;

				default:
					try {
						return parent::__get($strName);
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
			}
		}

		/////////////////////////
		// Public Properties: SET
		/////////////////////////
		public function __set($strName, $mixValue) {
			$this->blnModified = true;

			switch ($strName) {
				case 'UploadDir':
					$this->strUploadDir = QType::Cast($mixValue, QType::String);
					break;
				case 'PerUserDirs':
					$this->blnPerUserDirs = QType::Cast($mixValue, QType::Boolean);
					break;
				default:
					try {
						if ($this->objUploadHandler->HasOption($strName)) {
							$this->objUploadHandler->SetOption($strName, $mixValue);
						}
						parent::__set($strName, $mixValue);
					} catch (QCallerException $objExc) {
						$objExc->IncrementOffset();
						throw $objExc;
					}
					break;
			}
		}
	}
?>