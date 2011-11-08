/*!
 * Handle the front-end behaviours for the aaOnepage plugin
 * 
 * @author Dave Kelly (http://www.ambientage.com)
 * @version 0.1
 * @date 7/11/11
 */

$(document).ready(function(){
    var aa = {};
    aa.onepage = {
      contact: {
           firstname: null,
           lastname: null,
           email: null,
           phone: null,
           message: null
       }, 
       
       validateForm: function( oForm ){
         $('.aa-form-error').remove();
         var errorsFound = false;
         $(oForm + ':input').each(function(){             
             if($(this).hasClass('required')){
                 $(this).after('<div class="aa-form-error">This field is required</div>');
                 errorsFound = true;
             }

             if($(this).attr('type') === 'email'){
                 var re = '/^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/';
                 var valid = re.test($(this).val());
                 console.log( valid );
             }

             if(errorsFound === true){
                 return false;
             }

         });

       },   
       postForm: function(){
           // for when ajaxy stuff lands....
       }    
    };
        
    $('form#aa-onepage-contactform').submit(function(){    
      /**
       * @todo Fix validation...
       */
      // aa.onepage.validateForm( $(this) );             
   });
});

