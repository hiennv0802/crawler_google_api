<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\CrawlImageService;
use App\Googl;
use Carbon\Carbon;
use App\Models\Image;

class ImagesController extends Controller
{

    protected $crawlImageService;

    public function __construct(CrawlImageService $crawlImageService)
    {
        $this->crawlImageService = $crawlImageService;
    }

    public function getImages()
    {
        //$categories = DB::table('categories')->get();
        // $this->crawlImageService->updateData();

        // $images = $this->crawlImageService->crawlImages();
        $images = Image::all();
        return response()->json(['images' => $images]);
    }
}
?>
