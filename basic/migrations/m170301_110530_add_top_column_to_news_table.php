<?php

use yii\db\Migration;

/**
 * Handles adding top to table `news`.
 */
class m170301_110530_add_top_column_to_news_table extends Migration
{
    /**
     * @inheritdoc
     */
    public function up()
    {
        $this->addColumn('news', 'top', $this->boolean());
    }

    /**
     * @inheritdoc
     */
    public function down()
    {
        $this->dropColumn('news', 'top');
    }
}
