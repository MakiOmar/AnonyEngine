function replaceAllText(editor, before, after){
	var selected_text = editor.getContent();
	//Matches all Characters in all languages
	//[\p{L}-]+
	//var test = selected_text.replace( new RegExp(/(\w+)(?!([^<]+)?>) /,"gm"),before + "\$1" + after);
	var test = selected_text.replace( new RegExp(/([\p{L}-]+)(?!([^<]+)?>)/,"ugm"),before + "\$1" + after + ' ');
	
	editor.setContent(test);
	
}
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

function insertPatternAll(editor,shortCut,description,command, before, after){
	editor.addShortcut(shortCut, description, command);
	editor.addCommand(command, function() {
		
		  replaceAllText(editor, before, after)
		  
	   });
}
(function() {
	"use strict";
   tinymce.PluginManager.add('keywords', function( editor, url ) {
	   editor.addButton('patterns_menu', {
               text: 'Diwan patterns',
		   		type : 'menubutton',
               classes: 'widget btn mfnsc',
		   		menu:[
					{
					   text: '(%%)',
					   icon: false,
					   onclick: function() {
							 // change the shortcode as per your requirement
							  replaceText(editor, '(%', '%)');

					   }
					},
					{
					   text: '(%%)*',
					   icon: false,
					   onclick: function() {
						 // change the shortcode as per your requirement
						  replaceAllText(editor, '(%', '%)');

					  }
				 	},
					{
						   text: '(##)',
						   icon: false,
						   onclick: function() {
							 // change the shortcode as per your requirement
							  replaceText(editor, '(#', '#)');

						  }
					 },
					{
						   text: '(##)*',
						   icon: false,
						   onclick: function() {
							 // change the shortcode as per your requirement
							  replaceAllText(editor, '(#', '#)');

						  }
					 }
				]
         });
	   // here I add the shortcut.
      insertPattern(editor,'ctrl+1', 'description', 'shkalt', '(%', '%)');
	  insertPattern(editor,'ctrl+2', 'description', 'shkeyword', '(#', '#)');
	  insertPatternAll(editor,'ctrl+shift+1', 'description', 'shkalt', '(%', '%)');
	  insertPatternAll(editor,'ctrl+shift+2', 'description', 'shkeyword', '(#', '#)');
   });
       
       
})();

