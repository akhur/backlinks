<?php

namespace app\models;

use Yii;
use yii\db\Expression;

/**
 * This is the model class for table "backlink".
 *
 * @property int $id
 * @property string|null $referring_page_title
 * @property string|null $referring_page_url
 * @property string|null $language
 * @property string|null $platform
 * @property int|null $referring_page_http_code
 * @property int|null $domain_rating
 * @property int|null $domain_traffic
 * @property int|null $referring_domains
 * @property int|null $linked_domains
 * @property int|null $external_links
 * @property int|null $page_traffic
 * @property int|null $keywords
 * @property string|null $target_url
 * @property string|null $left_context
 * @property string|null $anchor
 * @property string|null $right_context
 * @property string|null $type
 * @property int|null $content
 * @property int|null $nofollow
 * @property int|null $ugs
 * @property int|null $sponsored
 * @property int|null $rendered
 * @property int|null $raw
 * @property string|null $lost_status
 * @property string|null $first_seen
 * @property string|null $last_seen
 * @property string|null $lost
 * @property int|null $links_in_group
 */
class Backlink extends \yii\db\ActiveRecord
{
    public $cnt;
    public $year_month;
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'backlink';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['referring_page_http_code', 'domain_rating', 'domain_traffic', 'referring_domains', 'linked_domains', 'external_links', 'page_traffic', 'keywords', 'links_in_group'], 'integer'],
            [['content', 'nofollow', 'ugs', 'sponsored', 'rendered', 'raw'], 'boolean'],
            [['first_seen', 'last_seen', 'lost'], 'safe'],
            [['referring_page_title', 'referring_page_url', 'language', 'platform', 'target_url', 'left_context', 'anchor', 'right_context', 'type', 'lost_status'], 'string', 'max' => 255],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'referring_page_title' => 'Referring Page Title',
            'referring_page_url' => 'Referring Page Url',
            'language' => 'Language',
            'platform' => 'Platform',
            'referring_page_http_code' => 'Referring Page Http Code',
            'domain_rating' => 'Domain Rating',
            'domain_traffic' => 'Domain Traffic',
            'referring_domains' => 'Referring Domains',
            'linked_domains' => 'Linked Domains',
            'external_links' => 'External Links',
            'page_traffic' => 'Page Traffic',
            'keywords' => 'Keywords',
            'target_url' => 'Target Url',
            'left_context' => 'Left Context',
            'anchor' => 'Anchor',
            'right_context' => 'Right Context',
            'type' => 'Type',
            'content' => 'Content',
            'nofollow' => 'Nofollow',
            'ugs' => 'Ugs',
            'sponsored' => 'Sponsored',
            'rendered' => 'Rendered',
            'raw' => 'Raw',
            'lost_status' => 'Lost Status',
            'first_seen' => 'First Seen',
            'last_seen' => 'Last Seen',
            'lost' => 'Lost',
            'links_in_group' => 'Links In Group',
        ];
    }

    /**
     * @param $untilDate - до какой даты взять данные
     * @return array
     */
    public static function getAllMonths($untilDate = null)
    {
        $query = self::find()->select("DATE_FORMAT(first_seen,'%Y-%m') as `year_month`");
        if ($untilDate) {
            $query->andWhere(['<=', 'first_seen', $untilDate]);
        }
        return $query->orderBy(['first_seen' => SORT_ASC])
            ->groupBy(new Expression("DATE_FORMAT(first_seen,'%Y-%m')"))
            ->column();
    }

    /**
     * @param $nofollow
     * @param $untilDate - до какой даты взять данные
     * @return array
     */
    public static function getCounts($nofollow = false, $untilDate = null)
    {
        $data = [];
        $query = self::find()->select("count(*) as cnt, DATE_FORMAT(first_seen,'%Y-%m') as `year_month`")
            ->where(['nofollow' => $nofollow]);
        if ($untilDate) {
            $query->andWhere(['<=', 'first_seen', $untilDate]);
        }
        $items = $query->orderBy(['first_seen' => SORT_ASC])
            ->groupBy(new Expression("DATE_FORMAT(first_seen,'%Y-%m')"))
            ->all();
        if ($items) {
            foreach ($items as $item) {
                $data[$item['year_month']] = $item['cnt'];
            }
        }
        return $data;
    }

    /**
     * @return array[]
     */
    public static function graphData()
    {
        $untilDate = '2018-03-03'; //можно убрать эту дату, тогда будет по всем записям
        $allMonths = self::getAllMonths($untilDate);
        $followLinks = self::getCounts(false, $untilDate);
        $nofollowLinks = self::getCounts(true, $untilDate);

        $followData = [];
        $noFollowData = [];
        foreach ($allMonths as $month) {
            if (array_key_exists($month, $followLinks)) {
                $followData[] = $followLinks[$month];
            } else {
                $followData[] = 0;
            }
            if (array_key_exists($month, $nofollowLinks)) {
                $noFollowData[] = $nofollowLinks[$month];
            } else {
                $noFollowData[] = 0;
            }
        }

        return [
            'allMonths' => $allMonths,
            'followData' => $followData,
            'noFollowData' => $noFollowData,
        ];
    }
}
