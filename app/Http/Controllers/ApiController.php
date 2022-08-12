<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Auth;

use App\Visitor;
use App\Category;
use App\Service;
use App\Rate;
use App\Order;

class ApiController extends Controller
{
    //

    public function VisitorLogIn(Request $request)
    {

        //Validate Inputs
        $validate = Validator::make(request()->all(), [
            'VisMailI'=>"required",
            'VisPassI'=>'required'
        ]);
        if ($validate->fails()) {
            return response()->json([
                'success'=>false,
                'err'=>'1',
                'message'=>'ValidationErr'
                ],400);
        }

        //Authenticate By Mail
        if (!$token = Auth::guard('api')->attempt(array('email'=>$request->input('VisMailI'),'password'=>$request->input('VisPassI')))) 
        {
                 
            return response()->json([
               'success'=>false,
               'err'=>'0',
               'message' => 'UnauthorizedErr'
               ], 401);

        }
        else{

            //get Visitor 
            $getVisitor=Auth::guard('api')->user();
            return response()->json([
                'success'=>true,
                'data'=>$getVisitor,
                'token' => $token,
                'expires' => auth('api')->factory()->getTTL() * 60,
            ],200);
        }   

    }



    public function VisitorRegister(Request $request)
    {
        
        
        //Validate inputs 
        $validate = Validator::make(request()->all(), [
            'VisNameI'=>"required",
            'VisLastNameI'=>'required',
            'VisMailI'=>'required',
            'VisUserNameI'=>'required',
            'VisPassI'=>'required',
            'VisPass2I'=>'required',
            'VisPhoneI'=>'required',
            'VisCityI'=>'required',
            'VisAddressI'=>'required',
        ]);
        if ($validate->fails()) {
            return response()->json([
                   'success'=>false,
                   'err'=>'1',
                   'message'=>'ValidationErr'
            ],400);
        }

        //Check If Visitor  Username Available
        $CheckVisUser=Visitor::where('vis_username',$request->input('VisUserNameI'))->count();
        if($CheckVisUser > 0){
    
            return response()->json([
                    'success'=>false,
                    'err'=>'5',
                    'message'=>'UserNameUsedErr'
            ],400);
        }

        //Check If Visitor Email Available
        $CheckVisMail=Visitor::where('email',$request->input('VisMailI'))->count();
        if($CheckVisMail > 0){
    
        return response()->json([
            'success'=>false,
            'err'=>'4',
            'message'=>'MailUsedErr'
        ],400);
        }

        //Check If Password Match
        if(! $request->input('VisPassI') == $request->input('VisPass2I') ){

            return response()->json([
                'success'=>false,
                'err'=>'6',
                'message'=>'PassNotMatchErr'
            ],400);
        }

        //generate RestPass And Activation Token
        $RestPassToken= md5(rand(1, 10) . microtime());
        $ActivationToken=md5(rand(1, 12) . microtime());

        //Save Visitor 
        $SaveVis=new Visitor([
            'vis_name'=>$request->input('VisNameI'),
            'vis_last_name'=>$request->input('VisLastNameI'),
            'email'=>$request->input('VisMailI'),
            'vis_username'=>$request->input('VisUserNameI'),
            'vis_password'=>bcrypt($request->input('VisPassI')),
            'vis_Status'=>0,
            'vis_phone'=>$request->input('VisPhoneI'),
            'vis_city'=>$request->input('VisCityI'),
            'vis_address'=>$request->input('VisAddressI'),
            'vis_restpass_token'=>$RestPassToken,
            'vis_activation_token'=>$ActivationToken,
            'role'=>0,
        ]);
        $SaveVis->save();
        
    return response()->json([
        'success'=>true,
        'err'=>'7',
        'message'=>'VisitorSavedErr'
    ],201);

    }



    public function VisitorInfo()
    {
        
       //get Visitor
       $Visitor=Auth::guard('api')->user();

       return response()->json($Visitor, 200);

    }

    public function VisitorUpdate(Request $request)
    {

        //Validate inputs 
        $validate = Validator::make(request()->all(), [
            'VisNameI'=>"required",
            'VisLastNameI'=>'required',
            'VisMailI'=>'required',
            'VisUserNameI'=>'required',
            'VisPhoneI'=>'required',
            'VisCityI'=>'required',
            'VisAddressI'=>'required',
        ]);
        if ($validate->fails()) {
            return response()->json(['err',['err'=>'1','message'=>'ValidationErr']],400);
        }

        
        //get Visitor Info 
        $Visitor=Auth::guard('api')->user();
        $VisitorId=$Visitor['id'];

        //Find Visitor On DB
        $getVisitor=Visitor::find($VisitorId);
        if(empty($getVisitor)){
            
           return 'Somthing Wrong';
        }

        //Update Visitor
        $UpdateVisitor=$getVisitor->update([
            'vis_name'=>$request->input('VisNameI'),
            'vis_last_name'=>$request->input('VisLastNameI'),
            'email'=>$request->input('VisMailI'),
            'vis_username'=>$request->input('VisUserNameI'),
            'vis_phone'=>$request->input('VisPhoneI'),
            'vis_city'=>$request->input('VisCityI'),
            'vis_address'=>$request->input('VisAddressI')
        ]);
        
        return response()->json(['err',['err'=>'8','message'=>'VisitorUpdated']],200);

    }

    public function VisitorLogOut()
    {
        
        //Destroy Token
        Auth::guard('api')->logout();

        return response(200);

    }

    public function CategoryAll (Request $request,$limit,$SortKey,$SortType)
    {

        //Check Limit Value
        if($limit ==='null'){
            $limit=null;
        }

        //Check OrderBy Inputs
        if($SortKey !="null"){
            $getCategory=Category::limit($limit)->orderBy($SortKey, $SortType)->get();
        }
        else{
            $getCategory=Category::limit($limit)->get();
        }

        return response()->json($getCategory,200);

    }


    public function CategoryOne($CatId)
    {
        
        if(!empty($CatId)){

            //get Category
            $getCategory=Category::find($CatId);

            if(!empty($getCategory)){

                return response()->json($getCategory,200);

            }
            else{
                return response()->json(['err',['err'=>'1','message'=>'SWErr']],400);
            }
        }
        else{
            return response()->json(['err',['err'=>'1','message'=>'ValidationErr']],400);
        }
    }



    public function ServiceAll($limit,$SortKey,$SortType)
    {
        
        //Check Limit Value
        if($limit ==='null'){
            $limit=null;
        }

        //Check OrderBy Inputs
        if($SortKey !="null"){
            $getService=Service::limit($limit)->orderBy($SortKey, $SortType)->get();
        }
        else{
            $getService=Service::limit($limit)->get();
        }

        $getService->load('Category');
        $getService->append('RatesAvg');
        return response()->json($getService,200);

    }

    public function ServiceOne($ServiceId)
    {
        
        if(!empty($ServiceId)){

            //get Service
            $getService=Service::find($ServiceId);

            if(!empty($getService)){

                $getService->load('Category');
                $getService->append('RatesAvg');
    
                
                return response()->json($getService,200);

            }
            else{
                return response()->json(['err',['err'=>'1','message'=>'SWErr']],400);
            }
        }
        else{
            return response()->json(['err',['err'=>'1','message'=>'ValidationErr']],400);
        }

    }

    public function ServiceByCat($CatId,$limit,$SortType,$SortKey)
    {
        //Check Limit Value
        if($limit ==='null'){
            $limit=null;
        }

        if(!empty($CatId)){

            //Check OrderBy Inputs
            if($SortKey !="null"){
                $getService=Service::where('category_id',$CatId)->limit($limit)->orderBy($SortKey, $SortType)->get();
            }
            else{
                $getService=Service::where('category_id',$CatId)->limit($limit)->get();
            }
        
            return response()->json($getService,200);

        }
        else{

            $getService->load('Category');
            $getService->append('RatesAvg');
            return response()->json(['err',['err'=>'1','message'=>'ValidationErr']],400);

        }
    }

    public function SaveRate(Request $request)
    {
        

        //Validate inputs
        $validate = Validator::make(request()->all(), [
            'VisitorIdI'=>"required",
            'ServiceIdI'=>'required',
            'RateValueI'=>'required'

        ]);
        if ($validate->fails()) {
            return response()->json(['err',['err'=>'1','message'=>'ValidationErr']],400);
        }

        //Check Visitor
        $getVisitor=Visitor::find($request->input('VisitorIdI'));

        if(empty($getVisitor)){

            return response()->json(['err',['err'=>'1','message'=>'SWErr']],400);
        }

        //Check Service
        $getService=Service::find($request->input('ServiceIdI'));

        if(empty($getService)){

            return response()->json(['err',['err'=>'1','message'=>'SWErr']],400);
        }

        //Save Rate
        $SaveRate=new Rate([
            'visitor_id'=>$request->input('VisitorIdI'),
            'service_id'=>$request->input('ServiceIdI'),
            'rate_value'=>$request->input('RateValueI')
        ]);
        $SaveRate->save();
        
        return response()->json(['err',['err'=>'9','message'=>'RateSavedErr']],201);

    }

    public function SaveOrder(Request $request)
    {
        

        //validate iNPUTS
        $validate = Validator::make(request()->all(), [
            'VisitorIdI'=>"required",
            'ServiceIdI'=>'required',
        ]);
        if ($validate->fails()) {
            return response()->json(['err',['err'=>'1','message'=>'ValidationErr']],400);
        }

        //Check Visitor
        $getVisitor=Visitor::find($request->input('VisitorIdI'));

        if(empty($getVisitor)){

            return response()->json(['err',['err'=>'1','message'=>'SWErr']],400);
        }

        //Check Service
        $getService=Service::find($request->input('ServiceIdI'));

        if(empty($getService)){

            return response()->json(['err',['err'=>'1','message'=>'SWErr']],400);
        }

        //get Service Price
        $ServicePrice=$getService['ser_price'];

        //Save Order
        $SaveRate=new Order([
            'Visitor_Id'=>$request->input('VisitorIdI'),
            'Service_Id'=>$request->input('ServiceIdI'),
            'Price'=>$ServicePrice,
            'Status'=>0,
            'Target_Id'=>'Dummy'
        ]);
        $SaveRate->save();

        return response()->json(['err',['err'=>'10','message'=>'OrderSavedErr']],201);
    }

    public function OrderAll($limit,$SortType,$SortKey)
    {
        
        //Check Limit Value
        if($limit ==='null'){
        $limit=null;
        }

        //get Visitor Id
        $VisitorInf=Auth::guard('api')->user();

        //Check OrderBy Inputs
        if($SortKey !="null"){
            $getOrder=Order::where('Visitor_Id',$VisitorInf['id'])->limit($limit)->orderBy($SortKey, $SortType)->get();
        }
        else{
            $getOrder=Order::where('Visitor_Id',$VisitorInf['id'])->limit($limit)->get();
        }

        $getOrder->load('Service.Category');
        $getOrder->load('Visitor');
        return response()->json($getOrder,200);   

    }

    public function OrderOne($OrderId)
    {
        //validate Param
        if(!empty($OrderId)){

            //get Order
            $getOrder=Order::find($OrderId);

            if(!empty($getOrder)){

                return response()->json($getOrder,200);

            }
            else{
                return response()->json(['err',['err'=>'1','message'=>'SWErr']],400);
            }
        }
        else{
            return response()->json(['err',['err'=>'1','message'=>'ValidationErr']],400);
        }
        


    }


}
