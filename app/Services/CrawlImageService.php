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
        $images = Image::all();
        $results = [];
        foreach ($images as $image) {
            $result = [];
            $result['id'] = $image->id;
            $result['link'] = $image->link;
            $result['category'] = $image->category->name;
            $results[] = $result;
        }
        $a = $this->getImageGoogl();
        // dd($a);
        return $a;
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
        $image_names = Image::all()->pluck('name');
        $files = $this->getImageGoogl();
        $data['subject'] = array('name' => 'Girl');
        $cate_names = Category::all()->pluck('name');
        foreach ($files as $file) {
            $data = [];
            $data['link'] = $file->webContentLink;
            $data['name'] = $file->name;

            $pre_cate = explode('_', $data['name']);
            $category = Category::firstOrCreate(['name' => $pre_cate[0]]);
            $data['category_id'] = $category->id;
            if (!in_array($data['name'], (array)$image_names) && !is_null($data['link']))
            {
                Image::create($data);
            }
        }
    }
}
