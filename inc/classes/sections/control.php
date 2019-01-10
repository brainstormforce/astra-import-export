<span class="customize-control-title">
	<?php _e( 'Export', 'customizer-export-import' ); ?>
</span>
<span class="description customize-control-description">
	<?php _e( 'Click the button below to export the customization settings for this theme.', 'astra-import-export' ); ?>
</span>
<input type="button" class="button" name="astra-export-button" value="<?php esc_attr_e( 'Export', 'astra-import-export' ); ?>" />

<hr class="astra-hr" />

<span class="customize-control-title">
	<?php _e( 'Import', 'astra-import-export' ); ?>
</span>
<span class="description customize-control-description">
	<?php _e( 'Upload a file to import customization settings for this theme.', 'astra-import-export' ); ?>
</span>
<div class="astra-import-controls">
	<input type="file" name="astra-import-file" class="astra-import-file" />
	<label class="astra-import-images">
		<input type="checkbox" name="astra-import-images" value="1" /> <?php _e( 'Download and import image files?', 'astra-import-export' ); ?>
	</label>
	<?php wp_nonce_field( 'astra-importing', 'astra-import' ); ?>
</div>
<div class="astra-uploading"><?php _e( 'Uploading...', 'astra-import-export' ); ?></div>
<input type="button" class="button" name="astra-import-button" value="<?php esc_attr_e( 'Import', 'astra-import-export' ); ?>" />