<?php 
/**
 * @var $block array
 * @var $this SprintEditorBlocksComponent
 */
?>

<div class="news-wrapper reverse">
	<div class="image">
		<?php $this->includeBlock($block['image']);?>
	</div>
	<div class="text">
		<?php $this->includeBlock($block['text']);?>
	</div>
</div>