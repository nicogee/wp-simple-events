<?php $this->show_errors(); ?>

<div class="event-infos">

	<header>
		<h2><?php echo stripslashes($event['info']['name']); ?></h2>
		<p><?php echo $this->format_date( $event['dates']['startdate'],  $event['dates']['enddate']); ?></p>
	</header>

	<div>
		<strong class="label">Additional Information</strong>
		<p><?php echo stripslashes($event['info']['info']) ?></p>
	</div>

</div> <!-- event-infos -->
<hr>

<form method="post" id="event-form" name="event-form" action="<?php echo $this->get_current_url() ?>">
	<?php wp_nonce_field($this->plugin_slug, 'wpse-do-signup'); ?>

		
		<?php if($settings[$this->plugin_slug.'form']['title'] == 'on'): ?>
		<p>
			<label for="title">Title</label>
			<select name="event[title]" id="title" required="required">
				<option value="">--</option>
				<option value="Mrs" <?php $this->isit('title', 'Mrs') ?>>Mrs</option>
				<option value="Mr" <?php $this->isit('title', 'Mr') ?>>Mr</option>
			</select>
		</p>
		<?php endif; ?>

		<?php if($settings[$this->plugin_slug.'form']['firstname'] == 'on'): ?>
		<p>
			<label for="firstname">First name</label>
			<input type="text" name="event[firstname]" id="firstname" value="<?php $this->isit('firstname') ?>" required="required">
		</p>
		<?php endif; ?>

		<?php if($settings[$this->plugin_slug.'form']['lastname'] == 'on'): ?>
		<p>
			<label for="lastname">Last name</label>
			<input type="text" name="event[lastname]" id="lastname" value="<?php $this->isit('lastname') ?>" required="required" />
		</p>
		<?php endif; ?>

		<?php if($settings[$this->plugin_slug.'form']['email'] == 'on'): ?>
		<p>
			<label for="email">Email</label>
			<input type="text" name="event[email]" id="email" value="<?php $this->isit('email') ?>" required="required" />
		</p>
		<?php endif; ?>

		<?php if($settings[$this->plugin_slug.'form']['terms'] == 'on'): ?>
			<p><input type="checkbox" name="event[terms]" required="required"> I have read and accepted the <a href="<?php echo $settings[$this->plugin_slug.'terms']?>" target="_blank" rel="nofollow, noindex">Terms & Conditions</a>.
			</p>
		<?php endif; ?>
		<p>
			<input type="hidden" name="event[event_id]" value="<?php echo $event['info']['id']; ?>">
			<input type="hidden" name="event[event_date_id]" value="<?php echo $event['dates']['id']; ?>">
			<button class="wpse-button" name="wpse-action" type="submit" value="signup">Signup</button>
		</p>
</form>