<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\CrawlImageService;
use App\Googl;
use Carbon\Carbon;
use App\Models\Image;
use App\Models\Category;

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
        $this->crawlImageService->updateData();
        $images = $this->crawlImageService->crawlImages();
        return response()->json(['images' => $images]);
    }

    public function getCategories()
    {
        $categories = Category::all();
        return response()->json(['categories' => $categories]);
    }
}
?>
