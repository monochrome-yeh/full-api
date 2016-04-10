<?php

namespace common\components\monochrome\grid;

use Closure;
use yii\base\InvalidConfigException;
use yii\helpers\Html;
use yii\db\BaseActiveRecord;
use Yii;
/**
 * CheckboxColumn displays a column of checkboxes in a grid view.
 *
 * To add a CheckboxColumn to the [[GridView]], add it to the [[GridView::columns|columns]] configuration as follows:
 *
 * ```php
 * 'columns' => [
 *     // ...
 *     [
 *         'class' => 'yii\grid\CheckboxColumn',
 *         // you may configure additional properties here
 *     ],
 * ]
 * ```
 *
 * Users may click on the checkboxes to select rows of the grid. The selected rows may be
 * obtained by calling the following JavaScript code:
 *
 * ```javascript
 * var keys = $('#grid').yiiGridView('getSelectedRows');
 * // keys is an array consisting of the keys associated with the selected rows
 * ```
 *
 * @author Qiang Xue <qiang.xue@gmail.com>
 * @since 2.0
 */
class SelectorColumn extends \yii\grid\CheckboxColumn
{

    public $parentModel;

    public $parentModelAttribute;

    public $tdClass = 'monochromeSelectorTd';

    public $targetElementID;

    public $extendsionInputName;

    public $extendsionInputPlaceholder = '';

    public $extendsionInputLabel = '';

    public $targetTag = '';

    private $removeButtonText;

    private $singleFlag = 'single';

    private $extendstionHtml;

    public $extendsDefaultValue = '';

    public $extendsionInputBeginWrapper = '';

    public $extendsionInputEndWrapper = '';

    public $multiple = false;    






    /**
     * @inheritdoc
     * @throws \yii\base\InvalidConfigException if [[name]] is not set.
     */
    public function init()
    {
        parent::init();

        if ($this->targetElementID == null) {
            Yii::error('請設定目標容器ID(element)', 'common/components/SelectorColumn');
        }

        if ($this->parentModel instanceof BaseActiveRecord && $this->parentModelAttribute != null) {
            //設定這個checkbox/radio td的class
            $this->singleFlag = 'single_' . $this->grid->id;

            $unMultipleMessage = Yii::t('common/app', "This Item Type can not be add for twice.");
            $alreadySelectedMessage = Yii::t('common/app', "This Item already in order.");
            $options = $this->contentOptions;
            $options['class'] = $this->tdClass;
            $this->contentOptions = $options;
            $this->removeButtonText = Yii::t('common/app', 'Remove');

            $parentFormName = strtolower($this->getModelName()). '-form';
            $updateInput = $this->getModelName();

            $randomName = substr(uniqid(), -8);

            $this->grid->getView()->registerJs("
                // var updateTotalBasePrice = function(){
                //     jQuery('input[name=\"{$this->getModelName()}\"]')
                // }

                var checkTableHaveItems{$randomName} = function() {
                    if (jQuery('#{$this->targetElementID} tbody tr').length > 0) {
                        jQuery('#{$this->targetElementID}').show();
                    }
                    else {
                        jQuery('#{$this->targetElementID}').hide();
                    }
                }
                
                checkTableHaveItems{$randomName}();    

                jQuery('#{$this->grid->id} table td.{$this->tdClass} button').on('click', function(e) {
                    var this_id = jQuery(this).attr('data-id');
                    
                    if (jQuery('#{$this->targetElementID} tbody button[data-id=' + this_id + ']').length > 0) {
                       alert('{$alreadySelectedMessage}');
                        e.preventDefault();  
                        return false; 
                    } 
                    else if (jQuery('#{$this->targetElementID} tbody button[data-single]').length > 0) {
                        alert('{$unMultipleMessage}');
                        e.preventDefault();  
                        return false;
                    }                 
                    else {
                        var emptyTr = jQuery('#{$this->targetElementID} tbody div.empty').closest('tr');
                        var tr = jQuery(this).closest('tr').clone().appendTo('#{$this->targetElementID} tbody');

                        //tr.find('td').eq(0).text(jQuery('#{$this->targetElementID} tbody tr').length);

                        tr.find('td.{$this->tdClass} .monochromeExtendsionInput').show();

                        tr.find('td.{$this->tdClass}  button').attr('class', 'pull-right btn btn-danger').html('<span class=\"glyphicon glyphicon-trash\"></span>')
                        .on('click', function(e) {
                             $(this).closest('tr').remove();
                             checkTableHaveItems{$randomName}();

                            if (jQuery('#{$this->targetElementID} tbody tr[data-key]').length == 0) {
                                    emptyTr.show();
                            }
                        });
            
                        if (jQuery('#{$this->targetElementID} tbody tr[data-key]').length > 0) {
                            emptyTr.hide();
                        }

                    }
                    
                    checkTableHaveItems{$randomName}();

                    e.preventDefault();                        
                });               
            ");
        }
        else {

        }  
    }

    protected function getModelName() {
        $reflect = new \ReflectionClass($this->parentModel);
        return $reflect->getShortName();
    }

    protected function getSelectorName() {
       return ".{$this->tdClass} > input";
    }


    /**
     * Renders the header cell content.
     * The default implementation simply renders [[header]].
     * This method may be overridden to customize the rendering of the header cell.
     * @return string the rendering result
     */
    protected function renderHeaderCellContent()
    {
        return '';
    }

    /**
     * @inheritdoc
     */
    protected function renderDataCellContent($model, $key, $index)
    {
        $options = ['class' => 'btn btn-success pull-right', 'type' => 'button', 'data-id' => $model->getId()];
        $pre = '<div class="form-inline">';
        $suff = '</div>';
        if (!$this->multiple) {
            $options['data-single'] = true; 
        }

        if ($this->extendsionInputName != null && is_string($this->extendsionInputName)) {
            $this->extendstionHtml = '
  
    <div style="display:none" class="monochromeExtendsionInput pull-left"><label for="exampleInputName2" class="hidden-sm hidden-xs">'.$this->extendsionInputLabel.'</label>'. $this->extendsionInputBeginWrapper.Html::activeTextInput($this->parentModel, $this->extendsionInputName."[{$model->getId()}]", ['type' => 'number', 'class' => 'form-control', 'value' => $this->extendsDefaultValue, 'placeholder' => $this->extendsionInputPlaceholder,]). $this->extendsionInputEndWrapper . '</div>';
        }


        return $pre.$this->extendstionHtml.Html::button(Yii::t('common/app', 'Add') , $options).Html::activeHiddenInput($this->parentModel, $this->parentModelAttribute.'[]', ['value' => $model->getId(), 'class' => 'pull-right']).$suff;       
    }
}
