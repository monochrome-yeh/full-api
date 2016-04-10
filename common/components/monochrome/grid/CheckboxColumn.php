<?php
namespace common\components\monochrome\grid;

use yii\grid\CheckboxColumn as BaseCheckboxColumn;
use yii\web\View;

class CheckboxColumn extends BaseCheckboxColumn {

	public function init() {
		parent::init();
		$this->contentOptions = [
			'class' => 'mono_selected',	
		];


		$this->grid->getView()->registerCss("
			.table > tbody > tr.selected {
				background-color: #FFC;
			}
		");

		//registerJs
		$this->grid->getView()->registerJs("
			$('.mono_selected input').on('ifChanged', function(e){
				if (this.checked) {
					$(this).closest('tr').addClass('selected');
				}
				else {
					$(this).closest('tr').removeClass('selected');
				}
			});

			$('.mono_selected input').on('change', function(e){
				if (this.checked) {
					$(this).closest('tr').addClass('selected');
				}
				else {
					$(this).closest('tr').removeClass('selected');
				}
			});		
		", View::POS_END);		
	}

}