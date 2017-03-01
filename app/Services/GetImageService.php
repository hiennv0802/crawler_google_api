<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Image;

class GetImageService
{
    public function crawlImages()
    {
        if (isset($_GET['page'])) { $page = $_GET['page']; } else {$page = 1;};
        $cateName = 'all';
        if (isset($_GET['category']))
        {
            $cateName = $_GET['category'];
        }
        $cate = Category::whereIn('name', [$cateName, strtolower($cateName)])->first();
        $images = is_null($cate) ? Image::all() : $cate->images;
        $perPage = config('image.default_record');
        $offSet = ($page * $perPage) - $perPage;
        $itemsForCurrentPages = array_slice($images->toArray(), $offSet, $perPage, true);
        $results = [];
        foreach ($itemsForCurrentPages as $itemsForCurrentPage) {
            $image = (object)$itemsForCurrentPage;
            $result = [];
            $result['id'] = $image->id;
            $img = Image::find($result['id']);
            $result['link'] = $image->link;
            $result['category'] = $img->category->name;
            $results[] = $result;
        }
        return $results;
    }
}
