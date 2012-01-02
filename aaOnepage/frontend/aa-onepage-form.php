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
        $aaContact['description']   = $_POST['aaonepage_contact_description'];
            
        // Break name into first & last
        list($firstname, $lastname) = preg_split('/\s+(?=[^\s]+$)/', $fullname, 2);    
        $aaContact['firstname']     = $firstname;        
        $aaContact['lastname']      = !empty ( $lastname ) ? $lastname : '';
                        

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
        
        $formOutput = '';
        
        $formOutput .= '<form style="width: '. $setFormWidth .'" id="aa-onepage-contactform" class="'. $basicStyleClass .'" method="post" action="">
            <h3>';
                
                  // Custom heading set?
                  if(!empty( $aa_form_header )){
                       $formOutput .= $aa_form_header; 
                  }else{
                      $formOutput .= 'Request a callback'; 
                  }
            $formOutput .= '</h3>';
            
            $formOutput .= wp_nonce_field('add-contact','aaonepage_added', true, false); 
            
            if($aa_show_error_message){ 
                $formOutput .= '<p class="aa-error-message">'
                                     . $aa_error_message_text . 
                                '</p>';
            }             

//  Name 
            if( $aaFormConfig['name']['show'] == '1' ): 
                $formOutput .= '<div class="aa-onepage-fieldgroup">
                    <label for="aaonepage_contact_fullname">Name</label>
                    <input type="text" id="aaonepage_contact_fullname" name="aaonepage_contact_fullname" value="' . $fullname .'"';
                     if( $aaFormConfig['name']['required'] == '1'){  
                         $formOutput .= 'class="required"';
                     } 
                     $formOutput .= '/>';
                     
                     if(isset( $isValid['aaonepage_contact_fullname'] ) ): 
                            $formOutput .= '<div class="aa-error-message-form">'.
                                                $isValid['aaonepage_contact_fullname'] .
                                           '</div>';
                    endif;
                $formOutput .= '</div>';
            endif; 

// Company                     
            if( $aaFormConfig['company']['show'] == '1' ){
                $formOutput .= '<div class="aa-onepage-fieldgroup">
                    <label for="aaonepage_contact_company">Company </label>
                    <input type="text" id="aaonepage_contact_company" name="aaonepage_contact_company" value="'. $aaContact['company'] .'"';
                        if( $aaFormConfig['company']['required'] == '1'){ 
                            $formOutput .= 'class="required"';                            
                        }
                        $formOutput .= '/>';
                    
                if(isset( $isValid['aaonepage_contact_company'] ) ){  // any errors? 
                            $formOutput .= '<div class="aa-error-message-form">' .
                                $isValid['aaonepage_contact_company'] .
                            '</div>';
                }
                $formOutput .= '</div>';
            }

// Phone 
            if( $aaFormConfig['phone']['show'] == '1' ): 
                $formOutput .= '<div class="aa-onepage-fieldgroup">
                    <label for="aaonepage_contact_phone">Phone</label>
                    <input type="text" id="aaonepage_contact_phone" name="aaonepage_contact_phone" value="'. $aaContact['phones'] .'"';
                        if( $aaFormConfig['phone']['required'] == '1'){ 
                            $formOutput .= 'class="required"';                             
                        } 
                        $formOutput .= '/>';
                    
                    if(isset( $isValid['aaonepage_contact_phone'] ) ):
                            $formOutput .= '<div class="aa-error-message-form">'.
                                    $isValid['aaonepage_contact_phone'] .
                            '</div>';
                    endif; 
                $formOutput .= '</div>';
            endif;

//  Email 
            if( $aaFormConfig['email']['show'] == '1' ): 
                $formOutput .= '<div class="aa-onepage-fieldgroup">
                    <label for="aaonepage_contact_email">Email</label>
                    <input type="email" id="aaonepage_contact_email" name="aaonepage_contact_email" value="'. $aaContact['emails'] .'" class="email ';
                    if( $aaFormConfig['email']['required'] == '1'){ 
                        $formOutput .= ' required ';
                    } 
                    $formOutput .= '" />';
                    
                    if(isset( $isValid['aaonepage_contact_email'] ) ): 
                        $formOutput .= '<div class="aa-error-message-form">' .
                                            $isValid['aaonepage_contact_email']
                                    . '</div>';
                    endif;
                $formOutput .= '</div>';
            endif; 

// Message
           if( $aaFormConfig['message']['show'] == '1') : 
                $formOutput .= '<div class="aa-onepage-fieldgroup">
                    <label for="aaonepage_contact_description">Message</label>
                    <textarea name="aaonepage_contact_description" id="aaonepage_contact_description" rows="5" cols="30"';
                   if( $aaFormConfig['message']['required'] == '1'){ 
                       $formOutput .= 'class="required"';
                   } 
                   $formOutput .= '>' . $aaContact['description'] . '</textarea>';
                    
                   if(isset( $isValid['aaonepage_contact_description'] ) ): 
                        $formOutput .= '<div class="aa-error-message-form">' .
                                            $isValid['aaonepage_contact_description']
                                    . '</div>';
                    endif; 
                $formOutput .= '</div>';                
            endif; 

// Submit 
            $formOutput .= '<input type="submit" name="aaonepage_submit" class="aa-onepage-submit btn primary" value="Send">            
        </form>';
    
// Form Submitted. All is good.
    }elseif( $aa_success_message){ 
        $formOutput .= '<div id="aa-onepage-contactform"
        <h3>Message Sent</h3>
        <div class="aa-success-message">';

        $aa_success_message_text = get_option('aa_onepage_success_message'); 
        if(isset( $aa_success_message_text ) && !empty( $aa_success_message_text )){
            // only allowing html p tags...
            $cleaned = wp_kses($aa_success_message_text, array('p' => array('class' => array(), 'id' => array() ), 'strong' => array(), 'em' => array() ) );
            $formOutput .= $cleaned;
        }else{ 
            // Show default success message
            $formOutput .= '<p>
                Thank you. Your details have been submitted and we will be
                in touch shortly.
            </p>';

        } 
                
        $formOutput .= '    </div>
        </div>';

    }         
 
    return $formOutput;
}

add_shortcode('aa_onepage_form', aa_onepage_contact_form);