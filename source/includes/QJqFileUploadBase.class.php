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
			exit;
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
		protected static $strStartLabel;
		protected static $strStartUploadLabel;
		protected static $strUploadLabel;
		protected static $strAddFilesLabel;
		protected static $strSelectFilesLabel;
		protected static $strCancelLabel;
		protected static $strCancelUploadLabel;
		protected static $strErrorLabel;
		protected static $strDeleteLabel;
		protected static $strAbortLabel;
		protected static $strProcessingLabel;

		public static function _static_init() {
			self::$strStartLabel = QApplication::HtmlEntities(QApplication::Translate('Start'));
			self::$strStartUploadLabel = QApplication::HtmlEntities(QApplication::Translate('Start upload'));
			self::$strUploadLabel = QApplication::HtmlEntities(QApplication::Translate('Upload'));
			self::$strAddFilesLabel = QApplication::HtmlEntities(QApplication::Translate('Add files...'));
			self::$strSelectFilesLabel = QApplication::HtmlEntities(QApplication::Translate('Select Files ...'));
			self::$strCancelLabel = QApplication::HtmlEntities(QApplication::Translate('Cancel'));
			self::$strCancelUploadLabel = QApplication::HtmlEntities(QApplication::Translate('Cancel upload'));
			self::$strErrorLabel = QApplication::HtmlEntities(QApplication::Translate('Error'));
			self::$strDeleteLabel = QApplication::HtmlEntities(QApplication::Translate('Delete'));
			self::$strAbortLabel = QApplication::HtmlEntities(QApplication::Translate('Abort'));
			self::$strProcessingLabel = QApplication::HtmlEntities(QApplication::Translate('Processing...'));
		}

		/** @var  boolean */
		protected $blnPerUserDirs;
		/** @var string */
		protected $strUploadDir;
		/** @var \QJqFileUploadHandler */
		protected $objUploadHandler;
		/** @var int */
		protected $intUiType = QJqFileUploadType::BASIC;

		public function __construct($objParentObject, $intUiType = QJqFileUploadType::BASIC, $blnUseBootstrap = true, $strControlId = null) {
			parent::__construct($objParentObject, $strControlId);
			$this->strDataType = 'json';
			$this->intUiType = $intUiType;
			$this->strParamName = $this->ControlId . '_files';
			if ($this->intUiType !== QJqFileUploadType::BASIC_PLUS || $this->intUiType !== QJqFileUploadType::BASIC_PLUS_UI) {
				$this->intUiType = QJqFileUploadType::BASIC;
			}

			if ($blnUseBootstrap) {
				$this->AddPluginCssFile('QJqFileUpload', '../bootstrap/css/bootstrap.min.css');
				$this->AddPluginCssFile('QJqFileUpload', '../bootstrap/css/bootstrap-responsive.min.css');
			}
			$this->AddPluginCssFile('QJqFileUpload', '../BlueImp/jQuery-File-Upload/css/jquery.fileupload-ui.css');

			if ($intUiType >= QJqFileUploadType::BASIC_PLUS_UI) {
				$this->AddPluginCssFile('QJqFileUpload', '../BlueImp/Gallery/css/blueimp-gallery.min.css');
				$this->AddPluginJavascriptFile('QJqFileUpload', '../BlueImp/JavaScript-Templates/js/tmpl.min.js');
			}

			if ($intUiType >= QJqFileUploadType::BASIC_PLUS) {
				$this->AddPluginJavascriptFile('QJqFileUpload', '../BlueImp/JavaScript-Load-Image/js/load-image.min.js');
				$this->AddPluginJavascriptFile('QJqFileUpload', '../BlueImp/JavaScript-Canvas-to-Blob/js/canvas-to-blob.min.js');
				$this->AddPluginCssFile('QJqFileUpload', '../BlueImp/jQuery-File-Upload/css/style.css');
			}
			if ($blnUseBootstrap) {
				$this->AddPluginJavascriptFile('QJqFileUpload', '../bootstrap/js/bootstrap.min.js');
			}
			if ($intUiType >= QJqFileUploadType::BASIC_PLUS_UI) {
				$this->AddPluginJavascriptFile('QJqFileUpload', '../BlueImp/Gallery/js/blueimp-gallery.min.js');
			}
			$this->AddPluginJavascriptFile('QJqFileUpload', '../BlueImp/jQuery-File-Upload/js/jquery.iframe-transport.js');
			$this->AddPluginJavascriptFile('QJqFileUpload', '../BlueImp/jQuery-File-Upload/js/jquery.fileupload.js');
			if ($intUiType >= QJqFileUploadType::BASIC_PLUS) {
				$this->AddPluginJavascriptFile('QJqFileUpload', '../BlueImp/jQuery-File-Upload/js/jquery.fileupload-process.js');
				$this->AddPluginJavascriptFile('QJqFileUpload', '../BlueImp/jQuery-File-Upload/js/jquery.fileupload-image.js');
				$this->AddPluginJavascriptFile('QJqFileUpload', '../BlueImp/jQuery-File-Upload/js/jquery.fileupload-audio.js');
				$this->AddPluginJavascriptFile('QJqFileUpload', '../BlueImp/jQuery-File-Upload/js/jquery.fileupload-video.js');
				$this->AddPluginJavascriptFile('QJqFileUpload', '../BlueImp/jQuery-File-Upload/js/jquery.fileupload-validate.js');
			}
			if ($intUiType >= QJqFileUploadType::BASIC_PLUS_UI) {
				$this->AddPluginJavascriptFile('QJqFileUpload', '../BlueImp/jQuery-File-Upload/js/jquery.fileupload-ui.js');
			}

			$this->objUploadHandler = new QJqFileUploadHandler($this, array(
				'param_name' => $this->strParamName
			));

			$this->strUploadDir = sys_get_temp_dir() . '/';
			$this->blnUseWrapper = false;
			$this->SetCustomAttribute('multiple', 'multiple');

			$this->setupDefaultEventHandlers();
		}

		protected function setupDefaultEventHandlers() {
			$this->setupDefaultProgressAllEventHandler();
			$this->setupDefaultDoneEventHandler();
			$this->setupDefaultAddEventHandler();
			$this->setupDefaultProcessAlwaysEventHandler();
		}
		protected function setupDefaultProgressAllEventHandler() {
			$objAction = new QJavaScriptAction("
				var progress = parseInt(ui.loaded / ui.total * 100, 10);
				jQuery('#{$this->ControlId}_progress .bar').css('width', progress + '%');
			");
			$this->AddAction(new QJqFileUpload_progressallEvent(), $objAction);
		}

		protected function setupDefaultDoneEventHandler() {
			if ($this->intUiType == QJqFileUploadType::BASIC) {
				$objAction = new QJavaScriptAction("
					jQuery.each(ui.result.{$this->strParamName}, function (index, file) {
						jQuery('<p/>').text(file.name).appendTo('#{$this->ControlId}_filelist');
					});
				");
				$this->AddAction(new QJqFileUpload_doneEvent(), $objAction);
			} else if ($this->intUiType == QJqFileUploadType::BASIC_PLUS) {
				$objAction = new QJavaScriptAction("
					jQuery.each(ui.result.{$this->strParamName}, function (index, file) {
						var link = jQuery('<a>').attr('target', '_blank').prop('href', file.url);
						jQuery(ui.context.children()[index]).wrap(link);
					});
				");
				$this->AddAction(new QJqFileUpload_doneEvent(), $objAction);
			}
		}

		protected function setupDefaultAddEventHandler() {
			if ($this->intUiType == QJqFileUploadType::BASIC_PLUS) {
				$strProcessingLabel = self::$strProcessingLabel;
				$strAbortLabel = self::$strAbortLabel;
				$objAction = new QJavaScriptAction("
					ui.context = jQuery('<div/>').appendTo('#{$this->ControlId}_filelist');
					jQuery.each(ui.{$this->strParamName}, function (index, file) {
						var node = jQuery('<p/>').append(jQuery('<span/>').text(file.name));
						if (!index) {
							var uploadButton = jQuery('<button/>').addClass('btn').prop('disabled', true).text('$strProcessingLabel')
								.on('click', function () {
									var self = jQuery(this), data = self.data();
									self.off('click').text('$strAbortLabel')
										.on('click', function () {
											self.remove();
											data.abort();
										});
									data.submit().always(function () {
										self.remove();
									});
								});
							node.append('<br>').append(uploadButton.data(ui));
						}
						node.appendTo(ui.context);
					});
				");
				$this->AddAction(new QJqFileUpload_addEvent(), $objAction);
			}
		}

		protected function setupDefaultProcessAlwaysEventHandler() {
			if ($this->intUiType == QJqFileUploadType::BASIC_PLUS) {
				$strUploadLabel = self::$strUploadLabel;
				$objAction = new QJavaScriptAction("
					var index = ui.index, file = ui.{$this->strParamName}[index], node = jQuery(ui.context.children()[index]);
					if (file.preview) {
						node.prepend('<br>').prepend(file.preview);
					}
					if (file.error) {
						node.append('<br>').append(file.error);
					}
					if (index + 1 === ui.{$this->strParamName}.length) {
						ui.context.find('button').text('$strUploadLabel').prop('disabled', !!ui.{$this->strParamName}.error);
					}
				");
				$this->AddAction(new QJqFileUpload_processalwaysEvent(), $objAction);
			}
		}

		protected function setupDefaultFailEventHandler() {
			if ($this->intUiType == QJqFileUploadType::BASIC_PLUS) {
				$objAction = new QJavaScriptAction("
					jQuery.each(ui.result.{$this->strParamName}, function (index, file) {
						var error = jQuery('<span/>').text(file.error);
						jQuery(ui.context.children()[index]).append('<br>').append(error);
					});
				");
				$this->AddAction(new QJqFileUpload_failEvent(), $objAction);
			}
		}

		protected function makeJqOptions() {
			$strJqOptions = parent::makeJqOptions();
			if ($strJqOptions) {
				$strJqOptions .= ', ';
			}
			return $strJqOptions . 'url: ""';
		}

		public function ParsePostData() {
			if (array_key_exists($this->strParamName, $_FILES)) {
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
			$strStartLabel = QApplication::Translate('Start');
			$strCancelLabel = QApplication::Translate('Cancel');
			$strErrorLabel = QApplication::Translate('Error');
			$strResult = <<<SCRIPT
{% for (var i=0, file; file=o.{$this->strParamName}[i]; i++) { %}
	<tr class="template-upload fade">
		<td>
			<span class="preview"></span>
		</td>
		<td>
			<p class="name">{%=file.name%}</p>
			{% if (file.error) { %}
				<div><span class="label label-important">$strErrorLabel</span> {%=file.error%}</div>
			{% } %}
		</td>
		<td>
			<p class="size">{%=o.formatFileSize(file.size)%}</p>
			{% if (!o.{$this->strParamName}.error) { %}
				<div class="progress progress-success progress-striped active" role="progressbar" aria-valuemin="0" aria-valuemax="100" aria-valuenow="0"><div class="bar" style="width:0%;"></div></div>
			{% } %}
		</td>
		<td>
			{% if (!o.{$this->strParamName}.error && !i && !o.options.autoUpload) { %}
				<button class="btn btn-primary start">
					<i class="icon-upload icon-white"></i>
					<span>$strStartLabel</span>
				</button>
			{% } %}
			{% if (!i) { %}
				<button class="btn btn-warning cancel">
					<i class="icon-ban-circle icon-white"></i>
					<span>$strCancelLabel</span>
				</button>
			{% } %}
		</td>
	</tr>
{% } %}
SCRIPT;
			return $strResult;
		}

		protected function getDownloadTemplate() {
			$strDeleteLabel = QApplication::Translate('Delete');
			$strErrorLabel = QApplication::Translate('Error');
			$strResult = <<<SCRIPT
{% for (var i=0, file; file=o.{$this->strParamName}[i]; i++) { %}
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
				<a href="{%=file.url%}" title="{%=file.name%}" class="{%=file.thumbnail_url?'gallery':''%}" download="{%=file.name%}">{%=file.name%}</a>
			</p>
			{% if (file.error) { %}
				<div><span class="label label-important">$strErrorLabel</span> {%=file.error%}</div>
			{% } %}
		</td>
		<td>
			<span class="size">{%=o.formatFileSize(file.size)%}</span>
		</td>
		<td>
			<button class="btn btn-danger delete" data-type="{%=file.delete_type%}" data-url="{%=file.delete_url%}"{% if (file.delete_with_credentials) { %} data-xhr-fields='{"withCredentials":true}'{% } %}>
				<i class="icon-trash icon-white"></i>
				<span>$strDeleteLabel</span>
			</button>
			<input type="checkbox" name="delete" value="1" class="toggle">
		</td>
	</tr>
{% } %}
SCRIPT;
			return $strResult;
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

			$strResult = '';
			switch ($this->intUiType) {
				case QJqFileUploadType::BASIC_PLUS_UI:
					$strAddFilesLabel = self::$strAddFilesLabel;
					$strStartUploadLabel = self::$strStartUploadLabel;
					$strCancelUploadLabel = self::$strCancelUploadLabel;
					$strDeleteLabel = self::$strDeleteLabel;
					$strResult .= <<<SCRIPT
<div class="row fileupload-buttonbar">
	<div class="span7">
		<span class="btn btn-success fileinput-button">
			<i class="icon-plus icon-white"></i>
			<span>$strAddFilesLabel</span>
			$strFileControlHtml
		</span>
		<button type="submit" class="btn btn-primary start">
			<i class="icon-upload icon-white"></i>
			<span>$strStartUploadLabel</span>
		</button>
		<button type="reset" class="btn btn-warning cancel">
			<i class="icon-ban-circle icon-white"></i>
			<span>$strCancelUploadLabel</span>
		</button>
		<button type="button" class="btn btn-danger delete">
			<i class="icon-trash icon-white"></i>
			<span>$strDeleteLabel</span>
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
<script id="template-upload" type="text/x-tmpl">{$this->getUploadTemplate()}</script>
<script id="template-download" type="text/x-tmpl">{$this->getDownloadTemplate()}</script>
SCRIPT;
					break;
				case QJqFileUploadType::BASIC_PLUS:
				case QJqFileUploadType::BASIC:
				default:
					$strFileLabel = $this->intUiType == QJqFileUploadType::BASIC_PLUS ? self::$strAddFilesLabel : self::$strSelectFilesLabel;
					$strResult .= <<<SCRIPT
<span class="btn btn-success fileinput-button">
	<i class="icon-plus icon-white"></i>
	<span>$strFileLabel</span>
	$strFileControlHtml
</span>
<div id="{$this->strControlId}_progress" class="progress progress-success progress-striped">
	<div class="bar"></div>
</div>
<div id="{$this->strControlId}_filelist" class="files"></div>
SCRIPT;
					break;
			}
			return $strResult;
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

	QJqFileUploadBase::_static_init();
?>