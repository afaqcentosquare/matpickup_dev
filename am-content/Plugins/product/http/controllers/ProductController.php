<?php

namespace Amcoders\Plugin\product\http\controllers;

use App\Category;
use App\Http\Controllers\Controller;
use App\Imports\ProductsImport;
use App\Meta;
use App\PostCategory;
use App\Productmeta;
use App\Terms;
use App\User;
use Auth;
use Excel;
use Exception;
use File;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ProductController extends controller
{

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if (!Auth()->user()->can('product.list')) {
            return abort(401);
        }
        if (!empty($request->src)) {
            $src = $request->src;
            $posts = Terms::with('preview', 'price', 'user')->withCount('order')->where('type', 6)->where($request->type, $request->src)->latest()->paginate(30);
            // return response()->json($posts);
            return view('plugin::admin.products', compact('posts', 'src'));
        }
        $posts = Terms::with('preview', 'price', 'user')->withCount('order')->where('type', 6)->latest()->paginate(30);
        // return response()->json($posts);

        return view('plugin::admin.products', compact('posts'));
    }

    public function checkProductAvailabilty(Request $request)
    {
        if (!empty($request->store)) {
            if ($request->check_availability) {
                try {
                    $data = Excel::toCollection(new ProductsImport, $request->file('file'));

                    if ($data->count() > 0) {
                        $products_id = array();
                        foreach ($data->toArray() as $key => $value) {

                            foreach ($value as $row) {

                                if ($row[0] != "ID" && $row[1] != "ProductTitle" && $row[1] != null &&
                                    $row[2] != "Price" && $row[2] != null && $row[2] != "Price" && $row[3] != null &&
                                    $row[4] != "Description" && $row[4] != null && $row[5] != "Image" && $row[5] != null && $row[6] != "Page URL") {

                                    $is_available = Terms::where("title", $row[1])->where("auth_id", $request->store)
                                        ->join("product_meta", "product_meta.term_id", "terms.id")
                                        ->where("product_meta.price", $row[2])
                                        ->first();
                                    if ($is_available) {
                                        $products_id[] = $is_available->id;
                                    }

                                }

                            }

                        }

                        $posts = Terms::whereIn("id", $products_id)->with('preview', 'price', 'user')->withCount('order')->where('type', 6)->latest()->paginate(30);
                        $users = User::select('id', 'name')->where('role_id', 3)->where('status', '!=', 'pending')->with("resturentlocationwithcity")->get();
                        return view('plugin::admin.product_availability', compact('posts'))->with("users", $users)->with("table_type", "availability");

                    } else {
                        $posts = Terms::where("id", 0)->with('preview', 'price', 'user')->withCount('order')->where('type', 6)->latest()->paginate(30);
                        $users = User::select('id', 'name')->where('role_id', 3)->where('status', '!=', 'pending')->with("resturentlocationwithcity")->get();
                        return view('plugin::admin.product_availability', compact('posts'))->with("users", $users)->with("table_type", "availability");
                    }
                } catch (Exception $e) {
                    return back()->withError($e->getMessage())->withInput();
                }
            }

            if ($request->check_unavailability) {
                try {
                    $data = Excel::toCollection(new ProductsImport, $request->file('file'));

                    if ($data->count() > 0) {
                        $posts = array();
                        foreach ($data->toArray() as $key => $value) {

                            foreach ($value as $row) {

                                if ($row[0] != "ID" && $row[1] != "ProductTitle" && $row[1] != null &&
                                    $row[2] != "Price" && $row[2] != null && $row[2] != "Price" && $row[3] != null &&
                                    $row[4] != "Description" && $row[4] != null && $row[5] != "Image" && $row[5] != null && $row[6] != "Page URL") {

                                    $is_available = Terms::where("title", $row[1])->where("auth_id", $request->store)
                                        ->join("product_meta", "product_meta.term_id", "terms.id")
                                        ->where("product_meta.price", $row[2])
                                        ->first();
                                    if (!$is_available) {
                                        $posts[] = [
                                            "store_id" => $request->store,
                                            "id" => $row[0],
                                            "title" => $row[1],
                                            "price" => $row[2],
                                            "description" => str_replace("_x000D_", "</br>", $row[4]),
                                            "image" => $row[5],
                                        ];
                                    }

                                }

                            }

                        }

                        $users = User::select('id', 'name')->where('role_id', 3)->where('status', '!=', 'pending')->with("resturentlocationwithcity")->get();
                        return view('plugin::admin.product_availability', compact('posts'))->with("users", $users)->with("table_type", "unavailability");

                    } else {
                        $posts = Terms::where("id", 0)->with('preview', 'price', 'user')->withCount('order')->where('type', 6)->latest()->paginate(30);
                        $users = User::select('id', 'name')->where('role_id', 3)->where('status', '!=', 'pending')->with("resturentlocationwithcity")->get();
                        return view('plugin::admin.product_availability', compact('posts'))->with("users", $users)->with("table_type", "availability");
                    }
                } catch (Exception $e) {
                    return back()->withError($e->getMessage())->withInput();
                }
            }

        } else {

            if ($request->upload_products) {

                try {

                    $count = 0;
                    foreach ($request->product_data as $key => $value) {
                        $product_details = explode("(--$$--)", $value);

                        $is_product_available = Terms::where("title", $product_details[1])->where("auth_id", $product_details[0])
                            ->join("product_meta", "product_meta.term_id", "terms.id")
                            ->where("product_meta.price", $product_details[3])
                            ->first();
                        if (!$is_product_available) {
                            $slug = Str::slug($product_details[1]);
                            if ($slug == '') {
                                $slug = str_replace(' ', '-', $product_details[1]);
                            }
                            $post = new Terms;
                            $post->title = $product_details[1];
                            $post->slug = $slug;
                            $post->type = 6;
                            $post->auth_id = $product_details[0];
                            $post->status = 1;
                            $post->save();

                            $post_meta = new Meta;
                            $post_meta->term_id = $post->id;
                            $post_meta->type = 'excerpt';
                            $post_meta->content = $product_details[2];
                            $post_meta->save();

                            $post_meta = new Meta;
                            $post_meta->term_id = $post->id;
                            $post_meta->type = 'preview';
                            $post_meta->content = $product_details[4];
                            $post_meta->save();

                            $product = new Productmeta;
                            $product->term_id = $post->id;
                            $product->price = $product_details[3];
                            $product->save();

                            $categories = Category::where('user_id', $product_details[0])->where("name", $row[6])->first();

                            if (!$categories) {
                                $category_slug = Str::slug($row[6]);
                                if ($category_slug == '') {
                                    $category_slug = str_replace(' ', '-', $row[6]);
                                }
                                $category = new Category;
                                $category->name = $row[6];
                                $category->avatar = null;
                                $category->slug = $category_slug;
                                $category->p_id = null;
                                $category->type = 1;
                                $category->user_id = $product_details[0];
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
                            $count++;
                        }
                    }

                    return redirect()->route("admin.check.product.availability")->withSuccess($count . " Products are uploaded Successfully")->withInput();

                } catch (Exception $e) {
                    return redirect()->route("admin.check.product.availability")->withError($e->getMessage())->withInput();
                }

            } else {
                $posts = Terms::where("id", 0)->with('preview', 'price', 'user')->withCount('order')->where('type', 6)->latest()->paginate(30);

                $users = User::select('id', 'name')->where('role_id', 3)->where('status', '!=', 'pending')->with("resturentlocationwithcity")->get();

                return view('plugin::admin.product_availability', compact('posts'))->with("users", $users)->with("table_type", "availability");
            }

        }

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request)
    {
        if (!Auth()->user()->can('product.delete')) {
            return abort(401);
        }
        if ($request->status == 'delete') {
            if ($request->ids) {
                foreach ($request->ids as $id) {
                    Terms::destroy($id);
                }
            }
        }

        return response()->json('Product Removed');

    }

    public function importExcelView()
    {

        $users = User::select('id', 'name')->where('role_id', 3)->where('status', '!=', 'pending')->with("resturentlocationwithcity")->get();
        return view('plugin::admin.import_view')->with("users", $users);
    }

    public function importExcelData(Request $request)
    {

        $this->validate($request, [
            'file' => 'required|mimes:xls,xlsx',
            'store' => 'required',
        ]);

        try {

            $data = Excel::toCollection(new ProductsImport, $request->file('file'));

            //  return response()->json($row);
            //    $data = Excel::import($path)->get();

            if ($data->count() > 0) {
                foreach ($data->toArray() as $key => $value) {

                    foreach ($value as $row) {

                        if ($row[0] != "ID" && $row[1] != "ProductTitle" && $row[1] != null &&
                            $row[2] != "Price" && $row[2] != null && $row[2] != "Price" && $row[3] != null &&
                            $row[4] != "Description" && $row[4] != null && $row[5] != "Image" && $row[5] != null && $row[6] != "Page URL") {
                            $category_products = Category::where("user_id", $request->store)->where("type", 1)->where("name", $row[6])->first();

                            if ($category_products) {
                                $post_category_ids = PostCategory::where("category_id", $category_products->id)->pluck("term_id");

                                Terms::whereIn("id", $post_category_ids)->delete();
                                PostCategory::where("category_id", $category_products->id)->delete();
                                Category::where("user_id", $request->store)->where("type", 1)->where("name", $row[6])->delete();
                            }
                            break;
                        }

                    }

                    foreach ($value as $row) {
                        if ($row[0] != "ID" && $row[1] != "ProductTitle" && $row[1] != null &&
                            $row[2] != "Price" && $row[2] != null && $row[2] != "Price" && $row[3] != null &&
                            $row[4] != "Description" && $row[4] != null && $row[5] != "Image" && $row[5] != null && $row[6] != "Page URL") {

                            $is_term_available = Terms::where("title", $row[1])->where("auth_id", $request->store)
                                ->join("post_category", "post_category.term_id", "terms.id")
                                ->join("categories", "categories.id", "post_category.category_id")
                                ->where("categories.name", $row[6])
                                ->join("product_meta", "product_meta.term_id", "terms.id")

                                ->where("product_meta.price", $row[2])
                                ->first();

                            if (!$is_term_available) {

                                $slug = Str::slug($row[1]);
                                if ($slug == '') {
                                    $slug = str_replace(' ', '-', $row[1]);
                                }

                                $post = new Terms;
                                $post->title = $row[1];
                                $post->slug = $slug;
                                $post->type = 6;
                                $post->auth_id = $request->store;
                                $post->status = 1;
                                $post->save();

                                $post_meta = new Meta;
                                $post_meta->term_id = $post->id;
                                $post_meta->type = 'excerpt';
                                $post_meta->content = str_replace("_x000D_", "</br>", $row[4]);
                                $post_meta->save();

                                $post_meta = new Meta;
                                $post_meta->term_id = $post->id;
                                $post_meta->type = 'preview';
                                $post_meta->content = $row[5];
                                $post_meta->save();

                                $product = new Productmeta;
                                $product->term_id = $post->id;
                                $product->price = $row[2];
                                $product->save();

                                $categories = Category::where('user_id', $request->store)->where("name", $row[6])->first();

                                if (!$categories) {
                                    $category_slug = Str::slug($row[6]);
                                    if ($category_slug == '') {
                                        $category_slug = str_replace(' ', '-', $row[6]);
                                    }
                                    $category = new Category;
                                    $category->name = $row[6];
                                    $category->avatar = null;
                                    $category->slug = $category_slug;
                                    $category->p_id = null;
                                    $category->type = 1;
                                    $category->user_id = $request->store;
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

                        // if ($request->category) {

                        //  foreach ($request->category as $cat_row) {

                        //         $cat= new PostCategory;
                        //         $cat->term_id=$post->id;
                        //         $cat->category_id=$cat_row;
                        //         $cat->save();

                        //  }
                        // }

                    }
                }

                return response()->json(["Data Insert Successfully"]);

                // if(!empty($insert_data))
                // {
                //  DB::table('tbl_customer')->insert($insert_data);
                // }
            } else {
                return response()->json(["Failed"]);
            }
        } catch (Exception $e) {
            return response()->json([$e->getMessage()]);
        }

    }

    public function test_jul()
    {
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
       //return;
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

        return response()->json(["Data Insert Successfully"]);

    }

    public function test_inspiration()
    {
        $user_id = 26;
        $category_id = 2409;
        $category_name = "Inspiration";
        $category_slug = Str::slug($category_name);
        if ($category_slug == '') {
            $category_slug = str_replace(' ', '-', $category_name);
        }
        $category_slug_url = $category_slug . '-' . '_' . $category_id;

        $category_products_url = 'https://handla.ica.se/api/content/v1/collection/customer-type/B2C/store/maxi-ica-stormarknad-flemingsberg-id_15973/products?categories=' . $category_slug_url;

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
        foreach ($category_products_response->items as $category_item) {
            if ($category_item->type == 'product') {
                $products_url = 'https://handla.ica.se/api/content/v1/collection/customer-type/B2C/store/maxi-ica-stormarknad-flemingsberg-id_15973/products-data?skus=' . $category_item->id;

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
                    $product->price = (int) $products_response[0]['price'];
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

        return response()->json(["Data Insert Successfully"]);

    }

    public function test_kott()
    {
        $user_id = 26;
        $category_id = 1;
        $category_name = "Kött, fågel & fisk";
        $category_slug = Str::slug($category_name);
        if ($category_slug == '') {
            $category_slug = str_replace(' ', '-', $category_name);
        }
        $category_slug_url = $category_slug . '-' . '_' . $category_id;

        $category_products_url = 'https://handla.ica.se/api/content/v1/collection/customer-type/B2C/store/maxi-ica-stormarknad-flemingsberg-id_15973/products?categories=' . $category_slug_url;

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
        foreach ($category_products_response->items as $category_item) {
            if ($category_item->type == 'product') {
                $products_url = 'https://handla.ica.se/api/content/v1/collection/customer-type/B2C/store/maxi-ica-stormarknad-flemingsberg-id_15973/products-data?skus=' . $category_item->id;

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
                    $product->price = (int) $products_response[0]['price'];
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

        return response()->json(["Data Insert Successfully"]);

    }

    public function test_vegetariskt()
    {
        $user_id = 26;
        $category_id = 2233;
        $category_name = "Vegetariskt";
        $category_slug = Str::slug($category_name);
        if ($category_slug == '') {
            $category_slug = str_replace(' ', '-', $category_name);
        }
        $category_slug_url = $category_slug . '-' . '_' . $category_id;

        $category_products_url = 'https://handla.ica.se/api/content/v1/collection/customer-type/B2C/store/maxi-ica-stormarknad-flemingsberg-id_15973/products?categories=' . $category_slug_url;

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
        foreach ($category_products_response->items as $category_item) {
            if ($category_item->type == 'product') {
                $products_url = 'https://handla.ica.se/api/content/v1/collection/customer-type/B2C/store/maxi-ica-stormarknad-flemingsberg-id_15973/products-data?skus=' . $category_item->id;

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
                    $product->price = (int) $products_response[0]['price'];
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

        return response()->json(["Data Insert Successfully"]);

    }

    public function test_mejeri()
    {
        $user_id = 26;
        $category_id = 256;
        $category_name = "Mejeri, ost & ägg";
        $category_slug = Str::slug($category_name);
        if ($category_slug == '') {
            $category_slug = str_replace(' ', '-', $category_name);
        }
        $category_slug_url = $category_slug . '-' . '_' . $category_id;

        $category_products_url = 'https://handla.ica.se/api/content/v1/collection/customer-type/B2C/store/maxi-ica-stormarknad-flemingsberg-id_15973/products?categories=' . $category_slug_url;

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
        foreach ($category_products_response->items as $category_item) {
            if ($category_item->type == 'product') {
                $products_url = 'https://handla.ica.se/api/content/v1/collection/customer-type/B2C/store/maxi-ica-stormarknad-flemingsberg-id_15973/products-data?skus=' . $category_item->id;

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
                    $product->price = (int) $products_response[0]['price'];
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

        return response()->json(["Data Insert Successfully"]);

    }

    public function test_skafferi()
    {
        $user_id = 26;
        $category_id = 939;
        $category_name = "Skafferi";
        $category_slug = Str::slug($category_name);
        if ($category_slug == '') {
            $category_slug = str_replace(' ', '-', $category_name);
        }
        $category_slug_url = $category_slug . '-' . '_' . $category_id;

        $category_products_url = 'https://handla.ica.se/api/content/v1/collection/customer-type/B2C/store/maxi-ica-stormarknad-flemingsberg-id_15973/products?categories=' . $category_slug_url;

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
        foreach ($category_products_response->items as $category_item) {
            if ($category_item->type == 'product') {
                $products_url = 'https://handla.ica.se/api/content/v1/collection/customer-type/B2C/store/maxi-ica-stormarknad-flemingsberg-id_15973/products-data?skus=' . $category_item->id;

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
                    $product->price = (int) $products_response[0]['price'];
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

        return response()->json(["Data Insert Successfully"]);

    }

    public function test_frukt()
    {
        $user_id = 26;
        $category_id = 627;
        $category_name = "Frukt & grönt";
        $category_slug = Str::slug($category_name);
        if ($category_slug == '') {
            $category_slug = str_replace(' ', '-', $category_name);
        }
        $category_slug_url = $category_slug . '-' . '_' . $category_id;

        $category_products_url = 'https://handla.ica.se/api/content/v1/collection/customer-type/B2C/store/maxi-ica-stormarknad-flemingsberg-id_15973/products?categories=' . $category_slug_url;

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
        foreach ($category_products_response->items as $category_item) {
            if ($category_item->type == 'product') {
                $products_url = 'https://handla.ica.se/api/content/v1/collection/customer-type/B2C/store/maxi-ica-stormarknad-flemingsberg-id_15973/products-data?skus=' . $category_item->id;

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
                    $product->price = (int) $products_response[0]['price'];
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

        return response()->json(["Data Insert Successfully"]);

    }

    public function test_barn()
    {
        $user_id = 26;
        $category_id = 434;
        $category_name = "Barn";
        $category_slug = Str::slug($category_name);
        if ($category_slug == '') {
            $category_slug = str_replace(' ', '-', $category_name);
        }
        $category_slug_url = $category_slug . '-' . '_' . $category_id;

        $category_products_url = 'https://handla.ica.se/api/content/v1/collection/customer-type/B2C/store/maxi-ica-stormarknad-flemingsberg-id_15973/products?categories=' . $category_slug_url;

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
        foreach ($category_products_response->items as $category_item) {
            if ($category_item->type == 'product') {
                $products_url = 'https://handla.ica.se/api/content/v1/collection/customer-type/B2C/store/maxi-ica-stormarknad-flemingsberg-id_15973/products-data?skus=' . $category_item->id;

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
                    $product->price = (int) $products_response[0]['price'];
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

        return response()->json(["Data Insert Successfully"]);

    }

    public function test_brod()
    {
        $user_id = 26;
        $category_id = 358;
        $category_name = "Bröd & kakor";
        $category_slug = Str::slug($category_name);
        if ($category_slug == '') {
            $category_slug = str_replace(' ', '-', $category_name);
        }
        $category_slug_url = $category_slug . '-' . '_' . $category_id;

        $category_products_url = 'https://handla.ica.se/api/content/v1/collection/customer-type/B2C/store/maxi-ica-stormarknad-flemingsberg-id_15973/products?categories=' . $category_slug_url;

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
        foreach ($category_products_response->items as $category_item) {
            if ($category_item->type == 'product') {
                $products_url = 'https://handla.ica.se/api/content/v1/collection/customer-type/B2C/store/maxi-ica-stormarknad-flemingsberg-id_15973/products-data?skus=' . $category_item->id;

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
                    $product->price = (int) $products_response[0]['price'];
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

        return response()->json(["Data Insert Successfully"]);

    }

    public function test_fryst()
    {
        $user_id = 26;
        $category_id = 628;
        $category_name = "Fryst";
        $category_slug = Str::slug($category_name);
        if ($category_slug == '') {
            $category_slug = str_replace(' ', '-', $category_name);
        }
        $category_slug_url = $category_slug . '-' . '_' . $category_id;

        $category_products_url = 'https://handla.ica.se/api/content/v1/collection/customer-type/B2C/store/maxi-ica-stormarknad-flemingsberg-id_15973/products?categories=' . $category_slug_url;

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
        foreach ($category_products_response->items as $category_item) {
            if ($category_item->type == 'product') {
                $products_url = 'https://handla.ica.se/api/content/v1/collection/customer-type/B2C/store/maxi-ica-stormarknad-flemingsberg-id_15973/products-data?skus=' . $category_item->id;

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
                    $product->price = (int) $products_response[0]['price'];
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

        return response()->json(["Data Insert Successfully"]);

    }

    public function test_fardigmat()
    {
        $user_id = 26;
        $category_id = 208;
        $category_name = "Färdigmat";
        $category_slug = Str::slug($category_name);
        if ($category_slug == '') {
            $category_slug = str_replace(' ', '-', $category_name);
        }
        $category_slug_url = $category_slug . '-' . '_' . $category_id;

        $category_products_url = 'https://handla.ica.se/api/content/v1/collection/customer-type/B2C/store/maxi-ica-stormarknad-flemingsberg-id_15973/products?categories=' . $category_slug_url;

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
        foreach ($category_products_response->items as $category_item) {
            if ($category_item->type == 'product') {
                $products_url = 'https://handla.ica.se/api/content/v1/collection/customer-type/B2C/store/maxi-ica-stormarknad-flemingsberg-id_15973/products-data?skus=' . $category_item->id;

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
                    $product->price = (int) $products_response[0]['price'];
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

        return response()->json(["Data Insert Successfully"]);

    }

    public function test_dryck()
    {
        $user_id = 26;
        $category_id = 306;
        $category_name = "Dryck";
        $category_slug = Str::slug($category_name);
        if ($category_slug == '') {
            $category_slug = str_replace(' ', '-', $category_name);
        }
        $category_slug_url = $category_slug . '-' . '_' . $category_id;

        $category_products_url = 'https://handla.ica.se/api/content/v1/collection/customer-type/B2C/store/maxi-ica-stormarknad-flemingsberg-id_15973/products?categories=' . $category_slug_url;

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
        foreach ($category_products_response->items as $category_item) {
            if ($category_item->type == 'product') {
                $products_url = 'https://handla.ica.se/api/content/v1/collection/customer-type/B2C/store/maxi-ica-stormarknad-flemingsberg-id_15973/products-data?skus=' . $category_item->id;

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
                    $product->price = (int) $products_response[0]['price'];
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

        return response()->json(["Data Insert Successfully"]);

    }

    public function test_glass()
    {
        $user_id = 26;
        $category_id = 399;
        $category_name = "Glass, godis & snacks";
        $category_slug = Str::slug($category_name);
        if ($category_slug == '') {
            $category_slug = str_replace(' ', '-', $category_name);
        }
        $category_slug_url = $category_slug . '-' . '_' . $category_id;

        $category_products_url = 'https://handla.ica.se/api/content/v1/collection/customer-type/B2C/store/maxi-ica-stormarknad-flemingsberg-id_15973/products?categories=' . $category_slug_url;

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
        foreach ($category_products_response->items as $category_item) {
            if ($category_item->type == 'product') {
                $products_url = 'https://handla.ica.se/api/content/v1/collection/customer-type/B2C/store/maxi-ica-stormarknad-flemingsberg-id_15973/products-data?skus=' . $category_item->id;

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
                    $product->price = (int) $products_response[0]['price'];
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

        return response()->json(["Data Insert Successfully"]);

    }

    public function test_stad()
    {
        $user_id = 26;
        $category_id = 515;
        $category_name = "Städ & disk";
        $category_slug = Str::slug($category_name);
        if ($category_slug == '') {
            $category_slug = str_replace(' ', '-', $category_name);
        }
        $category_slug_url = $category_slug . '-' . '_' . $category_id;

        $category_products_url = 'https://handla.ica.se/api/content/v1/collection/customer-type/B2C/store/maxi-ica-stormarknad-flemingsberg-id_15973/products?categories=' . $category_slug_url;

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
        foreach ($category_products_response->items as $category_item) {
            if ($category_item->type == 'product') {
                $products_url = 'https://handla.ica.se/api/content/v1/collection/customer-type/B2C/store/maxi-ica-stormarknad-flemingsberg-id_15973/products-data?skus=' . $category_item->id;

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
                    $product->price = (int) $products_response[0]['price'];
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

        return response()->json(["Data Insert Successfully"]);

    }

    public function test_halsa()
    {
        $user_id = 26;
        $category_id = 629;
        $category_name = "Hälsa & skönhet";
        $category_slug = Str::slug($category_name);
        if ($category_slug == '') {
            $category_slug = str_replace(' ', '-', $category_name);
        }
        $category_slug_url = $category_slug . '-' . '_' . $category_id;

        $category_products_url = 'https://handla.ica.se/api/content/v1/collection/customer-type/B2C/store/maxi-ica-stormarknad-flemingsberg-id_15973/products?categories=' . $category_slug_url;

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
        foreach ($category_products_response->items as $category_item) {
            if ($category_item->type == 'product') {
                $products_url = 'https://handla.ica.se/api/content/v1/collection/customer-type/B2C/store/maxi-ica-stormarknad-flemingsberg-id_15973/products-data?skus=' . $category_item->id;

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
                    $product->price = (int) $products_response[0]['price'];
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

        return response()->json(["Data Insert Successfully"]);

    }

    public function test_djur()
    {
        $user_id = 26;
        $category_id = 491;
        $category_name = "Djur";
        $category_slug = Str::slug($category_name);
        if ($category_slug == '') {
            $category_slug = str_replace(' ', '-', $category_name);
        }
        $category_slug_url = $category_slug . '-' . '_' . $category_id;

        $category_products_url = 'https://handla.ica.se/api/content/v1/collection/customer-type/B2C/store/maxi-ica-stormarknad-flemingsberg-id_15973/products?categories=' . $category_slug_url;

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
        foreach ($category_products_response->items as $category_item) {
            if ($category_item->type == 'product') {
                $products_url = 'https://handla.ica.se/api/content/v1/collection/customer-type/B2C/store/maxi-ica-stormarknad-flemingsberg-id_15973/products-data?skus=' . $category_item->id;

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
                    $product->price = (int) $products_response[0]['price'];
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

        return response()->json(["Data Insert Successfully"]);

    }

    public function test_blommor()
    {
        $user_id = 26;
        $category_id = 2371;
        $category_name = "Blommor";
        $category_slug = Str::slug($category_name);
        if ($category_slug == '') {
            $category_slug = str_replace(' ', '-', $category_name);
        }
        $category_slug_url = $category_slug . '-' . '_' . $category_id;

        $category_products_url = 'https://handla.ica.se/api/content/v1/collection/customer-type/B2C/store/maxi-ica-stormarknad-flemingsberg-id_15973/products?categories=' . $category_slug_url;

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
        foreach ($category_products_response->items as $category_item) {
            if ($category_item->type == 'product') {
                $products_url = 'https://handla.ica.se/api/content/v1/collection/customer-type/B2C/store/maxi-ica-stormarknad-flemingsberg-id_15973/products-data?skus=' . $category_item->id;

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
                    $product->price = (int) $products_response[0]['price'];
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

        return response()->json(["Data Insert Successfully"]);

    }

    public function test_kok()
    {
        $user_id = 26;
        $category_id = 557;
        $category_name = "Kök";
        $category_slug = Str::slug($category_name);
        if ($category_slug == '') {
            $category_slug = str_replace(' ', '-', $category_name);
        }
        $category_slug_url = $category_slug . '-' . '_' . $category_id;

        $category_products_url = 'https://handla.ica.se/api/content/v1/collection/customer-type/B2C/store/maxi-ica-stormarknad-flemingsberg-id_15973/products?categories=' . $category_slug_url;

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
        foreach ($category_products_response->items as $category_item) {
            if ($category_item->type == 'product') {
                $products_url = 'https://handla.ica.se/api/content/v1/collection/customer-type/B2C/store/maxi-ica-stormarknad-flemingsberg-id_15973/products-data?skus=' . $category_item->id;

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
                    $product->price = (int) $products_response[0]['price'];
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

        return response()->json(["Data Insert Successfully"]);

    }

    public function test_hem()
    {
        $user_id = 26;
        $category_id = 2514;
        $category_name = "Hem & Inredning";
        $category_slug = Str::slug($category_name);
        if ($category_slug == '') {
            $category_slug = str_replace(' ', '-', $category_name);
        }
        $category_slug_url = $category_slug . '-' . '_' . $category_id;

        $category_products_url = 'https://handla.ica.se/api/content/v1/collection/customer-type/B2C/store/maxi-ica-stormarknad-flemingsberg-id_15973/products?categories=' . $category_slug_url;

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
        foreach ($category_products_response->items as $category_item) {
            if ($category_item->type == 'product') {
                $products_url = 'https://handla.ica.se/api/content/v1/collection/customer-type/B2C/store/maxi-ica-stormarknad-flemingsberg-id_15973/products-data?skus=' . $category_item->id;

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
                    $product->price = (int) $products_response[0]['price'];
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

        return response()->json(["Data Insert Successfully"]);

    }

    public function test_fritid()
    {
        $user_id = 26;
        $category_id = 2515;
        $category_name = "Fritid & Trädgård";
        $category_slug = Str::slug($category_name);
        if ($category_slug == '') {
            $category_slug = str_replace(' ', '-', $category_name);
        }
        $category_slug_url = $category_slug . '-' . '_' . $category_id;

        $category_products_url = 'https://handla.ica.se/api/content/v1/collection/customer-type/B2C/store/maxi-ica-stormarknad-flemingsberg-id_15973/products?categories=' . $category_slug_url;

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
        foreach ($category_products_response->items as $category_item) {
            if ($category_item->type == 'product') {
                $products_url = 'https://handla.ica.se/api/content/v1/collection/customer-type/B2C/store/maxi-ica-stormarknad-flemingsberg-id_15973/products-data?skus=' . $category_item->id;

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
                    $product->price = (int) $products_response[0]['price'];
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

        return response()->json(["Data Insert Successfully"]);

    }

    public function test_receptfria()
    {
        $user_id = 26;
        $category_id = 860;
        $category_name = "Receptfria läkemedel";
        $category_slug = Str::slug($category_name);
        if ($category_slug == '') {
            $category_slug = str_replace(' ', '-', $category_name);
        }
        $category_slug_url = $category_slug . '-' . '_' . $category_id;

        $category_products_url = 'https://handla.ica.se/api/content/v1/collection/customer-type/B2C/store/maxi-ica-stormarknad-flemingsberg-id_15973/products?categories=' . $category_slug_url;

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
        foreach ($category_products_response->items as $category_item) {
            if ($category_item->type == 'product') {
                $products_url = 'https://handla.ica.se/api/content/v1/collection/customer-type/B2C/store/maxi-ica-stormarknad-flemingsberg-id_15973/products-data?skus=' . $category_item->id;

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
                    $product->price = (int) $products_response[0]['price'];
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

        return response()->json(["Data Insert Successfully"]);

    }

    public function test_kiosk()
    {
        $user_id = 26;
        $category_id = 1627;
        $category_name = "Kiosk";
        $category_slug = Str::slug($category_name);
        if ($category_slug == '') {
            $category_slug = str_replace(' ', '-', $category_name);
        }
        $category_slug_url = $category_slug . '-' . '_' . $category_id;

        $category_products_url = 'https://handla.ica.se/api/content/v1/collection/customer-type/B2C/store/maxi-ica-stormarknad-flemingsberg-id_15973/products?categories=' . $category_slug_url;

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
        foreach ($category_products_response->items as $category_item) {
            if ($category_item->type == 'product') {
                $products_url = 'https://handla.ica.se/api/content/v1/collection/customer-type/B2C/store/maxi-ica-stormarknad-flemingsberg-id_15973/products-data?skus=' . $category_item->id;

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
                    $product->price = (int) $products_response[0]['price'];
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

        return response()->json(["Data Insert Successfully"]);

    }

    // public function test()
    // {
    //     $category_url = 'https://handla.ica.se/api/product-info/v1/store/15973/category/catalog80002';

    //     $category_ch = curl_init();
    //     curl_setopt($category_ch, CURLOPT_URL, $category_url);
    //     curl_setopt($category_ch, CURLOPT_POST, 0);
    //     curl_setopt($category_ch, CURLOPT_RETURNTRANSFER, true);

    //     $category_response = curl_exec($category_ch);
    //     $err = curl_error($category_ch); //if you need
    //     curl_close($category_ch);
    //     $category_response = json_decode($category_response);
    //     $store_products = array();
    //     foreach ($category_response->childCategories as $childcategories) {
    //         $category_slug = Str::slug($childcategories->name);
    //         if ($category_slug == '') {
    //             $category_slug = str_replace(' ', '-', $childcategories->name);
    //         }

    //         $category_slug_url = $category_slug.'-'.'_'.$childcategories->categoryId;

    //         $category_products_url = 'https://handla.ica.se/api/content/v1/collection/customer-type/B2C/store/maxi-ica-stormarknad-flemingsberg-id_15973/products?categories='.$category_slug_url;

    //         $category_products_ch = curl_init();
    //         curl_setopt($category_products_ch, CURLOPT_URL, $category_products_url);
    //         curl_setopt($category_products_ch, CURLOPT_POST, 0);
    //         curl_setopt($category_products_ch, CURLOPT_RETURNTRANSFER, true);

    //         $category_products_response = curl_exec($category_products_ch);
    //         $err = curl_error($category_products_ch); //if you need
    //         curl_close($category_products_ch);
    //         $category_products_response = json_decode($category_products_response);

    //         $category_products_array = array();

    //         $products = array();
    //         foreach ($category_products_response->items as $category_item) {
    //             if($category_item->type == 'product'){
    //                 $products_url = 'https://handla.ica.se/api/content/v1/collection/customer-type/B2C/store/maxi-ica-stormarknad-flemingsberg-id_15973/products-data?skus='.$category_item->id;

    //                 $products_ch = curl_init();
    //                 curl_setopt($products_ch, CURLOPT_URL, $products_url);
    //                 curl_setopt($products_ch, CURLOPT_POST, 0);
    //                 curl_setopt($products_ch, CURLOPT_RETURNTRANSFER, true);

    //                 $products_response = curl_exec($products_ch);
    //                 $err = curl_error($products_ch); //if you need
    //                 curl_close($products_ch);
    //                $products_response = json_decode($products_response, true);
    //                if($products_response){
    //                 $products[] = [
    //                     'product_name' => $products_response[0]['name'],
    //                     'product_price' => $products_response[0]['price'],
    //                 ];
    //                }

    //             }

    //         }

    //         $store_products[] = [
    //             'category_id' => $childcategories->categoryId,
    //             'category_name' => $childcategories->name,
    //             'products' => $products
    //         ];

    //         break;
    //     }

    //     return $store_products;
    // }
}
