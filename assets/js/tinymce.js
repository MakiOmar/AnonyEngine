/*(function() {
  'use strict';
  tinymce.create('tinymce.plugins.Keywords', {
    init: function(ed, url) {
      ed.addButton('kalt', {
        title: 'Keyword alternative',
        cmd: 'kalt'
      });

      // here I add the shortcut.
      ed.addShortcut('ctrl+k', 'description', 'kalt');
      ed.addCommand('kalt', function() {
        var selected_text = ed.selection.getContent(),
          $node = jQuery(ed.selection.getStart()),
          return_text = '';

        if (selected_text !== "") {
          return_text = '<code>' + selected_text + '</code>';
        }
        ed.execCommand('mceInsertContent', 0, return_text);
      });
    }
  });
  // Register plugin
  tinymce.PluginManager.add('keywords', tinymce.plugins.Keywords);
})();*/
function replaceText(editor, before, after){
	var selected_text = editor.selection.getContent(),
			  $node = jQuery(editor.selection.getStart()),
			  return_text = '';
		  if (selected_text !== "") {
			  return_text = before + selected_text + after;
			  editor.execCommand('mceInsertContent', 0, return_text);return_text
		  }else{
			  editor.insertContent(before + after);
		  }
}
function insertPattern(editor,shortCut,description,command, before, after){
	editor.addShortcut(shortCut, description, command);
	editor.addCommand(command, function() {
		
		  replaceText(editor, before, after)
		  
	   });
}
(function() {
	"use strict";
   tinymce.PluginManager.add('keywords', function( editor, url ) {
       editor.addButton('kalt', {
               text: '(%%)',
               icon: false,
               onclick: function() {
                 // change the shortcode as per your requirement
                  replaceText(editor, '(%', '%)');
                  
              }
         });
	   
	   editor.addButton('keyword', {
               text: '(##)',
               icon: false,
               onclick: function() {
                 // change the shortcode as per your requirement
                  replaceText(editor, '(#', '#)');
                  
              }
         });
	   
	   // here I add the shortcut.
      insertPattern(editor,'ctrl+1', 'description', 'shkalt', '(%', '%)');
	  insertPattern(editor,'ctrl+2', 'description', 'shkeyword', '(#', '#)');
   });
       
       
})();

