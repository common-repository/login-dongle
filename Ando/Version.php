<?php 

class Ando_Version
{
	/**
     * Returns -1, 0, +1 respectively if the given $x is <, =, > than 0
     * 
     * @param numeric $x
     * @return integer
     */
    static protected function sign( $x )
    {
        return $x == 0 ? 0 : ($x > 0 ? 1 : -1);
    }
    
    /**
     * Returns -1, 0, +1 respectively if the given version $a is <, =, > than the given version $b
     * 
     * @param string $a
     * @param string $b
     * @return integer
     */
    static protected function deltaSign($a, $b)
    {
        $a = explode('.', $a);
        $b = explode('.', $b);
        $ca = count($a);
        $cb = count($b);
        if ($ca < $cb) $a = array_pad($a, $cb, 0);
        else           $b = array_pad($b, $ca, 0);
        foreach ($a as $index => $value)
        {
            if ($value == $b[$index]) continue; 
            else                      break;
        }
        $result = self::sign($a[$index] - $b[$index]);
        return $result;
    }
    
    static public function compare($comparison)
    {
        if (! preg_match('@((?:\d\.)*\d)\s*(\<|\<=|=|==|\>=|\>)\s*((?:\d\.)*\d)@', trim($comparison), $matches))
        {
            return NULL;
        }
        $sign = self::deltaSign($matches[1], $matches[3]);
        switch ($matches[2])
        {
            case '<':
                $result = -1 == $sign;
            break;
            case '<=':
                $result = -1 == $sign || 0 == $sign;
            break;
            case '=':
            case '==':
                $result = 0 == $sign;
            break;
            case '>':
                $result = +1 == $sign;
            break;
            case '>=':
                $result = +1 == $sign || 0 == $sign;
            break;
        }
        return $result;
    }    
}
