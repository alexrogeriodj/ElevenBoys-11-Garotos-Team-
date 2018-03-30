/**
 * @license Copyright (c) 2003-2012, CKSource - Frederico Knabben. All rights reserved.
 * For licensing, see LICENSE.html or http://ckeditor.com/license
 */

CKEDITOR.editorConfig = function( config ) {

	config.height = 400;
	config.toolbarCanCollapse = true;
	config.language = 'en';
	config.menu_groups = 'clipboard,table,anchor,link,image';
	config.uiColor = '#f2f2f2';

	config.toolbar = 'Basic';
		
	config.toolbar_Minimal = [
		['Bold', 'Italic', '-', 'NumberedList', 'BulletedList', '-', 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock', 
		 '-', 'Link', 'Unlink','-','About']
	];
	
	config.toolbar_Basic = [
		{ name: 'document',    items : [ 'Save','DocProps','Preview' ] },
	    { name: 'clipboard',   items : [ 'Cut','Copy','Paste','PasteText','PasteFromWord','-','Undo','Redo' ] },
	    { name: 'editing',     items : [ 'Find','Replace','-','SelectAll','-','SpellChecker', 'Scayt' ] },
	    { name: 'paragraph',   items : [ 'NumberedList','BulletedList','-','Outdent','Indent','-','Blockquote','CreateDiv' ] },
	    { name: 'links',       items : [ 'Link','Unlink','Anchor' ] },
	    { name: 'insert',      items : [ 'Image','Table','HorizontalRule','Smiley','SpecialChar','PageBreak' ] },
	    { name: 'basicstyles', items : [ 'Bold','Italic','Underline','Strike','Subscript','Superscript','-','RemoveFormat' ] },
	    { name: 'align',   items : [ 'JustifyLeft','JustifyCenter','JustifyRight','JustifyBlock','-','BidiLtr','BidiRtl' ] },
	    { name: 'styles',      items : [ 'Styles','Format',/*'Font',*/'FontSize' ] },
	    { name: 'colors',      items : [ 'TextColor','BGColor' ] },
	    { name: 'tools',       items : [ 'Maximize', 'ShowBlocks', '-', 'Source' ] }
	];
};
