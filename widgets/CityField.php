<?php

namespace mirocow\cdek\widgets;

use yii\helpers\Html;
use yii\bootstrap\Widget;
use yii\web\View;
use yii\widgets\ActiveForm;

class CityField extends Widget
{

    public $callbackUrl = 'http://api.cdek.ru/city/getListByTerm/jsonp.php?callback=?';

    /** @var ActiveForm */
    public $form;
    public $model;
    public $attribute = 'city';
    public $hidden_attribute = 'cityId';

    public function run()
    {
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
                      value: item.name,
                      id: item.id
                    }
                  }));
                }
              });
            },
            minLength: 1,
            select: function(event,ui) {
                $(\'#'.$this->hidden_attribute.'\').val(ui.item.id);
            }
          });        
        ', View::POS_END, 'ArrayField');

        $fields = [];
        $fields[] = $this->form->field($this->model, $this->hidden_attribute)->hiddenInput(['id' => $this->hidden_attribute]);
        $fields[] = $this->form->field($this->model, $this->attribute)->textInput(['maxlength' => TRUE, 'id' => $this->attribute]);
        return implode(PHP_EOL, $fields);
    }
}