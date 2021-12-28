<?php

namespace app\models;

use app\components\Encode;
use Yii;
use yii\base\Model;
use yii\web\UploadedFile;

class BacklinkUploadForm extends Model
{
    public $file;


    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            [['file'], 'required'],
            [['file'], 'file', 'extensions' => 'csv'],
        ];
    }

    /**
     * @return array
     */
    public function attributeLabels()
    {
        return [
            'file' => 'Файл(csv)',
        ];
    }

    /**
     * @return bool
     */
    public function save()
    {
        $file = UploadedFile::getInstance($this, 'file');
        if ($file) {
            $handle = fopen($file->tempName, "r");
            $delimiter = self::detectDelimiter($file->tempName);
            $i = 0;
            while($line = fgetcsv($handle, 0, $delimiter)) {
                $i++;
                if ($i == 1) continue; //пропускаем название колонок
                if (!isset($line[1])) break;//последняя строка
                foreach ($line as &$column) {
                    $column = trim(Encode::toUtf8($column), '"'); //приводим в порядок кодировку
                    $column = Encode::clearstr($column); //удаляем левые символы
                }
                $model = new Backlink();
                $model->referring_page_title = (string) $line[0];
                $model->referring_page_url = (string) $line[1];
                $model->language = (string) $line[2];
                $model->platform = (string) $line[3];
                $model->referring_page_http_code = (int) $line[4];
                $model->domain_rating = (int) $line[5];
                $model->domain_traffic = (int) $line[6];
                $model->referring_domains = (int) $line[7];
                $model->linked_domains = (int) $line[8];
                $model->external_links = (int) $line[9];
                $model->page_traffic = (int) $line[10];
                $model->keywords = (int) $line[11];
                $model->target_url = (string) $line[12];
                $model->left_context = (string) $line[13];
                $model->anchor = (string) $line[14];
                $model->right_context = (string) $line[15];
                $model->type = (string) $line[16];
                $model->content = $line[17] == 'true';
                $model->nofollow = $line[18] == 'true';
                $model->ugs = $line[19] == 'true';
                $model->sponsored = $line[20] == 'true';
                $model->rendered = $line[21] == 'true';
                $model->raw = $line[22] == 'true';
                $model->lost_status = (string )$line[23];
                $model->first_seen = $line[24] ? date('Y-m-d H:i:s', strtotime($line[24])) : null;
                $model->last_seen = $line[25] ? date('Y-m-d H:i:s', strtotime($line[25])) : null;
                $model->lost = $line[26] ? date('Y-m-d H:i:s', strtotime($line[26])) : null;
                $model->links_in_group = (int) $line[27];
                $model->save();
            }
            return true;
        } else {
            return false;
        }
    }

    /**
     * @param string $csvFile Path to the CSV file
     * @return string Delimiter
     */
    public static function detectDelimiter($csvFile)
    {
        $delimiters = array(
            ';' => 0,
            ',' => 0,
            "\t" => 0,
            "|" => 0
        );

        $handle = fopen($csvFile, "r");
        $firstLine = fgets($handle);
        fclose($handle);
        foreach ($delimiters as $delimiter => &$count) {
            $count = count(str_getcsv($firstLine, $delimiter));
        }

        return array_search(max($delimiters), $delimiters);
    }
}
