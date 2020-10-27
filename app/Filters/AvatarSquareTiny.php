<?php

namespace App\Filters;

use Intervention\Image\Image;
use Intervention\Image\Filters\FilterInterface;

class AvatarSquareTiny implements FilterInterface
{
    public function applyFilter(Image $image)
    {
        if ($image->width() > $image->height()) {
            $image->resize(null, 200, function ($constraint) {
                $constraint->aspectRatio();
            });
        } else {
            $image->resize(200, null, function ($constraint) {
                $constraint->aspectRatio();
            });
        }
        $image->resizeCanvas(200, 200, 'center', false, array(255, 255, 255, 0));

        $image->resize(50, null, function ($constraint) {
            $constraint->aspectRatio();
        });

        ob_end_clean();
        
        return $image->encode('jpg', config('settings.jpeg_quality'));
    }
}
