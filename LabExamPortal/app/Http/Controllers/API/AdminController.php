<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User; 
use App\admin_detail;
use App\exam_detail;
use App\question; 
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AdminController extends Controller
{
    //
    public function number_of_online_users()
    {
        $isUser = "";
        $accessToken = Auth::user()->token();
        $remoteUser = json_decode($accessToken);
        $isUser = DB::table('users')->select('isAdmin')->where('id',$remoteUser->user_id)->get()[0];
        if(!$isUser->isAdmin)
        {
            return response()->json(["message" => "access denied"],403);
        }
        $users = DB::table('users')->where([['isLogin','=','1'],['isAdmin','=','0'],])->count();
       
        if(is_null($users))
        {
            return response()->json(["message" => "record not found"],404);
        }
        return response()->json($users,200);
    }
    public function list_online_users()
    {
       // $userIsAdmin = "";
        $isUser = "";
        //$listname = "";
        $accessToken = Auth::user()->token();
        $remoteUser = json_decode($accessToken);
        $isUser = DB::table('users')->select('isAdmin')->where('id',$remoteUser->user_id)->get()[0];
        if(!$isUser->isAdmin)
        {
            return response()->json(["message" => "access denied"],403);
        }
        $users = DB::table('users')->select('name')->where([['isLogin','=','1'],['isAdmin','=','0'],])->get();
        if(is_null($users))
        {
            return response()->json(["message" => "record not found"],404);
        }
        foreach ($users as $value) {
            //echo "$value <br>";
            $listname[] = $value->name;
          }
        return response()->json($listname,200);
    }
    public function create_exam(Request $request)
    {
        $isUser = "";
        //$exam_sub = "";
        $accessToken = Auth::user()->token();
        $remoteUser = json_decode($accessToken);
        $isUser = DB::table('users')->select('isAdmin')->where('id',$remoteUser->user_id)->get()[0];
        if(!$isUser->isAdmin)
        {
            return response()->json(["message" => "access denied"],403);
        }
        $exam_unique_code = rand(1000,9999);
       // $accessToken = Auth::user()->token();
        //$remoteUser = json_decode($accessToken);
        
        //$exam_sub = DB::table('admin_details')->select('course_code')->where('admin_id',$remoteUser->user_id)->get()[0];
        $validatedData = $request->validate([
            'exam_name' => 'required', 
            'exam_duration' => 'required', 
            'exam_date' => 'required',
            'scheduled_at' => 'required',
            
        ]);
        $exam_info = [
                 'exam_name' => $validatedData['exam_name'],
                 'exam_duration' => $validatedData['exam_duration'],
                 'exam_date' => $validatedData['exam_date'],
                 'scheduled_at' => $validatedData['scheduled_at'],
                 'exam_for' => $remoteUser->user_id, //$exam_sub->course_code,
                 'exam_code' => $exam_unique_code,
                ];
        $exam = exam_detail::create($exam_info);
        
        return response()->json($exam_unique_code,201);
    }
    public function list_exam()
    {
        $isUser = "";
        $name = "";
        $accessToken = Auth::user()->token();
        $remoteUser = json_decode($accessToken);
        $isUser = DB::table('users')->select('isAdmin')->where('id',$remoteUser->user_id)->get()[0];
        if(!$isUser->isAdmin)
        {
            return response()->json(["message" => "access denied"],403);
        }
        // $name = DB::table('users')->select('username')->where('id',$remoteUser->user_id)->get()[0];
         $exams = DB::table('exam_details')->select('exam_id','exam_name','exam_date','exam_duration')->where('exam_for',$remoteUser->user_id)->latest()->get();//get();
         if(is_null($exams))
           {
                return response()->json(["message" => "record not found"],404);
           }
         return response()->json($exams,200);
        
        
    }
    public function add_question(Request $request)
    {
        $isUser = "";
        $examfor = "";
        $accessToken = Auth::user()->token();
        $remoteUser = json_decode($accessToken);
        $isUser = DB::table('users')->select('isAdmin')->where('id',$remoteUser->user_id)->get()[0];
        if(!$isUser->isAdmin)
        {
            return response()->json(["message" => "access denied"],403);
        }
        $validatedData = $request->validate([
            'title' => 'required', 
            'description' => 'required', 
            'marks' => 'required',
            
        ]);
        $examfor = DB::table('exam_details')->select('exam_id')->where('exam_for',$remoteUser->user_id)->get()[0];
        $question_info = [
            'title' => $validatedData['title'],
            'description' => $validatedData['description'],
            'marks' => $validatedData['marks'],
            'admin_id' =>$remoteUser->user_id,
            'exam_id' => $examfor->exam_id,
            
           ];
   $ques = question::create($question_info);
   
   return response()->json(['message' => 'Successfully added'],200);
    }
    public function view_question()
    {
        $accessToken = Auth::user()->token();
        $remoteUser = json_decode($accessToken);
        $isUser = DB::table('users')->select('isAdmin')->where('id',$remoteUser->user_id)->get()[0];
        if(!$isUser->isAdmin)
        {
            return response()->json(["message" => "access denied"],403);
        }
        $question_info = DB::table('questions')->select('title','description','marks')->where('admin_id',$remoteUser->user_id)->get();
        return response()->json($question_info,200);
    }
    public function update_instructor(Request $request)
    {
        $isUser = "";
       // $code = "";
        $accessToken = Auth::user()->token();
        $remoteUser = json_decode($accessToken);
        $isUser = DB::table('users')->select('isAdmin')->where('id',$remoteUser->user_id)->get()[0];
        if(!$isUser->isAdmin)
        {
            return response()->json(["message" => "access denied"],403);
        }
        $affectInstructorName = DB::table('admin_details')
              ->where('admin_id',$remoteUser->user_id)
              ->update(['instructor_name' => $request->instructor_name]);
        $affectEmail = DB::table('users')
              ->where('id',$remoteUser->user_id)
              ->update(['email' => $request->email]);
         $affectUserName = DB::table('users')
              ->where('id',$remoteUser->user_id)
              ->update(['name' => $request->instructor_name]);
          

             
        /*$code = DB::table('admin_details')->select('course_code')->where('admin_id',$remoteUser->user_id)->get()[0];
        $course = admin_detail::find($code->course_code);
        if(is_null($course))
           {
                return response()->json(["message" => "record not found"],404);  
           }
        $course->update($request->all());*/
        return response()->json(['message' => 'Successfully updated'],200);

    }
}
