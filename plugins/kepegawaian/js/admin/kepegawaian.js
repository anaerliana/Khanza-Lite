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
    "buttons": ['excel', 'pdf']
  });
  $('#laporan').DataTable({
    "dom": 'Bfrtip',
    "buttons": ['excel', 'pdf']
  });
});

$("#index").on("click","#reset",function(event){
  event.preventDefault();
  var baseURL = mlite.url + '/' + mlite.admin;
  var url = baseURL + '/kepegawaian/statusdel?t=' + mlite.token;
  var id = $(this).attr("data-id");

  // tampilkan dialog konfirmasi
  bootbox.confirm("Apakah Anda yakin ingin menghapus data ini?", function(result){
      // ketika ditekan tombol ok
      if (result){
      // mengirimkan perintah penghapusan
      $.post(url, {
          id: id
      } ,function(data) {
          // sembunyikan form, tampilkan data yang sudah di perbaharui, tampilkan notif
          $("#display").load(baseURL + '/kepegawaian/cuti?t=' + mlite.token);
          bersih();
          $('#notif').html("<div class=\"alert alert-danger alert-dismissible fade in\" role=\"alert\" style=\"border-radius:0px;margin-top:-15px;\">"+
          "Data Status telah direset!"+
          "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">&times;</button>"+
          "</div>").show();
      });
      }
  });
});

function bersih(){
  location.reload();
}


