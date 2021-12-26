<?php 

namespace Amcoders\Plugin\shop\http\controllers;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Auth;
use App\Media;
use App\Terms;
use App\Meta;
use App\PostCategory;
use App\Productmeta;
use App\Addon;
use App\Shopday;
use App\Location;
use App\Usermeta;
use App\Usercategory;
use App\User;
use App\Onesignal;
use App\Options;
use File;
use App\Category;
use Illuminate\Support\Facades\Storage;
use Excel;
/**
 * 
 */
class ImportExcelController extends controller
{
	

    public function index()
    {
        return view('plugin::products.import_view');
    }

}