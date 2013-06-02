<table class="bt_languages" width="100%">
	<thead>
		<tr>
			<th><?php echo lang('bt_language'); ?></th>
			<th class="bt_cell_dir"><?php echo lang('bt_direction'); ?></th>
			<th class="bt_cell_req"><?php echo lang('bt_required'); ?></th>
			<th class="bt_cell_del"><?php echo lang('bt_remove'); ?></th>
		</tr>
	</thead>
	<tbody>
		<?php foreach($languages as $key => $value): ?>
		<tr data-language_key="<?php echo $key; ?>" data-language_name="<?php echo $value['name']; ?>">
			<td><?php echo $value['name']; ?></td>
			<td class="bt_cell_dir"><?php echo $value['dir_radios']; ?></td>
			<td class="bt_cell_req"><?php echo $value['req_checkbox']; ?></td>
			<td class="bt_cell_del"><a href="#" class="bt_remove"><?php echo lang('bt_delete'); ?></a></td>
		</tr>
		<?php endforeach; ?>
	</tbody>
	<tfoot>
		<tr>
			<td colspan="4">
				<?php echo $lang_dropdown; ?>
				<input type="button" name="bt_lang_add" value="<?php echo lang('bt_add'); ?>" />
				<?php echo $lang_keys_hidden; ?>
				<?php echo $lang_names_hidden; ?>
			</td>
		</tr>
	</tfoot>
</table>