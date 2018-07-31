<div id="event-table">

<table>
	<?php if(!is_null($eventdata['info'])):?>
		
		<tr>
			<td colspan="2"><strong><?php echo stripslashes($eventdata['info']['name']) ?></strong></td>
		</tr>
		<?php if(!$eventdata['info']['active']):?>
			<tr>
				<td><?php echo $settings[$this->plugin_slug.'message_closed']?></td>
			</tr>
		<?php else: ?>
			<?php if(!empty($eventdata['dates']['future'])): ?>
			<?php foreach($eventdata['dates']['future'] as $dates): ?>
				<tr>
					<td class="dates">
						<?php echo $this->format_date($dates['startdate'], $dates['enddate']);?>
					</td>
		      <td><a href="<?php echo wp_nonce_url( $this->get_current_url(array('event_id' => $eventdata['info']['id'].'-'.$dates['id'])), $this->plugin_slug, 'wpse-register' ); ?>" class="event-signup" rel="nofollow,noindex">signup &raquo;</a></td>
				</tr>
			<?php endforeach; ?>
			<?php else: ?>
				<tr>
					<td><?php echo $settings[$this->plugin_slug.'message_noevent']?>
				</td>
			<?php endif; ?>

		<?php endif; ?>

	<?php else: ?>
			<tr>
				<td><strong><?php echo $settings[$this->plugin_slug.'message_noevent']?></strong>
			</td>
		</tr>
	<?php endif; ?>
</table>

</div>