<?php
echo '<script type="text/javascript">
  $(document).ready(function(){
   $("#empTable").DataTable({
      "processing": true,
      "serverSide": true,
      "serverMethod": "post",
      "ajax": {
          "url":"ajaxfile.php"
      },
      "columns": [
         { data: "id" },
         { data: "username" },
         { data: "password" },
         { data: "email" },
         { data: "userdescr" },
         { data: "utype" },
         { data: "Options" },
      ]
   });
});
</script>';
?>
