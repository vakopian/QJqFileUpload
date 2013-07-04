<?php

$objPlugin = new QPlugin();
$objPlugin->strName = "QJqFileUpload";
$objPlugin->strDescription = 'File Upload control based on BlueImp jQuery File Upload plugin.';
$objPlugin->strVersion = "0.1";
$objPlugin->strPlatformVersion = "2.2";
$objPlugin->strAuthorName = "Vardan Akopian";
$objPlugin->strAuthorEmail = "vakopian+qcubed [at] gmail [dot] com";

$components = array();

$components[] = new QPluginJsFile("BlueImp/jQuery-File-Upload/js");
$components[] = new QPluginCssFile("BlueImp/jQuery-File-Upload/css");
$components[] = new QPluginImageFile("BlueImp/jQuery-File-Upload/img");
$components[] = new QPluginJsFile("BlueImp/JavaScript-Canvas-to-Blob/js");
$components[] = new QPluginJsFile("BlueImp/JavaScript-Load-Image/js");
$components[] = new QPluginJsFile("BlueImp/JavaScript-Templates/js");
$components[] = new QPluginJsFile("BlueImp/Gallery/js");
$components[] = new QPluginCssFile("BlueImp/Gallery/css");

$components[] = new QPluginJsFile("bootstrap/js");
$components[] = new QPluginCssFile("bootstrap/css");
$components[] = new QPluginImageFile("bootstrap/img");

$components[] = new QPluginControlFile("includes/QJqFileUpload.class.php");
$components[] = new QPluginControlFile("includes/QJqFileUploadBase.class.php");
$components[] = new QPluginControlFile("includes/QJqFileUploadGen.class.php");
$components[] = new QPluginControlFile("BlueImp/jQuery-File-Upload/server/php/UploadHandler.php");
$components[] = new QPluginIncludedClass("UploadHandler", "BlueImp/jQuery-File-Upload/server/php/UploadHandler.php");
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
