# UserCMS_bitode_multilingual
Multilingual edition of UserCMS_bitcode
changes log
-added multilingual mode. 
	languages are set in config.ini, also you need to set is_multilingual an default_language options. see example in config.php
	you need to create language directory in theme directory and language directories in it named as languages.
	in language directories you need to create language.php file with $data_language array which will contain variables with your language strings
	see example in themes/default directory
-added language attribute to menus so you can create various menus for each language
- multilingual options for other modules and components are being developed and will be uploaded soon (contact zmkhitaryan88@gmail.com for more info)
-added module_generator which help to generate basic stricture of module
-added gallery_box plugin which helps to show any gallry album in any place on site
-gallery changes (
	multiple images upload, 
	drag and drop images sort, 
	added gallery categories priority control function
	)
-changed image save helper (added multiple upload function)

ATTENTION! all changes in core besides load_view method in user_cms/modules/component.php, gallery, mode generator and image saver helper changes are not in core. They`re added modules/ directory as modules so it allows to update cms withhout losing these changes  

Original version website http://www.usercms.ru
Original version official documentation http://www.usercms.ru/docs
