<?php
class CF_AR_Apartment_Application{
	public static function init(){
        $class = __CLASS__;
        new $class;
    }
	public function __construct(){
		add_shortcode('ar_apartment_application_form',array($this,'abr_application_form'));
		add_shortcode('ar_email_favorites_to_friends',array($this,'email_favorites_to_friends'));
	}
	
	/** Application form html
	*
	*/
	public function abr_application_form(){
		ob_start(); ?>
		<form name="" id="application_form" method="post" action="">
		<table cellspacing="9" cellpadding="0" border="0">
		  <tbody>
			<tr>
			  <td valign="top" align="left" colspan="2"><h1>Personal Information</h1></td>
			</tr>
			<tr>
			  <td width="275" valign="top" align="right"><font color="#8a2529">* </font>First Name:</td>
			  <td valign="top" align="left"><input type="text" tabindex="1" size="39" name="First_Name" id="First_Name" required class="frmINPT"></td>
			</tr>
			<tr>
			  <td width="275" valign="top" align="right"><font color="#8a2529">* </font>Last Name:</td>
			  <td valign="top" align="left"><input type="text" tabindex="2" size="39" name="Last_Name" id="Last_Name" required class="frmINPT"></td>
			</tr>
			<tr>
			  <td width="275" valign="top" align="right"><font color="#8a2529">* </font>Date of Birth:</td>
			  <td valign="top" align="left"><input type="date" tabindex="3" size="39" name="Dob" id="Dob" required class="frmINPT datepicker" style="width:58.5%"></td>
			</tr>
			<tr>
			  <td width="275" valign="top" align="right"><font color="#8a2529">* </font>Current Address:</td>
			  <td valign="top" align="left"><input type="text" tabindex="4" size="39" name="Current_Address" id="Current_Address" required class="frmINPT"></td>
			</tr>
			<tr>
			  <td width="275" valign="top" align="right"><font color="#8a2529">* </font>Current Home Phone:</td>
			  <td valign="top" align="left"><input type="text" tabindex="5" size="39" name="Current_Home_Phone" id="Current_Home_Phone" required class="frmINPT"></td>
			</tr>
			<tr>
			  <td width="275" valign="top" align="right"><font color="#8a2529">* </font>Current Work Phone:</td>
			  <td valign="top" align="left"><input type="text" tabindex="6" size="39" name="Current_Work_Phone" id="Current_Work_Phone" required class="frmINPT"></td>
			</tr>
			<tr>
			  <td width="275" valign="top" align="right"><font color="#8a2529">* </font>Email:</td>
			  <td valign="top" align="left"><input type="email" tabindex="7" size="39" name="Email" id="Email" required class="frmINPT"></td>
			</tr>
			<tr>
			  <td width="275" valign="top" align="right">Roommate's Names:</td>
			  <td valign="top" align="left"><input type="text" tabindex="9" size="39" name="Roommates_Names" id="Roommates_Names" class="frmINPT"></td>
			</tr>
			<tr>
			  <td width="275" valign="top" align="right">Roommate's Phone:</td>
			  <td valign="top" align="left"><input type="text" tabindex="10" size="39" name="Roommates_Phone" id="Roommates_Phone" class="frmINPT"></td>
			</tr>
			<tr>
			  <td width="275" valign="top" align="right">Roommate's Email:</td>
			  <td valign="top" align="left"><input type="email" tabindex="11" size="39" name="Roommate_Email" id="Roommate_Email" class="frmINPT"></td>
			</tr>
			<tr>
			  <td valign="top" align="left" colspan="2"><h2>Property Needs</h2></td>
			</tr>
			<tr>
			  <td width="275" valign="top" align="right"><font color="#8a2529"> * </font> Location / Property Applying For:</td>
			  <td valign="top" align="left"><input type="text"  tabindex="13" size="39" name="Location_Applying_For" id="Location_Applying_For" required class="frmINPT"></td>
			</tr>
			<tr>
			  <td width="275" valign="top" align="right"><font color="#8a2529"> * </font>Number of Bedrooms Needed:</td>
			  <td valign="top" align="left"><input type="text" tabindex="15" size="39" name="Bedrooms_Needed" id="Bedrooms_Needed" required class="frmINPT"></td>
			</tr>
			<tr>
			  <td width="275" valign="top" align="right">Units you are interested in:</td>
			  <td valign="top" align="left">
			 
				  <select id="communities" multiple="multiple" size="5" name="communities[]" style="width:58.5%" >
				  <?php
					$aptUtilitiesTAX	=	About_rental_cf_exe::get_terms_id_title_ARR('apartment_utilities');
					if($aptUtilitiesTAX){
						foreach($aptUtilitiesTAX as $tid=>$term){
							echo '<option value="'.$term.'">'.$term.'</option>';
						}
					}
				?>
				  </select>
				
				<input type="hidden" id="communities_interested" name="communities_interested" id="communities_interested" class="frmINPT">
				</td>
			</tr>
			<tr>
			  <td width="275" valign="top" align="right"><font color="#8a2529">* </font>Lease Would Begin On:</td>
			  <td valign="top" align="left"><label>
			  <select tabindex="14" size="1" name="Month_To_Begin" id="Month_To_Begin" required style="width:58.5%">
				  <option >Choose month</option>
				  <option value="January">January</option><option value="February">February</option>
				  <option value="March">March</option><option value="April">April</option>
				  <option value="May">May</option><option value="June">June</option>
				  <option value="July">July</option><option value="August">August</option>
				  <option value="September">September</option><option value="October">October</option>
				  <option value="November">November</option><option value="December">December</option>
				</select>
				</label>
				</td>
			</tr>
			
			<tr>
			  <td valign="top" align="left" colspan="2"><h2>Permanent Information</h2></td>
			</tr>
			<tr>
			  <td width="275" valign="top" align="right"><font color="#8a2529">* </font>Permanent Address:</td>
			  <td valign="top" align="left"><input type="text" tabindex="16" size="39" name="Permanent_Address" id="Permanent_Address" required class="frmINPT"></td>
			</tr>
			<tr>
			  <td width="275" valign="top" align="right"><font color="#8a2529">* </font>Permanent Phone:</td>
			  <td valign="top" align="left"><input type="text" tabindex="17" size="39" name="Permanent_Phone" id="Permanent_Phone" required class="frmINPT"></td>
			</tr>

			<tr>
			  <td width="275" valign="top" align="right"></td>
			  <td valign="top" align="left">
			  <input type="submit" tabindex="18" value="Submit Application" name="SubmitApplication" id="SubmitApplication" class="frmINPT">
			  </td>
			</tr>
		  </tbody>
		</table>
		</form>
		<?php
		$o=ob_get_contents();
		ob_end_clean();
		return $o;
	}
	
public function email_favorites_to_friends(){
	$CF_AR_Apartment_Application= new CF_AR_Apartment_Application();
	ob_start(); ?>
	<form action="" name="" id="frmregister" method="post">
		<table width="550" cellspacing="2" cellpadding="2" class="content">
			<tbody>
			<tr><td valign="top" colspan="2"><h1><?php echo __('Email favorites to your friends','ar') ;?></h1></td></tr>
			<tr>
			<td width="237" align="right" class="content"><font color="#8a2529">* </font><?php echo __('Recipient Email Address1:','ar') ;?></td>
			<td width="297"><input size="39" id="remail" class="contacttxt" name="remail" id="remail" type="email" required></td></tr>
			<tr>
			  <td align="right" class="content"><?php echo __('Recipient Email Address2','ar') ;?></td>
			  <td><input size="39" id="remail2" class="contacttxt" name="remail2" id="remail2" type="email" ></td>
			</tr>
			<tr>
			  <td align="right" class="content"><?php echo __('Recipient Email Address3','ar') ;?></td>
			  <td><input size="39" id="remail3" class="contacttxt" name="remail3" id="remail3" type="email" >
			  </td>
			</tr>
			<tr>
			  <td align="right" class="content"><?php echo __('Recipient Email Address4','ar') ;?></td>
			  <td><input size="39" id="remail4" class="contacttxt" name="remail4" id="remail4" type="email" >
			  </td>
			</tr>
			<tr>
			  <td align="right" class="content"><?php echo __('Recipient Email Address5','ar') ;?></td>
			  <td><input size="39" id="remail5" class="contacttxt" name="remail5" id="remail5" type="email" >
			  </td>
			</tr>
			<tr>
			  <td align="right" class="content"><font color="#8a2529">* </font><?php echo __('Your Email Address :' ,'ar') ;?></td>
			  <td>
				<input size="39"  id="semail" class="contacttxt" name="semail" id="semail" type="email" required>   </td>
			</tr>
			<tr>
			  <td width="237" align="right" class="content"><font color="#8a2529">* </font><?php echo __('Your Name :','ar') ;?></td>
			  <td>				
				  <input type="text"  size="39" id="sname" class="contacttxt" id="sname" name="sname" required>
			  </td>
			</tr>
			<tr>
			  <td width="237" align="right" class="content"><font color="#8a2529">* </font><?php echo __('Subject :','ar') ;?> </td>
			  <td><input type="text"  size="39" id="email_subject" class="contacttxt" id="email_subject" name="email_subject" required></td>
			</tr>
			<tr>
			  <td width="237" valign="top" align="right" class="content"><font color="#8a2529">* </font><?php echo __('Message :' ,'ar') ;?></td>
			  <td>
			  <textarea  id="message" rows="10" id="message" name="message" required> <?php echo$CF_AR_Apartment_Application->abr_retrieve_favourites();?>
			</textarea>
			</td>
			</tr>
		   <tr>
			  <td>&nbsp;</td>
			  <td>
			  <input type="submit" value="Submit" id="add" class="emailFriends" name="add"></td>
			</tr>
			</tbody></table>
		</form>
		<style>.sweet-alert input{display:none !important}	</style>
		<?php $o=ob_get_contents();
		ob_end_clean();
		return $o;
	}
	
	public function abr_retrieve_favourites(){
		global $wp_session;
		$wp_session = WP_Session::get_instance();
		if(isset($wp_session['fav_apartment_id'])){
			$seeion_arr = $wp_session['fav_apartment_id']->toArray();
			echo 'My favorite list :- ';
			if(!empty($seeion_arr)){
				foreach($seeion_arr as $aid){
					$apartment	=	get_post($aid);
					echo'<br /><a href='.get_permalink($aid).'>'.$apartment->post_title.'</a>';
				}
			}
		}
	}
	
}
add_action('plugins_loaded',array('CF_AR_Apartment_Application','init'));