<?php

Yii::app()->moduleManager->register(array(
    'id' => 'embeddedmedia',
    'class' => 'application.modules.embeddedmedia.EmbeddedMediaModule',
    'import' => array(
        'application.modules.embeddedmedia.*',
    ),
    'events' => array(
//        array('class' => 'TopMenuWidget', 'event' => 'onInit', 'callback' => array('EmbeddedMediaEvents', 'onTopMenuInit')),
    ),
));
?>