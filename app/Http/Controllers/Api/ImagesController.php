<?php

namespace App\Http\Controllers\Api;

use Illuminate\Http\Request;
use App\Services\GetImageService;
use App\Models\Image;
use App\Models\Category;

class ImagesController extends Controller
{

    protected $crawlImageService;

    public function __construct(GetImageService $getImageService)
    {
        $this->getImageService = $getImageService;
    }

    public function getImages()
    {
        $images = $this->getImageService->crawlImages();
        return response()->json(['images' => $images]);
    }

    public function getCategories()
    {
        $categories = Category::all();
        return response()->json(['categories' => $categories]);
    }
}
?>
