<?php 
namespace App\Services;
use App\Services\GeneratorService;
use Cloudinary\Transformation\Adjust;
use Cloudinary\Transformation\Compass;
use Cloudinary\Transformation\FocusOn;
use Cloudinary\Transformation\Gravity;
use Cloudinary\Transformation\ImageTransformation;
use Cloudinary\Transformation\Overlay;
use Cloudinary\Transformation\Position;
use Cloudinary\Transformation\Resize;
use Cloudinary\Transformation\RoundCorners;
use Cloudinary\Transformation\Source;
use CloudinaryLabs\CloudinaryLaravel\Facades\Cloudinary;

class CloudinaryService {

    public static function uploadImage($file, $folder, $options)
    {
        $name = GeneratorService::randomAlphaNumeric(6).time();
        $showOverlay = '';
        $resize1 = '';
        $resize2 = '';
        if (!empty($options['overlayImageURL'])){
            $showOverlay = Overlay::imageSource(Source::fetch($options['overlayImageURL'])
                ->transformation((new ImageTransformation())
                    ->adjust(Adjust::brightness()->level(10))
                    ->adjust(Adjust::opacity(60))
                    ->resize(Resize::scale()->width(150))
                ))
                ->position((new Position())
                    ->gravity(Gravity::compass(Compass::southEast()))
                    ->offsetX(5)
                    ->offsetY(5)
                );
        }
        if (!empty($options['thumbnail']))
        {
            $resize1 = Resize::thumbnail()
            ->width($options['thumbnail']['width'] ?? 400)
            ->height($options['thumbnail']['height'] ?? 400)
            ;
            // ->gravity(Gravity::focusOn(FocusOn::face()));
        }
        
        if (!empty($options['dimensions'])){
            $resize2 = Resize::fill()
            ->width($options['dimensions']['width'] ?? 400)
            ->height($options['dimensions']['height'] ?? 400)
            ->gravity(Gravity::focusOn(FocusOn::face()));
        }

        return Cloudinary::upload($file->getRealPath(),[
            'folder' => $folder,
            "public_id" => $name,
            "overwrite" => true,
            'transformation' => [
                RoundCorners::byRadius($options['roundCorners']?? null),
                $resize1,
                $resize2,
                $showOverlay,
            ]
        ]);
    }

    /**
     * @param $public_id
     * @param array $options
     */
    public static function destroy($public_id, $options = []){
        $token = explode('/', $public_id);

        $folder = "";
        
        for($i=7; $i < sizeof($token)-1; $i++ )
            $folder .= $token[$i].'/';
        
        $filename = explode('.', $token[sizeof($token)-1])[0];

        return  Cloudinary::destroy($folder.$filename, $options);
    }

}