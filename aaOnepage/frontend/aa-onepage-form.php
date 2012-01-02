<?php
/**
 * @package aaOnepage
 * 
 * @todo Get rid of hardcoded form. 
 */

function aa_onepage_contact_form() {
        
    $aaFormConfig = array(
        'name' => array(    
            'show'      => '1',
            'required'  => '1',          // required by Onepage
            'error'     => array(
                'required'      => 'Please include your name',
                'firstname'  => 'Please include a first &amp; last name'
            )
        ),
        'company' => array(
            'show'      => '1',
            'required'  => '1',          // required by Onepage
            'error'     => array(
                'required'  => 'Please include your company name'
            )
        ),
        'phone' => array(
            'show'      => get_option('aa_onepage_form_show_phone'),
            'required'  => get_option('aa_onepage_form_require_phone'),
            'error'     => array(
                'required'  => 'Please include your phone number'
            )
        ),
        'email' => array(
            'show'      => get_option('aa_onepage_form_show_email'),
            'required'  => get_option('aa_onepage_form_require_email'),
            'error'     => array(
                'required'  => 'Please include your email',
                'invalid'   => 'Please check the email address'
            )
        ),
        'message' => array(
            'show'      => get_option('aa_onepage_form_show_message'),
            'required'  => get_option('aa_onepage_form_require_message'),
            'error'     => array(
                'required'  => 'Please include a message'
            )
        )                
    );
    
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
        $aaContact['lastname']      = empty ( $lastname ) ? $lastname : '';
                        

        if (isset( $_POST['aaonepage_added'] ) && wp_verify_nonce($_POST['aaonepage_added'], 'add-contact') ){            
            // nonce is ok, go ahead
            $onePageApiContact  = new AAOnepage_Api_Contact();            
            $isValid            = $onePageApiContact->validateContact( $aaContact, $aaFormConfig);
                        
            if( is_array( $isValid ) ){     // Error message array
                $output_form = true;
                $aa_show_error_message = true;
                $aa_error_message_text = 'The form had errors.';
                
            }else{
                // Valid form....send to onepage
                die(' hit valid ');
                $onePageResponse    = $onePageApiContact->createContact( $aaContact );                        

                // Handle response...
                if($onePageResponse === true){  // all ok...                    
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
            
            // Use styling?
            $basicStyleClass = '';
            if(!empty($addBasicStyleClass) && $addBasicStyleClass == '1'){
                $basicStyleClass = 'aa-basic-style-it';
            }
            
            // Form Width
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
            <?php endif; ?>            

<!-- Name -->                    
            <?php if( $aaFormConfig['name']['show'] == '1' ): ?>
                <div class="aa-onepage-fieldgroup">
                    <label for="aaonepage_contact_fullname">Name</label>
                    <input type="text" id="aaonepage_contact_fullname" name="aaonepage_contact_fullname" value="<?php echo $fullname; ?>" 
                       <?php if( $aaFormConfig['name']['required'] == '1'){ ?> class="required" <?php } ?> />
                    <?php if(isset( $isValid['aaonepage_contact_fullname'] ) ): ?>
                            <div class="aa-error-message-form">
                                <?php echo $isValid['aaonepage_contact_fullname']; ?>
                            </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

<!-- Company -->                    
            <?php if( $aaFormConfig['company']['show'] == '1' ): ?>
                <div class="aa-onepage-fieldgroup">
                    <label for="aaonepage_contact_company">Company </label>
                    <input type="text" id="aaonepage_contact_company" name="aaonepage_contact_company" value="<?php echo $aaContact['company'] ?>" 
                        <?php if( $aaFormConfig['company']['required'] == '1'){ ?> class="required" <?php } ?> />
                    
                    <?php if(isset( $isValid['aaonepage_contact_company'] ) ):  // any errors? ?>
                            <div class="aa-error-message-form">
                                <?php echo $isValid['aaonepage_contact_company']; ?>
                            </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

<!-- Phone -->
            <?php if( $aaFormConfig['phone']['show'] == '1' ): ?>
                <div class="aa-onepage-fieldgroup">
                    <label for="aaonepage_contact_phone">Phone</label>
                    <input type="text" id="aaonepage_contact_phone" name="aaonepage_contact_phone" value="<?php echo $aaContact['phones']; ?>" 
                        <?php if( $aaFormConfig['phone']['required'] == '1'){ ?> class="required" <?php } ?> />
                    
                    <?php if(isset( $isValid['aaonepage_contact_phone'] ) ): ?>
                            <div class="aa-error-message-form">
                                <?php echo $isValid['aaonepage_contact_phone']; ?>
                            </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

<!-- Email -->
            <?php if( $aaFormConfig['email']['show'] == '1' ): ?>
                <div class="aa-onepage-fieldgroup">
                    <label for="aaonepage_contact_email">Email</label>
                    <input type="email" id="aaonepage_contact_email" name="aaonepage_contact_email" value="<?php echo $aaContact['emails']; ?>" class="email 
                        <?php if( $aaFormConfig['email']['required'] == '1'){ ?> required <?php } ?>" />
                    
                    <?php if(isset( $isValid['aaonepage_contact_email'] ) ): ?>
                            <div class="aa-error-message-form">
                                <?php echo $isValid['aaonepage_contact_email']; ?>
                            </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

<!-- Message -->
           <?php if( $aaFormConfig['message']['show'] == '1') : ?>
                <div class="aa-onepage-fieldgroup">
                    <label for="aaonepage_contact_description">Message</label>
                    <textarea name="aaonepage_contact_description" id="aaonepage_contact_description" rows="5" cols="30"
                              <?php if( $aaFormConfig['message']['required'] == '1'){ ?> class="required" <?php } ?>
                              ><?php echo $aaContact['description']; ?></textarea>
                    
                    <?php if(isset( $isValid['aaonepage_contact_description'] ) ): ?>
                            <div class="aa-error-message-form">
                                <?php echo $isValid['aaonepage_contact_description']; ?>
                            </div>
                    <?php endif; ?>
                </div>                
            <?php endif; ?>

<!-- Submit -->
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