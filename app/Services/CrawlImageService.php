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

    public function getImageGoogl($query)
    {
        $result = [];
        $pageToken = NULL;

        $three_months_ago = Carbon::now()->subMonths(3)->toRfc3339String();

        do {
            try {
                $parameters = [
                    'q' => $query,
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
        $cateQuery = "mimeType='application/vnd.google-apps.folder' and 'root' in parents and trashed=false";
        $files = $this->getImageGoogl($cateQuery);
        foreach ($files as $f) {
            if (in_array($f->name, config('category.categories_list')))
            {
                $this->updateImageIntoCate($f->id, $f->name);
            }
        }
    }

    public function updateImageIntoCate($cateId, $cateName)
    {
        $imageQuery = "'$cateId' in parents and trashed=false";
        $imageFiles = $this->getImageGoogl($imageQuery);
        $currentCategory = Category::where('name', $cateName)->first();
        foreach ($imageFiles as $img) {
            $data = [];
            if($this->isImageFormat($img->name))
            {
                $image_names = Image::all()->pluck('name')->toArray();

                $data['name'] = $img->name;
                $data['link'] = $img->webContentLink;
                $data['category_id'] = $currentCategory->id;
                if (!in_array($data['name'], (array)$image_names) &&
                    !is_null($data['link']) && $this->isImageFormat($img->name))
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
