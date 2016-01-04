<?php
/**
 * Custom PDF page class
 *
 * @package HCMS
 * @subpackage Pdf
 * @copyright Horisen
 * @author milan
 */
class HCMS_Pdf_Page extends Zend_Pdf_Page {
    
    const TEXT_ALIGN_LEFT = 'left';
    const TEXT_ALIGN_CENTER = 'center';
    const TEXT_ALIGN_RIGHT = 'right';

    /**
     * Word Wrap Text in a box
     * 
     * @param string $text
     * @param int $x1
     * @param int $y1
     * @param int $x2
     * @param string $position
     * @param double $lineHeight
     * @param string $encoding
     * @return Zend_Pdf_Canvas_Interface
     */
    public function drawTextBox($text, $x1, $y1, $x2 = null, $position = self::TEXT_ALIGN_LEFT, $lineHeight = 1.1, $encoding = 'UTF-8') {
        $lines = explode("\n", $text);

        $bottom = $y1;
        $lineHeight = $this->getFontSize() * $lineHeight;
        foreach( $lines as $line ) {
            preg_match_all('/([^\s]*\s*)/i', $line, $matches);

            $words = $matches[1];

            $lineText = '';
            $lineWidth = 0;
            foreach( $words as $word ) {
                $wordWidth = $this->getTextWidth($word, $this);

                if( $lineWidth+$wordWidth < $x2-$x1 ) {
                    $lineText .= $word;
                    $lineWidth += $wordWidth;
                }else {
                    $this->drawTextAligned($lineText, $x1, $bottom, $x2, $position, $encoding );
                    $bottom -= $lineHeight;
                    $lineText = $word;
                    $lineWidth = $wordWidth;
                }
            }

            $this->drawTextAligned($lineText, $x1, $bottom, $x2, $position, $encoding );
            $bottom -= $lineHeight;
        }

        return $this;
    }

    /**
     * Draw aligned text
     * 
     * @param string $text
     * @param int $x1
     * @param int $y1
     * @param int $x2
     * @param string $position
     * @param string $encoding
     * @return Zend_Pdf_Canvas_Interface
     */
    public function drawTextAligned($text, $x1, $y1, $x2 = null, $position = self::TEXT_ALIGN_LEFT, $encoding = 'UTF-8') {

        $bottom = $y1; // could do the same for vertical-centering
        switch ($position) {
            case self::TEXT_ALIGN_LEFT :
                $left = $x1;
                break;
            case self::TEXT_ALIGN_RIGHT :
                if (null === $x2) {
                    throw new Exception ( "Cannot right-align text horizontally, x2 is not provided" );
                }
                $textWidth = $this->getTextWidth ( $text, $this );
                $left = $x2 - $textWidth;
                break;
            case self::TEXT_ALIGN_CENTER :
                if (null === $x2) {
                    throw new Exception ( "Cannot center text horizontally, x2 is not provided" );
                }
                $textWidth = $this->getTextWidth ( $text, $this );
                $left = $x1 + $textWidth / 2;
                break;
            default :
                throw new Exception ( "Invalid position value \"$position\"" );
        }

        // display multi-line text
        $this->drawText ( $text, $left, $y1, $encoding );
        return $this;
    }

    /**
     * Calculate text width
     * 
     * @param string $text
     * @param Zend_Pdf_Page|Zend_Pdf_Resource_Font $resource
     * @param int $fontSize
     * @param string $encoding
     * @return double
     */
    public function getTextWidth($text, $resource, $fontSize = null, $encoding = 'UTF-8') {

        if( $resource instanceof Zend_Pdf_Page ) {
            $font = $resource->getFont();
            $fontSize = $resource->getFontSize();
        }elseif( $resource instanceof Zend_Pdf_Resource_Font ) {
            $font = $resource;
            if( $fontSize === null ) throw new Exception('The fontsize is unknown');
        }

        if( !$font instanceof Zend_Pdf_Resource_Font ) {
            throw new Exception('Invalid resource passed');
        }

        //$drawingText = iconv ( '', $encoding, $text );
        $drawingText = $text;
        $characters = array ();
        for($i = 0; $i < strlen ( $drawingText ); $i ++) {
            $characters [] = ord ( $drawingText [$i] );
        }
        $glyphs = $font->glyphNumbersForCharacters ( $characters );
        $widths = $font->widthsForGlyphs ( $glyphs );
        $textWidth = (array_sum ( $widths ) / $font->getUnitsPerEm ()) * $fontSize;
        return $textWidth;
    }
}
?>