            <div class="sometype"> <br> <br>
                <div class="btn-group" role="group">
                    <script type="text/javascript"> var test_no = $('#test_no').val(); </script>
                    <button type="button" class="btn btn-primary" onclick="tableToExcel('rank_list','Skol Table','User_assessment_rank_report_test'+test_no+'.xlsx')">Download</button>        
                    <a id="dlink"  style="display:none;"></a> 
                </div>
           <br> </div> <br>
        <div class="box">
           
            <div class="box-body table-responsive">

                <table id="rank_list" class="table table-bordered table-striped">
                    <thead>
                        <tr>
                            <th>Rank</th>
                            <th>Registration NO</th>
                            <th>User Name</th>
                            <th>Test No</th>
                            <th>Test Name</th>
                            <th>Test Taken Date</th>
                            <th>Total Marks</th>
                            <th>Marks Obtained</th>
                            <th>Percentage</th>                    
                        </tr>
                    </thead>
                    <tbody>
                            <?php  $count = 1;
                            if(isset($rank_list)){
                                foreach ($rank_list as $res) {
                                    echo "<tr>";
                                        echo "<td>".$count++."</td>";
                                        echo "<td>".$res->registration_no."</td>";
                                        echo "<td>".$res->user_fname."</td>";
                                        echo "<td>".$res->test_no."</td>";
                                        echo "<td>".$res->test_name."</td>";
                                        echo "<td>".$res->test_date."</td>";
                                        echo "<td>".($res->no_of_questions*4)."</td>";
                                        echo "<td>".$res->test_score."</td>";
                                        echo "<td>".$res->test_percentage."</td>";
                                    echo "</tr>";
                                }
                            }else{
                                echo "<h3 style='color: red;''>No Reports to be displayed</h3>";
                            }
                            ?>
                    </tbody>
<!--                     <tfoot>
                        <tr>
                            <th>Rank</th>
                            <th>Registration NO</th>
                            <th>User Name</th>
                            <th>Test No</th>
                            <th>Test Name</th>
                            <th>Test Taken Date</th>
                            <th>Total Marks</th>
                            <th>Marks Obtained</th>
                            <th>Percentage</th>
                        </tr>
                    </tfoot> -->
                </table>
            </div>
        </div>

        <script type="text/javascript">

    var tableToExcel = (function() {
        var uri = 'data:application/vnd.ms-excel;base64,', 
        template = '<html xmlns:o="urn:schemas-microsoft-com:office:office" xmlns:x="urn:schemas-microsoft-com:office:excel" xmlns="http://www.w3.org/TR/REC-html40"><head><!--[if gte mso 9]><xml><x:ExcelWorkbook><x:ExcelWorksheets><x:ExcelWorksheet><x:Name>{worksheet}</x:Name><x:WorksheetOptions><x:DisplayGridlines/></x:WorksheetOptions></x:ExcelWorksheet></x:ExcelWorksheets></x:ExcelWorkbook></xml><![endif]--></head><body><table>{table}</table></body></html>', 
        base64 = function(s) {
            return window.btoa(unescape(encodeURIComponent(s)))
        }, format = function(s, c) {
            return s.replace(/{(\w+)}/g, function(m, p) {
                return c[p];
            })
        }
        return function(table, name, filename) {
            if (!table.nodeType)
            table = document.getElementById(table);
                var x = "<?php echo $count; ?>";
                    if(x == 1){
                        alert('No data to Download');
                        return false;
                    }

                    var cln=table.cloneNode(true);
                    var paras = cln.getElementsByClassName('ignore');
                  
                    while(paras[0]) {
                        paras[0].parentNode.removeChild(paras[0]);
                    }

                    var ctx = {
                        worksheet : name || 'Worksheet',
                        table : cln.innerHTML
                    }            
                    
                    document.getElementById("dlink").href = uri + base64(format(template, ctx));
                    document.getElementById("dlink").download = filename;
                    document.getElementById("dlink").click();
        }

    })();
</script>