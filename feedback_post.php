<?

session_start();

error_reporting(E_ALL ^ E_DEPRECATED);
require_once('../../../wp-config.php');

require_once(ABSPATH . 'wp-admin/includes/plugin.php');
if (!is_plugin_active(dirname(plugin_basename(__FILE__)).'/feedback.php')) die('PLUGIN DISABLED');

header('Content-type: text/plain;charset=utf-8');

load_plugin_textdomain('feedback', 'wp-content/plugins/'.basename(dirname(plugin_basename(__FILE__))), dirname(plugin_basename(__FILE__)));

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

$plugin_feedback_send_label = get_option('_plugin_feedback_send_label');
if (empty($plugin_feedback_send_label)) $plugin_feedback_send_label = __('Send', 'feedback');

$plugin_feedback_mail_from_name = get_option('_plugin_feedback_mail_from_name');

$plugin_feedback_mail_from_email = get_option('_plugin_feedback_mail_from_email');
if (empty($plugin_feedback_mail_from_email))
{
	if (!empty($_POST['email'])) $plugin_feedback_mail_from_email = $_POST['email'];
	else $plugin_feedback_mail_from_email = get_option('admin_email');
}

$plugin_feedback_mail_to = get_option('_plugin_feedback_mail_to');
if (empty($plugin_feedback_mail_to)) $plugin_feedback_mail_to = get_option('admin_email');

$plugin_feedback_success_text = get_option('_plugin_feedback_success_text');
if (empty($plugin_feedback_success_text)) $plugin_feedback_success_text = __('<p>Thank you! Your message was send successfully!</p><p>We\'ll contact you as soon as possible.</p><p align="right">- Administration</p>', 'feedback');

$plugin_feedback_mail_charset = get_option('_plugin_feedback_mail_charset');
if (empty($plugin_feedback_mail_charset)) $plugin_feedback_mail_charset = 'windows-1251';


foreach ($_POST as $k=>$v) $_POST[$k] = stripslashes($v);

$please_enter = __('Please, enter', 'feedback');
if (empty($_POST['name']) && $plugin_feedback_name_required) die("{$please_enter} {$plugin_feedback_name_label}!");
if (empty($_POST['email']) && $plugin_feedback_email_required) die("{$please_enter} {$plugin_feedback_email_label}!");
if (empty($_POST['phone']) && $plugin_feedback_phone_required) die("{$please_enter} {$plugin_feedback_phone_label}!");
if (empty($_POST['message']) && $plugin_feedback_message_required) die("{$please_enter} {$plugin_feedback_message_label}!");
if (empty($_POST['captcha']) && $plugin_feedback_captcha_required) die("{$please_enter} {$plugin_feedback_captcha_label}!");

if (!empty($_POST['email']) && !preg_match("/^[a-zA-Z0-9\-_.]{2,}@[a-zA-Z0-9\-_.]{2,}\.[a-zA-Z0-9\-_.]{2,}$/", $_POST['email'])) die(__('E-mail address is invalid!', 'feedback'));

if ($plugin_feedback_captcha_required && $_SESSION['feedback_captcha_keystring'] != $_POST['captcha']) die(__('Secret code is invalid!', 'feedback'));

session_destroy();

$__PHPMAILER = 'phpmailer5';
if (version_compare(PHP_VERSION, '5.0.0', '<')) $__PHPMAILER = 'phpmailer4';

require_once(realpath(dirname(__FILE__))."/{$__PHPMAILER}/class.phpmailer.php");
$Mailer = new PHPMailer();
$Mailer->CharSet = $plugin_feedback_mail_charset;
$Mailer->SetLanguage('ru', realpath(dirname(__FILE__))."/{$__PHPMAILER}/language/");
$Mailer->ContentType = 'text/plain';
$Mailer->Sender = $plugin_feedback_mail_from_email;
$Mailer->From = !empty($_POST['email']) ? $_POST['email'] : $plugin_feedback_mail_from_email;

$feedback = __('Feedback', 'feedback');
if (!empty($_POST['name'])) $Mailer->FromName = iconv(get_bloginfo('charset'), $plugin_feedback_mail_charset, $_POST['name']);
elseif (!empty($plugin_feedback_mail_from_name)) $Mailer->FromName = iconv(get_bloginfo('charset'), $plugin_feedback_mail_charset, $plugin_feedback_mail_from_name);
else $Mailer->FromName = iconv(get_bloginfo('charset'), $plugin_feedback_mail_charset, $feedback);

$message = '';
$message .= "{$plugin_feedback_name_label}: {$_POST['name']}\n";
$message .= "{$plugin_feedback_email_label}: {$_POST['email']}\n";
$message .= "{$plugin_feedback_phone_label}: {$_POST['phone']}\n";
$message .= "{$plugin_feedback_message_label}: {$_POST['message']}\n";

$Mailer->Subject = iconv(get_bloginfo('charset'), $plugin_feedback_mail_charset, "Wordpress, {$feedback}").
	(!empty($_POST['message']) ? ' '.substr(iconv(get_bloginfo('charset'), $plugin_feedback_mail_charset, $_POST['message']), 0, 100).'...' : '');
$Mailer->Body = iconv(get_bloginfo('charset'), $plugin_feedback_mail_charset, $message);

$plugin_feedback_mail_to = preg_split('/\s*,\s*/', $plugin_feedback_mail_to);
foreach ($plugin_feedback_mail_to as $k=>$v)
{
	if (!empty($v))
	{
		$Mailer->ClearAllRecipients();
		$Mailer->AddAddress($v);
		$Mailer->Send();
	}
}


die($plugin_feedback_success_text);

?>