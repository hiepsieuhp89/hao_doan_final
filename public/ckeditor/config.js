/**
 * @license Copyright (c) 2003-2013, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {
	// Define changes to default configuration here. For example:
	// config.language = 'fr';
	// config.uiColor = '#AADC6E';
	var domainer=location.hostname;
        config.protectedSource = [/\r|\n/g];
	config.removeDialogTabs = 'image:advanced;link:advanced';
	config.filebrowserBrowseUrl = 'http://Whaale.store/public/ckfinder/ckfinder.html';

config.filebrowserImageBrowseUrl = 'http://Whaale.store/public/ckfinder/ckfinder.html?type=Images';

config.filebrowserFlashBrowseUrl = 'http://Whaale.store/demo/public/ckfinder/ckfinder.html?type=Flash';

config.filebrowserUploadUrl ='http://Whaale.store/public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files';

config.filebrowserImageUploadUrl = 'http://Whaale.store/public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images';

config.filebrowserFlashUploadUrl = 'http://Whaale.store/public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash';
config.entities = false;
config.htmlEncodeOutput = false;
};
