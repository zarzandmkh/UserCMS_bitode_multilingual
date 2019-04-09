# UserCMS_bitcode_multilingual <br />
Multilingual edition of UserCMS_bitcode <br />
changes log <br />
- added multilingual mode. <br />
	languages are set in config.ini, also you need to set is_multilingual and default_language options. see example in config.php <br />
	you need to create language directory in theme directory and language directories in it named as languages. <br />
	in language directories you need to create language.php file with $data_language array which will contain variables with your language strings <br />
	language can be set via POST[language_global] or GET[usercms_language] <br />
	system automatically detects user preferred language if user hasn't chosen language yet <br />
	system saves users chosed language one month
	admin panel language automatically changes to default. there is only russian for admin default theme for now
	see example in themes/default directory <br />
- added language attribute to menus so you can create various menus for each language <br />
- multilingual options for other modules and components are being developed and will be uploaded soon (contact zmkhitaryan88@gmail.com for more info) <br />
- added module_generator which help to generate basic stricture of module <br />
- added gallery_box plugin which helps to show any gallry album in any place on site <br />
- gallery changes ( <br />
	multiple images upload,  <br />
	drag and drop images sort,  <br />
	added gallery categories priority control function <br />
	)
-changed image save helper (added multiple upload function) <br />

ATTENTION! all changes in core besides load_view method in user_cms/modules/component.php, gallery, mode generator and image saver helper changes are not in core. They`re added modules/ directory as modules so it allows to update cms withhout losing these changes   <br />
<br />
Original version website http://www.usercms.ru <br />
Original version official documentation http://www.usercms.ru/docs <br />
