<?php

namespace App\Filters;

use Intervention\Image\Image;
use Intervention\Image\Filters\FilterInterface;

class Carousel implements FilterInterface
{
    public function applyFilter(Image $image)
    {
        if ($image->width() >= 300) {
            $image->resize(500, null, function ($constraint) {
                $constraint->aspectRatio();
            });
        }
        if ($image->width() <= 250) {
            $image->resize(500, null, function ($constraint) {
                $constraint->aspectRatio();
            });
        }

        $image->resizeCanvas(500, 300, 'center', false, array(0, 0, 0, 0));

        ob_end_clean();

        return $image->encode('jpg', config('settings.jpeg_quality'));
    }
}
