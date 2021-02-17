<?php

namespace App\Http\Controllers;

use App\Utils\GoogleCustom;
use Illuminate\Http\Request;
use Google\Cloud\Dialogflow\V2\SessionsClient;
use Google\Cloud\Dialogflow\V2\TextInput;
use Google\Cloud\Dialogflow\V2\QueryInput;
use Laravel\Lumen\Routing\Controller;


class ChatbotController extends Controller
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

    public function chat(Request $request){
        $this->validate($request, [
            'q' => 'required'
        ]);

        // new session
        $sessionsClient = new SessionsClient([
            'credentials' => base_path().'/'.env('DIALOGFLOW_FILE_NAME').'.json'
        ]);
        $session = $sessionsClient->sessionName(env('DIALOGFLOW_PROJECT_ID'), uniqid());

        // create text input
        $textInput = new TextInput();
        $textInput->setText($request->q);
        $textInput->setLanguageCode('id-ID');

        // create query input
        $queryInput = new QueryInput();
        $queryInput->setText($textInput);

        // get response and relevant info
        $response = $sessionsClient->detectIntent($session, $queryInput);
        $queryResult = $response->getQueryResult();
        $fulfilmentText = $queryResult->getFulfillmentText();
        if ($queryResult->getAction() == "input.search"){
            $searchable = $queryResult->getParameters()->getFields()->offsetGet("searchable")->getStringValue();

            $google = GoogleCustom::getInstance();
            $google_res = $google->get($searchable, 0);
            $decluttered = $google->declutter($google_res['items']);
        }
        else {
            $decluttered = [];
        }

        $sessionsClient->close();

        return response()->json([
            'fulfilmentText' => $fulfilmentText,
            'articles' => $decluttered
        ]);
    }
}
