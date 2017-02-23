<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Image;
use App\Googl;
use Carbon\Carbon;

class CrawlImageService
{
    private $client;
    private $drive;

    public function __construct(Googl $googl)
    {
        $this->client = $googl->client();
        $this->client->setAccessToken(session('user.token'));
        $this->drive = $googl->drive($this->client);
    }

    public function crawlImages()
    {
        if (isset($_GET['page'])) { $page = $_GET['page']; } else {$page = 1;};
        $images = Image::all();
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
            $result['name'] = $image->name;
            $result['category'] = $img->category->name;
            $results[] = $result;
        }
        return $results;
    }

    public function getImageGoogl()
    {
        $result = [];
        $pageToken = NULL;

        $three_months_ago = Carbon::now()->subMonths(3)->toRfc3339String();

        do {
            try {
                $parameters = [
                    'q' => "viewedByMeTime >= '$three_months_ago' or modifiedTime >= '$three_months_ago'",
                    'orderBy' => 'modifiedTime',
                    'fields' => 'nextPageToken, files(id, name, modifiedTime, iconLink, webViewLink, webContentLink)',
                ];

                if ($pageToken) {
                    $parameters['pageToken'] = $pageToken;
                }

                $result = $this->drive->files->listFiles($parameters);
                $files = $result->files;

                $pageToken = $result->getNextPageToken();

            } catch (Exception $e) {
                return redirect('/files')->with('message',
                    [
                        'type' => 'error',
                        'text' => 'Something went wrong while trying to list the files'
                    ]
                );
              $pageToken = NULL;
            }
        } while ($pageToken);

        $page_data = [
            'files' => $files
        ];

        return $files;
    }

    public function updateData()
    {
        $files = $this->getImageGoogl();
        $data['subject'] = array('name' => 'Girl');
        $cate_names = Category::all()->pluck('name');
        foreach ($files as $file) {
            $data = [];

            if ($this->isImageFormat($file->name)) {
                $data['name'] = $file->name;
                $data['link'] = $file->webContentLink;
                $pre_cate = explode('_', $data['name']);
                $category = Category::firstOrCreate(['name' => $pre_cate[0]]);

                $image_names = Image::all()->pluck('name')->toArray();
                $data['category_id'] = $category->id;

                if (!in_array($data['name'], (array)$image_names) && !is_null($data['link']) && $this->isImageFormat($file->name))
                {
                    Image::create($data);
                }
            }
        }
    }

    public function isImageFormat($image_name)
    {
        $tmp = explode('.', $image_name);
        $format = end($tmp);
        return in_array($format, config('image.image_formats'));
    }
}
