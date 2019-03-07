/*
* sEditor 1.0 - online image editor
* Written by Igor Simic
* 
*/
jQuery(document).ready(function($){


 var sEditorFunction=function(){ 

      
   /*+++++++++++++++++++++ vars ++++++++++++++++++++++++++++++*/
     var typeOfSelection='block';//selector type 
     var colorOnOff='Off';//coloring is enabled

      
      
     var scrImageOrig=$("#cropbox").attr('src'); // original image needed for revert
     var scrImageOrigrelative=$("#cropbox").attr('relsrc'); // original image needed for revert
      //needed for changing images

     /*split path*/
    
     var splitPath = new Array();
     splitPath = (scrImageOrig.split("/"));
     var sEditorNoOfPathElements=splitPath.length;

     var scrImageVar='temp';

     var  selectedColorPicker= {}; //selected color by default - later by color picker
     selectedColorPicker['r']=255;
     selectedColorPicker['g']=255;
     selectedColorPicker['b']=255;

    var effect;//selected efect
    var textValue;//text for write on image   
    var noColor='on'; /*if off no color would be applied to effect*/ 

    /*+++++++++++++++++++++/ vars ++++++++++++++++++++++++++++++*/

     //hide color editor
     $("#sEditorColorChoser").hide();

      //add to each block carousel and hide it //because if i add carousel later the imagedwon't  be clickable
      $(".sEditorEffectsSampleImages").each(function() {

            //get id of curent block
            var currentId=$(this).attr('id');

            //assign the scroller to every block of effects if there are more then 6 elenemnts

            var sCoderNumberOfImageElements = $('#'+currentId+' div').length;

            if(sCoderNumberOfImageElements>6){

              $('#'+currentId).scrollingCarousel({
                        scrollerAlignment : 'horizontal', // or vertical
                        autoScroll: true, //Boolean - If set to 'true' the scroller will scroll automatically at set speed in a set direction when the mouse cursor is not over it.
                        looped:false, //Boolean - If set to 'false' the content will stop scrolling when its edge reaches the edge of the container, otherwise it will loop infinitely.
                        autoScrollDirection: 'left'
                      });/*adding carousel*/
            }/*if more then 6*/

            //and hide them all
            $(this).fadeOut();
          });/*each block loop*/

$("#sEditorDefaultEffectsList").slideDown();

/* +++++++++++++++++++++ slider +++++++++++++++++++++ */
var sliderRange = $("#slider");
var displayRange = $("#slider > div");
var initialValue = 50;
var handle = null;

var updateSliderValue = function (event, ui) {
  displayRange.css('visibility','visible');
  handle = handle || $(".ui-slider-handle", sliderRange);
  displayRange.text(ui.value || initialValue)
  .css(handle.position());
$("#sEditorTextBox textarea").css('font-size',ui.value);

$('.opacityImage').css({
  'opacity':ui.value/100
  //'filter':'alpha(opacity='+ui.value+')'
})

}

sliderRange.slider({
                    min: 0, max: 100,
                    slide: updateSliderValue,
                    create: updateSliderValue,
                    value: initialValue
                  });
/* +++++++++++++++++++++ slider +++++++++++++++++++++ */

/* +++++++++++++++++++++ color picker +++++++++++++++++++++ */
$('#colorSelector').ColorPicker({
  color: '#0000ff',
  onShow: function (colpkr) {
    $(colpkr).fadeIn(500);
    return false;
  },
  onHide: function (colpkr) {
    $(colpkr).fadeOut(500);
    return false;
  },
  onChange: function (hsb, hex, rgb) {
    $('#colorSelector').css('backgroundColor', '#' + hex);
        selectedColorPicker=rgb;
      }
    });

/*  +++++++++++++++++++++ /color picker +++++++++++++++++++++ */

/* +++++++++++++++++++++ jcrop +++++++++++++++++++++ */
$('#cropbox').Jcrop({
  boxWidth: 640, boxHeight: 480,
  onChange: showCoords,
  onSelect: showCoords,
  bgColor:     'white',
  bgOpacity:   .5,
  setSelect:   [ 640, 480, 640, 480 ],

  addClass: 'jcrop-dark' 
                        //aspectRatio: 640/480

                      },function(){
                        jcrop_api = this; 
                        jcrop_api.release(function(){
                          jcrop_api.setOptions({bgOpacity: .9});
                        });
                         jcrop_api.animateTo([ 0,0,0,0],function(){
                            jcrop_api.release();
                          });
                      });

function showCoords(c)
{
  x1=c.x;
  y1=c.y;
  x2=c.x2;
  y2=c.y2;
  w=c.w;
  h=c.h;

  $("#sEditorXpos").val(x1);
  $("#sEditorYpos").val(y1);
  $("#sEditorX2pos").val(x2);
  $("#sEditorY2pos").val(y2);
  $("#sEditorWidthpos").val(w);
  $("#sEditorHeightpos").val(h);
};
/* +++++++++++++++++++++ jcrop +++++++++++++++++++++ */



/* ++++++++++++++++++++++ scrollingCarousel ++++++++++++++ */

        // assign scroll to lower effects menu
        $("#sEditorBottomMenu").scrollingCarousel({
          autoScroll: true,
          looped:false,
          height:'80px'
        });

        /*on lower menu button click hide visible and show current list of effects*/
        $('.sEditorBottomMenu').click(function(){

         var currentClickedId=$(this).attr('id');
         /*hide all visible list (previously showed) and assign carousel to clicked on witha same ID+List*/
         $('.sEditorEffectsSampleImages:visible').fadeOut();/* .sEditorEffectsSampleImages:visible fadein*/

         $('#'+currentClickedId+'List').fadeIn();/*'#'+currentId+'List'fadeIn*/

       })/*click sEditorBottomMenu*/


        /* ++++++++++++++++++++++ /scrollingCarousel ++++++++++++++ */

        /* +++++++++++++++++++++ / main navi click buttons +++++++++++++++++++++ */

        $('#sEditorSelectAllButton').click(function(){

          /*get current image real dimensions*/
          var cropImgWidth=$("#sEditorImageHolder img").width();
          var cropImgHeight=$("#sEditorImageHolder img").height();

          /*and animate to selection*/
          jcrop_api.animateTo([cropImgWidth,cropImgHeight,0,0]);
          return false;
        });

        //$('#sEditorWidthpos, #sEditorHeightpos,#sEditorXpos,#sEditorYpos').bind('change',function(e){
        $('#sEditorWidthpos, #sEditorHeightpos').bind('change',function(e){
           e.stopImmediatePropagation();

          var currentWidth=$("#sEditorWidthpos").val();
          var currentHeight=$("#sEditorHeightpos").val();
          // var currentTop=$("#sEditorXpos").val();
          // var currentLeft=$("#sEditorYpos").val();
          
          //jcrop_api.animateTo([currentWidth,currentHeight,currentTop,currentLeft]);
          jcrop_api.setSelect([currentWidth,currentHeight,0,0]);
        })

        $('#sEditorDeselectButton').click(function(e) {
          // Release method clears the selection
          jcrop_api.animateTo([ 0,0,0,0],function(){
            jcrop_api.release();
          });
    
  });

     // circle selection
     $("#sEditorSelectionCircleButton").click(function(){
        //assign value to selection type
        typeOfSelection='circle';

        //change background
        $(this).css('background-position', '0px -70px');

        //deselect the block selection
        $("#sEditorSelectionBlockButton").css('background-position', '0px 0px');

        //add circle
        $(".jcrop-holder div").css("border-radius","50%");

      })

     // block selection
     $("#sEditorSelectionBlockButton").click(function(){

        //assign value to selection type
        typeOfSelection='block';
        //change background
        $(this).css('background-position', '0px -70px');

        //deselect the block selection
        $("#sEditorSelectionCircleButton").css('background-position', '0px 0px');

        //remove circle
        $(".jcrop-holder div").css("border-radius","0px");
       

      })

     //color on off
     $("#sEditorColorPalleteButton").click(function(){

        //if is on then turn it off
        if(colorOnOff=='On'){
          colorOnOff='Off';
          noColor='Off';
          $("#sEditorColorChoser:visible").fadeOut();
          $(this).css('background-position', '0px 0px');
        }else{
          noColor='On';
          colorOnOff='On';
          $("#sEditorColorChoser").fadeIn();
          $(this).css('background-position', '0px -70px');
        }

      })

     //revert
     $("#sEditorRevertButton").click(function(){
      $("#whiteLoading").css('visibility','visible');
      // change image in jcrop
      
      
      // delete current effect image  
      $.ajax({
        type: 'POST',
        url: sEditVars.sEdit_pluginurl+'actions.php',
        data: 'scrImageVar='+scrImageVar+'&action=delete',
                // dataType: 'json',

                success: function(data){
                 
                  jcrop_api.setImage(scrImageOrig+'?'+Math.random(),function(){  $("#whiteLoading").css('visibility','hidden');});

                },/*success*/
                error: function(){
                  alert('failed to delete!');
                  $("#whiteLoading").css('visibility','hidden');
                  jcrop_api.setImage(scrImageOrig+'?'+Math.random(),function(){ $("#whiteLoading").css('visibility','hidden'); });
                }
              })/*ajax */
      });/*revert button clicked*/

     /* +++++++++++++++++++++ /main navi click buttons +++++++++++++++++++++ */

     /* +++++++++++++++++++++ apply effect+++++++++++++++++++++ */  

    

     $("#sEditorApplyButton").click(function(){
       if(typeof(effect) === 'undefined')
      //if(effect==null)
     { 
       }else{
       var formValues='x='+x1+'&x2='+x2+'&y='+y1+'&y2='+y2+'&w='+w+'&h='+h;
       var currentSliderValue = $( "#slider" ).slider( "option", "value" );/* get current slider value*/
       var currentTextValue=$("#sEditorTextBox textarea").val();
       $("#whiteLoading").css('visibility','visible');

        $.ajax({
        type: 'POST',
        url: sEditVars.sEdit_pluginurl+'apply-effect.php',
        data: formValues+'&imgsrc='+scrImageOrig+'&srcImagevar='+scrImageVar+'&typeOfSelection='+typeOfSelection+'&noColor='+noColor+'&slider='+currentSliderValue +'&currentTextValue='+currentTextValue+'&effect='+effect+'&effectCategory='+effectCategory+'&color='+selectedColorPicker['r']+','+selectedColorPicker['g']+','+selectedColorPicker['b'],
        dataType: 'json',

                        success: function(data){
                          
                          /*if return is canNotCreate image*/
                            if(data.status=='CanNotCreateImage'){
                              alert('Hey, i am not able to work. Give me some rights dude! Thanks! (I can not write in your image folder!)')
                            }else if(data.status=='OK'){
                               
                                //jcrop_api.setOptions({ bgOpacity: .6 }); 
                                //replace current image in jcrop
                                // set var image to this name bellow while scrImageOrig remains the same (for revert)
                                 
                               scrImageVar='temp/'+ data.uniqueImageName;
                               jcrop_api.setImage(sEditVars.sEdit_pluginurl+scrImageVar+'?'+Math.random(),function(){
                                $("#whiteLoading").css('visibility','hidden');
                              });
                            }/*elseif*/
                            
                            
                              
                            }/*success*/
                          })/*ajax */
 }
      });/*apply effekt*/
    
/* +++++++++++++++++++++ /apply effect+++++++++++++++++++++ */   

/* +++++++++++++++++++++ css tricks  +++++++++++++++++++++  */


      //change font color of simple effect on clickk
      $(".TopSubMenuListDivs").click(function(){

        
        //assign value to effect & category
        effect=$(this).attr('sEffect');
        effectCategory=$(this).attr('sEffectCategory');

        $(".jcrop-vline").next('.jcrop-tracker').html('');
          
        /*enter current efect in sEditorCurrentEffect div*/
        $("#sEditorCurrentEffect").html('Selected effect: <p>' +effect+ '</p>');

        /*hide slider for some effects*/
        if(effect=='Negative' || effect=='Greyscale' || effect=='Greyscale-out' || effect=='Emboss' )
        {
          $("#sEditorSliderHolder:visible").slideUp();
        }else{
          $("#sEditorSliderHolder:hidden").slideDown();
        }

        if(effect!='Text'){
          $("#sEditorTextBox:visible").slideUp();
        }
        //add all elements on simple effects WHITE COLOR
          $(".TopSubMenuListDivs").each(function() {
            $(this).css('color','#f5f5f5')
          });

        //add all effects elements in lower menu default border
        $(".imageEffectSampleImageHodlder").each(function() {
          $(this).children('img').css('border','2px solid #626262')

        });

        //add to clicked element blue color
        $(this).css('color','#00f0ff')

        /*+++++++++++++++++++++++++++++++++ changes based on selected effect +++++++++++++++++++++++++++++++ */
       

          switch (effect){

            /* if is pixelate change slider desc to pixel size*/
            case 'Pixelate':
                  $("#currentSliderStatusDescription").html('Pixel size');
            break;

            case 'Pixelate-out':
                  $("#currentSliderStatusDescription").html('Pixel size');
             break;

             /* if is negative - hide slider*/
            case 'Blur':
              $("#currentSliderStatusDescription").html('Blur density');
            break;
            case 'Blur-out':
             $("#currentSliderStatusDescription").html('Blur density');
            break;
            
            case 'Contrast':
              $("#currentSliderStatusDescription").html('Contrast strength');
            break;

            /*if is colorize show color pallete and add value to noColor to On*/
            case 'Colorize':
            $("#sEditorColorChoser").fadeIn();
            noColor='On';
            $("#sEditorColorPalleteButton").css('background-position', '0px -70px');
            $("#currentSliderStatusDescription").html('Color opacity');
            break; 

            case 'Text':
              $("#currentSliderStatusDescription").html('Text size');
              $("#sEditorTextBox:hidden").slideDown();
              noColor='On';
              $("#sEditorColorPalleteButton").css('background-position', '0px -70px');
              $("#sEditorColorChoser").fadeIn();
            break;

            default:
            break;
          }//switch
        /*+++++++++++++++++++++++++++++++++ changes based on selected effect +++++++++++++++++++++++++++++++ */

      });/*.TopSubMenuListDivs click*/

      /* click on lower effects and add border around clicked element */
      $(".imageEffectSampleImageHodlder").click(function(){

          //assign value to effect & category
          effect=$(this).attr('sEffect');
          effectCategory=$(this).attr('sEffectCategory');
          console.log(effectCategory);
          //get image for display inside of selection
          var imgSrcLinkPreview=$(this).children('img').attr('src');
          
          //hide text box
          $("#sEditorTextBox:visible").slideUp();


          //if selector is circle switch back to rectanlge
          if(typeOfSelection=='circle'){
            typeOfSelection='block';
            $("#sEditorSelectionBlockButton").css('background-position', '0px -70px');
            $("#sEditorSelectionCircleButton").css('background-position', '0px 0px');
          }
 

          /*show color pallete slider desc and */
          noColor='Off';
          //$("#sEditorColorPalleteButton").css('background-position', '0px -70px');
          $("#currentSliderStatusDescription").html('Effect Opacity');
         // $("#sEditorColorChoser:hidden").fadeIn();

          /*enter current efect in sEditorCurrentEffect div*/
          $("#sEditorCurrentEffect").html('Selected effect:<p>' + effect + '</p>');

          //add all elements on simple effects WHITE COLOR
          $(".TopSubMenuListDivs").each(function() {
            $(this).css('color','#f5f5f5')
          });

          //if the effect category is not compiled effects display slider
          if(effectCategory!='compiledsEffects')
          {
            $("#sEditorSliderHolder:hidden").slideDown();
            $("#currentSliderStatusDescription").html('Effect Opacity');
          }else{
            $("#sEditorSliderHolder").slideUp();
            /*+++++++++++++++++++++++ Select whole image for these effects ++++++++++++++++++++++++++*/
            /*get current image real dimensions*/
            var cropImgWidth=$("#sEditorImageHolder img").width();
            var cropImgHeight=$("#sEditorImageHolder img").height();

            /*and animate to selection*/
            jcrop_api.animateTo([ cropImgWidth,cropImgHeight,0,0]);


           /*+++++++++++++++++++++++ /Select whole image for these effects ++++++++++++++++++++++++++*/
          }
          
          //apply thumb prev only on png effects
          if(effectCategory !='simplesEffects' ){
            //add image to selection
            var currentSliderValue = $( "#slider" ).slider( "option", "value" );
            $(".jcrop-vline").next('.jcrop-tracker').html('<img class="opacityImage pacity05" src='+imgSrcLinkPreview+' width="100%" height="100%" />')
            $('.opacityImage').css({
                  'opacity':currentSliderValue/100
                })
          }else{
             $(".jcrop-vline").next('.jcrop-tracker').html('');
          }
          if(effectCategory == 'compiledsEffects'){
            $(".jcrop-vline").next('.jcrop-tracker').html('');
          }
        
          
        //add all elements default border
        $(".imageEffectSampleImageHodlder").each(function() {
          $(this).children('img').css('border','2px solid #626262')

        });
        
        //add to clicked element yellow border
        $(this).children('img').css('border','2px solid #00f0ff')


       
      })/*imageEffectSampleImageHodlder click*/

      /* +++++++++++++++++++++ css tricks  +++++++++++++++++++++  */

      /* +++++++++++++++++++++  save options +++++++++++++++++++++ */

          var saveOption;

          /*+++++++++++++++++++++ when owervrite is choosen+++++++++++++++++++++ */
          $("#saveOverwrite").click(function(){
            $('#saveOverwrite p').css('color','#00f0ff')

            $("#saveNew").slideUp(function(){

                saveOption='Overwrite';/*assign overwrite value to save option*/

                /*show save options*/
                $("#sEditorSave-CancelButtonHolder").slideDown();
            });/*save new slideUp*/

          });/*saveOverwrite clicked*/

          /*+++++++++++++++++++++ /when owervrite is choosen+++++++++++++++++++++ */

          /*+++++++++++++++++++++ when save new is choosen+++++++++++++++++++++*/
          $("#saveNew").click(function(){
            
            $('#saveNew p').css('color','#00f0ff')

            $("#saveOverwrite").slideUp(function(){

                saveOption='SaveNew';/*assign SaveNew value to save option*/

                /*show save options*/
                $("#sEditorSave-CancelButtonHolder,#sEditorSave").slideDown();
            });/* saveOverwrite slideUp*/

          });/*saveNew clicked*/

          /*+++++++++++++++++++++ /when save new is choosen+++++++++++++++++++++*/

          /*when cancel is pressed*/
          $("#sEditorCancelBtn").click(function(){
            $("#saveOverwrite:hidden, #saveNew:hidden").slideDown(); 
            $('#saveNew p, #saveOverwrite p').css('color','#f5f5f5');
            $("#sEditorSave-CancelButtonHolder,#sEditorSave").slideUp();
          })/*when cancel is pressed*/

           /*when save is pressed*/
          $("#sEditorSaveBtn").click(function(){
            var sEditorNewImageName=$("#newImageName").val();
            

            if(saveOption=='SaveNew'){
              
              window.location=sEditVars.sEdit_pluginurl+'save-image.php?saveOption='+saveOption+'&scrImageVar='+scrImageVar+'&scrImageOrig=../../'+scrImageOrig+'&sEditorNewImageName='+sEditorNewImageName;

            }else{
                  
            /*send vars to php*/
                  var savePath=sEditVars.sEdit_pluginurl+'save-image.php?saveOption='+saveOption+'&scrImageVar='+scrImageVar+'&scrImageOrig=../../'+scrImageOrigrelative+'&sEditorNewImageName='+sEditorNewImageName;
                  //var relativeImagePath="../../../wp-content/uploads/2012/11/DSCF2555-1000x288.jpg";
                  //var savePath=sEditVars.sEdit_pluginurl+'save-image.php?saveOption='+saveOption+'&scrImageVar=temp/3Xe1CqStZL.jpg&scrImageOrig='+relativeImagePath+'&sEditorNewImageName='+sEditorNewImageName;


                  $.ajax({
                    type: 'POST',
                    url: savePath,
                   // data: 'saveOption='+saveOption+'&scrImageVar='+scrImageVar+'&scrImageOrig='+scrImageOrig+'&sEditorNewImageName='+sEditorNewImageName,
                    timeout:7000, //wait time until php file response (7 sec)
                    // dataType: 'json',

                              success: function(data){
                                   
                                    if(data=='Success'){
                                      alert('Original image has been overwritten!')
                                    }else{
                                      alert('I am not able to overwrite original image!')
                                    }

                                  },/*success*/
                              error: function(data){
                                alert('Currently i am not able to save this file. Please try later!')
                              }
                          })/*ajax */
          }/*if saveOption is SaveNew or Owervrite*/

/*send vars to php*/


          })

      /* +++++++++++++++++++++  /save options +++++++++++++++++++++ */

      /*clir input text on first click*/
      $("#newImageName").click(function(){
       if($(this).val()=='New image name'){
        $(this).val('');
       }/*if = New image name*/
      })/*newImageName click*/

    }();/*end igor_s self executed function*/


    


  });/*documentReady*/

