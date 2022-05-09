var reader = new FileReader();
reader.addEventListener("load", function () {
  $("#photoPreview").attr('src', reader.result);
}, false);
$("input[name=photo]").change(function () {
  reader.readAsDataURL(this.files[0]);
});
$(function () {
  $(".tanggal").datetimepicker({
    format: 'YYYY-MM-DD',
    locale: 'id'
  });
});

$(document).ready(function () {
  jQuery('.timeline').timeline({
    //mode: 'horizontal',
    //visibleItems: 4
    //Remove this comment for see Timeline in Horizontal Format otherwise it will display in Vertical Direction Timeline
  });
});

$("#form").on("click", "#manage", function (event) {
  var baseURL = mlite.url + '/' + mlite.admin;
  event.preventDefault();
  var url = baseURL + '/profil/bridgingbkd?t=' + mlite.token;
  var bulan = $("input:hidden[name=bulan]").val();
  var nik = $("input:hidden[name=nik]").val();
  var shift = $("input:text[name='shift[]']").map(function () { return $(this).val(); }).get();
  var jam_datang = $("input:text[name='jam_datang[]']").map(function () { return $(this).val(); }).get();
  var jam_pulang = $("input:text[name='jam_pulang[]']").map(function () { return $(this).val(); }).get();

  // tampilkan dialog konfirmasi
  bootbox.confirm("Apakah Anda yakin ingin mengirim data ini?", function (result) {
    // ketika ditekan tombol ok
    if (result) {
      // mengirimkan perintah penghapusan
      $.post(url, {
        bulan: bulan,
        nik: nik,
        shift: shift,
        jam_datang: jam_datang,
        jam_pulang: jam_pulang,
      }, function (data) {
        console.log(data);
        if (data == 'Sukses') {
          $("#display").load(baseURL + '/profil/rekap_presensi?t=' + mlite.token);
          $('#notif').html("<div class=\"alert alert-success alert-dismissible fade in\" role=\"alert\" style=\"border-radius:0px;margin-top:-15px;\">" +
            "Data presensi telah dikirim!" +
            "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">&times;</button>" +
            "</div>").show();
        } else {
          $("#display").load(baseURL + '/profil/rekap_presensi?t=' + mlite.token);
          $('#notif').html("<div class=\"alert alert-warning alert-dismissible fade in\" role=\"alert\" style=\"border-radius:0px;margin-top:-15px;\">" +
            "Data presensi gagal dikirim!" +
            "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">&times;</button>" +
            "</div>").show();
        }
        // sembunyikan form, tampilkan data yang sudah di perbaharui, tampilkan notif
      });
    }
  });
});

setTimeout(function(){
  $('#notif').fadeOut('fast');
}, 5000);
