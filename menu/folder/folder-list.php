<div class="wrap">
	<h1><?php the_issuu_message('Folders list'); ?></h1>
	<div id="issuu-panel-ajax-result">
		<p></p>
	</div>
	<form action="" method="post" id="delete-folders" accept-charset="utf-8">
		<input type="hidden" name="delete" value="true">
		<button type="submit" class="issuu-submit-button"><?php the_issuu_message('Delete'); ?></button>
		<?php if (isset($folders['totalCount']) && $folders['totalCount'] > $folders['pageSize']) : ?>
			<div id="issuu-painel-pagination">
				<?php for ($i = 1; $i <= $number_pages; $i++) : ?>
					<?php if ($page == $i) : ?>
						<span class="issuu-painel-number-page"><?= $i; ?></span>
					<?php else : ?>
						<a class="issuu-painel-number-page" href="?page=issuu-folder-admin&pn=<?= $i; ?>"><?= $i; ?></a>
					<?php endif; ?>
				<?php endfor; ?>
			</div>
		<?php endif; ?>
		<div class="issuu-folder-content">
			<?php foreach ($folders_documents as $key => $value) : ?>
				<div class="issuu-folder">
					<input type="checkbox" name="folderId[]" class="issuu-checkbox" value="<?= $key; ?>">
					<a href="admin.php?page=issuu-folder-admin&issuu-panel-subpage=update&folder=<?= $key; ?>">
						<?php for ($i = 0; $i < 3; $i++) : ?>
							<?php if (isset($value['documentsId'][$i])) : ?>
								<div class="folder-item folder-item-doc">
									<img src="<?= sprintf($image, $value['documentsId'][$i]->documentId); ?>">
								</div><!-- END folder-item -->
							<?php else: ?>
								<div class="folder-item"></div><!-- END folder-item -->
							<?php endif; ?>
						<?php endfor; ?>
						<div>
							<p>
								<span><?= $value['name']; ?></span>
							</p>
						</div>
					</a>
				</div><!-- END issuu-folder -->
			<?php endforeach; ?>
			<div class="issuu-folder">
				<a href="admin.php?page=issuu-folder-admin&issuu-panel-subpage=add">
					<div class="folder-item"></div><!-- END folder-item -->
					<div class="folder-item"></div><!-- END folder-item -->
					<div class="folder-item"></div><!-- END folder-item -->
					<div>
						<p>
							<span class="add-stack" title="<?php the_issuu_message('Create new folder'); ?>">
							</span><!-- END add-stack -->
						</p>
					</div>
				</a>
			</div><!-- END issuu-folder -->
		</div><!-- END issuu-folder-content -->
	</form>
</div>
<script type="text/javascript">
	(function($){
		$('#delete-folders').submit(function(e){
			e.preventDefault();
			var $form = $(this);
			var $ajaxResult = $('#issuu-panel-ajax-result > p');
			var formData = new FormData($form[0]);
			formData.append('action', 'issuu-panel-delete-folder');
			$('html, body').scrollTop(0);
			$.ajax(ajaxurl, {
				type : "POST",
				data : formData
			}).done(function(data){
				$ajaxResult.html(data.message);

				if (data.folders.length > 0) {
					$.each(data.folders, function(i, item) {
						var folder = $('input[value="' + item + '"]');

						if (folder.length > 0) {
							folder.parents('.issuu-folder').remove();
						}
					});
				}
			}).fail(function(x, y, z){
				console.log(x);
				console.log(y);
				console.log(z);
			});
		});
	})(jQuery);
</script>