<?php

namespace ApiBird\Extension;

class Html implements \ApiBird\ExtensionInterface
{

    /**
     * Mime types parsed
     * @var array 
     */
    protected static $types = [
        'text/html',
        'application/xhtml+xml',
    ];

    /**
     * Parse type from format
     * @param string $data
     * @return array
     */
    public function fromFormat($data)
    {
        return array();
    }

    /**
     * Parse type to format
     * @param array $data
     * @return string
     */
    public function toFormat($data)
    {
        $output = '';
        if (count($data) > 0) {
            $output .= '<table><thead><tr><th>';
            $output .= implode('</th><th>', array_keys(current($data)));
            $output .= '</th></tr></thead><tbody>';
            foreach ($data as $row) {
                $output .= '<tr>';
                foreach ($row as $value) {
                    if (!is_array($value)) {
                        $output .= '<td>';
                        $output .= htmlentities($value);
                        $output .= '</td>';
                    } else {
                        $output .= $this->toFormat($value);
                    }
                }
                $output .= '</tr>';
            }
            $output .= '<tbody></table>';
        }
        return $output;
    }

    public static function getTypes()
    {
        return static::$types;
    }

}
