<?php
class BarcodeGenerator {
    private $code;
    private $height;
    private $scale;
    
    public function __construct($code, $height = 50, $scale = 2) {
        $this->code = $code;
        $this->height = $height;
        $this->scale = $scale;
    }
    
    public function generate() {
        // Create a new image
        $img = imagecreatetruecolor($this->getWidth(), $this->height);
        
        // Colors
        $white = imagecolorallocate($img, 255, 255, 255);
        $black = imagecolorallocate($img, 0, 0, 0);
        
        // Fill background
        imagefilledrectangle($img, 0, 0, $this->getWidth(), $this->height, $white);
        
        // Generate barcode pattern for Code 128
        $pattern = $this->getCode128Pattern();
        
        // Draw bars
        $x = 10;
        foreach ($pattern as $bar) {
            if ($bar['bar']) {
                imagefilledrectangle(
                    $img,
                    $x,
                    0,
                    $x + ($bar['width'] * $this->scale),
                    $this->height,
                    $black
                );
            }
            $x += $bar['width'] * $this->scale;
        }
        
        // Output image
        header('Content-Type: image/png');
        imagepng($img);
        imagedestroy($img);
    }
    
    private function getWidth() {
        return strlen($this->code) * 11 * $this->scale + 30;
    }
    
    private function getCode128Pattern() {
        $pattern = [];
        $chars = str_split($this->code);
        
        // Start with Code 128B start character
        $pattern[] = ['bar' => 1, 'width' => 2];
        $pattern[] = ['bar' => 0, 'width' => 1];
        $pattern[] = ['bar' => 1, 'width' => 1];
        $pattern[] = ['bar' => 0, 'width' => 2];
        
        foreach ($chars as $char) {
            // Simple pattern for each character
            $pattern[] = ['bar' => 1, 'width' => 1];
            $pattern[] = ['bar' => 0, 'width' => 2];
            $pattern[] = ['bar' => 1, 'width' => 2];
            $pattern[] = ['bar' => 0, 'width' => 1];
        }
        
        // Add stop character
        $pattern[] = ['bar' => 1, 'width' => 2];
        $pattern[] = ['bar' => 0, 'width' => 2];
        $pattern[] = ['bar' => 1, 'width' => 2];
        
        return $pattern;
    }
}
?>