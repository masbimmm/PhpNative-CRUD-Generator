<?php
function gen_func($table){
$pf = PrimaryField($table);
$string = '';
$string .= "<?php
require_once '../config/conn.php';

function GetAll(){
  \$query = \"SELECT * FROM ".$table."\";
  \$exe = mysqli_query(Connect(),\$query);
  while(\$data = mysqli_fetch_array(\$exe)){
    \$datas[] = array(";
    $fields = AllField($table);
    foreach($fields as $fieldName){
      $string .= "'".$fieldName['column_name']."' => \$data['".$fieldName['column_name']."'],\n\t\t";
    }
$string .= "
    );
  }
  return \$datas;
}
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
      echo '{\"status\":\"0\",\"title\":\" Oops \",\"detail\":\" Harap Perhatikan ".ucwords(strtolower(str_replace('_', ' ', $fieldName['column_name'])))." tidak boleh kosong \",\"type\":\"error\"}';
      die();
  }\n\t";
    }else{
    $string .="if (!empty(\$_POST['".$fieldName['column_name']."'])) {\n\t\t";
    $string .="\$".$fieldName['column_name']." = ucwords(strtolower(\$_POST['".$fieldName['column_name']."'])); \n\t";
    $string .="}else{
      echo '{\"status\":\"0\",\"title\":\" Oops \",\"detail\":\" Harap Perhatikan ".ucwords(strtolower(str_replace('_', ' ', $fieldName['column_name'])))." tidak boleh kosong \",\"type\":\"error\"}';
      die();
  }\n\t";
    }
    
  }
}
$string .="
  \$query = \"INSERT INTO `$table` (";
foreach($fields as $fieldName){
  $string .="`".$fieldName['column_name']."`,";
}
$string .=")
  VALUES (NULL,";
foreach($nopf as $fieldName){
  $string .="'\$".$fieldName['column_name']."',";
}
$string .=")\";
  \$exe = mysqli_query(Connect(),\$query);
  if(\$exe){
    // kalau berhasil
    echo '{\"status\":\"1\",\"title\":\" Sukses \",\"detail\":\" Data Sukses ditambahkan. \",\"type\":\"success\"}';
  }else{
    echo '{\"status\":\"0\",\"title\":\" Oops... \",\"detail\":\" Gagal Menambahkan Data. \",\"type\":\"error\"}';
    die();
  }
}";
$string .="
if (strtolower(\$param)==\"getdata\") {";
$nopf = NoPrimaryField($table);
$string .='
  $id = deurl($_POST[\''.$pf.'\']);
  if (empty($id)) {
    echo \'<div class="modal-body"><div class="text-center"><b>Error UNK ID PARAM<b></div></div>\';
    die();
  }
  $sql = "SELECT * FROM  `'.$table.'` WHERE  `'.$pf.'` =  \'$id\'";
  $query = mysqli_query(Connect(),$sql);
  $row = mysqli_num_rows($query);
  if($row==0){
    echo \'<div class="modal-body"><div class="text-center"><b>Data tidak ada dalam database<b></div></div>\';
    die();
  }else{
    $result = mysqli_fetch_assoc($query);
  }
  ';

$string .= "echo '
  <div class=\"modal-body\">
    <input type=\"hidden\" class=\"form-control\" name=\"".$pf."\" value=\"'.enurl(\$id).'\" readonly>";
foreach($nopf as $field){
  if (preg_match('/tanggal/', strtolower($field['column_name']))) {

  }else{
  $string .="
    <div class=\"form-group\">
      <label for=\"".$field['column_name']."\"> ".ucwords(strtolower(str_replace('_', ' ', $field['column_name'])))."</label>
      <input type=\"text\" class=\"form-control\" id=\"".$field['column_name']."\" name=\"".$field['column_name']."\" placeholder=\"".ucwords(strtolower(str_replace('_', ' ', $field['column_name'])))."\" value=\"'.\$result['".$field['column_name']."'].'\" required>
    </div>";
  }
  
}
$string .='
  </div>
  <div class="modal-footer">
    <button type="submit" class="btn btn-primary waves-effect waves-light mr-1">Simpan</button>
      <button type="button" class="btn btn-secondary waves-effect" data-dismiss="modal">Cancel</button>
  </div>';
$string .= "';
}";

$nopf = NoPrimaryField($table);
$string .='
if (strtolower($param)=="edit") {
  $id = deurl($_POST[\''.$pf.'\']);
  if (empty($id)) {
    echo \'<div class="modal-body"><div class="text-center"><b>Error UNK ID PARAM<b></div></div>\';
    die();
  }

  $sql = "SELECT * FROM  `'.$table.'` WHERE  `'.$pf.'` =  \'$id\'";
  $query = mysqli_query(Connect(),$sql);
  $row = mysqli_num_rows($query);
  if($row==0){
    echo \'{"status":"0","title":" Oops... ","detail":" Data tidak ada dalam database. ","type":"error"}\';
    die();
  }else{
    $result = mysqli_fetch_assoc($query);
  }'."\n\t\t";

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
      echo '{\"status\":\"1\",\"title\":\" Sukses \",\"detail\":\" Data Sukses diperbaharui. \",\"type\":\"success\"}';
    }else{
      echo '{\"status\":\"0\",\"title\":\" Oops... \",\"detail\":\" Gagal Memperbaharui Data. \",\"type\":\"error\"}';
      die();
    }";
$string .= '
}';

$string .="
if (strtolower(\$param)==\"delete\") {
  ";
$nopf = NoPrimaryField($table);
$string .='
  $id = deurl($_POST[\''.$pf.'\']);
  if (empty($id)) {
    echo \'{"status":"0","title":" Oops... ","detail":" Unk Param ID. ","type":"error"}\';
    die();
  }
  $sql = "SELECT * FROM  `'.$table.'` WHERE  `'.$pf.'` =  \'$id\'";
  $query = mysqli_query(Connect(),$sql);
  $row = mysqli_num_rows($query);
  if($row==0){
    echo \'{"status":"0","title":" Oops... ","detail":" Data tidak ada dalam database. ","type":"error"}\';
    die();
  }else{
    $query = "DELETE FROM `'.$table.'` WHERE `'.$pf.'` = \'$id\'";
    $exe = mysqli_query(Connect(),$query);
      if($exe){
        echo \'{"status":"1","title":" Sukses ","detail":" Data berhasil dihapus. ","type":"success"}\';
        die();
      }else{
        echo \'{"status":"0","title":" Oops... ","detail":" Gagal Menghapus data. ","type":"error"}\';
        die();
      }
  }
';

$string .= "
}";

$string .= "
?>";

mkdir("../".$table);
createFile($string, "../".$table."/func.php");
Replace($table,"func",",)",")");
Replace($table,"func",",WHERE"," WHERE");
}

?>