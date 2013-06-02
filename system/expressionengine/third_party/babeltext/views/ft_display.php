<div class="bt_tabs">
	<ul class="bt_tabnav">
		<?php foreach($fields as $key => $value): ?>
			<li>
				<a href="<?php echo '#bt_tab_' . $key ; ?>">
					<?php echo $value['name']; ?>
					<?php if($value['required']): ?>
						<em class="required">*</em>
					<?php endif; ?>
				</a>
			</li>
		<?php endforeach; ?>
	</ul>
	<?php foreach($fields as $key => $value): ?>
		<div class="bt_tab" id="<?php echo 'bt_tab_' . $key; ?>">
			<?php echo $value['field']; ?>
		</div>
	<?php endforeach; ?>
	<?php echo $placeholder_field; ?>
</div>