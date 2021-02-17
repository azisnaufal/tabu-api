<?php


namespace App\Utils;


use Illuminate\Support\Facades\Http;

class GoogleCustom
{
    /**
     * The Singleton's instance is stored in a static field. This field is an
     * array, because we'll allow our Singleton to have subclasses. Each item in
     * this array will be an instance of a specific Singleton's subclass. You'll
     * see how this works in a moment.
     */
    private static $instances = null;

    /**
     * The Singleton's constructor should always be private to prevent direct
     * construction calls with the `new` operator.
     */
    protected function __construct() { }

    /**
     * Singletons should not be cloneable.
     */
    protected function __clone() { }

    /**
     * Singletons should not be restorable from strings.
     */
    public function __wakeup()
    {
        throw new \Exception("Cannot unserialize a singleton.");
    }

    /**
     * This is the static method that controls the access to the singleton
     * instance. On the first run, it creates a singleton object and places it
     * into the static field. On subsequent runs, it returns the client existing
     * object stored in the static field.
     *
     * This implementation lets you subclass the Singleton class while keeping
     * just one instance of each subclass around.
     */
    public static function getInstance(): GoogleCustom
    {
        $cls = static::class;
        if (!isset(self::$instances)) {
            self::$instances = new static();
        }

        return self::$instances;
    }

    public function get($query, $page = 0){
        $data = Http::get('https://www.googleapis.com/customsearch/v1',[
            'key' => env('GOOGLE_SEARCH_KEY'),
            'cx' => env('GOOGLE_SEARCH_CX'),
            'q' => $query,
            'start' => $page > 0 ? ($page*10)+1 : 0
        ])->json();

        return $data;
    }

    public function declutter($arr){
        $result = [];
        foreach ($arr as $item){
            $temp = [];
            $temp['title'] = $item['title'];
            $temp['link'] = $item['link'];
            foreach ($item['pagemap']['metatags'][0] as $key => $value){
                if (strpos($key, 'description') !== false) {
                    $temp['snippet'] = $value;
                    break;
                }
            }

            if (isset($item['pagemap']['cse_image'])){
                $temp['image'] = $item['pagemap']['cse_image'][0]['src'];
            }
            else {
                foreach ($item['pagemap']['metatags'][0] as $key => $value){
                    if (strpos($key, 'image') !== false) {
                        if (filter_var($value, FILTER_VALIDATE_URL)){
                            $temp['image'] = $value;
                            break;
                        }
                    }
                }
            }

            array_push($result, $temp);
        }
        return $result;
    }
}
