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
	config.filebrowserBrowseUrl = 'http://ChiTrungTech.com/public/ckfinder/ckfinder.html';

config.filebrowserImageBrowseUrl = 'http://ChiTrungTech.com/public/ckfinder/ckfinder.html?type=Images';

config.filebrowserFlashBrowseUrl = 'http://ChiTrungTech.com/demo/public/ckfinder/ckfinder.html?type=Flash';

config.filebrowserUploadUrl ='http://ChiTrungTech.com/public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Files';

config.filebrowserImageUploadUrl = 'http://ChiTrungTech.com/public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Images';

config.filebrowserFlashUploadUrl = 'http://ChiTrungTech.com/public/ckfinder/core/connector/php/connector.php?command=QuickUpload&type=Flash';
config.entities = false;
config.htmlEncodeOutput = false;
};
