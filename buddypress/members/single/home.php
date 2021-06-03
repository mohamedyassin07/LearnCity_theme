<?php
/**
 * BuddyPress - Members Home
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */

$lms_plugin_exists = defined( 'STM_LMS_FILE' );

if ( $lms_plugin_exists ):
	$current_user = STM_LMS_User::get_current_user( '', false, true );
	$tpl           = 'buddypress/';
	$profile       = 'private';

	if ( STM_LMS_BuddyPress::is_bp_current_user() ) {
		$tpl .= "account/{$profile}/main";
	} else {
		$profile = 'public';
		$tpl     .= "account/{$profile}/main";

		$currentUserID = bp_displayed_user_id();
		$current_user  = STM_LMS_User::get_current_user( $currentUserID, false, true );
	};

	stm_lms_register_style( 'user' );


	?>

	<div class="stm-lms-wrapper">
		<div class="container">
			<?php STM_LMS_Templates::show_lms_template( $tpl, compact( 'current_user' ) ); ?>
		</div>
	</div>

<?php else: ?>
	<?php get_template_part('buddypress/members/single/home-default'); ?>
<?php endif;