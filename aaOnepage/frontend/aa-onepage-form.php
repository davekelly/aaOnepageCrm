<?php
/**
 * @package aaOnepage
 * 
 * @todo Get rid of hardcoded form. Give options on settings page to allow
 *       choice of fields to include and what to require.
 * 
 */

function aa_onepage_contact_form() {
    
    $aaFormShowMessage  = get_option('aa_onepage_form_show_message');
    
    if (isset($_POST['aaonepage_submit']) ) { 
        $output_form    = false;
        $aaContact      = array();
        
        $fullname                   = $_POST['aaonepage_contact_fullname'];
        $aaContact['emails']        = $_POST['aaonepage_contact_email'];
        $aaContact['phones']        = $_POST['aaonepage_contact_phone'];
        $aaContact['company']       = $_POST['aaonepage_contact_company'];
        
        if( !empty( $aaFormShowMessage )){
            $aaContact['description']   = $_POST['aaonepage_contact_description'];
        }
    
        // Break name into first & last
        list($firstname, $lastname) = preg_split('/\s+(?=[^\s]+$)/', $fullname, 2);    
        $aaContact['firstname']     = $firstname;
        $aaContact['lastname']      = $lastname;
                        

        if (isset( $_POST['aaonepage_added'] ) && wp_verify_nonce($_POST['aaonepage_added'], 'add-contact') ){            
            // nonce is ok, go ahead
            $onePageApiContact = new AAOnepage_Api_Contact();
            $onePageResponse = $onePageApiContact->createContact( $aaContact );            
            
            // Handle response...
            if($onePageResponse === true){ 
                // all ok...
                $output_form  = false; 
                $aa_success_message = true;
            }else{
                // There's been a problem, fall back to email...
                $mailSent = $onePageApiContact->emailContactDetails($aaContact, $error = $onePageResponse);
                if( $mailSent ){
                    // mail sent to admin with contact details & error
                    $output_form = false;
                    $aa_success_message = true;
                }else{
                    // Mail failed too...give them an error.
                    $output_form = false;
                    $aa_show_error_message = true;
                    $aa_error_message_text = 'Apologies, we are currently having a problem with this contact form.';
                }
                
            }
            
        }else{
            $output_form = true;
            $aa_show_error_message = true;
            $aa_error_message_text = 'An error occurred when sending the form. Please try again.';
        }

    } else {        // ! form submit
        
       $output_form = true;
       $name = "";
       $description = "";
    }
    
    // Do we display the form?
    if ($output_form) { ?>
        <?php            
            $aa_form_header     = get_option('aa_onepage_form_header'); 
            $addBasicStyleClass = get_option('aa_onepage_basic_style');
            $aaFormWidth        = get_option('aa_onepage_form_width');            
            
            $basicStyleClass = '';
            if(!empty($addBasicStyleClass) && $addBasicStyleClass == '1'){
                $basicStyleClass = 'aa-basic-style-it';
            }
            
            $setFormWidth = '100%;';
            if(!empty($aaFormWidth)){
                $setFormWidth = $aaFormWidth . 'px;';
            }            
        ?>
        <form style="width: <?php echo $setFormWidth; ?>" id="aa-onepage-contactform" class="<?php echo $basicStyleClass;?>" method="post" action="">
            <h3>
                <?php 
                  // Custom heading set?
                  if(!empty( $aa_form_header )){
                       echo $aa_form_header; 
                  }else{
                      echo 'Request a callback'; 
                  }
                ?>
            </h3>
            <?php wp_nonce_field('add-contact','aaonepage_added'); ?>
            <?php if($aa_show_error_message): ?>
                    <p class="aa-error-message">
                         <?php echo $aa_error_message_text; ?>
                    </p>
            <?php endif; // aa_show_error_message ?>
            <div class="aa-onepage-fieldgroup">
                <label for="aaonepage_contact_fullname">Name</label>
                <input type="text" class="required" id="aaonepage_contact_fullname" name="aaonepage_contact_fullname" value="<?php echo $fullname; ?>" />
            </div>
            <div class="aa-onepage-fieldgroup">
                <label for="aaonepage_contact_company">Company </label>
                <input type="text" class="required" id="aaonepage_contact_company" name="aaonepage_contact_company" value="<?php echo $aaContact['company'] ?>" />
            </div>
            <div class="aa-onepage-fieldgroup">
                <label for="aaonepage_contact_phone">Phone</label>
                <input type="text" class="required" id="aaonepage_contact_phone" name="aaonepage_contact_phone" value="<?php echo $aaContact['phones']; ?>" />
            </div>
            <div class="aa-onepage-fieldgroup">
                <label for="aaonepage_contact_email">Email</label>
                <input type="email" class="required" id="aaonepage_contact_email" name="aaonepage_contact_email" value="<?php echo $aaContact['emails']; ?>" />
            </div>
                    
            <?php
                // Show the message box? 
                if(! empty( $aaFormShowMessage )): ?>
                <div class="aa-onepage-fieldgroup">
                    <label for="aaonepage_contact_description">Message</label>
                    <textarea name="aaonepage_contact_description" id="aaonepage_contact_description" rows="5" cols="30"><?php echo $aaContact['description']; ?></textarea>
                </div>                
            <?php endif; ?>
                    
            <input type="submit" name="aaonepage_submit" class="aa-onepage-submit btn primary" value="Send">            
        </form>
    <?php
    
    // Form Submitted. All is good.
    }elseif( $aa_success_message){ ?>
        <div id="aa-onepage-contactform"
        <h3>Message Sent</h3>
        <div class="aa-success-message">
            <?php 
            $aa_success_message_text = get_option('aa_onepage_success_message'); 
            if(isset( $aa_success_message_text ) && !empty( $aa_success_message_text )){
                // only allowing html p tags...
                $cleaned = wp_kses($aa_success_message_text, array('p' => array('class' => array(), 'id' => array() ), 'strong' => array(), 'em' => array() ) );
                echo $cleaned;
            }else{ 
                // Show default success message
                ?>
                <p>
                    Thank you. Your details have been submitted and we will be
                    in touch shortly.
                </p>

       <?php } ?>
                
            </div>
        </div>

<?php } ?>
        
        
        
    <?php
       

};

add_shortcode('aa_onepage_form', aa_onepage_contact_form);