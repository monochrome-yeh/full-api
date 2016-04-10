<?php
use yii\helpers\Html;

\yii\jui\DatePicker::widget(['class' => 'hidden']);
$t_month = Yii::t('common/app', 'Month');
$t_week = Yii::t('common/app', 'Week');

if (in_array('day' ,$buttons)) {
    $this->registerJs("
        // Select today on click on week number
        $('#today-selector').on('click', function(e){
            $('.dateSelector, .dateSelector2').val($.datepicker.formatDate('yy-mm-dd', new Date()));
        });
    ", $this::POS_END);
}

if (in_array('week', $buttons)) {
    $this->registerJs("
        $( '.week-picker' ).datepicker({
            changeMonth: true,
            changeYear: true,
            showOn: 'button',
            maxDate: '{$max_date}',
            buttonText: '{$t_week}',
            showWeek: true,
            dateFormat: 'yy-mm-dd',
            onSelect: function(dateText, inst) { 
                var date = $(this).datepicker('getDate');
                startDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + {$week_start});
                endDate = new Date(date.getFullYear(), date.getMonth(), date.getDate() - date.getDay() + {$week_start} + 6);
                var dateFormat = inst.settings.dateFormat || $.datepicker._defaults.dateFormat;
                $('.dateSelector').val($.datepicker.formatDate( dateFormat, startDate, inst.settings ));
                $('.dateSelector2').val($.datepicker.formatDate( dateFormat, endDate, inst.settings ));

            },    
        }).next('.ui-datepicker-trigger').addClass('btn btn-info btn-sm');

        // Highlight week on hover week number
        $(document).on('mouseenter','.ui-datepicker-week-col',
                       function(){ $(this).siblings().find('a').addClass('ui-state-hover');} );
        $(document).on('mouseleave','.ui-datepicker-week-col',
                       function(){ $(this).siblings().find('a').removeClass('ui-state-hover');} );

        // Select week on click on week number
        $(document).on('click','.ui-datepicker-week-col',
            function(){
                $(this).siblings().find('a').first().trigger('click');
        });
    ", $this::POS_END);
}

if (in_array('month', $buttons)) {
    $this->registerJs("
        $('.monthButton').datepicker({
            changeMonth: true,
            changeYear: true,
            showOn: 'button',
            buttonText: '{$t_month}',
            maxDate: '{$max_date}',
            dateFormat: 'yy-mm-dd',
            showButtonPanel: true,
            onClose: function(dateText, inst) {
                var dateFormat = inst.settings.dateFormat || $.datepicker._defaults.dateFormat;
                var month = parseInt($('#ui-datepicker-div .ui-datepicker-month :selected').val());
                var year = $('#ui-datepicker-div .ui-datepicker-year :selected').val();
                // $('.dateSelector').datepicker('setDate', new Date(year, month, 1));
                // $('.dateSelector2').datepicker('setDate', new Date(year, month+1, 0));
                $('.dateSelector').val($.datepicker.formatDate( dateFormat, new Date(year, month, 1), inst.settings ));
                $('.dateSelector2').val($.datepicker.formatDate( dateFormat, new Date(year, month+1, 0), inst.settings ));                
            },
            beforeShow: function() {
                setTimeout(function() {
                    $('.ui-datepicker-calendar').hide();
                }, 90)
            },
            beforeShowDay: function(date) {
                return 0;
            },
            onChangeMonthYear: function(date, inst) {
                setTimeout(function() {
                    $('.ui-datepicker-calendar').hide();
                }, 90)
            }
        }).next('.ui-datepicker-trigger').addClass('btn btn-info btn-sm').end();
            $(document).on('mouseenter', '.ui-datepicker-calendar td', function() { $(this).siblings().find('a').not('.ui-state-hover').addClass('ui-state-hover'); });
            $(document).on('mouseleave', '.ui-datepicker-calendar td', function() { $(this).siblings().find('a').removeClass('ui-state-hover'); });
    ", $this::POS_END);
}
?>

<div class="form-group">
    <div class="btn-toolbar">
         <?= Html::input('hidden', 'monochrome_date_search',1 ,['class' => 'hide']) ?>
        <?php if (in_array('month', $buttons)) : ?>
            <?= Html::input('text', 'month','',['class' => 'monthButton hide']) ?>
        <?php endif ?>

        <?php if (in_array('week', $buttons)) : ?>
            <?= Html::input('text', 'month','',['class' => 'week-picker hide']) ?>
        <?php endif ?>

        <?php if (in_array('day', $buttons)) : ?>
            <?= Html::button(Yii::t('common/app', 'Today'), ['class' => 'btn btn-info btn-sm', 'id' => "today-selector"]) ?>
        <?php endif ?>
    </div>
</div>