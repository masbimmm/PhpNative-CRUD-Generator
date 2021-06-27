<?php
function gen_func_json($table){
$pf = PrimaryField($table);
$string = '';
$string .= "<?php
header(\"Content-Type: application/json; charset=UTF-8\");
ini_set('error_reporting', E_STRICT);
ini_set('max_execution_time', '0');
\$internalErrors = libxml_use_internal_errors(true);
\$json = NULL;
require_once '../config/conn.php';

";
$string .="
if (strtolower(\$param)==\"add\") {
  ";
$nopf = NoPrimaryField($table);
foreach($nopf as $fieldName){
  if (preg_match('/tanggal/', strtolower($fieldName['column_name']))) {
   $string .= "\$".$fieldName['column_name']." = date(\"Y-m-d H:i:s\");\n\t";
  }else{
    if (preg_match('/password/', strtolower($fieldName['column_name']))) {
    $string .="if (!empty(\$_POST['".$fieldName['column_name']."'])) {\n\t\t";
    $string .="\$".$fieldName['column_name']." = md5(\$_POST['".$fieldName['column_name']."']); \n\t";
    $string .="}else{
      \$json->status = 0;
      \$json->title = \"Oops...\";
      \$json->detail = \" Harap Perhatikan ".ucwords(strtolower(str_replace('_', ' ', $fieldName['column_name'])))." tidak boleh kosong \";
      \$json->type = \"error\";
      echo json_encode(\$json);
      die();
  }\n\t";
    }else{
    $string .="if (!empty(\$_POST['".$fieldName['column_name']."'])) {\n\t\t";
    $string .="\$".$fieldName['column_name']." = ucwords(strtolower(\$_POST['".$fieldName['column_name']."'])); \n\t";
    $string .="}else{
      \$json->status = 0;
      \$json->title = \"Oops...\";
      \$json->detail = \" Harap Perhatikan ".ucwords(strtolower(str_replace('_', ' ', $fieldName['column_name'])))." tidak boleh kosong \";
      \$json->type = \"error\";
      echo json_encode(\$json);
      die();
  }\n\t";
    }
    
  }
}
$string .="
  \$query = \"INSERT INTO `$table` (";
foreach($nopf as $fieldName){
  $string .="`".$fieldName['column_name']."`,";
}
$string .=")
  VALUES (";
foreach($nopf as $fieldName){
  $string .="'\$".$fieldName['column_name']."',";
}
$string .=")\";
  \$exe = mysqli_query(Connect(),\$query);
  if(\$exe){
    // kalau berhasil
    \$json->status = 1;
    \$json->title = \"Sukses\";
    \$json->detail = \"Data Sukses ditambahkan.\";
    \$json->type = \"success\";
    echo json_encode(\$json);
    die();
  }else{
    \$json->status = 0;
    \$json->title = \"Oops...\";
    \$json->detail = \"Data Gagal ditambahkan.\";
    \$json->type = \"error\";
    echo json_encode(\$json);
    die();
  }
}";
$string .="
if (strtolower(\$param)==\"getdata\") {";
$nopf = NoPrimaryField($table);
$string .='
  $id = deurl($_POST[\''.$pf.'\']);
  if (empty($id)) {
    $json->status = 0;
    $json->detail = "Error UNK ID PARAM.";
    $json->type = "error";
    echo json_encode($json);
    die();
  }
  $sql = "SELECT * FROM  `'.$table.'` WHERE  `'.$pf.'` =  \'$id\'";
  $query = mysqli_query(Connect(),$sql);
  $row = mysqli_num_rows($query);
  if($row==0){
    $json->status = 0;
    $json->detail = "Data Tidak Ada.";
    $json->type = "error";
    echo json_encode($json);
    die();
  }else{
    $result = mysqli_fetch_assoc($query);
  ';
  $string .= "
    \$modal = '
    <div class=\"modal-body\">
      <input type=\"hidden\" class=\"form-control\" name=\"".$pf."\" value=\"'.enurl(\$id).'\" readonly>';";
  foreach($nopf as $field){
    if (preg_match('/tanggal/', strtolower($field['column_name']))) {

    }else{
    $string .="
    \$modal .= '
      <div class=\"form-group\">
        <label for=\"".$field['column_name']."\"> ".ucwords(strtolower(str_replace('_', ' ', $field['column_name'])))."</label>
        <input type=\"text\" class=\"form-control\" id=\"".$field['column_name']."\" name=\"".$field['column_name']."\" placeholder=\"".ucwords(strtolower(str_replace('_', ' ', $field['column_name'])))."\" value=\"'.\$result['".$field['column_name']."'].'\" required>
      </div>';";
    }
    
  }
  $string .="
    \$modal .= '
    </div>
    <div class=\"modal-footer\">
      <button type=\"submit\" class=\"btn btn-primary waves-effect waves-light mr-1\">Simpan</button>
        <button type=\"button\" class=\"btn btn-secondary waves-effect\" data-dismiss=\"modal\">Cancel</button>
    </div>';";
$string .= "
    \$modal = str_replace(\"\\n\", '', \$modal);
    //\$modal = str_replace('\"', '\\\"', \$modal);

    \$json->status = 1;
    \$json->title = \"Sukses\";
    \$json->type = \"success\";
    \$json->modal = \$modal;

    \$json->data->".$pf." = enurl(\$id);";
foreach($nopf as $field){
  if (preg_match('/tanggal/', strtolower($field['column_name']))) {

  }else{
  $string .="
    \$json->data->".$field['column_name']." = \$result['".$field['column_name']."'];";
  }
  
}
$string .='
    echo json_encode($json);
    die();';
$string .= "
  }
}";

$nopf = NoPrimaryField($table);
$string .='
if (strtolower($param)=="edit") {
  $id = deurl($_POST[\''.$pf.'\']);
  if (empty($id)) {
    $json->status = 0;
    $json->detail = "Error UNK ID PARAM.";
    $json->type = "error";
    echo json_encode($json);
    die();
  }

  $sql = "SELECT * FROM  `'.$table.'` WHERE  `'.$pf.'` =  \'$id\'";
  $query = mysqli_query(Connect(),$sql);
  $row = mysqli_num_rows($query);
  if($row==0){
    $json->status = 0;
    $json->detail = "Data Tidak Ada.";
    $json->type = "error";
    echo json_encode($json);
    die();
  }else{
    $result = mysqli_fetch_assoc($query);'."\t\t";
foreach($nopf as $fieldName){
  if (preg_match('/password/', strtolower($fieldName['column_name']))) {
    $string .="\n\t\tif (!empty(\$_POST['".$fieldName['column_name']."'])) {\n\t\t";
    $string .="\t\$".$fieldName['column_name']." = md5(\$_POST['".$fieldName['column_name']."']); \t";
    $string .='
    }else{
      $'.$fieldName['column_name'].' = $result[\''.$fieldName['column_name'].'\'];
    }';
  }else{
    $string .="\n\t\tif (!empty(\$_POST['".$fieldName['column_name']."'])) {\n\t\t";
    $string .="\t\$".$fieldName['column_name']." = ucwords(strtolower(\$_POST['".$fieldName['column_name']."'])); \t";
    $string .='
    }else{
      $'.$fieldName['column_name'].' = $result[\''.$fieldName['column_name'].'\'];
    }';
  }
}
$string .="
  }
  \$query = \"UPDATE `$table` SET ";
  $tmp = '';
  foreach($nopf as $fieldName){
    $tmp .="`".$fieldName['column_name']."` = '\$".$fieldName['column_name']."', ";
  }
  $string .= substr($tmp,0, -2);
  $string .=" WHERE  `$pf` =  '\$id'";
  $string .="\";
  \$exe = mysqli_query(Connect(),\$query);
    if(\$exe){
      // kalau berhasil
      \$json->status = 1;
      \$json->title = \"Sukses\";
      \$json->detail = \"Data Sukses Di Perbaharui.\";
      \$json->type = \"success\";
      echo json_encode(\$json);
      die();
    }else{
      \$json->status = 0;
      \$json->title = \"Oops...\";
      \$json->detail = \"Data Gagal Di Perbaharui.\";
      \$json->type = \"error\";
      echo json_encode(\$json);
      die();
    }";
$string .= '
}';

$string .="
if (strtolower(\$param)==\"delete\") {";
$nopf = NoPrimaryField($table);
$string .='
  $id = deurl($_POST[\''.$pf.'\']);
  if (empty($id)) {
    $json->status = 0;
    $json->detail = "Error UNK ID PARAM.";
    $json->type = "error";
    echo json_encode($json);
    die();
  }
  $sql = "SELECT * FROM  `'.$table.'` WHERE  `'.$pf.'` =  \'$id\'";
  $query = mysqli_query(Connect(),$sql);
  $row = mysqli_num_rows($query);
  if($row==0){
    $json->status = 0;
    $json->detail = "Data tidak ada dalam database.";
    $json->type = "error";
    echo json_encode($json);
    die();
  }else{
    $query = "DELETE FROM `'.$table.'` WHERE `'.$pf.'` = \'$id\'";
    $exe = mysqli_query(Connect(),$query);
    if($exe){
      // kalau berhasil
      $json->status = 1;
      $json->title = "Sukses";
      $json->detail = "Data Sukses dihapus.";
      $json->type = "success";
      echo json_encode($json);
      die();
    }else{
      $json->status = 0;
      $json->detail = "Data Gagal dihapus.";
      $json->type = "error";
      echo json_encode($json);
      die();
    }
  }
';

$string .= "
}
\$json->status = 99;
\$json->detail = \"Unknown Parameter Action.\";
\$json->type = \"error\";
echo json_encode(\$json);
die();";

$string .= "
?>";
$string = str_replace(",)",")", $string);
$string = str_replace(",WHERE"," WHERE", $string);
createFile($string, "../".$table."/act.php");
}
?>