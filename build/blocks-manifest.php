<?php
// This file is generated. Do not modify it manually.
return array(
	'build' => array(
		'$schema' => 'https://schemas.wp.org/trunk/block.json',
		'apiVersion' => 3,
		'name' => 'telex/block-telex-hours-block',
		'version' => '0.1.0',
		'title' => 'Business Hours Block',
		'category' => 'widgets',
		'icon' => 'clock',
		'description' => 'Displays the current day\'s business hours or a full weekly schedule with customizable seasons and holidays.',
		'example' => array(
			'attributes' => array(
				'displayMode' => 'week',
				'showTodaysDate' => true,
				'showReasonClosed' => true,
				'friendlyTwelves' => true
			)
		),
		'supports' => array(
			'html' => false,
			'interactivity' => true,
			'color' => array(
				'background' => true,
				'text' => true
			),
			'typography' => array(
				'fontSize' => true,
				'lineHeight' => true
			),
			'spacing' => array(
				'margin' => true,
				'padding' => true
			),
			'align' => array(
				'wide',
				'full'
			)
		),
		'attributes' => array(
			'displayMode' => array(
				'type' => 'string',
				'default' => 'week',
				'enum' => array(
					'week',
					'day'
				)
			),
			'showTodaysDate' => array(
				'type' => 'boolean',
				'default' => true
			),
			'showReasonClosed' => array(
				'type' => 'boolean',
				'default' => true
			),
			'friendlyTwelves' => array(
				'type' => 'boolean',
				'default' => true
			),
			'hideWeekends' => array(
				'type' => 'boolean',
				'default' => false
			)
		),
		'textdomain' => 'telex-hours-block',
		'editorScript' => 'file:./index.js',
		'editorStyle' => 'file:./index.css',
		'style' => 'file:./style-index.css',
		'viewScriptModule' => 'file:./view.js',
		'render' => 'file:./render.php'
	)
);
