                        <?php 
                            if(isset($course_tree)){
                                
                                // Course Name to be displayed
                                //echo "<small class='badge pull-right bg-green'>new</small>";
                                echo "<a href='#'>
                                        <i class='fa icon-book'></i> 
                                        <span onClick='get_student_course_tree(".$course_details['course_id'].");'>".$course_details['course_name']."</span>
                                        <i class='fa fa-angle-left pull-right'></i>
                                        
                                     </a>";
                                
                                // Generate Subject List
                                echo "<ul class='treeview-menu'>";
                                foreach ($course_tree as $res) {
                                    if($res->payment_status == '2'){
                                        $subject_name = $res->subject_name;
                                        $course_id    = $res->course_id;
                                        $subject_list = '<li><a href="#" 
                                                                onClick="open_subject_resources(\'$course_id\',\'$subject_name\');">
                                                                <i class="fa fa-angle-double-right"></i>'.$subject_name.'</a></li>';
                                        // echo $subject_list;
                                        echo "<li><a href='#' title='$subject_name' onClick='open_subject_resources(\"$course_id\",\"$subject_name\");'><i class='fa fa-arrow-circle-o-right'></i>".$subject_name."</a></li>";
                                    }
                                    if($res->payment_status == '8'){
                                        $subject_name = $res->subject_name;
                                        $course_id    = $res->course_id;
                                        echo "<li><a href='#' title='$subject_name Payment Under Verification !'><i class='fa fa-arrow-circle-o-right'></i>".$subject_name."</a></li>";
                                    }
                                }
                                echo "</ul>";
                                
                            }
                        ?>