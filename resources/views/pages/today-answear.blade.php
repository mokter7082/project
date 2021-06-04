@extends('welcome')

@section('content')
<div class="content">
<div class="container">
  <div class="row">
                      <div class="col-md-12">
                              <div class="panel panel-default">
                                  <div class="panel-heading">
                                      <h3 class="panel-title">Today Answear</h3>
                                  </div>
                                  <div class="panel-heading">
                                    <?php
                                    $teacher = Session::get('type');
                                    $user_id = Session::get('user_id');
                                    $todaydate = date("Y-m-d");
                                    $te_ans_co = DB::table('ans')->where('user_id',$user_id)->get();
                                    $today_to_ans = DB::select("SELECT * FROM ans WHERE ans.date LIKE '%$todaydate%'");
                                    $today_ans_count = count($today_to_ans);
                                    $t_answer_count = count($te_ans_co);
                                    ?>
                                    @if($teacher  == 1)
                                    <h3 class="panel-title">Today Total Answer = {{$t_answer_count}}</h3>
                                    @elseif($teacher  == 3)
                                    
                                    @else
                                    <h3 class="panel-title">Today Total Answer = {{$today_ans_count}}</h3>
                                    @endif
                                      
                                  </div>

                                 @php
                                 $teacher = Session::get('type');
                                 
                                 @endphp

                             @if($teacher  == 1)

                             <?php
                              $user_id = Session::get('user_id');
                             $teacher_ans =  DB::table('ans')
                                                ->where('user_id',$user_id)
                                                ->get();
                                                //dd( $teacher_a);
                                                
                                        
                             ?>
                              <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12 table-responsive">
                                        <table id="datatable" class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                  <th>ID</th>
                                                  <th>User Name</th>
                                                  <th>Answear</th>
                                                  <th>Subject</th>
                                                  <th>Date</th>
                                                  <th>Subject</th>
                                                  <th>Institution Name</th>
                                                  <th>Action</th>
                                              </tr>
                                          </thead>
                                                  <tbody>
                                                    @foreach($teacher_ans as $val)
                                                        <tr id = "tr-{{$val->id}}">
                                                            <td>{{$val->id}}</td>
                                                            <td>{{$val->user_name}}</td>
                                                            <td>{{$val->ans}}</td>
                                                            <td>{{$val->subject}}</td>
                                                            <td>{{$val->date}}</td>
                                                            <td>{{$val->subject}}</td>
                                                            <td>{{$val->institutionname}}</td>
                                                             <td>
                                                              <button type="submit" class="btn btn-danger btn-sm delete" id="a_delete{{$val->id}}" onclick="a_delete({{$val->id}})">Delete</button>
                                                            </td>
                                                        </tr>
                                                      @endforeach
                                                  </tbody>
                                              </table>

                                          </div>
                                      </div>
                                  </div>
                             @elseif($teacher  == 3)
                              
                             <?php
                             $user_id = Session::get('user_id');
                             $teacher_ans_t_3 =  DB::table('ans')
                                                ->where('user_id',$user_id)
                                                ->get();
                                                
                                        
                             ?>
                              <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12 table-responsive">
                                        <table id="datatable" class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                  <th>ID</th>
                                                  <th>User Name</th>
                                                  <th>Answear</th>
                                                  <th>Subject</th>
                                                  <th>Date</th>
                                                  <th>Subject</th>
                                                  <th>Institution Name</th>
                                                  <th>Action</th>
                                              </tr>
                                          </thead>
                                                  <tbody>
                                                    @foreach($teacher_ans_t_3 as $val)
                                                        <tr id = "tr-{{$val->id}}">
                                                            <td>{{$val->id}}</td>
                                                            <td>{{$val->user_name}}</td>
                                                            <td>{{$val->ans}}</td>
                                                            <td>{{$val->subject}}</td>
                                                            <td>{{$val->date}}</td>
                                                            <td>{{$val->subject}}</td>
                                                            <td>{{$val->institutionname}}</td>
                                                             <td>
                                                              <button type="submit" class="btn btn-danger btn-sm delete" id="a_delete{{$val->id}}" onclick="a_delete({{$val->id}})">Delete</button>
                                                            </td>
                                                        </tr>
                                                      @endforeach
                                                  </tbody>
                                              </table>

                                          </div>
                                      </div>
                                </div>
                             @elseif($teacher  == 4)
                              <?php
                                // date_default_timezone_set("Asia/Dhaka");
                                // $todaydate = date("Y-m-d");
                                $today_ans = DB::table('ans')
                                             ->join('users','users.id','ans.user_id')
                                             ->select('ans.*','users.*')
                                             ->where('ans.date', 'like', '%' . $todaydate. '%')
                                             ->get();
                                           // /  dd($today_ans);
                                                
                                        
                             ?>
                              <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12 table-responsive">
                                        <table id="datatable" class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                  <th>ID</th>
                                                  <th>User Name</th>
                                                  <th>Answear</th>
                                                  <th>Subject</th>
                                                  <th>Date</th>
                                                  <th>Subject</th>
                                                  <th>Designations</th>
                                                  <th>Institution Name</th>
                                                  <th>Action</th>
                                              </tr>
                                          </thead>
                                                  <tbody>
                                                    @foreach($today_ans as $val)
                                                        <tr id = "tr-{{$val->id}}">
                                                            <td>{{$val->id}}</td>
                                                            <td>{{$val->user_name}}</td>
                                                            <td>{{$val->ans}}</td>
                                                            <td>{{$val->subject}}</td>
                                                            <td>{{$val->date}}</td>
                                                            <td>{{$val->subject}}</td>
                                                            <td>
                                                                 @if($val->type == 1)
                                                                 <p>Teacher</p>
                                                                 @elseif($val->type == 2)
                                                                 <p>Student</p>
                                                                  @elseif($val->type == 3)
                                                                 <p>Answer Hero</p>
                                                                  @elseif($val->type == 4)
                                                                 <p>Admin</p>
                                                                  @elseif($val->type == 5)
                                                                 <p>Others</p>
                                                                  @elseif($val->type == 5)
                                                                 <p>Editor</p>
                                                                 @else
                                                                 @endif
                                                            </td>
                                                            <td>{{$val->institutionname}}</td>
                                                             <td>
                                                              <button type="submit" class="btn btn-danger btn-sm delete" id="a_delete{{$val->id}}" onclick="a_delete({{$val->id}})">Delete</button>
                                                            </td>
                                                        </tr>
                                                      @endforeach
                                                  </tbody>
                                              </table>

                                          </div>
                                      </div>
                                  </div>
                             @elseif($teacher  == 6)
                             
                            <div class="panel-body">
                                <div class="row">
                                    <div class="col-md-12 col-sm-12 col-xs-12 table-responsive">
                                        <table id="datatable" class="table table-striped table-bordered">
                                            <thead>
                                                <tr>
                                                  <th>ID</th>
                                                  <th>User Name</th>
                                                  <th>Answear</th>
                                                  <th>Subject</th>
                                                  <th>Date</th>
                                                  <th>Subject</th>
                                                  <th>Institution Name</th>
                                              </tr>
                                          </thead>
                                                  <tbody>
                                                    @foreach($today_ans as $val)
                                                        <tr id = "tr-{{$val->id}}">
                                                            <td>{{$val->id}}</td>
                                                            <td>{{$val->user_name}}</td>
                                                            <td>{{$val->ans}}</td>
                                                            <td>{{$val->subject}}</td>
                                                            <td>{{$val->date}}</td>
                                                            <td>{{$val->subject}}</td>
                                                            <td>{{$val->institutionname}}</td>
                                                        </tr>
                                                      @endforeach
                                                  </tbody>
                                              </table>

                                          </div>
                                      </div>
                                  </div>
                                  @else
                                  @endif
                              </div>
                          </div>

                      </div>

</div> <!-- container -->
</div>
<script type="text/javascript">

 function a_delete(id){
      // var t_td = (id);
      $('.delete').click(function(){
        swal({   
            title: "Are you sure?",   
            text: "Delete this Ans!",   
            type: "warning",   
            showCancelButton: true,   
            confirmButtonColor: "#DD6B55",   
            confirmButtonText: "Yes, delete ",   
            closeOnConfirm: false 
        }, function(){ 
          $.ajax({
            url: '<?php echo URL::to('ans-delete');?>',
            method: 'GET',
            data: {id:id},
            cache: false,
            success: function(html){
            console.log(html);
            $("#tr-"+id).remove();
            }
          });  
            swal("Deleted!", "Your Ans delete successfull.", "success"); 
        });
    });

       
        
 }


</script>
@endsection
