<?php

/**
 * EmbeddedEvents is responsible to handle events defined by autostart.php
 *
 * @author David Findlay <davidjwfindlay@gmail.com>
 */
class EmbeddedMediaEvents
{

    /**
     * On build of the TopMenu
     *
     * @param CEvent $event
     */
    public static function onTopMenuInit($event)
    {
        $event->sender->addItem(array(
            'label' => Yii::t('EmbeddedMediaModule.base', 'Embedded Media Item'),
            'url' => Yii::app()->createUrl('/embeddedmedia/main/index', array()),
            'icon' => '<i class="fa fa-sun-o"></i>',
            'isActive' => (Yii::app()->controller->module && Yii::app()->controller->module->id == 'embeddedmedia'),
        ));
    }

}
