<?php 

namespace Amcoders\Theme\khana\http\controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\User;
use App\Terms;
use App\Usermeta;
use App\Featured;
use App\Options;
use Session;
use Artesaos\SEOTools\Facades\SEOMeta;
use Artesaos\SEOTools\Facades\OpenGraph;
use Artesaos\SEOTools\Facades\TwitterCard;
use Artesaos\SEOTools\Facades\JsonLd;
use Amcoders\Plugin\contactform\Contact;
use DB;
use App\Category;
use Cart;
use Illuminate\Support\Str;
use Auth;
use App\Location;
use App\Address;

class WelcomeController extends controller
{
    
     /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

       try {
          DB::connection()->getPdo();
          if(DB::connection()->getDatabaseName()){
           $top_rated= Usermeta::wherehas('users')->where('type','rattings')->wherehas('locations')->orderBy('content','DESC')->with('users','coupons')->take(8)->get()->map(
                function($data){
                    $qry= array();
                    $qry['city']= $data->users->city->city ?? '';
                    $qry['rattings']= $data->content;
                    $qry['slug']= $data->users->slug;
                    $qry['name']= $data->users->name;
                    $qry['avg_ratting']= number_format($data->users->avg_ratting->content ?? 0,1);
                    $qry['coupon']= $data->coupons;
                    $qry['time']= $data->users->delivery->content ?? null;
                    
                    
                    if (!empty($data->users->preview->content)) {
                       $qry['avatar']= ImageSize($data->users->preview->content,'medium');
                       
                    }
                    else{
                        $qry['avatar']= $data->users->avatar;
                    }



                    return $qry;
                }
            ) ;
           $features_resturent=Featured::where('type','featured_seller')->wherehas('users')->wherehas('locations')->with('users','coupons')->latest()->inRandomOrder()->take(8)->get()->map(function($data){

                    $qry= array();
                    $qry['city']=  $qry['city']= $data->users->city->city ?? '';;
                    $qry['rattings']= $data->users->ratting->content ?? 0;
                    $qry['slug']= $data->users->slug;
                    $qry['name']= $data->users->name;
                    $qry['avg_ratting']= number_format($data->users->avg_ratting->content ?? 0,1);
                    $qry['coupon']= $data->coupons;
                    $qry['time']= $data->users->delivery->content ?? null;
                    
                    
                    if (!empty($data->users->preview->content)) {
                       $qry['avatar']= ImageSize($data->users->preview->content,'medium');
                    }
                    else{
                        $qry['avatar']= $data->users->avatar;
                    }



                    return $qry;
           });

            $locations=Terms::where('type',2)->withcount('Locationcount')->with('preview')->latest()->get()->map(function($data){
                $qry['title']=$data->title;
                $qry['slug']=$data->slug;
                $qry['count']=$data->locationcount_count;
                $qry['preview']=$data->preview->content;
                return $qry;
            });

            $offer_resturents = Terms::where([
                ['type',10],
                ['status',1]
            ])->inRandomOrder()->wherehas('userwithpreview')->with('userwithpreview')->take(8)->get();
            
            $seo=Options::where('key','seo')->first();
            $seo=json_decode($seo->value);

            SEOMeta::setTitle($seo->title);
            SEOMeta::setDescription($seo->description);
            SEOMeta::setCanonical($seo->canonical);

            OpenGraph::setDescription($seo->description);
            OpenGraph::setTitle($seo->title);
            OpenGraph::setUrl($seo->canonical);
            OpenGraph::addProperty('keywords', $seo->tags);

            TwitterCard::setTitle($seo->title);
            TwitterCard::setSite($seo->twitterTitle);

            JsonLd::setTitle($seo->title);
            JsonLd::setDescription($seo->description);
            JsonLd::addImage(content('header','logo'));
            
            $color = Options::where('key','color')->first();
            if($color)
            {
                $theme_color = $color->value;
            }else{
                $theme_color = '#FF3252';
            }

            
            return view('theme::welcome.home',compact('top_rated','features_resturent','locations','offer_resturents','theme_color'));
          }else{
            return redirect()->route('install'); 
          }
        } catch (\Exception $e) {
            return redirect()->route('install'); 
        } 
    }

    public function topresturent()
    {
        $featured_resturents= Usermeta::where('type','rattings')->orderBy('content','DESC')->with('users')->take(8)->get();
       // dd($featured_resturents);
        return view('theme::welcome.section',compact('featured_resturents'));
    }

    public function notify()
    {
        Session::put('header_notify',[
            'status' => 'ok'
        ]);

        return Session::get('header_notify');
    }

    public function page($slug)
    {
        $info=Terms::where('type',1)->where('status',1)->where('slug',$slug)->with('excerpt','content')->first();
        if (empty($info)) {
            abort(404);
        }

        SEOMeta::setTitle($info->title);
        SEOMeta::setDescription($info->excerpt->content);

        OpenGraph::setDescription($info->excerpt->content);
        OpenGraph::setTitle($info->title);
        OpenGraph::setUrl(url()->current());
       

        TwitterCard::setTitle($info->title);
        TwitterCard::setSite($info->title);

        JsonLd::setTitle($info->title);
        JsonLd::setDescription($info->excerpt->content);
        JsonLd::addImage(content('header','logo'));
        return view('theme::welcome.page',compact('info'));
    }


    public function contact(Request $request)
    {
        
        Contact::send($request->name,$request->email,$request->subject,$request->message);

        return response()->json('ok');
    }

    /**
     * New FrontEnd Begin 
     */

     public function home()
     {
        
        $stores = User::where("role_id",3)->where("users.status","approved")
        ->with(["productsTerm" => function($q){
                $q->join("meta","meta.term_id","terms.id")
                ->join("product_meta","product_meta.term_id","terms.id")
                ->where("meta.type","preview")
                ->where("terms.type",6)
                ->inRandomOrder()
                ->limit(8);
            }])
        ->wherehas('productsTerm')
        ->limit(3)->get();
        


        // $locations=Terms::where('type',2)->withcount('Locationcount')->with('preview')->latest()->get()->map(function($data){
        //     $qry['title']=$data->title;
        //     $qry['slug']=$data->slug;
        //     $qry['count']=$data->locationcount_count;
        //     $qry['preview']=$data->preview->content;
        //     return $data;
        // });


       //$info=Terms::where('slug',"helsingborg")->where('status',1)->where('type',2)->with('excerpt','preview')->first();

    //    $posts=Location::where('locations.role_id',3)
	// 	->where('term_id',4330)
	// 	->wherehas('users')
	// 	->with('users')
	// 	->paginate(12);

    // $rest = Terms::where('type',2)->where('status',1)->get();
    //     return response()->json($rest);

       // return response()->json($stores);

       $allCities= Terms::where('type',2)->where('status',1)->get();
	

		// $allCitiesStores=Location::where('locations.role_id',3)
		// ->wherehas('users')
		// ->with('users')
		// ->get();

        $allCitiesStores=Terms::where('type',2)->where('status',1)->with("preview")->get();

      //  return response()->json($allCitiesStores);

         return view('theme::frontend.home')->with("stores", $stores)->with("allCities", $allCities)->with("allCitiesStores", $allCitiesStores);
     }

     public function singleProduct($slug)
     {

        
        
         $single_product = Terms::with('preview', 'price', 'user','excerpt')->where("slug",$slug)->where("terms.type",6)->first();
         
        if(!empty($single_product)){
            Session::put('restaurant_cart',[
                'count' => Cart::instance('cart_'.$single_product->user->slug)->count(),
                'slug' => $single_product->user->slug
            ]);
          
            
            Session::put('restaurant_id',[
                'id' => $single_product->user->id,
                'name' => $single_product->user->name
            ]);
            $related_products = Terms::with('preview', 'price')->where("auth_id",$single_product->user->id)->where("slug",'!=',$slug)->inRandomOrder()->limit(10)->get();
       
         
            return view("theme::frontend.single_product")->with("single_product", $single_product)->with("related_products", $related_products);
        }
        
        $related_products = Terms::with('preview', 'price')->where("slug",'!=',$slug)->inRandomOrder()->limit(10)->get();
       
         
        return view("theme::frontend.single_product")->with("single_product", $single_product)->with("related_products", $related_products);
        return response()->json($single_product);
        
     }



     public function storeProducts($slug)
     {
         $store_id  = User::where("slug", $slug)->with("preview")->first();

        // return response()->json($store_id);

         $categories = Category::where("user_id", $store_id->id)->where("type",1)->get();
         $products = Terms::with('preview', 'price')->where("auth_id",$store_id->id)->where("terms.type",6)->paginate(20);
         Session::put('restaurant_cart',[
			'count' => Cart::instance('cart_'.$store_id->slug)->count(),
			'slug' => $store_id->slug
		]);

		Session::put('restaurant_id',[
			'id' => $store_id->id,
			'name' => $store_id->name
		]);
        
         return view("theme::frontend.store_products")->with("categories", $categories)->with("products", $products)->with("store_data", $store_id);
     }

     public function categoryProducts(Request $request)
     {
        
        $category_products = Category::where("slug", $request->slug)->where("user_id", $request->store_pure_id)->first();
        
        //return response()->json($category_products);
        $products = $category_products->products()->paginate(20);
        
       
        $html = view('theme::frontend.store_products_view')->with(compact('products'))->render();
        return response()->json(['products' => $html]);
     }


     public function allStores()
     {
        $stores=User::with('preview')->where("role_id",3)->where("users.status","approved")->wherehas('productsTerm')->get();
       
        //return response()->json($stores);
       return view("theme::frontend.stores")->with("stores",$stores);
     }

     public function cart()
     {
         return view('theme::frontend.cart',compact('store'));
     }

     public function test(Request $request)
     {
        // $cart = Cart::instance('cart_ica-maxi')->content();
        // return response()->json($cart);
        //return response()->json(Cart::instance('cart_'.Session::get('restaurant_cart')['slug'])->content());

       // return response()->json($request->all());
        //return view("theme::frontend.test_search");
    }

    public function searchProduct(Request $request)
    {

        // return response()->json($request);
        if($request->selected_serach_text == null && $request->copy_search == null){
            return back();
        }
        if($request->selected_serach_text == null){
            $slug = Str::slug($request->copy_search);
            if ($slug == '') {
                $slug = str_replace(' ', '-', $request->copy_search);
            }
            return redirect()->route('single.product', $slug);
        }else{
            return redirect()->route('single.product', $request->selected_serach_text);
        }
        
    }

    public function autocomplete(Request $request)
    {

        $search = $request->get('query');
        $products_search_data = Terms::with('preview', 'price')->where("auth_id",26)->where('title', 'LIKE', '%'. $search. '%')->where("terms.type",6)->get();
        $data = array();
        foreach ($products_search_data as $product)
        {
                $data[] = array(
                    "slug" => $product->slug,
                    "name" => $product->title,
                    "image" => $product->preview->content,
                    "price" => $product->price->price
                );
        }
        return response()->json($data);
    }

    public function bankIdLoginForm()
    {
        return view('theme::frontend.bankid_login_form');
    }


    public function bankIdLogin(Request $request)
    {

            $sign = 'https://api.banksignering.se/api/sign';
            
            $postData = [
                'apiUser' => 'matpickup',
                'password' => '942cff35-78e0-43e4-bd5b-aa1a33a814ad',
                'companyApiGuid' => '7ca71b82-0978-44b7-8534-9e01f86e569d',
                'userVisibleData' => 'Företagsnamnet kommer att ändras till.MatPickup Sweden AB',
                'personalNumber' => $request->personal_number,
                'endUserIp' => '204.137.185.2'
            ];

            // $sign = 'http://banksign-test.azurewebsites.net/api/sign';
            
            // $postData = [
            //     'apiUser' => 'matpickup',
            //     'password' => 'a3e1f629-4fe2-4e05-a57a-5bb07e5b1c21',
            //     'companyApiGuid' => 'acd75fdf-dc60-48db-b5cf-e08237f48e4c',
            //     'userVisibleData' => 'Företagsnamnet kommer att ändras till.MatPickup Sweden AB',
            //     'personalNumber' => $request->personal_number,
            //     'endUserIp' => '204.137.185.2'
            // ];
    
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $sign);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            
    
            $response = curl_exec ($ch);
            $err = curl_error($ch);  // if you need
            curl_close ($ch);

            $sign_response_data = json_decode($response);

            return response()->json(['data'=> $sign_response_data]);
            
           
        
    }

    public function bankIdLoginCollectStatus(Request $request)
    {
        $collect_status_url = 'https://api.banksignering.se/api/collectstatus';
            
        $postData = [
            'apiUser' => 'matpickup',
            'password' => '942cff35-78e0-43e4-bd5b-aa1a33a814ad',
            'companyApiGuid' => '7ca71b82-0978-44b7-8534-9e01f86e569d',
            'orderRef' => $request->orderRef
        ];

        // $collect_status_url = 'http://banksign-test.azurewebsites.net/api/collectstatus';
            
        // $postData = [
        //     'apiUser' => 'matpickup',
        //     'password' => 'a3e1f629-4fe2-4e05-a57a-5bb07e5b1c21',
        //     'companyApiGuid' => 'acd75fdf-dc60-48db-b5cf-e08237f48e4c',
        //     'orderRef' => $request->orderRef
        // ];
        for ($i=1; $i <= 30; $i++) {
            sleep(2);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $collect_status_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        

        $response = curl_exec ($ch);
        $err = curl_error($ch);  // if you need
        curl_close ($ch);

        $collect_status_data = json_decode($response);

        if($collect_status_data){
            if($collect_status_data->authResponse->Success == true && $collect_status_data->apiCallResponse->Success == true){

                
               
                    if($collect_status_data->apiCallResponse->StatusMessage == "complete"){
                        $is_user_exist = User::where('bankid_number',$collect_status_data->apiCallResponse->Response->CompletionData->user->personalNumber)->first();
                        if($is_user_exist){
                            Auth::login($is_user_exist,true);
                            return response()->json(["status" => 200]);
                        }else{
                            $user_slug = Str::slug($collect_status_data->apiCallResponse->Response->CompletionData->user->name);
                            $user = User::where('slug',$user_slug)->first();
            
                            if ($user) {
                                $slug= $collect_status_data->apiCallResponse->Response->CompletionData->user->name.Str::random(5);
                            }
                            else{
                                $slug = Str::slug($collect_status_data->apiCallResponse->Response->CompletionData->user->name);
                            }
                            $badge = Terms::where('type',5)->where('status',1)->where('count',1)->first();
            
                            $user = new User();
                            $user->role_id = 2;
                            $user->name = $collect_status_data->apiCallResponse->Response->CompletionData->user->name;
                            $user->slug = $slug;
                            $user->bankid_number = $collect_status_data->apiCallResponse->Response->CompletionData->user->personalNumber;
                            $user->email = null;
                            $user->password = null;
                            if (!empty($badge)) {
                               $user->badge_id = $badge->id; 
                            }
                            $user->save();
            
                            Auth::login($user,true);
            
                            return response()->json(["status" => 200]);
                        }
                    }else{
                        if($i==30){
                            return response()->json(["status" => 101]);
                        }
                    }
                    
               
            }else{
                return response()->json(["status" => 101]);
            }
        }else{
            return response()->json(["status" => 101]);
        }
    }

        // $user_slug = Str::slug($request->name);
        // $user = User::where('slug',$user_slug)->first();

        // if ($user) {
        //     $slug= $request->name.Str::random(5);
        // }
        // else{
        //     $slug = Str::slug($request->name);
        // }
        // $badge = Terms::where('type',5)->where('status',1)->where('count',1)->first();

        // $user = new User();
        // $user->role_id = 2;
        // $user->name = $request->name;
        // $user->slug = $slug;
        // $user->email = $request->email;
        // $user->password = null;
        // if (!empty($badge)) {
        //    $user->badge_id = $badge->id; 
        // }
        // $user->save();

        // Auth::login($user,true);

        // return redirect()->route('login');

        //return response()->json(['data'=> $collect_status_data]);
    }

    public function bankIdLoginApp(Request $request)
    {

            $sign = 'http://banksign-test.azurewebsites.net/api/sign';
            
            $postData = [
                'apiUser' => 'matpickup',
                'password' => 'a3e1f629-4fe2-4e05-a57a-5bb07e5b1c21',
                'companyApiGuid' => 'acd75fdf-dc60-48db-b5cf-e08237f48e4c',
                'userVisibleData' => '0y0nBk70',
                'personalNumber' => $request->personal_number,
                'endUserIp' => '204.137.185.2'
            ];
    
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, $sign);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
            
    
            $response = curl_exec ($ch);
            $err = curl_error($ch);  // if you need
            curl_close ($ch);

            $sign_response_data = json_decode($response);

            return response()->json(['data'=> $sign_response_data]);
            
           
        
    }

    public function bankIdLoginCollectStatusApp(Request $request)
    {
        $collect_status_url = 'http://banksign-test.azurewebsites.net/api/collectstatus';
            
        $postData = [
            'apiUser' => 'matpickup',
            'password' => 'a3e1f629-4fe2-4e05-a57a-5bb07e5b1c21',
            'companyApiGuid' => 'acd75fdf-dc60-48db-b5cf-e08237f48e4c',
            'orderRef' => $request->orderRef
        ];

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $collect_status_url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        

        $response = curl_exec ($ch);
        $err = curl_error($ch);  // if you need
        curl_close ($ch);

        $collect_status_data = json_decode($response);

        if($collect_status_data){
            if($collect_status_data->authResponse->Success == true && $collect_status_data->apiCallResponse->Success == true){
                $is_user_exist = User::where('email',$collect_status_data->apiCallResponse->Response->CompletionData->user->personalNumber)->first();
                if($is_user_exist){
                    Auth::login($is_user_exist,true);
                    return response()->json(["status" => 200]);
                }else{
                    $user_slug = Str::slug($collect_status_data->apiCallResponse->Response->CompletionData->user->name);
                    $user = User::where('slug',$user_slug)->first();
    
                    if ($user) {
                        $slug= $collect_status_data->apiCallResponse->Response->CompletionData->user->name.Str::random(5);
                    }
                    else{
                        $slug = Str::slug($collect_status_data->apiCallResponse->Response->CompletionData->user->name);
                    }
                    $badge = Terms::where('type',5)->where('status',1)->where('count',1)->first();
    
                    $user = new User();
                    $user->role_id = 2;
                    $user->name = $collect_status_data->apiCallResponse->Response->CompletionData->user->name;
                    $user->slug = $slug;
                    $user->email = $collect_status_data->apiCallResponse->Response->CompletionData->user->personalNumber;
                    $user->password = null;
                    if (!empty($badge)) {
                       $user->badge_id = $badge->id; 
                    }
                    $user->save();
    
                    Auth::login($user,true);
    
                    return response()->json(["status" => 200]);
                }
            }else{
                return response()->json(["status" => 101]);
            }
        }else{
            return response()->json(["status" => 101]);
        }
        
    }



}