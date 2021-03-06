        <link href="/static/theme/css/datatables/dataTables.bootstrap.css" rel="stylesheet" type="text/css" />
        <!-- Content Header (Page header) -->
        <section class="content-header">
            <h3><p>My User's Assessment Reports</p></h3>
        </section>

        <!-- Main content -->
        <section class="content">
            
            <!-- TAB PANEL -->
            <div role="tabpanel">

                <!-- Nav tabs -->
               <!--  <ul class="nav nav-tabs" role="tablist">
                    <li role="presentation" class="active"><a href="#student_results" id="assessment_results" oncick="open_students_reports()" aria-controls="students" role="tab" data-toggle="tab">National Level Rank List</a></li>
                    <li role="presentation"><a href="#batchwise_associates" id="associates" onClick="batchwise_rank_list();" aria-controls="students" role="tab" data-toggle="tab">Batch Level Rank List</a></li>
                    <li role="presentation"><a href="#payment_not_verified_students" onClick="payment_not_verified_students();" aria-controls="students" role="tab" data-toggle="tab">Payment not Verified Students</a></li>
                </ul> -->
                <!-- Nav tabs -->

                <!-- Tab panes -->
                <br>
                <br>
                <div class="tab-content">
                    <div role="tabpanel" class="tab-pane active" id="national_level_results">
                                                
                        <div class="tab-content" id="course_names">
                            <div class="row">
                                <div class="col-xs-12 col-sm-6 col-md-3">
                                    <div class="control"> 
                                        <select id="course_id" class="form-control input" tabindex="5" placeholder="Course" onchange="get_all_tests(value);">
                                            <option value="">Select Course</option>
                                            <?php 
                                                if(isset($courses)){
                                                    foreach($courses as $res){
                                                        echo "<option value=".$res->course_id.">".$res->course_name."</option>";
                                                    } 
                                                } 
                                            ?>
                                        </select>            
                                    
                                    </div>
                                    <span class="help-block"></span>
                                </div>
                                
                                <div id="nationallevel_test_names" class="col-xs-12 col-sm-6 col-md-3">
                                    
                                </div>
                            </div>
                    
                        </div>
                        <ul class="nav nav-tabs" role="tablist" id="ranks_tab">
                            <li role="presentation" id="tab_national_level" class="active"><a href="#student_results" id="assessment_results" onclick="nation_level_rank_list()" aria-controls="students" role="tab" data-toggle="tab">International Level Rank List</a></li>
                            <li role="presentation" id="tab_batch_level"><a href="#batchwise_associates" id="associates" onclick="batch_level_rank_list()" aria-controls="students" role="tab" data-toggle="tab">National Level Rank List</a></li>
                            <li role="presentation" id="tab_mybatch_level"><a href="#payment_not_verified_students" onclick="mybatch_level_rank_list();" aria-controls="students" role="tab" data-toggle="tab">My Batch Level Rank List</a></li>
                        </ul>
                        <div class = "tab-content" id="ranks">

                        </div>
                    </div>
                <!-- Tab panes -->
            </div>
            <!-- ./TAB PANEL -->

        </section>
        <!-- ./Main content -->

        <!-- Content Header (Page footer) -->
        <!-- DATA TABES SCRIPT -->
        <script src="/static/theme/js/plugins/datatables/jquery.dataTables.js" type="text/javascript"></script>
        <script src="/static/theme/js/plugins/datatables/dataTables.bootstrap.js" type="text/javascript"></script>

        <!-- page script -->
        <script type="text/javascript">
            // assessment_selected("");
        </script>        

