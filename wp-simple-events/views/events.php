<div class="wrap">
  <h1>All Events</h1>
  <div class="event-container-grid">

    <div class="events">
      <?php if(!empty($events)): foreach($events as $event): ?>
        <?php $status = $event['active'] ?>
        <div class="event-wrapper event_status_<?php echo $status ?>">
          <h3><a href="<?php echo $this->get_page_url(array('view' => 'edit', 'event_id' => $event['id'])) ?>"><?php echo stripslashes($event['name']); ?></a> <?php echo !$status ? '<small>event is closed</small>' : '' ?></h3>
          <p><strong>Next date(s):</strong>
            <?php $dates = $this->get_dates($event['id'], 10) ?>
            <?php if(!empty($dates)): foreach($dates as $date): ?>
              <br> <?php echo $this->format_date($date['startdate'], $date['enddate']); ?>
            <?php endforeach; else: ?>
              No upcoming dates
            <?php endif; ?>
          </p>

          <p><strong>Additional infos:</strong> <br><?php echo substr(stripslashes($event['info']), 0, 100) ?>&hellip;</p>

          <p><strong>Shortcode:</strong> [wpsevents id="<?php echo $event['id'] ?>"]</p>

          <a href="<?php echo wp_nonce_url($this->get_page_url(array('wpse-action' => 'delete_event', 'event_id' => $event['id'])), $this->plugin_slug, 'wpse-nonce') ?>" title="delete event" onclick="return confirm('Do you really want to delete this event?')" class="delete-event">&times;</a>

         
        </div> <!-- event-wrapper -->
      <?php endforeach; else: ?>
        <div class="event-wrapper">
          <p>You don't have any events yet. Create your first event <a href="<?php echo $this->get_current_url(array('view' => 'new'))?>">here</a></p>
        </div>
      <?php endif; ?>
    </div> <!-- events -->
    
    <div class="event-wrapper">
      <div class="panel">
        <div class="panel-header">
          <strong>About WP Simple Events</strong>
        </div>
        <div class="panel-body">
          <p>Hi there, thank you for using WP Simple Events.</p>
        </div>
      </div> <!-- event-wrapper -->
    </div>

  </div> <!-- event-container -->
</div>