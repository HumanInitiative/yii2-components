<?php

namespace pkpudev\components\helpers;

use yii\base\BaseObject;
use yii\web\JsExpression;

/**
 * Using options for component kartik\select2\Select2
 *
 * Coding sample:
 *
 * ```php
 * $form->field($model, 'project_id')->widget(Select2::classname(), [
 *   'options'=>compact('placeholder'),
 *   'pluginOptions'=>Select2Options::toArray(ArrayHelper::merge($options, [
 *     'url'=>Url::to(['api/project'], 'https'),
 *     'placeholder'=>$placeholder,
 *     'codeField'=>'project_no',
 *     'idValue'=>$model->project_id,
 *   ])),
 * ]);
 * ```
 */
class Select2Options extends BaseObject
{
    /**
     * @var bool $allowClear
     */
    public $allowClear;
    /**
     * @var array $ajax
     */
    public $ajax;
    /**
     * @var integer $minimumInputLength
     */
    public $minimumInputLength;
    /**
     * @var JsExpression $templateResult
     */
    public $templateResult;
    /**
     * @var JsExpression $templateSelection
     */
    public $templateSelection;
    /**
     * @var JsExpression $escapeMarkup
     */
    public $escapeMarkup;
    /**
     * @var JsExpression $initSelection
     */
    public $initSelection;
    /**
     * @var array $config
     */
    protected $config;

    /**
     * Constructing Object Options
     */
    public function __construct($config=[])
    {
        parent::__construct([]);

        foreach (['url', 'codeField'] as $field) {
            if (is_null($config[$field])) {
                throw new \yii\base\InvalidConfigException(ucfirst($field)." config for select2options not found!");
            }
        }

        $codeField = array_key_exists('codeField', $config) ? $config['codeField'] : 'code';
        $textField = array_key_exists('textField', $config) ? $config['textField'] : 'text';
        $codeValue = array_key_exists('codeValue', $config) ? $config['codeValue'] : '';
        // Sizzle bugfix
        $textValue = array_key_exists('textValue', $config) ? str_replace(['(', ')', '.', '&', "'", '/', '"', '_'], '', $config['textValue']) : '';
        $placeholder = array_key_exists('placeholder', $config) ? $config['placeholder'] : '';

        $callbackOptions = ($idValue = $config['idValue']) ?
            "{id:$idValue, $codeField:'$codeValue', text:'$textValue'}" :
            "{id:0, $codeField:'---', text:' $placeholder ---'}";

        /* allowClear */
        $this->allowClear = array_key_exists('allowClear', $config) ? $config['allowClear'] : true;
        /* ajax */
        $this->ajax = array_key_exists('ajax', $config) ? $config['ajax'] : [
            'url'            => array_key_exists('url', $config) ? $config['url'] : '',
            'delay'          => array_key_exists('ajax.delay', $config) ? $config['ajax.delay'] : 250,
            'dataType'       => array_key_exists('ajax.dataType', $config) ? $config['ajax.dataType'] : 'json',
            'data'           => array_key_exists('ajax.data', $config) ? $config['ajax.data'] : new JsExpression('function(params) { return {q:params.term} }'),
            'processResults' => array_key_exists('ajax.result', $config) ? $config['ajax.result'] : new JsExpression('function(data) {
                var results = [];
                $.each(data, function (index, item) {
                    results.push({
                        id: item.id,
                        text: "<div><strong>" + item.'.$codeField.' + "</strong> &mdash; " + item.'.$textField.' + "</div>"
                    })
                })
                return { results: results }
            }'),
        ];
        /* minimumInputLength */
        $this->minimumInputLength = array_key_exists('minLength', $config) ? $config['minLength'] : 3;
        /* templateResult */
        $this->templateResult = array_key_exists('templateResult', $config) ? $config['templateResult'] : new JsExpression("function(p) {
            if (p.id) {
                return p.text
            }
            return jQuery('' + p.text + '')
        }");
        /* templateSelection */
        $this->templateSelection = array_key_exists('templateSelection', $config) ? $config['templateSelection'] : new JsExpression("function(p) {
            if (p.text) {
                jQuery('' + p.text + '')
            }
            return p.text
        }");
        /* escapeMarkup */
        $this->escapeMarkup = array_key_exists('escapeMarkup', $config) ? $config['escapeMarkup'] : new JsExpression("function (markup) { return markup }");
        /* initSelection */
        $this->initSelection = array_key_exists('initSelection', $config) ? $config['initSelection'] : new JsExpression("function (element, callback) {
            callback({$callbackOptions})
        }");
    }

    /**
     * Transform from object properties to array
     */
    public static function toArray($config=[])
    {
        $object = new static($config);

        $reflector = new \ReflectionClass($object);
        $properties = $reflector->getProperties(\ReflectionProperty::IS_PUBLIC);

        $options = [];
        foreach ($properties as $property) {
            $prop = $property->getName();
            $options[$prop] = $object->$prop;
        }
        return $options;
    }
}
