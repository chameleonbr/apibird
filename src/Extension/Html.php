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
        $output = "<table cellspacing=\"0\" border=\"2\">\n";
        $output .= $this->show_array($data, 1, 0);
        $output .= "</table>\n";
        return $output;
    }

    public static function getTypes()
    {
        return static::$types;
    }

    function do_offset($level)
    {
        $offset = "";             // offset for subarry 
        for ($i = 1; $i < $level; $i++) {
            $offset = $offset . "<td></td>";
        }
        return $offset;
    }

    function show_array($array, $level, $sub)
    {
        $output = '';
        if (is_array($array) == 1) {          // check if input is an array
            foreach ($array as $key_val => $value) {
                $offset = "";
                if (is_array($value) == 1) {   // array is multidimensional
                    $output .= "<tr>";
                    $offset = $this->do_offset($level);
                    $output .= $offset . "<td>" . $key_val . "</td>";
                    $this->show_array($value, $level + 1, 1);
                } else {                        // (sub)array is not multidim
                    if ($sub != 1) {          // first entry for subarray
                        $output .= "<tr nosub>";
                        $offset = $this->do_offset($level);
                    }
                    $sub = 0;
                    $output .= $offset . "<td main " . $sub . " width=\"120\">" . $key_val .
                            "</td><td width=\"120\">" . $value . "</td>";
                    $output .= "</tr>\n";
                }
            } //foreach $array
        }
        return $output;
    }
}
