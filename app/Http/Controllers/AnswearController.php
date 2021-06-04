<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Session;
use Illuminate\Support\Facades\Redirect;

class AnswearController extends Controller
{
  public function allAnswer(){
     return view('pages.all-answear');
  }
  //ALL ANSWER DATA FETCH BY AJAX DATATABLE
   public function allanswerDataFetch(Request $request){


    $limit = $request->input('length');
    $start = $request->input('start');
    $search = $request->input('search.value');

    $column_order = array(
        "ans.id",
        "ans.user_name",
        "ans.ans",
        "ans.subject",
        "ans.date",
        "ans.institutionname"); //set column field database for datatable orderable

    $column_search = array(
        "ans.user_name",
        "ans.ans",
        "ans.subject",
        "ans.date",
        "ans.institutionname"); //set column field database for datatable searchable

    $order = array("ans.id" => 'desc');

    $recordsTotal = DB::table('ans')
            //->where('users.isTeacher',1)
            ->count();
   
    $list = DB::table('ans')
            ->join('users','users.id','=','ans.user_id');


   // echo $list->toSql(); exit;

    // if (!empty($EMPLOYEE_ID)) {
    //   $list->where("users.EMPLOYEE_ID", $EMPLOYEE_ID);
    // }

    if (!empty($search)) {
      $list->where(function($query) use ($search, $column_search) {
        $query->where("ans.id", 'LIKE', "%{$search}%");
        foreach ($column_search as $item) {
          $query->orWhere($item, 'LIKE', "%{$search}%");
        }
      });
    }

    $recordsFiltered = $list->count();

    $list->offset($start);
    $list->limit($limit);

    if (!empty($request->input('order.0.column'))) { // here order processing
      $list->orderBy($column_order[$request->input('order.0.column')], $request->input('order.0.dir'));
    } else {
      $list->orderBy(key($order), $order[key($order)]);
    }

    //echo $list->toSql(); exit;

    $list = $list->get();

    // generate server side datatables
    $data = array();

    $arr =[
        1 => "Teacher",
        2 => "Student",
        3 => "Answer Hero",
        4 => "Admin",
        5 => "Others",
        6 => "Editior",
    ];
  //  dd($arr);

    $sl = $start;
    if (!empty($list)) {
      foreach ($list as $value) {
        $row = array();
       
        $row[] = ++$sl;
        $row[] = $value->user_name;
        $row[] = $value->ans;
        $row[] = $value->subject;
        $row[] = $value->date;
        // $row[] = $value->type == 1 ? 'Teacher' : 0;

        $row[] = $arr[$value->type];
        $row[] = $value->institutionname;
        $row[] = '<button type="submit" class="btn btn-danger btn-sm delete" id="a_delete' . $value->id . '" onclick="a_delete(' . $value->id . ')">Delete</button>';
        //$row[] = 0;

        $data[] = $row;
      }
    }

    //print_r($data); exit;

    $output = array(
        "draw" => intval($request->input('draw')),
        "recordsTotal" => intval($recordsTotal),
        "recordsFiltered" => intval($recordsFiltered),
        "data" => $data
    );

    // output to json format
    return response()->json($output);
  }

  //END ALL ANSWER DATA FATCH BY AJAX
  public function todayAnswear(){
    date_default_timezone_set("Asia/Dhaka");
    $todaydate = date("Y-m-d");
    $today_ans =  DB::table('ans')
                    ->where('date', 'like', '%'.$todaydate.'%')
                    ->get();

     return view('pages.today-answear',compact('today_ans'));
 }
  public function ansDelete(Request $req){
           $id = $req->input('id');
         DB::table('ans')
                 ->where('id', $id)
                 ->delete();
         return response()->json('delete success');
      
    }

    public function Answering($id){
         $id;
         DB::table('post_q')
         ->where('id',$id)
         ->update(['status' => '2']);
            return response()->json('someone answering success');
 }
    public function Ainsert(Request $request){
          $post_id = $request->id;
         // $post_user_id = $request->post_user_id;
         // $ans = $request->ans;
         // $user_id = $request->l_user_id;
         // $user_name = $request->username;
         // $subject = $request->subject;
         // $date = $request->date;
         // $institutionname = $request->institutionname;
       $data = array();
       $data['post_id'] = $request->id;
       $data['post_user_id'] = $request->post_user_id;
       $data['ans'] = $request->ans;
       $data['user_id'] = $request->l_user_id;
       $data['user_name'] = $request->username;
       $data['subject'] = $request->subject;
       $data['date'] = $request->date;
       $data['image'] = '0';
       $data['institutionname'] = $request->institutionname ? : 0;
      // dd($data);
       $inset =DB::table('ans')->insert($data);

        // $insert = DB::insert("INSERT INTO ans (post_id, post_user_id, ans, user_id, user_name, subject,date,image,institutionname) VALUES ('$post_id', '$post_user_id', '$ans','$user_id', '$user_name', '$subject','$date', '0',$institutionname)");
              DB::table('post_q')
             ->where('id',$post_id)
             ->update(['status' => '1']);
         return response()->json('success');    
   }

 public function lastweekAnswer(){

       
      return view('pages.last_week-answer');
 }
 public function weeklyDecrement(Request $request){
   $date = date('Y-m-d', strtotime($request->id . ' -7 day'));
   return response()->json($date);
 }
 public function getldata (Request $request){
     $date_1 = $request->dates;
     $date_minus = date('Y-m-d', strtotime($date_1 . ' -1 day'));
     $date_2 = date('Y-m-d', strtotime($date_minus . ' -6 day'));

        $all_teacher = DB::select("SELECT users.`name`,users.`mobile`,users.`institutionname`,COUNT( CASE WHEN ( ans.date >= '$date_2' AND ans.date <= '$date_minus' ) THEN 1 END ) AS total 
      FROM `users` LEFT JOIN ans ON users.id = ans.user_id WHERE
  users.isTeacher = 1 GROUP BY users.id,users.`name`,users.`mobile`,users.`institutionname`");
        $html = '';
        foreach($all_teacher as $teacher){
          $html .= '<tr><td>'.$teacher->name.'</td><td>'.$teacher->mobile.'</td><td>'.$teacher->institutionname.'</td><td>'.$teacher->total.'</td></tr>';
        }
        
      
       return response()->json($html);
 }
  public function weeklyIncrement(Request $request){
   $date = date('Y-m-d', strtotime($request->id . ' +7 day'));
   return response()->json($date);
 }
  public function getData (Request $request){
     $date_1 = $request->dates;
     $date_plus = date('Y-m-d', strtotime($date_1 . ' -1 day'));
     $date_2 = date('Y-m-d', strtotime($date_plus . ' -6 day'));
       /*
       $a_count = DB::select("SELECT COUNT( CASE WHEN ( ans.date >= '2021-04-24' AND ans.date <= '2021-04-30' ) THEN 1 END ) AS total FROM `users` LEFT JOIN ans ON users.id = ans.user_id WHERE users.isTeacher = 1 GROUP BY users.id"); */
        $all_teacher = DB::select("SELECT users.`name`,users.`mobile`,users.`institutionname`,COUNT( CASE WHEN ( ans.date >= '$date_2' AND ans.date <= '$date_plus' ) THEN 1 END ) AS total 
      FROM `users` LEFT JOIN ans ON users.id = ans.user_id WHERE
  users.isTeacher = 1 GROUP BY users.id,users.`name`,users.`mobile`,users.`institutionname`");
        $html = '';
        foreach($all_teacher as $teacher){
          $html .= '<tr><td>'.$teacher->name.'</td><td>'.$teacher->mobile.'</td><td>'.$teacher->institutionname.'</td><td>'.$teacher->total.'</td></tr>';
        }
        
      
       return response()->json($html);
 }

 public function ajaxList(Request $request)
  {
    $post_data = json_decode($request->getContent(), true);

    $input_date = $request->input('dates');

    date_default_timezone_set("Asia/Dhaka");

    $date = date("Y-m-d");
    if (!empty($input_date)) {
      $date = $input_date;
    }

    $to_date = date('Y-m-d', strtotime($date . ' -1 day'));
    $from_date = date('Y-m-d', strtotime($to_date . ' -6 day'));

    $limit = $request->input('length');
    $start = $request->input('start');
    $search = $request->input('search.value');

    $column_order = array(
        "users.id",
        "users.name",
        "users.mobile",
        "institutes.institute_name"); //set column field database for datatable orderable

    $column_search = array(
        "users.name",
        "users.mobile",
        "institutes.institute_name"); //set column field database for datatable searchable

    $order = array("users.id" => 'desc');

    $recordsTotal = DB::table('users')
            ->where('users.type',1)
            ->count();
   
    $list = DB::table('users')
            ->selectRaw("users.`name`,users.`mobile`,users.`type`,institutes.`institute_name`,(SELECT COUNT(ans.id) FROM ans WHERE ans.user_id = users.id AND ans.`date` BETWEEN '$from_date' AND '$to_date') AS total")
             ->join('institutes','institutes.user_id','=','users.id')
             ->join('teachers','teachers.institute_id','=','institutes.id')
            ->where('users.type',1)
            ->orderBy('total', 'DESC');
                    // ->get();
                      
          //  dd($list);
            //->groupByRaw('users.id,users.`name`,users.`mobile`,users.`institutionname');
    /* new code  

      $qu1 = DB::table('users')
             ->join('institutes','institutes.user_id','=','users.id')
             ->join('teachers','teachers.institute_id','=','institutes.id')
             ->select('users.id as user_id','users.name','users.email','institutes.institute_name')
             ->where('users.type',1)
             ->get();
      
      $qu2 = DB::select("SELECT
            user_id,
            COUNT( ans.id ) as total
          FROM
            ans 
          WHERE
            ans.`date` BETWEEN '2021-05-21' 
            AND '2021-05-27' 
          GROUP BY
            user_id");

      $by_users_ans_arr=[];
      foreach($qu2 as $val){
        $by_users_ans_arr[$val->user_id]=$val->total;

      }

      $final_arr=[];
     foreach($qu1 as $k=>$value){
        $final_arr[$k]['user_id']=$value->user_id;
        $final_arr[$k]['name']=$value->name;
        $final_arr[$k]['mobile']=$value->email;
        $final_arr[$k]['institute_name']=$value->institute_name;
        $final_arr[$k]['total']=isset($by_users_ans_arr[$value->user_id])?$by_users_ans_arr[$value->user_id]:0;

      }
        dd($final_arr);
     // $list=(object)$final_arr;  */ 



    //**********//
     //END NEW CODE   
   //**********//
    // echo $list->toSql(); exit;

    // if (!empty($EMPLOYEE_ID)) {
    //   $list->where("users.EMPLOYEE_ID", $EMPLOYEE_ID);
    // }

    if (!empty($search)) {
      $list->where(function($query) use ($search, $column_search) {
        $query->where("users.id", 'LIKE', "%{$search}%");
        foreach ($column_search as $item) {
          $query->orWhere($item, 'LIKE', "%{$search}%");
        }
      });
    }

    //$recordsFiltered = $list->count();
     $recordsFiltered = $list->count();

    $list->offset($start);
    $list->limit($limit);

    if (!empty($request->input('order.0.column'))) { // here order processing
      $list->orderBy($column_order[$request->input('order.0.column')], $request->input('order.0.dir'));
    } else {
      $list->orderBy(key($order), $order[key($order)]);
    }
    
    //echo $list->toSql(); exit;

    $list = $list->get();

    // generate server side datatables
    $data = array();
      $arr =[
        1 => "Teacher",
        2 => "Student",
        3 => "Answer Hero",
        4 => "Admin",
        5 => "Others",
        6 => "Editior",
    ];
  //  dd($arr);
    $sl = $start;
    if (!empty($list)) {
      foreach ($list as $value) {
        $row = array();
      // dd($row);
        $row[] = ++$sl;
        $row[] = $value->name;
        $row[] = $value->mobile;
        $row[] = $arr[$value->type];
        $row[] = $value->institute_name;
        $row[] = $value->total;
        //$row[] = 0;

        $data[] = $row;
      }
    }

    //print_r($data); exit;

    $output = array(
        "draw" => intval($request->input('draw')),
        "recordsTotal" => intval($recordsTotal),
        "recordsFiltered" => intval($recordsFiltered),
        "data" => $data
    );

    // output to json format
    return response()->json($output);
  }

  public function dailyAnswer(){
    return view('pages.daily-answer');
  }
  public function dailyIncrement(Request $request){
   $date = date('Y-m-d', strtotime($request->id . ' +1 day'));
   return response()->json($date);
 }
  public function dailyDecrement(Request $request){
   $date = date('Y-m-d', strtotime($request->id . ' -1 day'));
   return response()->json($date);
 }



 public function dailyAnswerData(Request $request)
  {
    $post_data = json_decode($request->getContent(), true);
    $input_date = $request->input('todaydate');
    //dd($input_date);

    date_default_timezone_set("Asia/Dhaka");

    $date = date("Y-m-d");
    if (!empty($input_date)) {
      $date = $input_date;
    }

    $corr_date = $date;
    //$from_date = date('Y-m-d', strtotime($to_date . ' -6 day'));

    $limit = $request->input('length');
    $start = $request->input('start');
    $search = $request->input('search.value');

    $column_order = array(
        "users.id",
        "users.name",
        "users.mobile",
        "institutes.institute_name"); //set column field database for datatable orderable

    $column_search = array(
        "users.name",
        "users.mobile",
        "institutes.institute_name"); //set column field database for datatable searchable

    $order = array("users.id" => 'desc');

    $recordsTotal = DB::table('users')
            ->where('users.type',1)
            ->count();
   
    $list = DB::table('users')
            ->selectRaw("users.`name`,users.`mobile`,users.`type`,institutes.`institute_name`,(SELECT COUNT( ans.user_id ) FROM ans WHERE ans.user_id = users.id AND ans.`date` LIKE '%$corr_date%') AS total")
            ->join('institutes','institutes.user_id','=','users.id')
            ->join('teachers','teachers.institute_id','=','institutes.id')
            ->where('users.type',1)
            ->orderBy('total','DESC');
            //->groupByRaw('users.id,users.`name`,users.`mobile`,users.`institutionname');

     //echo $list->toSql(); exit;

    // if (!empty($EMPLOYEE_ID)) {
    //   $list->where("users.EMPLOYEE_ID", $EMPLOYEE_ID);
    // }

    if (!empty($search)) {
      $list->where(function($query) use ($search, $column_search) {
        $query->where("users.id", 'LIKE', "%{$search}%");
        foreach ($column_search as $item) {
          $query->orWhere($item, 'LIKE', "%{$search}%");
        }
      });
    }

    $recordsFiltered = $list->count();

    $list->offset($start);
    $list->limit($limit);

    if (!empty($request->input('order.0.column'))) { // here order processing
      $list->orderBy($column_order[$request->input('order.0.column')], $request->input('order.0.dir'));
    } else {
      $list->orderBy(key($order), $order[key($order)]);
    }

    //echo $list->toSql(); exit;

    $list = $list->get();

    // generate server side datatables
    $data = array();
      $arr =[
        1 => "Teacher",
        2 => "Student",
        3 => "Answer Hero",
        4 => "Admin",
        5 => "Others",
        6 => "Editior",
    ];
    $sl = $start;
    if (!empty($list)) {
      foreach ($list as $value) {
        $row = array();
       
        $row[] = ++$sl;
        $row[] = $value->name;
        $row[] = $value->mobile;
        $row[] = $arr[$value->type];
        $row[] = $value->institute_name;
        $row[] = $value->total;
        //$row[] = 0;

        $data[] = $row;
      }
    }

    //print_r($data); exit;

    $output = array(
        "draw" => intval($request->input('draw')),
        "recordsTotal" => intval($recordsTotal),
        "recordsFiltered" => intval($recordsFiltered),
        "data" => $data
    );

    return response()->json($output);
  }

  public function monthlyAnswer(){
       $custom_date = date('Y-m-d');
       $custom_date = explode('-',$custom_date);
       $custome_year = $custom_date[0];
       $custome_month = $custom_date[1];
       $custome_day = $custom_date[2];
        //  AFTER CUSTOMIZE GET DATA
       $start_date = $custome_year.'-'.$custome_month.'-'.'01';
       $end_date = $custome_year.'-'.$custome_month.'-'.date('t');
        
     $monthly = DB::select("SELECT users.`name`, users.`mobile`, users.`institutionname`, users.`type`,
     COUNT( CASE WHEN ( ans.date >= '$start_date' AND ans.date <= '$end_date' ) THEN 1 END ) AS total  FROM
     `users` LEFT JOIN ans ON users.id = ans.user_id  WHERE users.type = 1 OR users.type = 3  GROUP BY users.id, users.`name`, users.`mobile`, users.`institutionname`, users.`type` ORDER BY
     `total` DESC");
     //dd($monthly);
       return view('pages.monthly-answer',compact('monthly'));
  }

  public function Save(Request $request){
        $start_date = $request->start_date;
        $end_date = $request->end_date;
    $monthly = DB::select("SELECT users.`name`, users.`mobile`, users.`institutionname`, users.`type`,
    COUNT( CASE WHEN ( ans.date >= '$start_date' AND ans.date <= '$end_date' ) THEN 1 END ) AS total  FROM
    `users` LEFT JOIN ans ON users.id = ans.user_id  WHERE users.type = 1 OR users.type = 3  GROUP BY users.id, users.`name`, users.`mobile`, users.`institutionname`, users.`type` ORDER BY
    `total` DESC");
       return view('pages.monthly-answer',compact('monthly'));

  }
  public function allAnswer_hero(){
    $all_ans_hero = DB::table('users')
                  ->where('type',3)
                  ->get();
    //dd($all_ans_hero);
    return view('pages.all-answear_hero',compact('all_ans_hero'));
  }

 

}