<?php
/**
 * Interact with the OnePageCRM Contacts api
 * 
 * @author Dave Kelly (@davkell / http://www.ambientage.com)
 * @version 0.1
 * @link http://www.onepagecrm.com/api/api-doc-for-dev-contacts.html
 */
class AAOnepage_Api_Contact extends AAOnepage_Api{


    public function createContact( array $contactDetails ){

        $args = array();
        $url = 'contacts';

        $contactDefaultValues = array(
            'firstname'     => '',                                          // Contact firstname
            'lastname'      => '',                                          // Contact lastname
            'company'       => '',                                          // Company name
            'job_title'     => null,                                        // Contact’s job title
            'address'       => null,                                          // Address
            'city'          => null,                                         // City (address)
            'state'         => null,                                         // State (address)
            'zip_code'      => null,                                         // ZIP code (address)
            'country'       => null,                                         // Country ISO code (address)
            'description'   => null,                                         // Contact description (AKA background)
            'phones'        => null,                                         // Contact phones (comma-separated list, every item in format phone_type|phone_number; list off possible phone types is attached in the further part of this document)
            'emails'        => null,                                         // Contact emails (comma-separated list, every item in format email_type|email_address, list off possible email types is attached in the further part of this document)
            'urls'          => null,                                         // Contact URLs (comma-separated list, every item in format url_type|url_address, list of possible email types is attached in the further part of this document)
            'vip'           => 0,                                            // Information if contact is VIP
            'status'        => null,                                         // Contact status, if invalid, lead will be used.
            'tags'          => get_option('aa_onepage_contact_tags'),        // Comma-separated tags list. Please note, that if "VIP" will be used here, contact will be marked as VIP and no such tag will be added.
        );                

        $contact = wp_parse_args( $contactDetails, $contactDefaultValues );
        
        /**
         * @todo expand to accept multiple phone types.         
         */
        if(isset($contact['phones'])){
            $contact['phones'] = 'work|'. $contact['phones'];
        }
        
        /**
         * @todo expand to accept multiple email types.
         */
        if(isset($contact['emails'])){
            $contact['emails'] = 'work|'. $contact['emails'];
        }
        $args['body'] = $contact;                            
        
        // Send it off to OnePage
        $onePageContact = $this->doApiCall($url, $args, 'POST');            
        
        // Valid id back?
        if($onePageContact->data->id){
            $nextAction = array(
              'next'    => true,
              'name'    => 'Web Contact: Callback',
              'cid'     => $onePageContact->data->id
            );
            $this->createAction( $nextAction );
            
            return true;
        }
        return  new WP_Error('broke', __( $onePageContact->message ));
    }

    
    /**
     * Create a new action for a contact.
     * 
     * @link http://www.onepagecrm.com/api/api-doc-for-dev-create-new-action.html
     * @param array $nextAction 
     * @return Object | Bool false on fail
     */
    public function createAction( array $nextAction ){
        
        $url = 'actions';
        $args = array();
        
        $actionDefaults = array(
            'cid'   => null,            // string Contact ID
            'date'  => null,            // date	Action date (dd.mm.yyyy)
            'next'  => 0,               // boolean Information if this action is marked as "next"
            'name'  => null             // string Action name/text
        );
                
        $args['body'] = wp_parse_args( $nextAction, $actionDefaults );
        
        $nextActionResponse = $this->doApiCall($url, $args, 'POST');  
        
        if($nextActionResponse->message === 'OK'){
            return true;
        }else{
            if(isset($nextActionResponse->message)){
                return  new WP_Error('broke', __( $nextActionResponse->message ));
            }
            return  new WP_Error('broke', __( "There was a problem creating a next action" ));
        }                
        
    }
    
    
    /**
     * Return a list of custom tags created by
     * the onepage user
     * @link http://www.onepagecrm.com/api/api-doc-for-dev-all-custom-tags.html
     * @return Mixed | Bool false on fail 
     */
    public function getCustomTags(){
        $url = 'tags';
        $data = $this->doApiCall( $url, array() );
        
        if(count($data->data) > 0){
            return $data->data;
        }
        return false;
    }
    
    
    public function emailContactDetails( $contact, $error = null){
        
        $to         = get_bloginfo('admin_email');
        $subject    = 'OnePage contact from Site';
        
        $message    = "A new contact was added to OnePageCRM. Sign in to check the next action \r\n";
        $message    .= "------------------------------------------------------ \r\n";
        
        foreach($contact as $key => $value ){
            $message .= $key . ': ' . $value . '\r\n';
        }
        
        if(isset( $error )){
            $message .= "================= ERROR Message ======================== \r\n";
            $message .= "There was an error adding the contact to OnePage. Details below: \r\n\r\n";
            $message .= $error->get_error_message();            
        }
        
        $resp = wp_mail($to, $subject, $message);
        
        if(! is_wp_error( $resp ) ){
            return true;
        }else{
            // at this point, you're pretty much screwed...the user gets an error message
            // telling them the form isn't working
            return false;
        }
        
    }

} 
