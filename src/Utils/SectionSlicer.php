<?php
namespace Ayeo\Barcode\Utils;

use Ayeo\Barcode\SectionBuilder;

class SectionSlicer
{
    /**
     * @var SectionBuilder
     */
    private $sectionBuilder;

    public function __construct()
    {
        $this->sectionBuilder = new SectionBuilder();
    }

    public function getSections($data)
    {   
        $A1 = substr($data , 0, 0); 
        $A2 = substr($data , 0); 

        $A = $A1 . "(" . $A2;

        $A = substr($A, 0, 4)  . ")" . substr($A, 4);
        $A = substr($A, 0, 18) . "(" . substr($A, 18);
        $A = substr($A, 0, 23) . ")" . substr($A, 23);
        $A = substr($A, 0, 34) . "(" . substr($A, 34);
        $A = substr($A, 0, 37) . ")" . substr($A, 37);
        $A = substr($A, 0, 46) . "(" . substr($A, 46);
        $A = substr($A, 0, 51) . ")" . substr($A, 51);


        $pattern = '#\((\d+)\)((?:[^\(])+)#';
        //$pattern = '/415/';
        preg_match_all($pattern, $A, $matches);

        $zupa = [];
        for ($x = 0; $x < count($matches[0]); $x++) {
            $zupa[] = $matches[1][$x];
            $zupa[] = $matches[2][$x];
        }
        
        $expected = [];
        foreach (array_chunk($zupa, 2) as $sectionData) {
            $expected[] = $this->sectionBuilder->build($sectionData[0], $sectionData[1]);
        }

        return $expected;
    }
}
