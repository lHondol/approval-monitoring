<?php

namespace App\Customs;

use setasign\Fpdi\Fpdi;

class PdfWithRotation extends Fpdi
{
    protected $angle = 0;

    function Rotate($angle, $x = -1, $y = -1)
    {
        if ($x == -1) $x = $this->x;
        if ($y == -1) $y = $this->y;
    
        if ($this->angle != 0) {
            $this->_out('Q');
        }
    
        $this->angle = $angle;
    
        if ($angle != 0) {
            $angleRad = $angle * M_PI / 180;
            $c = cos($angleRad);
            $s = sin($angleRad);
    
            $cx = $x * $this->k;
            $cy = ($this->h - $y) * $this->k;
    
            $this->_out(sprintf(
                "q %.5F %.5F %.5F %.5F %.5F %.5F cm",
                $c, $s, -$s, $c,
                $cx - $c * $cx + $s * $cy,
                $cy - $s * $cx - $c * $cy
            ));
        }
    }
    
    function _endpage()
    {
        if ($this->angle != 0) {
            $this->angle = 0;
            $this->_out('Q');
        }
        parent::_endpage();
    }
}
