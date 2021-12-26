<?php 

namespace Amcoders\Theme\khana\http\controllers\Author;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Order;
use Auth;
use App\Address;
use DB;
use App\Terms;
/**
 * 
 */
class DashboardController extends controller
{
	public function dashboard()
	{
		$address_count = Address::groupBy('city')
		->select('addresses.*', DB::raw('count(*) as total'))->pluck("total","city");

		// return response()->json($address_count['Karachi']);
		// $addresses = Address::where('user_id',Auth::User()->id)->get();

		// $design_id = 'Karachi';
		// $list_desings_ids = array('Karachi','hh','sss');

		// if(in_array($design_id, $list_desings_ids))
		// {
		// return "Yes, design_id: $design_id exits in array";

		// }
		$orders = Order::where('user_id',Auth::User()->id)->orderBy('id','DESC')->with('orderlist')->get();
		$addresses = Address::where('user_id',Auth::User()->id)->get();

		$cities = Terms::where("type", 2)->pluck('title')->toArray();
		$cities = array_map('strtolower', $cities);
		
		return view('theme::frontend.myaccount')->with("orders", $orders)->with("addresses", $addresses)->with("address_count", $address_count)->with("cities", $cities);
	}
	
	
    public function storeAddress(Request $request)
    {
        $validator = \Validator::make($request->all(), [
            'address_name' => 'required',
            'city' => 'required',
            'address' => 'required'
        ]);

        $address = new Address();
        $address->user_id = Auth::User()->id;
        $address->name = $request->address_name;
        $address->city = $request->city;
        $address->address = $request->address;
        $address->save();
		return back();
        

    }
}