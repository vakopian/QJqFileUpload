<?php

$objPlugin = new QPlugin();
$objPlugin->strName = "QJqFileUpload";
$objPlugin->strDescription = 'File Upload control based on BlueImp jQuery File Upload plugin.';
$objPlugin->strVersion = "0.1";
$objPlugin->strPlatformVersion = "2.2";
$objPlugin->strAuthorName = "Vardan Akopian";
$objPlugin->strAuthorEmail = "vakopian+qcubed [at] gmail [dot] com";

$components = array();

$components[] = new QPluginJsFile("includes/jQuery-File-Upload/js");
$components[] = new QPluginCssFile("includes/jQuery-File-Upload/css");
$components[] = new QPluginImageFile("includes/jQuery-File-Upload/img");
$components[] = new QPluginJsFile("includes/JavaScript-Canvas-to-Blob/js");
$components[] = new QPluginJsFile("includes/JavaScript-Load-Image/js");
$components[] = new QPluginCssFile("includes/bootstrap/css");
$components[] = new QPluginImageFile("includes/bootstrap/img");
$components[] = new QPluginCssFile("includes/Gallery/css");

$components[] = new QPluginControlFile("includes/QJqFileUpload.class.php");
$components[] = new QPluginControlFile("includes/QJqFileUploadBase.class.php");
$components[] = new QPluginControlFile("includes/QJqFileUploadGen.class.php");
$components[] = new QPluginControlFile("includes/jQuery-File-Upload/server/php/UploadHandler.php");
$components[] = new QPluginIncludedClass("UploadHandler", "includes/jQuery-File-Upload/server/php/UploadHandler.php");
$components[] = new QPluginIncludedClass("QJqFileUploadHandler", "includes/QJqFileUploadBase.class.php");
$components[] = new QPluginIncludedClass("QJqFileUpload", "includes/QJqFileUpload.class.php");
$components[] = new QPluginIncludedClass("QJqFileUploadBase", "includes/QJqFileUploadBase.class.php");
$components[] = new QPluginIncludedClass("QJqFileUploadGen", "includes/QJqFileUploadGen.class.php");

$components[] = new QPluginExample("example/jq_file_upload.php", "QJqFileUpload: File Upload control based on BlueImp jQuery File Upload plugin");
$components[] = new QPluginExampleFile("example/jq_file_upload.php");
$components[] = new QPluginExampleFile("example/jq_file_upload.tpl.php");

$objPlugin->addComponents($components);
$objPlugin->install();

?>
