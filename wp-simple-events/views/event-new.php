<div class="wrap">
  <h1>Add Event</h1>

  <form action="<?php echo $this->get_current_url() ?>"  method="POST" class="event-form panel">
    <?php wp_nonce_field($this->plugin_slug, 'wpse-nonce'); ?>
    
    <div class="panel-body">
      <p><label for="event_title"><?php _e('Title') ?></label>
      <input type="text" name="event[name]" value="" id="event_title"></p>

      <p><label for="event_info"><?php _e('Information') ?></label>
        <textarea name="event[info]" id="event_info"></textarea>
        <small>Add any additional information, that should be included in the confirmation email. For example an address, materials to bring, or food to prepare</small>
      </p>

      <div class="flex">
        <p><label for="event_start"><?php _e('Start') ?></label>
        <input type="text" name="date[start]" value="" id="event_start" class="dtpicker"></p>
    
        <p><label for="event_end_0"><?php _e('End') ?></label>
        <input type="text" name="date[end]" value="" id="event_end" class="dtpicker"></p>
      </div>
      <small>You can add additional dates after you created the event</small>
    </div> <!-- .panel-body -->
     <footer class="panel-footer">
        <button type="submit" name="wpse-action" value="add_event" class="button-primary">save</button>  
      </footer>
  </form>
</div>