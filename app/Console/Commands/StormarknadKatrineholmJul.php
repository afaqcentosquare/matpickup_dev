<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Category;
use App\Meta;
use App\PostCategory;
use App\Productmeta;
use App\Terms;
use App\User;
use Exception;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class StormarknadKatrineholmJul extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'stormarknadkatrineholmjul:cron';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        try{
            $user_id = 26;
            $category_id = 2676;
            $category_name = "Jul";
            $category_slug = Str::slug($category_name);
            if ($category_slug == '') {
                $category_slug = str_replace(' ', '-', $category_name);
            }
            $category_slug_url = $category_slug . '-' . '_' . $category_id;
    
            $category_products_url = 'https://handla.ica.se/api/content/v1/collection/customer-type/B2C/store/maxi-ica-stormarknad-katrineholm-id_10735/products?categories=' . $category_slug_url;
    
            $category_products_ch = curl_init();
            curl_setopt($category_products_ch, CURLOPT_URL, $category_products_url);
            curl_setopt($category_products_ch, CURLOPT_POST, 0);
            curl_setopt($category_products_ch, CURLOPT_RETURNTRANSFER, true);
    
            $category_products_response = curl_exec($category_products_ch);
            $err = curl_error($category_products_ch); //if you need
            curl_close($category_products_ch);
            $category_products_response = json_decode($category_products_response);
    
            $category_products_array = array();
    
            $products = array();
            $category_products = Category::where("user_id", $user_id)->where("type", 1)->where("name", $category_name)->first();
    
            if ($category_products) {
                $post_category_ids = PostCategory::where("category_id", $category_products->id)->pluck("term_id");
    
                Terms::whereIn("id", $post_category_ids)->delete();
                PostCategory::where("category_id", $category_products->id)->delete();
                Category::where("user_id", $user_id)->where("type", 1)->where("name", $category_name)->delete();
            }
            \Log::info("updated cron is working fine!");
    
            /*
            Write your database logic we bellow:
            Item::create(['name'=>'hello new']);
             */
    
            $this->info('Demo:Cron Cummand Run successfully!');
        //     return;
            foreach ($category_products_response->items as $category_item) {
                if ($category_item->type == 'product') {
                    $products_url = 'https://handla.ica.se/api/content/v1/collection/customer-type/B2C/store/maxi-ica-stormarknad-katrineholm-id_10735/products-data?skus=' . $category_item->id;
    
                    $products_ch = curl_init();
                    curl_setopt($products_ch, CURLOPT_URL, $products_url);
                    curl_setopt($products_ch, CURLOPT_POST, 0);
                    curl_setopt($products_ch, CURLOPT_RETURNTRANSFER, true);
    
                    $products_response = curl_exec($products_ch);
                    $err = curl_error($products_ch); //if you need
                    curl_close($products_ch);
                    $products_response = json_decode($products_response, true);
    
                    //return response()->json($products_response);
    
                    $is_term_available = Terms::where("title", $products_response[0]['name'])->where("auth_id", $user_id)
                        ->join("post_category", "post_category.term_id", "terms.id")
                        ->join("categories", "categories.id", "post_category.category_id")
                        ->where("categories.name", $category_name)
                        ->join("product_meta", "product_meta.term_id", "terms.id")
                        ->where("product_meta.price", $products_response[0]['price'])
                        ->first();
    
                    $descriptionLong = isset($products_response[0]['descriptionLong']) ? $products_response[0]['descriptionLong'] : "";
                    $productDisclaimer = isset($products_response[0]['productDisclaimer']) ? $products_response[0]['productDisclaimer'] : "";
                    $nutritionalText = isset($products_response[0]['nutritionalText']) ? $products_response[0]['nutritionalText'] : "";
    
                    if (!$is_term_available) {
    
                        $slug = Str::slug($products_response[0]['name']);
                        if ($slug == '') {
                            $slug = str_replace(' ', '-', $products_response[0]['name']);
                        }
    
                        $post = new Terms;
                        $post->title = $products_response[0]['name'];
                        $post->slug = $slug;
                        $post->type = 6;
                        $post->auth_id = $user_id;
                        $post->status = 1;
                        $post->save();
    
                        $post_meta = new Meta;
                        $post_meta->term_id = $post->id;
                        $post_meta->type = 'excerpt';
                        $post_meta->content = $descriptionLong . "</br>" . $productDisclaimer . "</br>" . $nutritionalText;
                        $post_meta->save();
    
                        $post_meta = new Meta;
                        $post_meta->term_id = $post->id;
                        $post_meta->type = 'preview';
                        $post_meta->content = "https://assets.icanet.se/t_product_large_v1,f_auto/" . $products_response[0]['cloudinaryImageId'] . ".jpg";
                        $post_meta->save();
    
                        $product = new Productmeta;
                        $product->term_id = $post->id;
                        $product->price = (double) $products_response[0]['price'];
                        $product->save();
    
                        $categories = Category::where('user_id', 26)->where("name", $category_name)->first();
    
                        if (!$categories) {
                            $category_slug = Str::slug($category_name);
                            if ($category_slug == '') {
                                $category_slug = str_replace(' ', '-', $category_name);
                            }
                            $category = new Category;
                            $category->name = $category_name;
                            $category->avatar = null;
                            $category->slug = $category_slug;
                            $category->p_id = null;
                            $category->type = 1;
                            $category->user_id = $user_id;
                            $category->save();
    
                            $cat = new PostCategory;
                            $cat->term_id = $post->id;
                            $cat->category_id = $category->id;
                            $cat->save();
                        } else {
                            $cat = new PostCategory;
                            $cat->term_id = $post->id;
                            $cat->category_id = $categories->id;
                            $cat->save();
                        }
                    }
    
                }
    
            }
        }catch(Exception $e){
            \Log::info($e->getMessage());

            /*
            Write your database logic we bellow:
            Item::create(['name'=>'hello new']);
             */
    
            $this->info($e->getMessage());
        }
       

       
    }
}
