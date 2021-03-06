<?php

namespace li3_themes\template\helper;


class Html extends \lithium\template\helper\Html {

    protected $_theme = 'default';

    public function _init() {
        parent::_init();
        $this->_theme = isset($this->_context->_config['theme']) ? $this->_context->_config['theme'] : $this->_theme;
    }

    /**
     * Override `style` method in order to build a correct
     * path to styles of the current theme
     *
     * @see \lithium\template\helper\Html::style
     *
     * @param mixed $path
     * @param array $options
     * @return mixed|null|string
     */
    public function style($path, array $options = array()) {
        $defaults = array('type' => 'stylesheet', 'inline' => true);
        list($scope, $options) = $this->_options($defaults, $options);

        if (is_array($path)) {
            foreach ($path as $i => $item) {
                $path[$i] = $this->style($item, $scope);
            }
            return ($scope['inline']) ? join("\n\t", $path) . "\n" : null;
        }
        $method = __METHOD__;
        $type = $scope['type'];
        $params = compact('type', 'path', 'options');
        $params['theme'] = array('theme' => $this->_theme);
        $filter = function ($self, $params, $chain) use ($defaults, $method) {
            $template = ($params['type'] === 'import') ? 'style-import' : 'style-link';
            return $self->invokeMethod('_render', array($method, $template, $params, $params['theme']));
        };
        $style = $this->_filter($method, $params, $filter);

        if ($scope['inline']) {
            return $style;
        }
        if ($this->_context) {
            $this->_context->styles($style);
        }
    }

    /**
     * Override `script` method in order to build a correct
     * path to scripts of the current theme
     *
     * @see \lithium\template\helper\Html::script
     *
     * @param mixed $path
     * @param array $options
     * @return mixed|null|string
     */
    public function script($path, array $options = array())
    {
        $defaults = array('inline' => true);
        list($scope, $options) = $this->_options($defaults, $options);

        if (is_array($path)) {
            foreach ($path as $i => $item) {
                $path[$i] = $this->script($item, $scope);
            }
            return ($scope['inline']) ? join("\n\t", $path) . "\n" : null;
        }
        $m = __METHOD__;
        $params = compact('path', 'options');
        
        $params['theme'] = array('theme' => $this->_theme);

        $script = $this->_filter(__METHOD__, $params, function ($self, $params, $chain) use ($m) {
            return $self->invokeMethod('_render', array($m, 'script', $params, $params['theme']));
        });
        if ($scope['inline']) {
            return $script;
        }
        if ($this->_context) {
            $this->_context->scripts($script);
        }
    }


} 