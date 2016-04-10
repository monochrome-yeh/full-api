<?php
	$this->registerJs("
		$('#{$id} button[type=\"reset\"]').on('click', function(e){
			e.preventDefault();
			$('#{$id} input:checkbox').removeAttr('checked');
			$('#{$id} select').val(0);
			$('#{$id} option:selected').prop('selected', false);
			$('#{$id} input').val(null);
			return false;
		});
	", $this::POS_END);
?>
<div class="form-group">
	<button class="btn btn-primary btn-xs" type="button" data-toggle="collapse" data-target="#<?= $cid ?>" aria-expanded="false" aria-controls="collapseExample">
	  <?= Yii::t('common/app', 'Advance Search')?>
	</button>
</div>	
<div class="collapse" id="<?= $cid ?>">