<?php

namespace ApiBird\Extension;

class Csv implements \ApiBird\ExtensionInterface
{
    protected static $types = [
        'text/csv'
    ];
    protected $options = ['delimiter' => ',', 'enclosure' => '"'];

    public function fromFormat($data)
    {
        $rows = explode('\n', $data);
        $i = 0;
        $keys = [];
        $out = [];
        if (count($rows) > 1) {
            foreach ($rows as $row) {
                if ($i == 0) {
                    $keys = str_getcsv($row, $this->options['delimiter'], $this->options['enclosure']);
                } else {
                    $val = str_getcsv($row, $this->options['delimiter'], $this->options['enclosure']);
                    $out[$i - 1] = array_combine($keys, $val);
                }
                $i++;
            }
        }
        return $out;
    }

    public function toFormat($data)
    {
        $limit = count($data);
        if ($limit >= 1) {
            $fields = array_keys($data[0]);
            if (!empty($fields)) {
                $fp = fopen('php://output', 'w');
                fputcsv($fp, $fields, $this->options['delimiter'], $this->options['enclosure']);
                for ($i = 0; $i < $limit; $i++) {
                    fputcsv($fp, $data[$i], $this->options['delimiter'], $this->options['enclosure']);
                }
                fclose($fp);
            } else {
                throw new \ApiBird\Error\InternalServerErrorException();
            }
        }
        return '';
    }

    public static function getTypes()
    {
        return static::$types;
    }

}
