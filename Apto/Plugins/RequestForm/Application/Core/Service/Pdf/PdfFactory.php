<?php

namespace Apto\Plugins\RequestForm\Application\Core\Service\Pdf;

use Mpdf\Mpdf;
use Mpdf\MpdfException;

class PdfFactory
{
    /**
     * @return Mpdf
     * @throws MpdfException
     */
    public static function create(): Mpdf
    {
        // create pdf and set options
        $mpdf = new Mpdf([
            'default_font' => 'Verdana',
            'margin_top' => 40,
            'margin_bottom' => 30,
        ]);

        $mpdf->shrink_tables_to_fit = 1;

        // get the current page break margin.
        $mpdf->SetAutoPageBreak(true, 30);

        return $mpdf;
    }
}
