<?php

namespace mirocow\cdek\widgets;

use yii\helpers\Html;
use yii\bootstrap\Widget;
use yii\jui\JuiAsset;
use yii\web\View;
use yii\widgets\ActiveForm;

class CityField extends Widget
{

    public $callbackUrl = 'http://api.cdek.ru/city/getListByTerm/jsonp.php?callback=?';

    /** @var ActiveForm */
    public $form;
    public $model;
    public $options = [];
    public $callback = '';
    public $attribute = 'city';
    public $hidden_attribute = 'city_id';

    public function init(){
        parent::init();
        JuiAsset::register($this->view);
    }

    public function run()
    {
        $this->callback .= "$('#{$this->hidden_attribute}').val(ui.item.id);\n";

        $this->view->registerJs('
          $("#'.$this->attribute.'").autocomplete({
            source: function(request,response) {
              $.ajax({
                url: "'.$this->callbackUrl.'",
                dataType: "jsonp",
                data: {
                    q: function () { return $("#'.$this->attribute.'").val() },
                    name_startsWith: function () { return $("#'.$this->attribute.'").val() }
                },
                success: function(data) {
                  response($.map(data.geonames, function(item) {
                    return {
                      label: item.name,
                      value: item.cityName,
                      id: item.id,
                      src: item,
                    }
                  }));
                }
              });
            },
            minLength: 1,
            appendTo: "#'.$this->id.'",
            select: function(event,ui) {
                '.$this->callback.'
            }
          });        
        ', View::POS_READY, 'CityField');

        $fields = [];
        $fields[] = $this->form->field($this->model, $this->hidden_attribute)->hiddenInput(['id' => $this->hidden_attribute])->label(false);
        $this->options['maxlength'] = TRUE;
        $this->options['id'] = $this->attribute;
        $fields[] = $this->form->field($this->model, $this->attribute)->textInput($this->options);
        $content = implode(PHP_EOL, $fields);
        return Html::tag('div', $content, ['id' => $this->id]);
    }
}