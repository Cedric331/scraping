<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Goutte\Client;
use Illuminate\Support\Facades\Cache;

class SpiderController extends Controller
{
    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function spider (Request $request): \Illuminate\Http\JsonResponse
    {
        $request->validate([
            'input' => 'required|string|min:1'
        ]);

        $collect = collect();
        $client = new Client();
        $crawler = $client->request('GET', 'https://www.larousse.fr/dictionnaires/francais/'.$request->input);

        $crawler->filter('h2.AdresseDefinition')->each(function ($value) use ($collect) {
            $string = preg_replace('/[\x00-\x1F\x7F-\xFF]/', '', $value->text());
            $string = trim($string);
            $collect->put('title', ucfirst($string));
        });

        $defs = collect();
        $crawler->filter('li.DivisionDefinition')->each(function ($value) use ($defs) {
            if ($value->text()) {
                $defs->push($value->text());
            }
        });
        $collect->put('defs', $defs);

        if ($collect->has(['title', 'defs'])) {
            return response()->json($collect);
        } else {
            return response()->json(null, 204);
        }
    }
}
