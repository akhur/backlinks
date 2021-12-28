<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%backlink}}`.
 */
class m211228_165724_create_backlink_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%backlink}}', [
            'id' => $this->primaryKey(),
            'referring_page_title' => $this->string(),
            'referring_page_url' => $this->string(),
            'language' => $this->string(),
            'platform' => $this->string(),
            'referring_page_http_code' => $this->integer(),
            'domain_rating' => $this->integer(),
            'domain_traffic' => $this->integer(),
            'referring_domains' => $this->integer(),
            'linked_domains' => $this->integer(),
            'external_links' => $this->integer(),
            'page_traffic' => $this->integer(),
            'keywords' => $this->integer(),
            'target_url' => $this->string(),
            'left_context' => $this->string(),
            'anchor' => $this->string(),
            'right_context' => $this->string(),
            'type' => $this->string(),
            'content' => $this->integer(1)->defaultValue(0),
            'nofollow' => $this->integer(1)->defaultValue(0),
            'ugs' => $this->integer(1)->defaultValue(0),
            'sponsored' => $this->integer(1)->defaultValue(0),
            'rendered' => $this->integer(1)->defaultValue(0),
            'raw' => $this->integer(1)->defaultValue(0),
            'lost_status' => $this->string(),
            'first_seen' => $this->dateTime(),
            'last_seen' => $this->dateTime(),
            'lost' => $this->dateTime(),
            'links_in_group' => $this->integer(),
        ]);

        $this->createIndex('idx_nofollow', '{{%backlink}}', 'nofollow');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('{{%backlink}}');
    }
}
