<?php

namespace Apto\Plugins\RequestForm\Application\Core\Service\Pdf;

use Mpdf\Mpdf;
use Mpdf\MpdfException;

/*  for saving into media folder do
        $mpdf->Output( $this->mediaDirectory . '/'. substr(bin2hex(random_bytes(5)), 0, 10) . '.pdf', 'F');
    for getting as text do
        $mpdf->Output('', 'S');
*/

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
            'antiAliasing' => true,
            'dpi' => 96,
            'margin_header' => 18,
            /* body margins */
            'margin_top' => 50, // margin in mm from page top
            'margin_bottom' => 0,
            'margin_left' => 20,
            'margin_right' => 20,

            'margin_footer' => 10, // margin in mm from page bottom
        ]);

        $mpdf->shrink_tables_to_fit = 1;

        // get the current page break margin.
        $mpdf->SetAutoPageBreak(true, 40);

        return $mpdf;
    }
}
