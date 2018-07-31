<?php 
/*
Plugin Name: WP Simple Events
Plugin URI: https://hellowp.de/wp-simple-events
Description: Simple Event Registration Plugin. Create your event, seminar, course, training or meetup and let people sign up.
Author: Nico Graff
Author URI: https://graff.cc
Version: 1.0
License: MIT
*/

define( 'WPSE_PLUGIN_DIR', dirname( __FILE__ ) );

class WPSE {

	public $plugin_slug = 'wpse_';
	public $errors = array();
	public $data;
	public $action;
	public $event_id = null;
	public $event = null;
	public $view = null;
	public $settings = null;
	public $page_url = null;

	function __construct() {

		$this->page_url = admin_url('admin.php?page=wpse-events');

		register_activation_hook(__FILE__, array($this, 'activate'));
		add_action('admin_menu', array($this, 'add_menu'));
		add_action('wp_enqueue_scripts', array($this, 'add_scripts'));

		add_action('init', array($this, 'events_action'));
		add_action('init', array($this, 'do_signup'));

		add_shortcode('wpsevents', array($this, 'events_shortcode'));
		
		if(!empty($_GET['wpse-register'])) {
			add_filter( 'the_title', array($this, 'content_filter_title'));
			add_filter( 'the_content', array($this, 'registration_form'));
		}

	}


	public function add_menu() {

		$events_page = add_menu_page(
		 	__('Events', $this->plugin_slug),
		 	__('Events', $this->plugin_slug),
		 	'manage_options',
		 	'wpse-events',
		 	array($this, 'events')
	 	);

		$add_event_page = add_submenu_page('wpse-events', __('Add Event', $this->plugin_slug).' > '.__('Events', $this->plugin_slug), __('Add Event', $this->plugin_slug), 'manage_options', 'wpse-events?view=new', array($this, 'add_event') );

	 	$settings_page = add_submenu_page('wpse-events', __('Settings', $this->plugin_slug).' > '.__('Events', $this->plugin_slug), __('Settings', $this->plugin_slug), 'manage_options', 'wpse-events?view=settings', array($this, 'events_settings') );

	 	add_action( 'load-' . $events_page, array($this, 'load_admin_scripts') );
	 	add_action( 'load-' . $add_event_page, array($this, 'load_admin_scripts') );
	 	add_action( 'load-' . $settings_page, array($this, 'load_admin_scripts') );

	}

	public function load_admin_scripts(){
      add_action( 'admin_enqueue_scripts', array($this, 'add_admin_scripts') );
   }

	public function add_admin_scripts() {
		
		wp_enqueue_style( $this->plugin_slug .'-admin-styles', plugins_url( 'wpseadmin.css', __FILE__ ), array(), '1' );
		wp_enqueue_style( $this->plugin_slug .'-flatpickr', plugins_url( 'flatpickr.css', __FILE__ ), array(), '1' );
		wp_enqueue_script( $this->plugin_slug .'-flatpickr', plugins_url( 'flatpickr.js', __FILE__ ), array('jquery'), '1' );
	}

	public function add_scripts() {
		wp_enqueue_style( $this->plugin_slug .'-wpsevents', plugins_url( 'wpsevents.css', __FILE__ ), array(), '1' );
	}


	public function events() {

		$this->event_id = !empty($_GET['event_id']) ? $_GET['event_id'] : null;
		$this->view = !empty($_GET['view']) ? $_GET['view'] : null;

		if(is_null($this->view)) {

			$events = $this->get_events();
			$events = $events ? $events : array();
			include WPSE_PLUGIN_DIR.'/views/events.php';

		} else if($this->view == 'edit'){

			$event = $this->get_event($this->event_id);
			$status = $event['info']['active'];

			include WPSE_PLUGIN_DIR.'/views/events-edit.php';

		} else if($this->view == 'new') {

			include WPSE_PLUGIN_DIR.'/views/event-new.php';

		}

	}

	public function add_event() {
		include WPSE_PLUGIN_DIR.'/views/event-new.php';
	}


	public function events_settings() {

		$this->events_action();

		$settings = get_option($this->plugin_slug.'settings', '');
		$settings = !empty($settings) ? $settings : array();

		if(empty($settings)) {
			$settings = array(
				$this->plugin_slug.'email' => get_bloginfo('admin_email'),
				$this->plugin_slug.'email_subject' => 'Thank you for your registration',
				$this->plugin_slug.'email_body' => 'Hello %FIRSTNAME% %LASTNAME%, thank you for signing up to my event. Below are all the details about the event. %EVENT%',
				$this->plugin_slug.'message_thank_you' => 'Thank you for signing up to this event. A confirmation email with details will arrive shortly.',
				$this->plugin_slug.'message_noevent' => 'There is no upcoming event at the moment',
				$this->plugin_slug.'message_closed' => 'Registration for this event is closed',
				$this->plugin_slug.'terms' => '',
				$this->plugin_slug.'form' => array(
					'title' => 'on', 
					'firstname' => 'on',
					'lastname' => 'on', 
					'email' => 'on', 
					'billingaddress' => 'on', 
					'terms' => 'on'
				),
			);
		} 

		include WPSE_PLUGIN_DIR.'/views/settings.php';
		
	}

	public function events_action() {



		if ( !current_user_can('edit_posts') ) {
		    return false;
		}

		global $wpdb;
		
		if(empty($_POST['wpse-action']) && empty($_GET['wpse-action'])) {
			return false;
		}

		$this->action = !empty($_REQUEST['wpse-action']) ? $_REQUEST['wpse-action'] : null;
		$nonce = !empty($_REQUEST[ 'wpse-nonce' ]) ? $_REQUEST[ 'wpse-nonce' ] : null;


		if(!wp_verify_nonce($nonce, $this->plugin_slug)){
			return false;
		}

		if($this->action == 'add_event') {
      

			if($wpdb->replace($wpdb->prefix.$this->plugin_slug.'events', $_POST['event'])) {

				$event_id = $wpdb->insert_id;

				$dates = array(
					'event_id' => $event_id,
					'startdate' => strtotime($_POST['date']['start']),
					'enddate' => strtotime($_POST['date']['end'])
				);

				if($wpdb->insert($wpdb->prefix.$this->plugin_slug.'dates', $dates)) {
					wp_safe_redirect($this->get_page_url(array('view' => 'edit', 'event_id' => $event_id)));
				}
				
			}

			return false;

			
		} //add_event
                
    if($this->action == 'edit_event') {

			if( !empty($_POST['event']['id']) ) {
        $wpdb->replace($wpdb->prefix.$this->plugin_slug.'events', $_POST['event']);
			}

			return false;

		} //save_event

		if($this->action == 'delete_event') {

			if( !empty($_GET['event_id']) ) {
        $wpdb->delete($wpdb->prefix.$this->plugin_slug.'events', array('id' => $_GET['event_id']));
        $wpdb->delete($wpdb->prefix.$this->plugin_slug.'dates', array('event_id' => $_GET['event_id']));
			}

			return true;

		} //delete_event

		if($this->action == 'close_event') {

			if( !empty($_GET['event_id']) ) {
				
				$query = $wpdb->prepare("UPDATE ".$wpdb->prefix.$this->plugin_slug."events SET active = !active WHERE id = %d", $_GET['event_id']);

        $wpdb->query($query);

        $redirect = $this->get_page_url(array('view' => 'edit', 'event_id' => $_GET['event_id']));
        wp_safe_redirect($redirect);
			}

			return true;

		} //delete_event

		if($this->action == 'add_date') {

			if( !empty($_POST['event_id']) && !empty($_POST['date']['start']) && !empty($_POST['date']['end'])) {
				
				$dates = array(
					'event_id' => $_POST['event_id'],
					'startdate' => strtotime($_POST['date']['start']),
					'enddate' => strtotime($_POST['date']['end'])
				);

        $wpdb->insert($wpdb->prefix.$this->plugin_slug.'dates', $dates);
			}

			return false;

		} //add_date


		if($this->action == 'delete_date') {

			if(!empty($_GET['date_id'])) {
        $wpdb->delete($wpdb->prefix.$this->plugin_slug.'dates', array('id' => $_GET['date_id']));
        $redirect = $this->get_page_url(array('view' => 'edit', 'event_id' => $_GET['event_id']));
        wp_safe_redirect($redirect);
        exit;
			}

			return false;

		} //delete_date

		if($this->action == 'save_settings') {

			$settings = $_POST['settings'];
			$form = array(
				'title' => 'off',
				'firstname' => 'off',
				'lastname' => 'off',
				'email' => 'off',
				'terms' => 'off'
			);

			if(!empty($settings[$this->plugin_slug.'form'])) {
				$settings[$this->plugin_slug.'form'] = array_merge($form, $settings[$this->plugin_slug.'form']);
			} else {
				$settings[$this->plugin_slug.'form'] = $form;
			}

			update_option($this->plugin_slug.'settings', $settings);

		} //save_settings
		
	}

	public function get_events() {

		global $wpdb;
		$sql = "SELECT * FROM {$wpdb->prefix}{$this->plugin_slug}events ORDER BY id DESC";
		$query = $wpdb->get_results($sql, ARRAY_A);
		
		return $query;
	}

	public function get_event($event_id = 0, $date_id = null) {
		global $wpdb;

		$sql = sprintf("SELECT * FROM {$wpdb->prefix}{$this->plugin_slug}events WHERE id = '%d'", $event_id);
		$info = $wpdb->get_row($sql, ARRAY_A);

		if(empty($info)) {
			return false;
		}

		if(is_null($date_id)) {

			$dates_future = $this->get_dates($event_id, 100, 'future');
			$dates_past = $this->get_dates($event_id, 100, 'past');

			$result = array(
				'info' => $info,
				'dates' => array(
					'future' => $dates_future,
					'past' => $dates_past
				)
			);

		} else {

			$date = $this->get_dates($event_id, 100, 'future', $date_id);
			$result = array(
				'info' => $info,
				'dates' => $date
			);
		}

		return $result;
	}

	public function get_dates($event_id, $limit = 1, $mode = 'future', $date_id = null) {
		global $wpdb;

		$where_date = !is_null($date_id) ? sprintf(" && id = %d", $date_id) : '';
		$where = $mode == "future" ? "startdate > UNIX_TIMESTAMP()" : "enddate < UNIX_TIMESTAMP()";
		$where = $where . $where_date;

		$sql = sprintf("SELECT * FROM {$wpdb->prefix}{$this->plugin_slug}dates WHERE event_id = '%d' && {$where} ORDER BY startdate ASC LIMIT %d, %d", $event_id, 0, $limit);
	

		$dates = is_null($date_id) ? $wpdb->get_results($sql, ARRAY_A) : $wpdb->get_row($sql, ARRAY_A);

		return $dates;
	}

	public function is_past_event($date) {
		
		if($date < time()) {
			return 'past-event';
		}

		return false;
	}

	public function events_shortcode($attr = '') {

		$option = shortcode_atts( array(
			'id' => null
    ), $attr );

		$eventdata = $this->get_event($option['id']);

		$settings = get_option('wpse_settings');

		ob_start();
		include WPSE_PLUGIN_DIR.'/views/events-table.php';
		return ob_get_clean();
		
	}


	public function content_filter_title($content) {
		$content = 'Signup';
		return $content;
	}

	public function registration_form($content) {
		
		$event_ids = explode('-', $_GET['event_id']);
    $event = $this->get_event( $event_ids[0], $event_ids[1]);

    if(!$event || !$event['info']['active']) {
    	return $content;
    }

    $settings = get_option($this->plugin_slug.'settings');

		ob_start();
		include_once WPSE_PLUGIN_DIR.'/views/events-register.php';
		return ob_get_clean();
		
	}


	public function do_signup() {

		$nonce = !empty($_POST['wpse-do-signup']) ? $_POST['wpse-do-signup'] : null;

		if(is_null($nonce) || !wp_verify_nonce($nonce, $this->plugin_slug)) {
			return false;
		}

		$event_ids = explode('-', $_GET['event_id']);
    $event = $this->event = $this->get_event( $event_ids[0], $event_ids[1]);
    $settings = $this->settings = get_option($this->plugin_slug.'settings');
  
		$fields = array(
			'title' => '',
			'firstname' => '',
			'lastname' => '',
			'email' => '',
			'terms' => ''
		);

		$this->data = $_POST['event'];

		foreach($this->data as $entry) {
		   if(empty($entry)) {
		      $this->errors['empty'] = 'Please fill out all fields.';
		   }
		}

		if(!is_email($this->data['email'])) {
			$this->errors['email'] = 'Please confirm that your email is correct.';
		}

		if(empty($this->errors)) {
			$this->data = array_merge($fields, $this->data);
			$this->finish_signup();
		}

		return false;
		

	}

	private function finish_signup() {

		global $wpdb;

		if(!$this->send_confirmation()){

			$this->errors['senderror'] = 'The email could not be send, please confirm your email address and try again.';

			return false;
		}

		if(empty($this->settings[$this->plugin_slug.'redirect'])) {

			add_filter( 'the_title', array($this, 'content_filter_title'));
			add_filter( 'the_content', function(){
				return $this->settings[$this->plugin_slug.'message_thank_you'];
			});

		} else {
			wp_safe_redirect($this->settings[$this->plugin_slug.'redirect']);
			exit;
		}
		
	}

	private function send_confirmation() {
		
		$data = $this->data;
		$settings = $this->settings;

		$subject = $settings[$this->plugin_slug.'email_subject'];
		$body = $this->generate_email($settings[$this->plugin_slug.'email_body']);
		$mailheader = "MIME-Version: 1.0\n";
		$mailheader .= 'Content-type: text/html; charset=UTF-8' . "\n";

		$mail = wp_mail(array($data['email'], $settings[$this->plugin_slug.'email']), $subject, $body, $mailheader);

		if($mail) {
			return true;
		}

		return false;

	}

	private function generate_email($message) {

		$settings = $this->settings;
		$person = $this->data;
		$event = $this->event;

		$event_formatted = 'Event: '.$event['info']['name'].'<br>';
		$event_formatted .= 'Dates: '. $this->format_date($event['dates']['startdate'], $event['dates']['enddate']).'<br>';
		$event_formatted .= 'Additional Infos: '.$event['info']['info'];

		$placeholder = array('%TITLE%', '%FIRSTNAME%', '%LASTNAME%', '%EMAIL%', '%EVENT%');
		$values = array($person['title'], $person['firstname'], $person['lastname'], $person['email'], $event_formatted);

		$body = str_replace($placeholder, $values, $message);
		return nl2br($body);

	}

	public function format_date($start, $end) {

		if(date('Ymd', $start) == date('Ymd', $end)) {
			$date = date(get_option('date_format').' - '. get_option('time_format'), $start) . ' - ' . date(get_option('time_format'), $end);
		} else {
			$date = date(get_option('date_format').' - '. get_option('time_format'), $start).' - '.date(get_option('date_format') .' - '. get_option('time_format'), $end);
		}

		return $date;
	}

	public function isit($term, $x=null, $default=null) {


		if(!empty($_POST['event'][$term])) {
			
			if(is_null($x)) {
				echo $_POST['event'][$term];
			} else if($_POST['event'][$term] == $x) {
				echo 'selected';
			}

		} else if(!is_null($default)) {
			echo $default;
		}

	}

	public function is_checked($value) {
		if($value == 'on') {
			return 'checked="checked"';
		}
	}

	public function show_errors() {

		if(empty($this->errors)) {
			return false;
		}

		echo '<p>';
		foreach($this->errors as $error) {
			echo '<strong>'.$error.'</strong> <br>';
		}
		echo '</p>';
	}

	public function get_current_url($args = array()) {
		return home_url(add_query_arg($args));
	}

	public function get_page_url($args = array()) {
		return add_query_arg($args, $this->page_url);
	}


	public function activate() {

		global $wpdb;

		$table_events = $wpdb->prefix.$this->plugin_slug.'events';
		$sql_events = "CREATE TABLE $table_events (
			id int(11) NOT NULL AUTO_INCREMENT,
			name varchar(255) NOT NULL,
			info text NOT NULL,
      active tinyint(1) NOT NULL DEFAULT 1,
			PRIMARY KEY  (id)
		);";

		$table_dates = $wpdb->prefix.$this->plugin_slug.'dates';
		$sql_dates = "CREATE TABLE $table_dates (
			id int(11) NOT NULL AUTO_INCREMENT,
			event_id int(11) NOT NULL,
			startdate bigint(12) NOT NULL,
			enddate bigint(12) NOT NULL,
			PRIMARY KEY  (id)
		);";

		
		require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
		dbDelta($sql_events);
		dbDelta($sql_dates);
	}

}

new WPSE();
?>