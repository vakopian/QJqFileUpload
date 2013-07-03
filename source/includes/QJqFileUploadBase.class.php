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

		public function RenderAjax($blnDisplayOutput = true) {
			if ($blnDisplayOutput) {
				$this->initialize();
				return null;
			}
			$content = $this->post(false);
			return json_encode($content);
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
		protected $blnPerUserDirs;
		/** @var string */
		protected $strUploadDir;
		/** @var \QJqFileUploadHandler */
		protected $objUploadHandler;
		/** @var int */
		protected $intUiType;

		public function __construct($objParentObject, $intUiType = QJqFileUploadType::BASIC, $blnUseBootstrapCss = true, $strControlId = null) {
			parent::__construct($objParentObject, $strControlId);
			$this->intUiType = $intUiType;

			$pluginIncludes = '../../plugins/QJqFileUpload/includes/';
			if ($blnUseBootstrapCss) {
				$this->AddCssFile($pluginIncludes . 'bootstrap/css/bootstrap.min.css');
				$this->AddCssFile($pluginIncludes . 'bootstrap/css/bootstrap-responsive.min.css');
			}
			$this->AddCssFile($pluginIncludes . 'jQuery-File-Upload/css/jquery.fileupload-ui.css');
			if ($intUiType >= QJqFileUploadType::BASIC_PLUS) {
				$this->AddJavascriptFile($pluginIncludes . 'JavaScript-Load-Image/js/load-image.min.js');
				$this->AddJavascriptFile($pluginIncludes . 'JavaScript-Canvas-to-Blob/js/canvas-to-blob.min.js');
				$this->AddCssFile($pluginIncludes . 'jQuery-File-Upload/css/style.css');
			}
			$this->AddJavascriptFile($pluginIncludes . 'jQuery-File-Upload/js/jquery.iframe-transport.js');
			$this->AddJavascriptFile($pluginIncludes . 'jQuery-File-Upload/js/jquery.fileupload.js');
			if ($intUiType >= QJqFileUploadType::BASIC_PLUS) {
				$this->AddJavascriptFile($pluginIncludes . 'jQuery-File-Upload/js/jquery.fileupload-process.js');
				$this->AddJavascriptFile($pluginIncludes . 'jQuery-File-Upload/js/jquery.fileupload-image.js');
				$this->AddJavascriptFile($pluginIncludes . 'jQuery-File-Upload/js/jquery.fileupload-audio.js');
				$this->AddJavascriptFile($pluginIncludes . 'jQuery-File-Upload/js/jquery.fileupload-video.js');
				$this->AddJavascriptFile($pluginIncludes . 'jQuery-File-Upload/js/jquery.fileupload-validate.js');
			}

			if ($intUiType >= QJqFileUploadType::BASIC_PLUS_UI) {
				$this->AddCssFile($pluginIncludes . 'Gallery/css/blueimp-gallery.min.css');
			}

			$this->objUploadHandler = new QJqFileUploadHandler($this, array(
				'param_name' => $this->ControlId
			));

			$this->strUploadDir = sys_get_temp_dir() . '/';
			$this->blnUseWrapper = false;
			$this->SetCustomAttribute('multiple', 'multiple');
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
			return $this->objUploadHandler->RenderAjax($blnDisplayOutput);
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

		protected function getUploadTemplate() {
			return '{% for (var i=0, file; file=o.files[i]; i++) { %}
			    <tr class="template-upload fade">
			        <td>
			            <span class="preview"></span>
			        </td>
			        <td>
			            <p class="name">{%=file.name%}</p>
			            {% if (file.error) { %}
			                <div><span class="label label-important">Error</span> {%=file.error%}</div>
			            {% } %}
			        </td>
			        <td>
			            <p class="size">{%=o.formatFileSize(file.size)%}</p>
			            {% if (!o.files.error) { %}
			                <div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="bar" style="width:0%;"></div></div>
			            {% } %}
			        </td>
			        <td>
			            {% if (!o.files.error && !i && !o.options.autoUpload) { %}
			                <button class="btn btn-primary start">
			                    <i class="icon-upload icon-white"></i>
			                    <span>Start</span>
			                </button>
			            {% } %}
			            {% if (!i) { %}
			                <button class="btn btn-warning cancel">
			                    <i class="icon-ban-circle icon-white"></i>
			                    <span>Cancel</span>
			                </button>
			            {% } %}
			        </td>
			    </tr>
			{% } %}';
		}

		protected function getDownloadTemplate() {
			return '{% for (var i=0, file; file=o.files[i]; i++) { %}
			    <tr class="template-download fade">
			        <td>
			            <span class="preview">
			                {% if (file.thumbnail_url) { %}
			                    <a href="{%=file.url%}" title="{%=file.name%}" class="gallery" download="{%=file.name%}"><img src="{%=file.thumbnail_url%}"></a>
			                {% } %}
			            </span>
			        </td>
			        <td>
			            <p class="name">
			                <a href="{%=file.url%}" title="{%=file.name%}" class="{%=file.thumbnail_url?\'gallery\':\'\'%}" download="{%=file.name%}">{%=file.name%}</a>
			            </p>
			            {% if (file.error) { %}
			                <div><span class="label label-important">Error</span> {%=file.error%}</div>
			            {% } %}
			        </td>
			        <td>
			            <span class="size">{%=o.formatFileSize(file.size)%}</span>
			        </td>
			        <td>
			            <button class="btn btn-danger delete" data-type="{%=file.delete_type%}" data-url="{%=file.delete_url%}"{% if (file.delete_with_credentials) { %} data-xhr-fields=\'{"withCredentials":true}\'{% } %}>
			                <i class="icon-trash icon-white"></i>
			                <span>Delete</span>
			            </button>
			            <input type="checkbox" name="delete" value="1" class="toggle">
			        </td>
			    </tr>
			{% } %}';
		}

		protected function GetControlHtml() {
			$strStyle = $this->GetStyleAttributes();
			if ($strStyle)
				$strStyle = sprintf('style="%s"', $strStyle);

			$strFileControlHtml = sprintf('<input type="file" name="%s[]" id="%s" %s%s />',
				$this->strControlId,
				$this->strControlId,
				$this->GetAttributes(),
				$strStyle);

			switch ($this->intUiType) {
				case QJqFileUploadType::BASIC_PLUS_UI:
					$strResult = sprintf('
<div class="row fileupload-buttonbar">
	<div class="span7">
		<span class="btn btn-success fileinput-button">
			<i class="icon-plus icon-white"></i>
			<span>%s</span>
			%s
		</span>
		<button type="submit" class="btn btn-primary start">
			<i class="icon-upload icon-white"></i>
			<span>%s</span>
		</button>
		<button type="reset" class="btn btn-warning cancel">
			<i class="icon-ban-circle icon-white"></i>
			<span>%s</span>
		</button>
		<button type="button" class="btn btn-danger delete">
			<i class="icon-trash icon-white"></i>
			<span>%s</span>
		</button>
	</div>
	<div class="span5 fileupload-progress fade">
		<!-- The global progress bar -->
		<div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100">
			<div class="bar" style="width:0%%;"></div>
		</div>
		<!-- The extended global progress information -->
		<div class="progress-extended">&nbsp;</div>
	</div>
</div>
<table role="presentation" class="table table-striped"><tbody class="files"></tbody></table>
<div id="blueimp-gallery" class="blueimp-gallery blueimp-gallery-controls">
	<div class="slides"></div>
	<h3 class="title"></h3>
	<a class="prev">‹</a>
	<a class="next">›</a>
	<a class="close">×</a>
	<ol class="indicator"></ol>
</div>
<script id="template-upload" type="text/x-tmpl">%s</script>
<script id="template-download" type="text/x-tmpl">%s</script>',
						QApplication::Translate('Add files...'), $strFileControlHtml, QApplication::Translate('Start upload'), QApplication::Translate('Cancel upload'), QApplication::Translate('Delete'),
						$this->getUploadTemplate(),
						$this->getDownloadTemplate()
					);
					return $strResult;
				case QJqFileUploadType::BASIC_PLUS:
				case QJqFileUploadType::BASIC:
				default:
					return sprintf('
<span class="btn btn-success fileinput-button">
	<i class="icon-plus icon-white"></i>
	<span>%s</span>
	%s
</span>
<div id="progress" class="progress progress-success progress-striped">
	<div class="bar"></div>
</div>
<div id="files" class="files"></div>',
						$this->intUiType == QJqFileUploadType::BASIC_PLUS ? QApplication::Translate('Add Files ...') : QApplication::Translate('Select Files ...'), $strFileControlHtml);
			}
		}

		public function GetControlJavaScript() {
			if ($this->intUiType == QJqFileUploadType::BASIC_PLUS_UI) {
				return '';
			}
			return parent::GetControlJavaScript();
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
					$this->strUploadDir = rtrim(QType::Cast($mixValue, QType::String), '/') . '/';
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