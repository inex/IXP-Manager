/**
 * Internationalization: norwegian (bokmål) language
 *
 * Depends on jWYSIWYG, $.wysiwyg.i18n
 *
 * By: strauman on github.com / strauman.net
 */
(function ($) {
	if (undefined === $.wysiwyg) {
		throw "lang.nb.js depends on $.wysiwyg";
	}
	if (undefined === $.wysiwyg.i18n) {
		throw "lang.nb.js depends on $.wysiwyg.i18n";
	}

	$.wysiwyg.i18n.lang.cs = {
		controls: {
			"Bold": "Fet",
			"Colorpicker": "Fargevelger",
			"Copy": "Kopier",
			"Create link": "Lag lenge",
			"Cut": "Klipp ut",
			"Decrease font size": "Mindre skrift",
			"Fullscreen": "Fullskjerm",
			"Header 1": "Stor tittel",
			"Header 2": "Mellomstor tittel",
			"Header 3": "Liten tittel",
			"View source code": "Vis kildekode",
			"Increase font size": "Større skrift",
			"Indent": "Rykk inn",
			"Insert Horizontal Rule": "Sett inn horisontal linje",
			"Insert image": "Sett inn bilde",
			"Insert Ordered List": "Sett inn organisert liste",
			"Insert table": "Sett inn tabell",
			"Insert Unordered List": "Sett inn uorganisert liste",
			"Italic": "Kursiv",
			"Justify Center": "Midtstill",
			"Justify Full": "Hele siden",
			"Justify Left": "Sett tekst til venstre",
			"Justify Right": "Set tekst til høyre",
			"Left to Right": "Venstre til høyre",
			"Outdent": "Rykk ut",
			"Paste": "Lim inn",
			"Redo": "Gjør om",
			"Remove formatting": "Fjern formatering",
			"Right to Left": "Høyre til venstre",
			"Strike-through": "Strek igjennom",
			"Subscript": "Underpotens",
			"Superscript": "Pontens",
			"Underline": "Understrek",
			"Undo": "Angre"
		},

		dialogs: {
			// for all
			"Apply": "Bruk",
			"Cancel": "Avbryt",

			colorpicker: {
				"Colorpicker": "Fargevelger",
				"Color": "Farge"
			},

			fileManager: {
				"file_manager": "Fil-utforsker",
				"upload_title": "Last opp fil",
				"rename_title": "Endre tittelnavn",
				"remove_title": "Fjern tittel",
				"mkdir_title": "Lag tittel",
				"upload_action": "Nahrát nový soubor do aktualního adresáře",
				"mkdir_action": "Vytvořit nový adresář",
				"remove_action": "Odstranit tento soubor",
				"rename_action": "Přejmenovat tento soubor" ,
				"delete_message": "Jste si jist, že chcete smazat tento soubor?",
				"new_directory": "Nový adresář",
				"previous_directory": "Vrať se do přechozího adresáře",
				"rename": "Přejmenovat",
				"select": "Vybrat",
				"create": "Vytvořit",
				"submit": "Vložit",
				"cancel": "Zrušit",
				"yes": "Ano",
				"no": "Ne"
			},
			
			fileManager: {
				"file_manager": 		"Utforsker",
				"upload_title":			"Last opp fil",
				"rename_title":			"Gi nytt navn",
				"remove_title":			"Slett fil",
				"mkdir_title":			"Ny mappe",
				"upload_action": 		"Last opp fil til denne mappen",
				"mkdir_action": 		"Lag ny mappe",
				"remove_action": 		"Slett filen",
				"rename_action": 		"Nytt navn" ,	
				"delete_message": 		"Er du sikker på at du vil slette denne filen?",
				"new_directory": 		"Mappe uten navn",
				"previous_directory": 	"Opp",
				"rename":				"Gi nytt navn",
				"select": 				"Velg",
				"create": 				"Lag",
				"submit": 				"Send",
				"cancel": 				"Avbryt",
				"yes":					"Ja",
				"no":					"Nei"
			},

			image: {
				"Insert Image": "Sett inn bilde",
				"Preview": "Forhåndsvisning",
				"URL": "URL",
				"Title": "Tittel",
				"Description": "Beskrivelse",
				"Width": "Bredde",
				"Height": "Høyde",
				"Original W x H": "Original B x H",
				"Float": "Flyt",
				"None": "Ingen",
				"Left": "Venstre",
				"Right": "Høyre",
				"Select file from server": "Velg fil fra server"
			},

			link: {
				"Insert Link": "Sett inn link",
				"Link URL": "Link URL",
				"Link Title": "Linktittel",
				"Link Target": "Link-mål"
			},

			table: {
				"Insert table": "Sett inn tabell",
				"Count of columns": "Antall kolonner",
				"Count of rows": "Antall rader"
			}
		}
	};
})(jQuery);