<?php

namespace App\Http\Controllers;

use App\Models\Animes;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class AnimeController extends Controller
{
    public function saveTopAnime()
    {
        $topAnime = [];
        $pagesNeeded = 4; // 4 pages to get 100 anime items (25 per page)

        for ($page = 1; $page <= $pagesNeeded; $page++) {
            $response = Http::get('https://api.jikan.moe/v4/top/anime', [
                'page' => $page,
            ]);

            if ($response->successful()) {
                $animeData = $response->json()['data'];

                // Loop through each anime and prepare it for insertion
                foreach ($animeData as $anime) {
                    $data = [
                        'mal_id' => $anime['mal_id'],
                        'titles' => json_encode($anime['titles']),
                        'slug' => str_replace(' ', '_', $anime['title']),
                        'image_url' => $anime['url'],
                        'created_at' => now()
                    ];
                    Animes::updateOrCreate(
	                    ['mal_id' => $anime['mal_id']], // Check for existing mal_id
	                    $data // Update or insert data
	                );
                }
            } else {
                return response()->json(['error' => 'Failed to fetch data from page ' . $page], $response->status());
            }
        }

        return response()->json(['message' => 'Top 100 anime saved successfully']);
    }
    public function show($slug)
	{
        $anime = Animes::where('slug', $slug)->first();
        if ($anime) {
            // Decode the 'titles' field from JSON string to array
            $titles = json_decode($anime->titles, true);
            
            // Find the title matching the requested language
            $title = collect($titles)->firstWhere('type', request('lang') ?: 'English');

            // If no title matches the requested language, return the default one
            if (!$title) {
                $title = collect($titles)->firstWhere('type', 'Default');
            }

            // Return the anime details with the appropriate title
            return response()->json([
                'status' => 200,
                'message' => 'Data fetched successfully',
                'data' => [
                    'id' => $anime->id,
                    'mal_id' => $anime->mal_id,
                    'title' => $title['title'],
                    'slug' => $anime->slug,
                    'image_url' => $anime->image_url,
                    'created_at' => $anime->created_at,
                    'updated_at' => $anime->updated_at,
                    'deleted_at' => $anime->deleted_at
                ]
            ]);
        } else {
            // If no data found, return message with status 200
            return response()->json([
                'status' => 200,
                'message' => 'No data found'
            ]);
        }  
	}


}
