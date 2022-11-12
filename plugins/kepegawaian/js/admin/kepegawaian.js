// Avatar
var reader  = new FileReader();
reader.addEventListener("load", function() {
  $("#photoPreview").attr('src', reader.result);
}, false);
$("input[name=photo]").change(function() {
  reader.readAsDataURL(this.files[0]);
});
$( function() {
  $('.tanggal').datetimepicker({
    format: 'YYYY-MM-DD',
    locale: 'id'
  });
} );
$(document).ready(function(){
  $('.display').DataTable({
    "language": { "search": "", "searchPlaceholder": "Search..." },
    "lengthChange": false,
    "scrollX": true,
    dom: "<<'data-table-title'><'datatable-search'f>><'row'<'col-sm-12'tr>><<'pmd-datatable-pagination' l i p>>",
  });
});

$(document).ready(function() {
  $('#pardah').DataTable({
    "dom": 'Bfrtip',
   // "buttons": ['excel', 'pdf'],
    "ordering": false,
    "buttons" : [
      {
          extend: 'excel',
          exportOptions: {
              columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 9 ]
          }
      }
    ]
  });
  $('#laporan').DataTable({
    "dom": 'Bfrtip',
    "buttons": ['excel', 'pdf']
  });
});