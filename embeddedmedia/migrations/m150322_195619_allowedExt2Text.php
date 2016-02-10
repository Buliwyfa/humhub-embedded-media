<?php

use yii\db\Schema;
use yii\db\Migration;
use humhub\models\Setting;

class m150322_195619_allowedExt2Text extends Migration
{

    public function up()
    {
        $allowedExtensions = Setting::Get('allowedExtensions', 'embeddedmedia');
        if ($allowedExtensions != "") {
            Setting::Set('allowedExtensions', '', 'embeddedmedia');
            Setting::SetText('allowedExtensions', $allowedExtensions, 'embeddedmedia');
        }

        $showFilesWidgetBlacklist = Setting::Get('showFilesWidgetBlacklist', 'embeddedmedia');
        if ($showFilesWidgetBlacklist != "") {
            Setting::Set('showFilesWidgetBlacklist', '', 'embeddedmedia');
            Setting::SetText('showFilesWidgetBlacklist', $showFilesWidgetBlacklist, 'embeddedmedia');
        }
    }

    public function down()
    {
        echo "m150322_195619_allowedExt2Text does not support migration down.\n";
        return false;
    }

    /*
      // Use safeUp/safeDown to do migration with transaction
      public function safeUp()
      {
      }

      public function safeDown()
      {
      }
     */
}
