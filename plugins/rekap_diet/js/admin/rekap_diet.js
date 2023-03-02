
   $(function () {
       $('.periode_rawat_jalan').datetimepicker({
         defaultDate: new Date(),
         format: 'YYYY-MM-DD',
         locale: 'id'
       });
   });

$(document).ready(function () {
  $("#pardah").DataTable({
    dom: "Bfrtip",
    order: [[0, "asc"]],
  });
});

$("#form").hide();
$('select').selectator();

// tombol buka form diklik
$("#index").on('click', '#bukaform', function(){
  var baseURL = mlite.url + '/' + mlite.admin;
  event.preventDefault();
  $("#form").show().load(baseURL + '/rekap_diet/form?t=' + mlite.token);
  $("#bukaform").val("Tutup Form");
  $("#bukaform").attr("id", "tutupform");
});

// tombol tutup form diklik
$("#index").on('click', '#tutupform', function(){
  event.preventDefault();
  $("#form").hide();
  $("#tutupform").val("Tambah Item Diet");
  $("#tutupform").attr("id", "bukaform");
});

// // ketika inputbox kd_diet diklik
// $("#form").on("click","#kd_diet", function(event){
//   var kd_diet_baru = $("#kd_diet_baru").val();
//   $("#kd_diet").val(kd_diet_baru);
// });

$("#form").on("click", ".kd_diet", function(event){
  var baseURL = mlite.url + '/' + mlite.admin;
  event.preventDefault();
  var url = baseURL + '/rekap_diet/kodediet?t=' + mlite.token;
  $.get(url ,function(data) {
    // tampilkan data
    //console.log(data);
    $("#kd_diet").val(data);
  });
});

$("#form").on("click",".batal",function(event){
    event.preventDefault();
    $("#form").hide();
    $("#tutupform").val("Tambah Item Diet");
    $("#tutupform").attr("kd_diet", "bukaform");
    $("input:text[name=kd_diet]").val("");
    $("input:text[name=nama_diet]").val("");
});

$("#form").on("click", "#simpan_itemdiet", function(event){
  var baseURL = mlite.url + '/' + mlite.admin;
  event.preventDefault();
  var kd_diet         = $('input:text[name=kd_diet]').val();
  var nama_diet       = $('input:text[name=nama_diet]').val();
  console.log({
    kd_diet, nama_diet
  });

  var url = baseURL + '/rekap_diet/itemdietsave?t=' + mlite.token;
  $.post(url, {
    kd_diet : kd_diet,
    nama_diet: nama_diet,
  }, function(data) {
    console.log(data);

     var url = baseURL + '/rekap_diet/itemdiet?t=' + mlite.token;
    $.get(url, function(data) {
      // tampilkan data
     // console.log(data);
      $("#index").html(data).show();

    });
      $('input:text[name=kd_diet]').val("");
      $('input:text[name=nama_diet]').val("");
      $('#notif').html("<div class=\"alert alert-success alert-dismissible fade in\" role=\"alert\" style=\"border-radius:0px;margin-top:-15px;\">"+
      "Data Item Diet telah disimpan!"+
      "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">&times;</button>"+
      "</div>").show();
    // } else {
    //   $('#notif').html("<div class=\"alert alert-danger alert-dismissible fade in\" role=\"alert\" style=\"border-radius:0px;margin-top:-15px;\">"+
    //   "Data Item Diet gagal disimpan!"+
    //   "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">&times;</button>"+
    //   "</div>").show();
    // }
  });
   //alert("coba lagi");
});

$("#display").on("click",".edititem",function(event){
    event.preventDefault();
    $("#form").show();
    $("#bukaform").val("Tutup Form");
    $("#bukaform").attr("id", "tutupform");
    var nama_diet = $(this).attr("data-nama_diet");
    var kd_diet = $(this).attr("data-kd_diet");

    $("input:text[name=nama_diet]").val(nama_diet);
    $("input:text[name=kd_diet]").val(kd_diet);
    // document.getElementById("kd_diet").value = kd_diet;
});

// // ketika tombol hapus ditekan
// $("#display").on("click",".hapus_itemdiet", function(event){
// var baseURL = mlite.url + '/' + mlite.admin;
// event.preventDefault();
// var url = baseURL + '/rekap_diet/hapusitemdiet?t=' + mlite.token;
// var kd_diet = $(this).attr("data-kd_diet");

// // tampilkan dialog konfirmasi
// bootbox.confirm("Apakah Anda yakin ingin menghapus data ini?", function(result){
//   // ketika ditekan tombol ok
//   if (result){
//     // mengirimkan perintah penghapusan
//     $.post(url, {
//       kd_diet: kd_diet,
//     } ,function(data) {
//       var url = baseURL + '/rekap_diet/itemdiet?t=' + mlite.token;
//       $.get(url, {kd_diet : kd_diet,
//       }, function(data) {
//         // tampilkan data
//         $("#manage").html(data).show();
//       });
//      $('input:text[name=nama_diet]').val("");
//       $('#notif').html("<div class=\"alert alert-danger alert-dismissible fade in\" role=\"alert\" style=\"border-radius:0px;margin-top:-15px;\">"+
//       "Data Item Diet telah dihapus!"+
//       "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">&times;</button>"+
//       "</div>").show();
//     });
//   }
// });
// });