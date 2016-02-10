<?php

use humhub\modules\content\widgets\WallEntryAddons;
use humhub\modules\space\widgets\Menu;
use humhub\commands\CronController;
use humhub\commands\IntegrityController;
use modules\embeddedmedia\Events;
use humhub\modules\user\models\User;
use humhub\components\ActiveRecord;

return [
    'id' => 'embeddedmedia',
    'class' => 'modules\embeddedmedia\Module',
    'namespace' => 'modules\embeddedmedia',
    'isCoreModule' => false,
    'events' => array(
	    array('class' => Menu::className(), 'event' => Menu::EVENT_INIT, 'callback' => array('modules\embeddedmedia\Events', 'onSpaceMenuInit')),
        array('class' => WallEntryAddons::className(), 'event' => WallEntryAddons::EVENT_INIT, 'callback' => array('modules\embeddedmedia\Events', 'onWallEntryAddonInit')),
        array('class' => CronController::className(), 'event' => CronController::EVENT_ON_DAILY_RUN, 'callback' => array('modules\embeddedmedia\Events', 'onCronDailyRun')),
        array('class' => IntegrityController::className(), 'event' => IntegrityController::EVENT_ON_RUN, 'callback' => array('modules\embeddedmedia\Events', 'onIntegrityCheck')),
        array('class' => ActiveRecord::className(), 'event' => \humhub\components\ActiveRecord::EVENT_BEFORE_DELETE, 'callback' => array('modules\embeddedmedia\Events', 'onBeforeActiveRecordDelete')),
        array('class' => User::className(), 'event' => User::EVENT_BEFORE_DELETE, 'callback' => array('modules\embeddedmedia\Events', 'onUserDelete')),
    ),
];
?>

    ),
];
?>