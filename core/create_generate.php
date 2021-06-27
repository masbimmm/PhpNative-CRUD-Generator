<?php
function gen_create($table){
$nopf = NoPrimaryField($table);
$pf   = PrimaryField($table);
$string ="<?php
require_once '../config/conn.php';
?>
<!DOCTYPE html>
<html lang=\"en\">
<head>
  <title>".$table."</title>
  <meta charset=\"utf-8\">
  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
  <meta name=\"viewport\" content=\"width=device-width, initial-scale=1\">
  <link rel=\"stylesheet\" href=\"../assets/css/bootstrap.min.css\">
  <link rel=\"stylesheet\" type=\"text/css\" href=\"../assets/css/jquery.dataTables.min.css\">
  <link rel=\"stylesheet\" href=\"../swall/sweetalert2.min.css\">

  <script src=\"../assets/js/jquery.min.js\"></script>
  <script src=\"../assets/js/bootstrap.min.js\"></script>
  <script type=\"text/javascript\" src=\"../assets/js/jquery.dataTables.min.js\"></script>
  <script src=\"../swall/sweetalert2.min.js\"></script>
  <script src=\"../swall/parsley.js\"></script>
<body>

    <div class=\"container\">
      <div id=\"inputdata\">
        <h2>Tambah Data</h2>
        <button type=\"button\" class=\"btn btn-info btn-lg\" data-toggle=\"modal\" data-target=\"#myModal\">Tambah Data</button>

        <div class=\"modal fade\" id=\"myModal\" role=\"dialog\">
          <div class=\"modal-dialog\">
          
            <div class=\"modal-content\">
              <div class=\"modal-header\">
                <button type=\"button\" class=\"close\" data-dismiss=\"modal\">&times;</button>
                <h4 class=\"modal-title\">Tambah Data ".ucwords(strtolower(str_replace('_', ' ', str_replace('tbl_', '', strtolower($table)))))."</h4>
              </div>
              <form id=\"xform\" action=\"#\" autocomplete=\"off\">
                <div class=\"modal-body\">
                  ";
                    $nopf = NoPrimaryField($table);
                    foreach($nopf as $field){
                      if (preg_match('/tanggal/', strtolower($field['column_name']))) {

                      }else{
                      $string .="
                      <div class=\"form-group\">
                        <label for=\"".ucwords(strtolower(str_replace('_', ' ', $field['column_name'])))."\"> ".ucwords(strtolower(str_replace('_', ' ', $field['column_name'])))."</label>
                        <input data-parsley-type=\"alphanum\" type=\"text\" class=\"form-control\" id=\"".$field['column_name']."\" name='".$field['column_name']."' placeholder='".ucwords(strtolower(str_replace('_', ' ', $field['column_name'])))."' required>
                      </div>";
                      }
                    }
                  $string .= "
                </div>
                <div class=\"modal-footer\">
                   <button type=\"submit\" class=\"btn btn-primary waves-effect waves-light mr-1\">Submit</button>
                  <button type=\"button\" class=\"btn btn-default\" data-dismiss=\"modal\">Close</button>
                </div>
              </form>
            </div>
            
          </div>
        </div>
      </div>
      <div id=\"editdata\">
        <div id=\"modal-edit\" class=\"modal fade\" role=\"dialog\">
            <div class=\"modal-dialog\">
                <div class=\"modal-content\">
                    <form role=\"form\" id=\"xeform\" method=\"post\">
                        <div class=\"modal-header\">
                            <h4 class=\"modal-title\" id=\"exampleModalLabel\">Edit Data ".ucwords(strtolower(str_replace('_', ' ', str_replace('tbl_', '', strtolower($table)))))."</h4>
                            <button type=\"button\" class=\"close\" data-dismiss=\"modal\" aria-label=\"Close\">
                                <span aria-hidden=\"true\">&times;</span>
                            </button>
                        </div>
                        <div id=\"data-edit\">

                        </div>
                        <div style=\"text-align: center; padding: 1rem; border-top: 1px solid #e9ecef;\">
                        </div>
                    </form>   
                </div>
            </div>
        </div> 
      </div>
      <div class='table-responsive'>
        <table id='myTable' class='table table-hover'>
        <thead>
          <tr>
          <th>No</th>
        ";
            foreach ($nopf as $th) {
             $string .= "<th>".ucwords(strtolower(str_replace('_', ' ', $th['column_name'])))."</th> \n";
            }
        $string .= "
          <th >Opsi</th>
          </tr>
          </thead>
          <tbody> 
        <?php
          \$sql = \"SELECT * FROM ".$table."\";
          \$query = \$db->query(\$sql);
          \$no = 1;
          while(\$result = mysqli_fetch_assoc(\$query)){
            echo '<tr>';
            echo '  <td>'.\$no++.'</td>';";
            foreach ($nopf as $field) {
              $string .="
            echo '  <td>'.\$result['".$field['column_name']."'].'</td>'; \n";
            }
        // $string .="
        //   }
        //   \$no = 1;
        //   foreach(\$ga as \$data){
        //     echo \"<tr>\";
        //     echo \"<td>\".\$no++.\"</td>\"; \n";
        //     foreach ($nopf as $field) {
        //       $string .="echo \"<td>\".\$data['".$field['column_name']."'].\"</td>\"; \n";
        //     }
        $string .= "
            echo ' <td>
                      <a href=\"#\" id=\"edit\" class=\"btn btn-warning btn-sm\" data-id=\"'.enurl(\$result['$pf']).'\">edit</a>
                      <a href=\"#\" id=\"delete\" class=\"btn btn-danger btn-sm\" data-id=\"'.enurl(\$result['$pf']).'\">Delete</a>
                    </td>
                </tr>';
          }
        ?>
          ";

        $string .="

        </tbody>
        </table>
        </div>

        </div>
    </div>

  </body>
</html>

    
    <script type=\"text/javascript\">
        $(document).ready(function() {
            $('form').parsley();
        });
        function numberOnly(id) {
            var element = document.getElementById(id);
            var regex = /[^0-9]/gi;
            element.value = element.value.replace(regex, \"\");
        }
        $(document).ready( function () {
            $('#myTable').DataTable();
        });
    </script>
    <script type=\"text/javascript\">
        $(document).ready(function(){
            $('#xform').on('submit',function(e) {
                $.ajax({
                    url:'act.php?add',
                    data:$(this).serialize(),
                    type:'POST',
                    success:function(response){
                        obj = response;
                        if (obj['status']!=1) {
                            Swal.fire({
                              title: obj['title'],
                              text: obj['detail'],
                              type: 'error'
                            });
                        }else{
                            Swal.fire({
                                type: 'success',
                                title: obj['title'],
                                text: obj['detail']
                            }).then((result) => {
                                location.reload();
                            });
                        }
                    },
                    error:function(data){
                        Swal.fire({
                            title: obj['title'],
                            text: obj['detail'],
                            type: 'error'
                        });
                    }
                });
                e.preventDefault(); 
                return false;
            });
        });
        $(document).on('click','#edit',function(e){
          var idd = $(this).attr('data-id');
          $.ajax({
            url:'act.php?getdata',
            data:{".$pf.":idd},
            type:'POST',
            success:function(response){
                obj = response;
                if (obj['status']!=1) {
                    Swal.fire({
                      title: \"Oops!!!\",
                      text: obj['detail'],
                      type: 'error'
                    });
                }else{
                    document.getElementById(\"data-edit\").innerHTML = (obj['modal']);
                    $(\"#modal-edit\").modal('show');
                }
            },
            error:function(data){
              Swal.fire({
                  type: 'error',
                  title: 'Oops...',
                  text: 'Something went wrong :('
              });
            }
          });
          e.preventDefault(); //This is to Avoid Page Refresh and Fire the Event \"Click\" 
          return false;
        });
        $(document).ready(function(){
            $('#xeform').on('submit',function(e) {  //Don't foget to change the id form
                $.ajax({
                    url:'act.php?edit',
                    data:$(this).serialize(),
                    type:'POST',
                    success:function(response){
                        obj = response;
                        if (obj['status']!=1) {
                            Swal.fire({
                              title: obj['title'],
                              text: obj['detail'],
                              type: 'error'
                            });
                        }else{
                            Swal.fire({
                                type: 'success',
                                title: obj['title'],
                                text: obj['detail']
                            }).then((result) => {
                                // Reload the Page
                                location.reload();
                            });
                        }
                    },
                    error:function(data){
                      Swal.fire({
                          type: 'error',
                          title: 'Oops...',
                          text: 'Something went wrong :('
                      });
                    }
                });
                e.preventDefault(); //This is to Avoid Page Refresh and Fire the Event \"Click\" 
                return false;
            });
        });
        $(document).on('click','#delete',function(e){
            var idd= $(this).attr('data-id');
            Swal.fire({
                title: \"Apa anda yakin untuk menghapusnya?\",
                text: \"Jika anda menghapus data tidak akan dapat dikembalikan / akan dihapus secara permanent.\",
                type: \"warning\",
                showCancelButton: true,
                confirmButtonColor: \"#58db83\",
                cancelButtonColor: \"#ec536c\",
                confirmButtonText: \"Yakin\"
              }).then(function (result) {
                if (result.value) {
                    $.ajax({
                    url: 'act.php?delete',
                    type: 'POST',
                    data: {".$pf." : idd},

                    success: function(response) {
                        obj = response;
                        //Success Message == 'Title', 'Message body', Last one leave as it is
                        if (obj['status']!=1) {
                            Swal.fire({
                              title: obj['title'],
                              text: obj['detail'],
                              type: 'error'
                            });
                        }else{
                            Swal.fire(obj['title'], obj['detail'], \"success\").then((result) => {
                                // Reload the Page
                                location.reload();
                                // redirect
                                //window.location.replace(\"url.php\");
                            });
                        }
                    }
                });
                e.preventDefault(); //This is to Avoid Page Refresh and Fire the Event \"Click\"
                return false;
                  
                }
            });
        });
    </script>

";
createFile($string, "../".$table."/index.php");
}
?>
