<?php

/**
 * @link https://www.humhub.org/
 * @copyright Copyright (c) 2015 HumHub GmbH & Co. KG
 * @license https://www.humhub.com/licences
 */

namespace modules\embeddedmedia;

/**
 * EmbeddedMedia Module
 *
 * @since 0.5
 */
class Module extends \humhub\components\Module
{

    /**
     * @inheritdoc
     */
    public $isCoreModule = false;

    /**
     * @var array mime types to show inline instead of download
     */
    public $inlineMimeTypes = [
		'video/webm',
		'audio/webm',
		'video/mp4',
		'audio/mp4',
		'video/ogg',
		'audio/ogg',
		'video/H264'
    ];

}
