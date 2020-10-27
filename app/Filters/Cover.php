<?php

namespace App\Filters;

use Intervention\Image\Image;
use Intervention\Image\Filters\FilterInterface;

class Cover implements FilterInterface
{
    public function applyFilter(Image $image)
    {
        if ($image->width() >= 300) {
            $image->resize(300, null, function ($constraint) {
                $constraint->aspectRatio();
            });
        }

        ob_end_clean();

        return $image->encode('jpg', config('settings.jpeg_quality'));
    }
}
