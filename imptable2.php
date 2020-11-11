<?php
echo '<script type="text/javascript">
  $(document).ready(function(){
   $("#labTable").DataTable({
      "processing": true,
      "serverSide": true,
      "serverMethod": "post",
      "ajax": {
          "url":"ajaxlab.php"
      },
      "columns": [
         { data: "id" },
         { data: "name" },
         { data: "total_stages" },
         { data: "total_cost" },
         { data: "density" },
         { data: "mw" },
         { data: "qty_out" },
         { data: "w_w_yield" },
         { data: "cc_kg_output" },
         { data: "api_cc" },
         { data: "actual_cc_kg_api" },
         { data: "mol" },
         { data: "mol_yield" },
         { data: "contribution" },
         { data: "created_at" },
         { data: "Options" },
      ]
   });
});
</script>';
?>
