<?php

namespace App\Http\Controllers;

use App\Models\Popular;
use App\Utils\GoogleCustom;
use Illuminate\Http\Request;
use Laravel\Lumen\Routing\Controller;

class PopularController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    public function get(Request $request){
        $data = Popular::simplePaginate(10)->toArray();

        if (empty($data['data'])){
            $google = GoogleCustom::getInstance();
            $google_res = $google->get('kesehatan seksual wanita', $request->page);
            $decluttered = $google->declutter($google_res['items']);

            Popular::insert($decluttered);
        }
        $data = Popular::simplePaginate(10);

        return response()->json($data);
    }
}
