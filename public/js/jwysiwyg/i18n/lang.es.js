/**
 * Internationalization: Spanish language
 * 
 * Depends on jWYSIWYG, $.wysiwyg.i18n
 *
 * By: Esteban Beltran (academo) <sergies@gmail.com>
 *
 */
(function ($) {
	if (undefined === $.wysiwyg) {
		throw "lang.es.js depends on $.wysiwyg";
	}
	if (undefined === $.wysiwyg.i18n) {
		throw "lang.es.js depends on $.wysiwyg.i18n";
	}

	$.wysiwyg.i18n.lang.es = {
		controls: {
			"Bold": "Negrilla",
			"Colorpicker": "",
			"Copy": "Copiar",
			"Create link": "Crear Link",
			"Cut": "Cortar",
			"Decrease font size": "Disminuir tamaño fuente",
			"Fullscreen": "",
			"Header 1": "Titulo 1",
			"Header 2": "Titulo 2",
			"Header 3": "Titulo 3",
			"View source code": "Ver fuente",
			"Increase font size": "Aumentar tamaño fuente",
			"Indent": "Agregar Sangría",
			"Insert Horizontal Rule": "Insertar linea horizontal",
			"Insert image": "Insertar Imagen",
			"Insert Ordered List": "Insertar lista numérica",
			"Insert table": "Insertar Tabla",
			"Insert Unordered List": "Insertar Lista viñetas",
			"Italic": "Cursiva",
			"Justify Center": "Centrar",
			"Justify Full": "Justificar",
			"Justify Left": "Alinear a la Izquierda",
			"Justify Right": "Alinear a la derecha",
			"Left to Right": "Izquierda a derecha",
			"Outdent": "Quitar Sangría",
			"Paste": "Pegar",
			"Redo": "Restaurar",
			"Remove formatting": "Quitar Formato",
			"Right to Left": "Derecha a izquierda",
			"Strike-through": "Invertir",
			"Subscript": "Subíndice",
			"Superscript": "Superíndice",
			"Underline": "Subrayar",
			"Undo": "Deshacer"
		},

		dialogs: {
			// for all
			"Apply": "",
			"Cancel": "",

			colorpicker: {
				"Colorpicker": "",
				"Color": ""
			},

			image: {
				"Insert Image": "",
				"Preview": "",
				"URL": "",
				"Title": "",
				"Description": "",
				"Width": "",
				"Height": "",
				"Original W x H": "",
				"Float": "",
				"None": "",
				"Left": "",
				"Right": ""
			},

			link: {
				"Insert Link": "",
				"Link URL": "",
				"Link Title": "",
				"Link Target": ""
			},

			table: {
				"Insert table": "",
				"Count of columns": "",
				"Count of rows": ""
			}
		}
	};
})(jQuery);
