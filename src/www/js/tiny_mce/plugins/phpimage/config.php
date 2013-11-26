<?php
/*
Php Image Plug-in uses a GPL licensed class "class.upload.php"
Authors website: http://www.verot.net/php_class_upload.htm
For a full list of extra options: http://www.verot.net/res/sources/class.upload.html

Default settings will resize any uploaded image to a maxiumum height of 400 px high or wide (saves bandwidth),
will replace spaces in filenames with _ (underscore), and will auto rename the file if it already exists.
*/

// Simple way to get back to server path minus the javascript directorys
$_cur_dir = getcwd(); if ($_cur_dir == FALSE) { $_cur_dir = dirname($_SERVER['SCRIPT_FILENAME']); }
$_cur_dir = $_SERVER["DOCUMENT_ROOT"]; // minus the amout of directorys back to root directory from current run script e.g. /js/tinymce/plugins/phpimage
// The default language for errors is english to change to another type add lang to the lang folder e.g. fr_FR (french) to get language packs please download the class zip from the above authors link
$language						= 'ru';
// server file directory to store images - IMPORTANT CHANGE PATH TO SUIT YOUR NEEDS!!!
$server_image_directory		= $_cur_dir.'/uploads/articles';  //e.g. '/images';
// URL directory to stored images (relative or absoulte) - IMPORTANT CHANGE PATH TO SUIT YOUR NEEDS!!!
$url_image_directory			= '/uploads/articles';
// file_safe_name formats the filename (spaces changed to _) (default: true)

?>