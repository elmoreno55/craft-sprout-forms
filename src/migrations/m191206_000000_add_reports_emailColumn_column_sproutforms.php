<?php /** @noinspection ClassConstantCanBeUsedInspection */

namespace barrelstrength\sproutforms\migrations;

use barrelstrength\sproutbaseemail\migrations\m190818_000000_update_to_cc_bcc_columns_to_text_type;
use barrelstrength\sproutbaseemail\migrations\m190818_000001_add_sendMethod_notification_column;
use barrelstrength\sproutbasereports\migrations\m191206_000000_add_reports_emailColumn_column;
use craft\db\Migration;
use yii\base\NotSupportedException;

class m191206_000000_add_reports_emailColumn_column_sproutforms extends Migration
{
    /**
     * @return bool
     * @throws NotSupportedException
     */
    public function safeUp(): bool
    {
        $migration = new m191206_000000_add_reports_emailColumn_column();

        ob_start();
        $migration->safeUp();
        ob_end_clean();

        return true;
    }

    /**
     * @inheritdoc
     */
    public function safeDown(): bool
    {
        echo "m191206_000000_add_reports_emailColumn_column_sproutforms cannot be reverted.\n";
        return false;
    }
}
