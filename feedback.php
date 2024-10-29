<?php
/*
Plugin Name: Beautiful Feedback
Plugin URI: http://www.alekseykostin.ru/301/
Description: Beautiful Feedback Form
Version: 0.1.6
Author: Kostin Aleksey
Author URI: http://www.alekseykostin.ru/
License: GNU General Public License v2.0
License URI: http://www.gnu.org/licenses/gpl-2.0.html

	Copyright (c) 2011 Kostin Aleksey

	Permission is hereby granted, free of charge, to any person obtaining a
	copy of this software and associated documentation files (the "Software"),
	to deal in the Software without restriction, including without limitation
	the rights to use, copy, modify, merge, publish, distribute, sublicense,
	and/or sell copies of the Software, and to permit persons to whom the
	Software is furnished to do so, subject to the following conditions:

	The above copyright notice and this permission notice shall be included in
	all copies or substantial portions of the Software.

	THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
	IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
	FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
	AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
	LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING
	FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER
	DEALINGS IN THE SOFTWARE.
*/

if ( ! function_exists( 'feedback_init' ) )
{
	function feedback_init()
	{
		if (!is_admin())
		{
			wp_deregister_script('jquery');
			wp_register_script('jquery', "https://ajax.googleapis.com/ajax/libs/jquery/1.5.1/jquery.min.js", false, '1.4.4');
			wp_register_script('feedback', site_url('/wp-content/plugins/') . dirname(plugin_basename(__FILE__)) . '/feedback.js', 'jquery', false, true);
			wp_enqueue_script('jquery');
			wp_enqueue_script('feedback');
		}
		
		load_plugin_textdomain('feedback', 'wp-content/plugins/'.basename(dirname(plugin_basename(__FILE__))), dirname(plugin_basename(__FILE__)));
	}
	add_action('init', 'feedback_init');
}

if ( ! function_exists( 'feedback_wp_head' ) )
{
	function feedback_wp_head()
	{
		wp_enqueue_script("jquery");
		?><link rel="stylesheet" type="text/css" media="all" href="<?php echo site_url('/wp-content/plugins/') . dirname(plugin_basename(__FILE__)) . '/feedback.css'; ?>" /><?
	}
	add_action('wp_head', 'feedback_wp_head');
}

if ( ! function_exists( 'feedback_wp_footer' ) )
{
	function feedback_wp_footer()
	{
		// Get options.
		$plugin_feedback_name_label = get_option('_plugin_feedback_name_label');
		if (empty($plugin_feedback_name_label)) $plugin_feedback_name_label = __('Your name', 'feedback');
		$plugin_feedback_name_required = @intval(get_option('_plugin_feedback_name_required'));
		
		$plugin_feedback_email_label = get_option('_plugin_feedback_email_label');
		if (empty($plugin_feedback_email_label)) $plugin_feedback_email_label = __('Your email', 'feedback');
		$plugin_feedback_email_required = @intval(get_option('_plugin_feedback_email_required'));
		
		$plugin_feedback_phone_label = get_option('_plugin_feedback_phone_label');
		if (empty($plugin_feedback_phone_label)) $plugin_feedback_phone_label = __('Your phone number', 'feedback');
		$plugin_feedback_phone_required = @intval(get_option('_plugin_feedback_phone_required'));
		
		$plugin_feedback_message_label = get_option('_plugin_feedback_message_label');
		if (empty($plugin_feedback_message_label)) $plugin_feedback_message_label = __('Message', 'feedback');
		$plugin_feedback_message_required = @intval(get_option('_plugin_feedback_message_required'));
		
		$plugin_feedback_captcha_required = @intval(get_option('_plugin_feedback_captcha_required'));
		$plugin_feedback_show_on_left = @intval(get_option('_plugin_feedback_show_on_left'));

		$plugin_feedback_send_label = get_option('_plugin_feedback_send_label');
		if (empty($plugin_feedback_send_label)) $plugin_feedback_send_label = __('Send', 'feedback');

		$plugin_feedback_top_position = get_option('_plugin_feedback_top_position');
		if (!is_numeric($plugin_feedback_top_position)) $plugin_feedback_top_position = 100;

		$plugin_feedback_success_text = get_option('_plugin_feedback_success_text');
		if (empty($plugin_feedback_success_text)) $plugin_feedback_success_text = __('<p>Thank you! Your message was send successfully!</p><p>We\'ll contact you as soon as possible.</p><p align="right">- Administration</p>', 'feedback');

		?>
		<script language="JavaScript" type="text/javascript"><!--
		var beautiful_feedback_captcha_required = <?php if ( $plugin_feedback_captcha_required ) : ?>true<?php else : ?>false<?php endif; ?>;
		var beautiful_feedback_success_text = '<?php echo addcslashes($plugin_feedback_success_text, "'"); ?>';
		//--></script>
		<style type="text/css">
		#feedback {
			background:#dfcbb2 url(<?php echo site_url('/wp-content/plugins/').dirname(plugin_basename(__FILE__)) ?>/images/<?php if (file_exists(realpath(dirname(__FILE__)).'/images/form.gif')) : ?>form.gif<?php else : ?>form-source.gif<?php endif; ?>) 0 0 no-repeat;
			top: <?php echo $plugin_feedback_top_position; ?>px;
		}
		#feedback-open {
			background:url(<?php echo site_url('/wp-content/plugins/').dirname(plugin_basename(__FILE__)) ?>/images/<?php if (file_exists(realpath(dirname(__FILE__)).'/images/open.png')) : ?>open.png<?php else : ?>open-source.png<?php endif; ?>) 0 0 no-repeat;
		}
		#feedback-close {
			background:url(<?php echo site_url('/wp-content/plugins/').dirname(plugin_basename(__FILE__)) ?>/images/<?php if (file_exists(realpath(dirname(__FILE__)).'/images/close.png')) : ?>close.png<?php else : ?>close-source.png<?php endif; ?>) 0 0 no-repeat;
		}
		</style>
		<div id="feedback" class="feedback-fixed <?php if ( !empty( $plugin_feedback_show_on_left ) ) : ?>feedback-left<?php else : ?>feedback-right<?php endif; ?>">
			<form id="feedback-form" action="<?php echo site_url('/wp-content/plugins/') . dirname(plugin_basename(__FILE__)) . '/feedback_post.php'; ?>" method="POST" onsubmit="return false;">
				<table cellpadding="0" cellspacing="0" border="0" width="270">
				<tr><td colspan="2" style="padding-left:90px;"><i><b><?php echo $plugin_feedback_name_label; ?></b></i><?php if ( $plugin_feedback_name_required ) : ?><span class="required">*</span><?php endif; ?></td></tr>
				<tr><td colspan="2" style="padding-left:90px;"><input id="feedback-name" type="text" name="name" value="" style="width:180px;" /></td></tr>

				<tr><td colspan="2" style="padding-left:90px;"><i><b><?php echo $plugin_feedback_phone_label; ?></b></i><?php if ( $plugin_feedback_phone_required ) : ?><span class="required">*</span><?php endif; ?></td></tr>
				<tr><td colspan="2" style="padding-left:90px;"><input id="feedback-phone" type="text" name="phone" value="" style="width:180px;" /></td></tr>

				<tr><td colspan="2" style="padding-left:90px;"><i><b><?php echo $plugin_feedback_email_label; ?></b></i><?php if ( $plugin_feedback_email_required ) : ?><span class="required">*</span><?php endif; ?></td></tr>
				<tr><td colspan="2" style="padding-left:90px;"><input id="feedback-email" type="text" name="email" value="" style="width:180px;" /></td></tr>

				<tr><td colspan="2"><i><b><?php echo $plugin_feedback_message_label; ?></b></i><?php if ( $plugin_feedback_message_required ) : ?><span class="required">*</span><?php endif; ?></td></tr>
				<tr><td colspan="2" style="padding-bottom:5px;"><textarea id="feedback-message" name="message" style="width:270px;height:60px;"></textarea></td></tr>
				<?php if ( $plugin_feedback_captcha_required ) : ?><tr>
					<td style="padding-right:20px;vertical-align:top;">
						<img id="feedback-captcha-img" src="<?php echo site_url('/wp-content/plugins/') . dirname(plugin_basename(__FILE__)) . '/captcha-image.php?' . uniqid('nocache'); ?>" title="<?php echo __('Secret code', 'feedback'); ?>" alt="<?php echo __('Secret code', 'feedback'); ?>" />
					</td>
					<td style="vertical-align:top;">
						<label for="captcha" style="font-size:80%;line-height:100%;display:block;">
							<?php echo __('Secret code', 'feedback'); ?><span class="required">*</span>
						</label>
						<input id="feedback-captcha" title="<?php echo __('Please, enter valid secret code', 'feedback'); ?>" id="captcha" name="captcha" type="text" value="" style="width:130px;" />
					</td>
				</tr><?php endif; ?>
				<tr>
					<td colspan="2" style="text-align:right;"><input id="feedback-send" class="submit" type="submit" name="feedback_submit" value="<?php echo $plugin_feedback_send_label; ?>" /></td>
				</tr>
				</table>
			</form>
			<div id="feedback-open"></div>
			<div id="feedback-close"></div>
		</div>
		<?
	}
	add_action('wp_footer', 'feedback_wp_footer');
}


if ( ! function_exists( 'feedback_options' ) && ! function_exists( 'feedback_menu' ) )
{
	function feedback_options()
	{
		// Check form submission and update options...
		if(isset($_POST['submit']))
		{
			update_option('_plugin_feedback_name_label', stripslashes($_POST['name_label']));
			update_option('_plugin_feedback_name_required', !empty($_POST['name_required']));
			update_option('_plugin_feedback_email_label', stripslashes($_POST['email_label']));
			update_option('_plugin_feedback_email_required', !empty($_POST['email_required']));
			update_option('_plugin_feedback_phone_label', stripslashes($_POST['phone_label']));
			update_option('_plugin_feedback_phone_required', !empty($_POST['phone_required']));
			update_option('_plugin_feedback_message_label', stripslashes($_POST['message_label']));
			update_option('_plugin_feedback_message_required', !empty($_POST['message_required']));
			update_option('_plugin_feedback_captcha_required', !empty($_POST['captcha_required']));
			update_option('_plugin_feedback_show_on_left', !empty($_POST['show_on_left']));
			update_option('_plugin_feedback_top_position', is_numeric($_POST['top_position']) ? $_POST['top_position'] : 100);
			update_option('_plugin_feedback_send_label', stripslashes($_POST['send_label']));
			update_option('_plugin_feedback_mail_from_name', stripslashes($_POST['mail_from_name']));
			update_option('_plugin_feedback_mail_from_email', stripslashes($_POST['mail_from_email']));
			update_option('_plugin_feedback_mail_to', stripslashes($_POST['mail_to']));
			update_option('_plugin_feedback_success_text', stripslashes($_POST['success_text']));
			update_option('_plugin_feedback_mail_charset', stripslashes($_POST['mail_charset']));
			
			// Output any action message (note, can only be from a POST or GET not both).
			echo "<div id='message' class='updated fade'><p>", __('Changes saved', 'feedback'), "</p></div>";
		}

		// Get options.
		$plugin_feedback_name_label = get_option('_plugin_feedback_name_label');
		if (empty($plugin_feedback_name_label)) $plugin_feedback_name_label = __('Your name', 'feedback');
		$plugin_feedback_name_required = @intval(get_option('_plugin_feedback_name_required'));
		
		$plugin_feedback_email_label = get_option('_plugin_feedback_email_label');
		if (empty($plugin_feedback_email_label)) $plugin_feedback_email_label = __('Your email', 'feedback');
		$plugin_feedback_email_required = @intval(get_option('_plugin_feedback_email_required'));
		
		$plugin_feedback_phone_label = get_option('_plugin_feedback_phone_label');
		if (empty($plugin_feedback_phone_label)) $plugin_feedback_phone_label = __('Your phone number', 'feedback');
		$plugin_feedback_phone_required = @intval(get_option('_plugin_feedback_phone_required'));
		
		$plugin_feedback_message_label = get_option('_plugin_feedback_message_label');
		if (empty($plugin_feedback_message_label)) $plugin_feedback_message_label = __('Message', 'feedback');
		$plugin_feedback_message_required = @intval(get_option('_plugin_feedback_message_required'));
		
		$plugin_feedback_captcha_required = @intval(get_option('_plugin_feedback_captcha_required'));
		$plugin_feedback_show_on_left = @intval(get_option('_plugin_feedback_show_on_left'));

		$plugin_feedback_send_label = get_option('_plugin_feedback_send_label');
		if (empty($plugin_feedback_send_label)) $plugin_feedback_send_label = __('Send', 'feedback');

		$plugin_feedback_top_position = get_option('_plugin_feedback_top_position');
		if (!is_numeric($plugin_feedback_top_position)) $plugin_feedback_top_position = 100;
		
		$plugin_feedback_mail_from_name = get_option('_plugin_feedback_mail_from_name');
		
		$plugin_feedback_mail_from_email = get_option('_plugin_feedback_mail_from_email');
		if (empty($plugin_feedback_mail_from_email)) $plugin_feedback_mail_from_email = get_option('admin_email');
		
		$plugin_feedback_mail_to = get_option('_plugin_feedback_mail_to');
		if (empty($plugin_feedback_mail_to)) $plugin_feedback_mail_to = get_option('admin_email');
		
		$plugin_feedback_success_text = get_option('_plugin_feedback_success_text');
		if (empty($plugin_feedback_success_text)) $plugin_feedback_success_text = __('<p>Thank you! Your message was send successfully!</p><p>We\'ll contact you as soon as possible.</p><p align="right">- Administration</p>', 'feedback');
		// <p>Спасибо! Ваше сообщение отправлено!</p><p>В ближайшее время мы свяжемся с вами, чтобы ответить на ваш вопрос.</p><p align="right">- Администрация</p>		
		
		$plugin_feedback_mail_charset = get_option('_plugin_feedback_mail_charset');
		if (empty($plugin_feedback_mail_charset)) $plugin_feedback_mail_charset = 'windows-1251';
		
		?>
		<div class="wrap">
			<h2><?php echo __('Feedback settings', 'feedback'); ?></h2>
			<form method="post" action="<?php echo $_SERVER['PHP_SELF'] . '?page=' . basename(__FILE__); ?>">
				<table class='form-table'>
					<tr>
						<th scope="row"><label for="name_label"><?php echo __('Name field label', 'feedback'); ?></label></th>
						<td><input type="text" name="name_label" id="name_label" class="regular-text" value="<?php echo $plugin_feedback_name_label; ?>" /></td>
						<td><label>
							<input type="checkbox" name="name_required" value="1" <?php echo !empty($plugin_feedback_name_required) ? 'checked' : ''; ?> />
							<?php echo __('Required', 'feedback'); ?>
						</label></td>
					</tr>
					<tr>
						<th scope="row"><label for="email_label"><?php echo __('E-mail field label', 'feedback'); ?></label></th>
						<td><input type="text" name="email_label" id="email_label" class="regular-text" value="<?php echo $plugin_feedback_email_label; ?>" /></td>
						<td><label>
							<input type="checkbox" name="email_required" value="1" <?php echo !empty($plugin_feedback_email_required) ? 'checked' : ''; ?> />
							<?php echo __('Required', 'feedback'); ?>
						</label></td>
					</tr>
					<tr>
						<th scope="row"><label for="phone_label"><?php echo __('Phone field label', 'feedback'); ?></label></th>
						<td><input type="text" name="phone_label" id="phone_label" class="regular-text" value="<?php echo $plugin_feedback_phone_label; ?>" /></td>
						<td><label>
							<input type="checkbox" name="phone_required" value="1" <?php echo !empty($plugin_feedback_phone_required) ? 'checked' : ''; ?> />
							<?php echo __('Required', 'feedback'); ?>
						</label></td>
					</tr>
					<tr>
						<th scope="row"><label for="message_label"><?php echo __('Message field label', 'feedback'); ?></label></th>
						<td><input type="text" name="message_label" id="message_label" class="regular-text" value="<?php echo $plugin_feedback_message_label; ?>" /></td>
						<td><label>
							<input type="checkbox" name="message_required" value="1" <?php echo !empty($plugin_feedback_message_required) ? 'checked' : ''; ?> />
							<?php echo __('Required', 'feedback'); ?>
						</label></td>
					</tr>
					<tr>
						<th scope="row"><?php echo __('CAPTCHA', 'feedback'); ?></th>
						<td><label><input type="checkbox" name="captcha_required" value="1" <?php echo !empty($plugin_feedback_captcha_required) ? 'checked' : ''; ?> /> <?php echo __('Required', 'feedback'); ?></label></td>
						<td><span class="description">
							<?php echo __('Turn on spam protection.', 'feedback'); ?>
						</span></td>
					</tr>
					<tr>
						<th scope="row"><?php echo __('Left side', 'feedback'); ?></th>
						<td><label><input type="checkbox" name="show_on_left" value="1" <?php echo !empty($plugin_feedback_show_on_left) ? 'checked' : ''; ?> /> <?php echo __('Yes', 'feedback'); ?></label></td>
						<td><span class="description">
							<?php echo __('Show the form on the left side of the window.', 'feedback'); ?>
						</span></td>
					</tr>
					<tr>
						<th scope="row"><label for="top_position"><?php echo __('Top position', 'feedback'); ?></label></th>
						<td><input type="text" name="top_position" id="top_position" class="regular-text" value="<?php echo is_numeric($plugin_feedback_top_position) ? $plugin_feedback_top_position : 100; ?>" /></td>
						<td><span class="description">
							<?php echo __('Top position of the form in pixels.', 'feedback'); ?>
						</span></td>
					</tr>
					<tr>
						<th scope="row"><label for="send_label"><?php echo __('Send button label', 'feedback'); ?></label></th>
						<td><input type="text" name="send_label" id="send_label" class="regular-text" value="<?php echo $plugin_feedback_send_label; ?>" /></td>
						<td><span class="description">
							<?php echo __('Text on the send button.', 'feedback'); ?>
						</span></td>
					</tr>
					<tr>
						<th scope="row"><label for="mail_from_email"><?php echo __('Sender E-mail', 'feedback'); ?></label></th>
						<td><input type="text" name="mail_from_email" id="mail_from_email" class="regular-text" value="<?php echo $plugin_feedback_mail_from_email; ?>" /></td>
						<td><span class="description">
							<?php echo __('Enter sender E-mail address of feedback message. if empty, address from form or «Mail to» address will be used.', 'feedback'); ?>
						</span></td>
					</tr>
					<tr>
						<th scope="row"><label for="mail_from_name"><?php echo __('Sender name', 'feedback'); ?></label></th>
						<td><input type="text" name="mail_from_name" id="mail_from_name" class="regular-text" value="<?php echo $plugin_feedback_mail_from_name; ?>" /></td>
						<td><span class="description">
							<?php echo __('Enter sender name of feedback message. if empty, name from form will be used.', 'feedback'); ?>
						</span></td>
					</tr>
					<tr>
						<th scope="row"><label for="mail_to"><?php echo __('Recipient E-mail', 'feedback'); ?></label></th>
						<td><input type="text" name="mail_to" id="mail_to" class="regular-text" value="<?php echo $plugin_feedback_mail_to; ?>" /></td>
						<td><span class="description">
							<?php echo __('Enter recipient E-mail address of feedback message. Seperate multiple addresses with comma.', 'feedback'); ?>
						</span></td>
					</tr>
					<tr>
						<th scope="row"><label for="success_text"><?php echo __('Success text', 'feedback'); ?></label></th>
						<td colspan="2">
							<textarea name="success_text" id="success_text" rows="7" cols="50" class="large-text"><?php echo $plugin_feedback_success_text; ?></textarea>
							<br />
							<span class="description">
							<?php echo __('Enter success message. Displays when message is sent. You can use HTML tags.', 'feedback'); ?>
						</span></td>
					</tr>
					<tr valign="top"> 
						<th scope="row"><label for="mail_charset"><?php echo __('Letter encoding', 'feedback'); //Кодировка писем ?></label></th> 
						<td><input name="mail_charset" type="text" id="mail_charset" value="<?php echo $plugin_feedback_mail_charset; ?>" class="regular-text" /></td>
						<td><span class="description">
							<?php echo __('Letter <a href="http://codex.wordpress.org/Glossary#Character_set">encoding</a> (UTF-8 NOT RECOMMENDED, <a href="http://en.wikipedia.org/wiki/Character_set">use other</a>)', 'feedback'); //<a href="http://codex.wordpress.org/Glossary#Character_set">Кодировка</a> писем (НЕ РЕКОМЕНДУЕТСЯ UTF-8, <a href="http://en.wikipedia.org/wiki/Character_set">используйте другие</a>) ?>
						</span></td> 
					</tr> 
				</table>
				<p class="submit"><input type="submit" name="submit" class="button-primary" value="<?php echo __('Save', 'feedback'); ?>" /></p>
			</form>
		</div>
		<?php
	}

	function feedback_menu()
	{
		add_options_page(__('Feedback', 'feedback'), __('Feedback', 'feedback'), 1, basename(__FILE__), 'feedback_options');
	}
	add_action('admin_menu', 'feedback_menu');
}

?>