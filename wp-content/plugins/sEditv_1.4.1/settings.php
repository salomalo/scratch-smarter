<?php 


//effects folder
 $settingsValue['effectsFolder']='effects';

//temp folder for temp images
 $settingsValue['tempFolder']='temp';

//font used in writing text over image. If you want to use your own font, please copy font.ttf file to this folder and replace value bellow
 $settingsValue['font']='fonts/arial.ttf';

// simple effects
$simpleEffects=array(
	array('value'=>'Pixelate','name'=>'Pixelate'),
	array('value'=>'Pixelate-out','name'=>'Pixelate outside'),
	array('value'=>'Negative','name'=>'Negative'),
	array('value'=>'Greyscale','name'=>'Greyscale'),
	array('value'=>'Greyscale-out','name'=>'Greyscale outside'),
	array('value'=>'Blur','name'=>'Blur'),
	array('value'=>'Blur-out','name'=>'Blur outside'),
	array('value'=>'Brightness','name'=>'Brightness'),
	array('value'=>'Contrast','name'=>'Contrast'),
	array('value'=>'Colorize','name'=>'Colorize'),
	array('value'=>'Emboss','name'=>'Emboss'),
	array('value'=>'Text','name'=>'Text'),
	array('value'=>'Crop','name'=>'Crop')
);

// compiled effects   comment if you dont want to use them
$compiledEffects=array(
	
	array('value'=>'Rosie','img'=>'web_images/compiledEffects/Rosie.jpg'),
	array('value'=>'Patty','img'=>'web_images/compiledEffects/Patty.jpg'),
	array('value'=>'Juliette','img'=>'web_images/compiledEffects/Juliette.jpg'),
	array('value'=>'Sonny','img'=>'web_images/compiledEffects/Sonny.jpg'),
	array('value'=>'Oddie','img'=>'web_images/compiledEffects/Oddie.jpg'),
	array('value'=>'FilmFrame','img'=>'web_images/compiledEffects/FilmFrame.jpg')
);
 ?>