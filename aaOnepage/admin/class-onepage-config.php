<?php
/**
 * @package aa_onepage
 * 
 * Based on Yoast's Wordpress SEO Plugin Admin Class
 */


if ( ! class_exists( 'AAOnepage_Admin' ) ) {
	
	class AAOnepage_Admin extends AAOnepage_Plugin_Admin {

		var $hook 		= 'aa-onepage';
		var $filename		= 'aaOnepage/aaOnepage.php';
		var $longname		= 'AA OnepageCRM Settings';
		var $shortname		= 'AAOnep';
		var $currentoption 	= 'aaonep';
		var $ozhicon		= 'tag.png';
		
		function AAOnepage_Admin() {
			add_action( 'init', array(&$this, 'init') );
		}
		
		function init() {
                    // if ( $this->grant_access() ) {
                        add_action( 'admin_init', array(&$this, 'options_init') );
                        add_action( 'admin_menu', array(&$this, 'register_settings_page') );
                        
                        add_filter( 'plugin_action_links', array(&$this, 'add_action_link'), 10, 2 );
/*
                    // }

                    add_action( 'wp_dashboard_setup', array(&$this,'widget_setup'));	                    
                    add_filter( 'wp_dashboard_widgets', array(&$this, 'widget_order'));                    
 * 
 */
		}

		function options_init() {
                    register_setting( 'aa_onepage_options', 'aa_onepage_username' );						
                    register_setting( 'aa_onepage_options', 'aa_onepage_pwd' );						                                        
                    register_setting( 'aa_onepage_options', 'aa_onepage_form_header' );						                                        
                    register_setting( 'aa_onepage_options', 'aa_onepage_form_width' );						                                        
                    register_setting( 'aa_onepage_options', 'aa_onepage_basic_style' );						                                        
                    register_setting( 'aa_onepage_options', 'aa_onepage_contact_tags' );						                                        
                    register_setting( 'aa_onepage_options', 'aa_onepage_success_message' );						                                        
		}
						
		
		function admin_sidebar() {
		?>
			<div class="postbox-container" style="width:20%;">
				<div class="metabox-holder">	
					<div class="meta-box-sortables">						
                                            Donate!! :)
					</div>
					<br/><br/><br/>
				</div>
			</div>
		<?php
		}		
		

                /**
                 * @todo Break this page into tabs...
                 */
		function aaonepage_settings_page() {           
                    $content = '';                                        
                    
                    if (!current_user_can('manage_options')){
                        wp_die( __('You do not have sufficient permissions to access this page.') );
                    }
                                            
                    
                    ?>
                        <div class="wrap">
                            <h2>Your OnePageCRM Settings</h2>
                            <form method="post" action="options.php">    
                                <?php
                                    settings_fields( 'aa_onepage_options' );                    
                                    do_settings_fields( 'aa_onepage_options', 'aaonepage_settings_page' );
                                ?>
                            <table class="form-table">                           
                                <tr valign="top">
                                    <th scope="row" colspan="2">
                                        <h3>OnepageCRM Details</h3>
                                    </th>
                                </tr>                               
                                
                                <?php
                                    if( null === $onePageApi){
                                        $onePageApi = new AAOnepage_Api();
                                    }
                                    $account_details = $onePageApi->getOnePageAccount();
                                    
                                    
                                    if( !is_wp_error( $account_details )):                                 
                                        // delete_transient( 'aa_onepage_account_details');
                                ?>
                                    <tr valign="top">
                                        <th scope="row">
                                            Signed in as:
                                        </th>
                                        <td>
                                            <?php echo $account_details->data->firstname . ' ' . $account_details->data->lastname; ?> (<?php echo $account_details->data->company; ?>)
                                            <br/>
                                            <small>
                                                (To sign out of OnePageCRM, deactivate the plugin in the <a href="<?php echo admin_url('plugins.php'); ?>">Plugins menu</a>. This will remove any
                                                OnePage forms on the site).
                                            </small>
                                        </td>
                                    </tr>
         
                                    <tr valign="top">
                                        <td colspan="2">
                                            <hr />
                                        </td>
                                    </tr>
                                    
                                    <tr valign="top">
                                        <th scope="row" colspan="2">
                                            <h3>Contact Form Setup</h3>
                                        </th>                                        
                                    </tr>
                                    
                                    <tr valign="top">
                                        <th scope="row">
                                            Give the form a heading
                                        </th>          
                                        <td>
                                            <input size="60" name="aa_onepage_form_header" type="text" value="<?php echo get_option('aa_onepage_form_header'); ?>" />
                                        </td>
                                    </tr>
                                    
                                    <tr valign="top">
                                        <th scope="row">
                                            Form Width?
                                            <br/>
                                            <small>
                                                Set a pixel width for the form. Leave empty for 100% width. Optimal is ~260px
                                            </small>
                                        </th>          
                                        <td>
                                            <input size="6" name="aa_onepage_form_width" type="text" value="<?php echo get_option('aa_onepage_form_width'); ?>" /> px
                                        </td>
                                    </tr>
                                    
                                    <tr valign="top">
                                        <th scope="row">
                                            Use basic styling<br/>
                                            <small>(You can write your own styles by targeting 
                                                #aa-onepage-contactform)</small>
                                        </th>          
                                        <td>                                            
                                            <?php $aa_onepage_basic_style = get_option('aa_onepage_basic_style'); ?>
                                                <input type="checkbox" name="aa_onepage_basic_style" value="1" 
                                                    <?php if($aa_onepage_basic_style == '1'){echo 'checked = "checked"';} ?> />                                                                                                       
                                        </td>
                                    </tr>
                                    
                                    <tr valign="top">
                                        <th scope="row">
                                            Add a tag to contacts:      
                                            <br/>
                                            <small>
                                                (When a new contact is added, they'll be tagged
                                                with this so you can find all your web contacts)
                                            </small>
                                        </th>
                                        <td>                                           
                                            <?php 
                                                if(null === $onePageApiContact){               
                                                    $onePageApiContact = new AAOnepage_Api_Contact();
                                                }                                                                   
                                                $tags = $onePageApiContact->getCustomTags(); 
                                                if($tags){ ?>
                                                    <select name="aa_onepage_contact_tags">
                                                        <option value="">Choose a Tag</option>
                                                    <?php
                                                        $chosenTag = get_option('aa_onepage_contact_tags');
                                                        foreach( $tags as $tag ){
                                                            $selected = '';
                                                            if($tag->name === $chosenTag ){
                                                                $selected = ' selected="selected" ';                                                                    
                                                            }
                                                            echo '<option value="'. $tag->name .'" '. $selected . '>' . $tag->name . '</option>';

                                                        }
                                                    ?>
                                                    </select>                                                       
                                                <?php                                                     
                                                }else{ ?>
                                                    You haven't created any tags in OnePageCRM yet.
                                            <?php                                               
                                                } 
                                            ?>                                                                                        
                                        </td>
                                    </tr>
                                    
                                    <tr valign="top">
                                        <th scope="row">
                                            Form Submit Success Message
                                            <br/>
                                            <small>
                                                (what the user sees when they submit the form. You're allowed
                                                use &lt;p&gt;, &lt;strong&gt; &amp; &lt;em&gt; html tags). For styling, the message is wrapped in css
                                                class is .aa-success-message.
                                            </small>
                                        </th>
                                        <td>
                                            <textarea name="aa_onepage_success_message" rows="3" cols="50"><?php echo get_option('aa_onepage_success_message'); ?></textarea>
                                        </td>
                                    </tr>
                                    
                                    <tr valign="top">
                                        <td colspan="2">
                                            <hr />
                                        </td>
                                    </tr>
                                <?php else: ?>                                    
                                     <?php if( is_wp_error( $account_details )): ?>
                                         <tr valign="top">
                                            <th scope="row" colspan="2">
                                                <div class="error">
                                                    <?php if( strtolower( $account_details->get_error_message()) === 'invalid request data'): ?>
                                                            <p>
                                                                Incorrect Username or Password.
                                                            </p>
                                                    <?php else: ?>
                                                        <?php echo $account_details->get_error_message(); ?>
                                                    <?php endif; ?>
                                                </div>
                                            </th>                                    
                                        </tr>
                                    <?php endif; ?>

                                    <tr valign="top">
                                        <th scope="row">
                                            Onepage Username
                                        </th>
                                        <td>
                                        <input name="aa_onepage_username" type="text" value="<?php echo get_option( 'aa_onepage_username' ); ?>"/>
                                    </td> 
                                    </tr>
                                    <tr valign="top">
                                        <th scope="row">
                                            Onepage Password
                                        </th>
                                        <td>
                                            <input type="password" name="aa_onepage_pwd" value="<?php echo get_option( 'aa_onepage_pwd' ); ?>" />
                                        </td>
                                    </tr>
                                    
                                <?php endif; ?>
                                                                    
                            </table>

                            <p class="submit">
                                <input type="submit" class="button-primary" value="Save Changes" />
                            </p>
                        </form>
                    </div>
                    <?php 
                    
                    /*
                    
                    $api = new AAOnepage_Api();
                    echo 'UID: ' . $api->getUid();
                     * 
                     */
		}
                
                
                public function textField(){
                    
                }
                
                public function emailField(){
                    
                }
                
                public function createDropdown(){
                    
                }
		
	} // end class
        
	$aaOnepage_admin = new AAOnepage_Admin();
}
