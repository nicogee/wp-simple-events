<div class="wrap">
  <h1>Settings</h1>
    
    <form action="<?php echo $this->get_current_url() ?>" method="POST" class="event-form" accept-charset="utf-8">
      <?php wp_nonce_field($this->plugin_slug, 'wpse-nonce'); ?>
      <div class="event-container">

        <div class="event-wrapper panel">
          <div class="panel-body">
          <p>
              <label for="wpse_email"><?php _e('Email address for confirmation') ?></label>
              <input type="text" name="settings[wpse_email]" value="<?php echo $settings['wpse_email'] ?>" placeholder="" id="wpse_email">
          </p>
          <p>
              <label for="wpse_email_subject"><?php _e('Email subject') ?></label>
              <input type="text" name="settings[wpse_email_subject]" value="<?php echo $settings['wpse_email_subject'] ?>" placeholder="" id="wpse_email_subject">
          </p>
          <p>
              <label for="wpse_email_body"><?php _e('Email body') ?> <span class="info" data-tooltip="This text will be send in the confirmation email. You should keep it unspecific if you offer more than one event">i</span></label>
              <textarea name="settings[wpse_email_body]" id="wpse_email_body"><?php echo $settings['wpse_email_body'] ?></textarea>
          </p>
          <p>
            <strong>Placeholders</strong> <br>
            %TITLE% - Person title <br>
            %FIRSTNAME% - Person first name <br>
            %LASTNAME% - Person last name <br>
            %EVENT% - These are all the information about the event the person signed up for
          </p>
        </div>
        </div><!-- event-wrapper -->

      <div class="event-wrapper panel">
        <div class="panel-body">
        <p>
          <label for="wpse_message_thank_you"><?php _e('Thank you message') ?> <span class="info" data-tooltip="This message will be shown after sign up is completed">i</span></label>
          <input type="text" name="settings[wpse_message_thank_you]" value="<?php echo $settings['wpse_message_thank_you'] ?>" id="wpse_message_thank_you">
        </p>
        <p>
          <label for="wpse_redirect"><?php _e('Redirect after signup') ?> <span class="info" data-tooltip="You can redirect to a custom URL after signup. This field will overwrite the 'Thank you message' field.">i</span></label>
          <input type="text" name="settings[wpse_redirect]" value="<?php echo $settings['wpse_redirect'] ?>" id="wpse_redirect">
        </p>
        <p>
          <label for="wpse_message_noevent"><?php _e('Info for when there is no upcoming event') ?></label>
          <input type="text" name="settings[wpse_message_noevent]" value="<?php echo $settings['wpse_message_noevent'] ?>" id="wpse_message_noevent">
        </p>
        <p>
          <label for="wpse_message_closed"><?php _e('Info for when registration is closed') ?></label>
          <input type="text" name="settings[wpse_message_closed]" value="<?php echo $settings['wpse_message_closed'] ?>" id="wpse_message_closed">
        </p>
        <p>
          <label for="wpse_terms"><?php _e('Link to your Terms and Conditions') ?> <span class="info" data-tooltip="You have to create a page for your Terms & Conditions. Put the link to this page here.">i</span></label>
          <input type="text" name="settings[wpse_terms]" value="<?php echo $settings['wpse_terms'] ?>" id="wpse_terms">
        </p>
      </div>
      </div><!-- seminrs-wrapper -->

      <div class="event-wrapper panel form-setting">
        <div class="panel-header">
          Signup Form Settings
        </div>
        <div class="panel-body">
          <p>
            <input type="checkbox" name="settings[wpse_form][title]" id="wpse_form_title" <?php echo $this->is_checked($settings['wpse_form']['title']) ?>>
            <label for="wpse_form_title">Title</label>
          </p>
          <p>
            <input type="checkbox" name="settings[wpse_form][firstname]" id="wpse_form_firstname" <?php echo $this->is_checked($settings['wpse_form']['firstname']) ?>>
            <label for="wpse_form_firstname">First name</label>
          </p>
          <p>
            <input type="checkbox" name="settings[wpse_form][lastname]" id="wpse_form_lastname" <?php echo $this->is_checked($settings['wpse_form']['lastname']) ?>>
            <label for="wpse_form_lastname">Last name</label>
          </p>
          <p>
            <input type="checkbox" name="settings[wpse_form][email]" id="wpse_form_email" <?php echo $this->is_checked($settings['wpse_form']['email']) ?>>
            <label for="wpse_form_email">Email</label>
          </p>
          
          <p>
            <input type="checkbox" name="settings[wpse_form][terms]" id="wpse_form_terms" <?php echo $this->is_checked($settings['wpse_form']['terms']) ?>>
            <label for="wpse_form_terms">Must accept Terms & Conditions</label>
          </p>
        </div>
      </div>

      </div> <!-- event-container -->

      <p><button type="submit" class="button-primary" name="wpse-action" value="save_settings"><?php _e('save') ?></button></p>
    </form>

</div>