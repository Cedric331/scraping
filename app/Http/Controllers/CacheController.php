<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;


class CacheController extends Controller
{

    /**
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCache (): \Illuminate\Http\JsonResponse
    {
        $elements = Cache::has('element'.request()->ip()) ? Cache::get('element'.request()->ip()) : '';
        $search = Cache::has('search'.request()->ip()) ? Cache::get('search'.request()->ip()) : '';

        return response()->json(['elements' => $elements, 'search' => $search]);

    }

    /**
     * @param Request $request
     */
    public function postCache (Request $request)
    {
        if (Cache::has('search'.request()->ip())) {
            $search = Cache::get('search'.request()->ip());
            array_push($search, $request->search);
        } else {
            $search = [$request->search];
        }

        Cache::forever('element'.request()->ip(), $request->data);
        Cache::forever('search'.request()->ip(), $search);
    }

    public function deleteCache ()
    {
        Cache::put('element'.request()->ip(), '', 0);
    }
}
