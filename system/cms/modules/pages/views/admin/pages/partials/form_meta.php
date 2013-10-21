<div class="form-group">
	<label for="meta_title"><?php echo lang('pages:meta_title_label');?></label>
	<input type="text" class="form-control" id="meta_title" name="meta_title" maxlength="255" value="<?php echo $page->meta_title; ?>" />
</div>
				
<div class="form-group">
	<label for="meta_keywords"><?php echo lang('pages:meta_keywords_label');?></label>
	<input type="text" class="form-control" id="meta_keywords" name="meta_keywords" maxlength="255" value="<?php echo $page->meta_keywords; ?>" />
</div>

<div class="form-group">
	<label for="meta_description"><?php echo lang('pages:meta_desc_label');?></label>
	<?php echo form_textarea(array('name' => 'meta_description', 'value' => $page->meta_description, 'rows' => 5, 'class' => 'form-control')); ?>
</div>