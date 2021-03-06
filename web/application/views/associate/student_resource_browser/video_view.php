                <!-- Content Header (Page header) -->
                <section class="content-header">
                    <h1>
                        Video View
                        <small><?php if(isset($resource_detail)) echo $resource_detail['resource_name']; ?></small>
                    </h1>
                    <ol class="breadcrumb">
                        <?php 
                            echo "<li><a href='#' onClick='get_student_course_tree(".$course_id."); get_student_course_modules(".$course_id.");'><i class='fa fa-dashboard'></i>".$course_name."</a></li>"; 
                            echo "<li><a href='#' onClick='get_student_course_tree(".$course_id."); get_student_course_groups(\"$course_id\",\"$module_name\");'>".$module_name."</a></li>"; 
                            echo "<li><a href='#' onClick='get_student_course_groups(\"$course_id\",\"$module_name\",\"$group_name\");'>".$group_name."</a></li>"; 
                            echo "<li><a href='#' onClick='get_student_cmgs_resources(\"$course_id\",\"$subject_name\");'>".$subject_name."</a></li>";
                            echo "<li class='active'>".$resource_detail['resource_name']."</li>";
                        ?>
                    </ol>
                </section>

                <!-- Main content -->
                <section class="content">
                    <div id="video-player" class="video-player embed-responsive embed-responsive-16by9" onLoad="load_video();">
                        <h3> Loading Video .. </h3>
                    </div>
                </section><!-- /.content -->
                <!-- Content Header (Page footer) -->
                <script type="text/javascript">
                $(document).ready(function(){
                    // Load Video When Document is Ready
                    associate_loadVideo('<?php echo $resource_link; ?>');
                    //setTimeout(function() { loadVideo('<?php echo $resource_link; ?>',500,300); }, 5000);
                });
                </script>