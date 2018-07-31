<div class="wrap">
  <h1>Edit event</h1>
  <div class="event-container-grid edit-event event_status_<?php echo $status ?>">

    <form action="<?php echo $this->get_current_url() ?>"  method="POST" class="event-form edit-event panel event-wrapper">
      <?php wp_nonce_field($this->plugin_slug, 'wpse-nonce'); ?>
      <header class="panel-header">
        <strong>Event details</strong>
      </header><!-- /header -->
      <div class="panel-body">
        <p><label for="event_title"><?php _e('Title', $this->plugin_slug) ?></label>
        <input type="text" name="event[name]" value="<?php echo stripslashes($event['info']['name']) ?>" id="event_title"></p>

        <p><label for="event_info"><?php _e('Information', $this->plugin_slug) ?></label>
          <textarea name="event[info]" id="event_info"><?php echo stripslashes($event['info']['info']) ?></textarea>
          <small>Add any additional information, that should be included in the email. For example an address, materials to bring, or food to prepare</small>
        </p>
        <p>
          <label for="wpse_shortcode"><?php _e('Shortcode', $this->plugin_slug) ?></label>
          <input type="text" readonly value='[wpsevents id="<?php echo $event['info']['id'] ?>"]' id="wpse_shortcode">
          <small>Copy and paste this code in your post to show a table of your upcoming dates for this event.</small>
        </p>
      </div> <!-- .panel-body -->
       <footer class="panel-footer">
        <input type="hidden" name="event[id]" value="<?php echo $event['info']['id'] ?>">
        <input type="hidden" name="event[active]" value="<?php echo $event['info']['active'] ?>">
        
        <p class="flex"><button type="submit" name="wpse-action" value="edit_event" class="button-primary">save</button><?php if(!$status): ?>
          <span>This event is closed</span>
        <?php endif; ?>
        <a href="<?php echo wp_nonce_url($this->get_current_url(array('wpse-action' => 'close_event', 'event_id' => $event['info']['id'])) , $this->plugin_slug, 'wpse-nonce') ?>" class="event_close"><?php echo !$status ? 'open' : 'close'?> event <span class="info" data-tooltip="Close the signup globally for this event">i</span></a></p>
        </footer>
    </form>

    <div class="dates panel event-wrapper">
        
        <strong class="panel-header"><?php _e('Dates', $this->plugin_slug) ?></strong>
        <div class="panel-body">
          <strong>Upcoming dates</strong>
          <ul>
            <?php $dates = $event['dates']['future']; ?>
            <?php foreach($dates as $date): ?>
              <li class="flex">
                <span><?php echo $this->format_date($date['startdate'], $date['enddate']) ?></span> <a href="<?php echo wp_nonce_url($this->get_current_url(array('wpse-action' => 'delete_date','date_id' => $date['id'])), $this->plugin_slug, 'wpse-nonce') ?>" title="Delete date">delete</a>
              </li>
            <?php endforeach; ?>
          </ul>

          <form class="flex" method="POST" action="<?php echo $this->get_current_url() ?>">
            <?php wp_nonce_field($this->plugin_slug, 'wpse-nonce'); ?>

            <p><label for="event_start"><?php _e('Start') ?></label>
            <input type="text" name="date[start]" value="" id="event_start" class="dtpicker" required></p>
            <p><label for="event_end_0"><?php _e('End') ?></label>
            <input type="text" name="date[end]" value="" id="event_end" class="dtpicker" required="required"></p>
            
            <input type="hidden" name="event_id" value="<?php echo $event['info']['id'] ?>">
            <button type="submit" name="wpse-action" value="add_date" class="submit button-secondary">add</button>
          </form>
          <hr>
          <strong>Past dates</strong>
          <ul>
            <?php $dates = $event['dates']['past']; ?>
            <?php foreach($dates as $date): ?>
              <li>
                <?php echo $this->format_date($date['startdate'], $date['enddate']) ?>
              </li>
            <?php endforeach; ?>
          </ul>

        </div><!-- .panel-body -->

      </div> <!-- .dates -->
</div>
</div> <!-- .wrap -->