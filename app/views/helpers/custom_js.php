<?php

/**
 * @copyright ICZ Corporation (http://www.icz.co.jp/)
 * @license See the LICENCE file
 * @author <matcha@icz.co.jp>
 * @version $Id$
 */
class CustomJsHelper extends JsHelper
{

	var $helpers = array(
		'Form',
		'Js',
		'Html'
	);

	function link($title, $url = null, $options = array())
	{
		if (! isset($options['id'])) {
			$options['id'] = 'link-' . intval(mt_rand());
		}
		list ($options, $htmlOptions) = $this->_getHtmlOptions($options);
		$out = $this->Html->link($title, $url, $htmlOptions);
		$this->get('#' . $htmlOptions['id']);
		$requestString = $event = '';
		if (isset($options['confirm'])) {
			$requestString = $this->confirmReturn($options['confirm']);
			unset($options['confirm']);
		}
		$buffer = isset($options['buffer']) ? $options['buffer'] : null;
		$safe = isset($options['safe']) ? $options['safe'] : true;
		unset($options['buffer'], $options['safe']);
		
		$requestString .= $this->request($url, $options);
		
		if (! empty($requestString)) {
			$event = $this->event('click', $requestString, $options + array(
				'buffer' => $buffer
			));
		}
		if (isset($buffer) && ! $buffer) {
			$opts = array(
				'safe' => $safe
			);
			$out .= $this->Html->scriptBlock($event, $opts);
		}
		
		return str_replace('type:"POST",', 'type:"POST", data:"token=' . session_id() . '",', $out);
	}

	function submitAfterConfirm($caption = null, $options = array(), $confirm = array(), $ajax = array())
	{
		if (! isset($options['id'])) {
			$options['id'] = 'submit-' . intval(mt_rand());
		}
		if (! isset($confirm['description'])) {
			$confirm['description'] = "''";
		}
		if (! isset($confirm['type'])) {
			$confirm['type'] = '"alert"';
		}
		if (! isset($confirm['close'])) {
			$confirm['close'] = true;
		}
		
		$confirm['yesAction'] = 'function() {
			' . (isset($ajax['before']) ? 'var before = ' . $ajax['before'] . ';before();' : '') . '
			$.post(' . "'" . Router::url($ajax['url']) . "'," . '$("#' . $options['id'] . '").closest("form").serialize()' . (isset($ajax['complete']) ? ', ' . $ajax['complete'] : '') . ')}';
		
		if (! isset($confirm['noAction'])) {
			$confirm['noAction'] = 'null';
		}
		
		$options['onclick'] = 'popupclass.confirm_open(' . "'" . $confirm['description'] . "'," . "'" . $confirm['type'] . "'," . $confirm['yesAction'] . ',' . $confirm['noAction'] . ',' . ($confirm['close'] ? 'true' : 'false') . ');return false;';
		
		return $this->Form->submit($caption, $options);
	}

	function linkAfterConfirm($title, $url = null, $options = array(), $confirm = array(), $ajax = array())
	{
		if (! isset($options['id'])) {
			$options['id'] = 'link-' . intval(mt_rand());
		}
		if (! isset($confirm['description'])) {
			$confirm['description'] = '';
		}
		if (! isset($confirm['type'])) {
			$confirm['type'] = 'alert';
		}
		if (! isset($confirm['close'])) {
			$confirm['close'] = true;
		}
		
		$confirm['yesAction'] = 'function() {
			' . (isset($ajax['before']) ? 'var before = ' . $ajax['before'] . ';before();' : '') . '
			$.post(' . "'" . Router::url($url) . "'," . "{'token' : '" . session_id() . "'}" . (isset($ajax['complete']) ? ', ' . $ajax['complete'] : '') . ');}';
		
		if (! isset($confirm['noAction'])) {
			$confirm['noAction'] = 'null';
		}
		
		$options['onclick'] = 'popupclass.confirm_open(' . "'" . h($confirm['description']) . "'," . "'" . $confirm['type'] . "'," . $confirm['yesAction'] . ',' . $confirm['noAction'] . ',' . ($confirm['close'] ? 'true' : 'false') . ');';
		
		return $this->Html->link($title, 'javascript:void(0);', $options);
	}
}
