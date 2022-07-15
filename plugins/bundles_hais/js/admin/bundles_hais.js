// Avatar
var reader = new FileReader();
reader.addEventListener("load", function () {
  $("#photoPreview").attr('src', reader.result);
}, false);
$(function () {
  $('.tanggal').datetimepicker({
    format: 'YYYY-MM-DD',
    locale: 'id'
  });
});
$(document).ready(function () {
  $('.display').DataTable({
    "language": { "search": "", "searchPlaceholder": "Search..." },
    "lengthChange": false,
    "scrollX": true,
    dom: "<<'data-table-title'><'datatable-search'f>><'row'<'col-sm-12'tr>><<'pmd-datatable-pagination' l i p>>"
  });
});

//bundles_insersi
// ketika tombol simpan diklik
$("#bundles_insersi").on("click", "#simpan_insersi", function (event) {
  var baseURL = mlite.url + '/' + mlite.admin;
  event.preventDefault();

  var no_rawat = $('input:text[name=no_rawat]').val();
  var tanggal = $('input:text[name=tanggalbundles]').val();
  var kd_kamar = $('input:text[name=kd_kamar]').val();
  var hand_vap = $('input:radio[name=hand_vap]:checked').val();
  var tehniksteril_vap = $('input:radio[name=tehniksteril_vap]:checked').val();
  var apd_vap = $('input:radio[name=apd_vap]:checked').val();
  var sedasi_vap = $('input:radio[name=sedasi_vap]:checked').val();
  var hand_iadp = $('input:radio[name=hand_iadp]:checked').val();
  var area_iadp = $('input:radio[name=area_iadp]:checked').val();
  var tehniksteril_iadp = $('input:radio[name=tehniksteril_iadp]:checked').val();
  var alcohol_iadp = $('input:radio[name=alcohol_iadp]:checked').val();
  var apd_iadp = $('input:radio[name=apd_iadp]:checked').val();
  var hand_vena = $('input:radio[name=hand_vena]:checked').val();
  var kaji_vena = $('input:radio[name=kaji_vena]:checked').val();
  var tehnik_vena = $('input:radio[name=tehnik_vena]:checked').val();
  var petugas_vena = $('input:radio[name=petugas_vena]:checked').val();
  var desinfeksi_vena = $('input:radio[name=desinfeksi_vena]:checked').val();
  var kaji_isk = $('input:radio[name=kaji_isk]:checked').val();
  var petugas_isk = $('input:radio[name=petugas_isk]:checked').val();
  var tangan_isk = $('input:radio[name=tangan_isk]:checked').val();
  var tehniksteril_isk = $('input:radio[name=tehniksteril_isk]:checked').val()

  console.log({
    no_rawat, kd_kamar,
    tanggal,
    hand_vap,
    tehniksteril_vap,
    apd_vap,
    sedasi_vap,
    hand_iadp,
    area_iadp,
    tehniksteril_iadp,
    alcohol_iadp,
    apd_iadp,
    hand_vena,
    kaji_vena,
    tehnik_vena,
    petugas_vena,
    desinfeksi_vena,
    kaji_isk,
    petugas_isk,
    tangan_isk,
    tehniksteril_isk
  });
  var url = baseURL + '/bundles_hais/saveinsersi?t=' + mlite.token;
  $.post(url, {
    no_rawat: no_rawat,
    tanggal: tanggal,
    kd_kamar: kd_kamar,
    hand_vap: hand_vap,
    tehniksteril_vap: tehniksteril_vap,
    apd_vap: apd_vap,
    sedasi_vap: sedasi_vap,
    hand_iadp: hand_iadp,
    area_iadp: area_iadp,
    tehniksteril_iadp: tehniksteril_iadp,
    alcohol_iadp: alcohol_iadp,
    apd_iadp: apd_iadp,
    hand_vena: hand_vena,
    kaji_vena: kaji_vena,
    tehnik_vena: tehnik_vena,
    petugas_vena: petugas_vena,
    desinfeksi_vena: desinfeksi_vena,
    kaji_isk: kaji_isk,
    petugas_isk: petugas_isk,
    tangan_isk: tangan_isk,
    tehniksteril_isk: tehniksteril_isk

  }, function (data) {
    // console.log(data);
    // tampilkan data
    // var url = baseURL + '/bundles_hais/bundlesinsersi?t=' + mlite.token;
    var url = baseURL + '/bundles_hais/bundlesinsersi/' + data + '?t=' + mlite.token;
    // $.post(url, {
    //   no_rawat: no_rawat,
    // }, function (data) {
      // tampilkan data
      window.location = url;
    //   console.log(data);
    //   $("#insersi").html(data).show();
    // });
    $('input:radio[name=hand_vap]:checked').val("");
    $('input:radio[name=tehniksteril_vap]:checked').val("");
    $('input:radio[name=apd_vap]:checked').val("");
    $('input:radio[name=sedasi_vap]:checked').val("");
    $('input:radio[name=hand_iadp]:checked').val("");
    $('input:radio[name=area_iadp]:checked').val("");
    $('input:radio[name=tehniksteril_iadp]:checked').val("");
    $('input:radio[name=alcohol_iadp]:checked').val("");
    $('input:radio[name=apd_iadp]:checked').val("");
    $('input:radio[name=hand_vena]:checked').val("");
    $('input:radio[name=kaji_vena]:checked').val("");
    $('input:radio[name=tehnik_vena]:checked').val("");
    $('input:radio[name=petugas_vena]:checked').val("");
    $('input:radio[name=desinfeksi_vena]:checked').val("");
    $('input:radio[name=kaji_isk]:checked').val("");
    $('input:radio[name=petugas_isk]:checked').val("");
    $('input:radio[name=tangan_isk]:checked').val("");
    $('input:radio[name=tehniksteril_isk]:checked').val("");
    $('input:text[name=tanggalbundles]').val("{?=date('Y-m-d')?}");

    $('#notif').html("<div class=\"alert alert-success alert-dismissible fade in\" role=\"alert\" style=\"border-radius:0px;margin-top:-15px;\">" +
      "Data Bundles Insersi telah disimpan!" +
      "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">&times;</button>" +
      "</div>").show();
  });
  // alert("coba lagi");
});

$("#bundles_insersi").on("click", "#selesai_insersi", function (event) {
  bersih();
  // $("#bundles_insersi").hide();
  // $("#bundles_maintanance").hide();
  // $("#bundles_ido").hide();

  // alert("coba lagi");
});

//bundles_maintanance
// // ketika tombol simpan diklik
$("#bundles_maintanance").on("click", "#simpan_maintanance", function (event) {
  var baseURL = mlite.url + '/' + mlite.admin;
  event.preventDefault();

  var no_rawat = $('input:text[name=no_rawat]').val();
  var tanggal = $('input:text[name=tanggalbundles]').val();
  var kd_kamar = $('input:text[name=kd_kamar]').val();
  var hand_mainvap = $('input:radio[name=hand_mainvap]:checked').val();
  var oral_mainvap = $('input:radio[name=oral_mainvap]:checked').val();
  var manage_mainvap = $('input:radio[name=manage_mainvap]:checked').val();
  var sedasi_mainvap = $('input:radio[name=sedasi_mainvap]:checked').val();
  var kepala_mainvap = $('input:radio[name=kepala_mainvap]:checked').val();
  var hand_mainiadp = $('input:radio[name=hand_mainiadp]:checked').val();
  var desinfeksi_mainiadp = $('input:radio[name=desinfeksi_mainiadp]:checked').val();
  var perawatan_mainiadp = $('input:radio[name=perawatan_mainiadp]:checked').val();
  var dreasing_mainiadp = $('input:radio[name=dreasing_mainiadp]:checked').val();
  var infus_mainiadp = $('input:radio[name=infus_mainiadp]:checked').val();
  var hand_mainvena = $('input:radio[name=hand_mainvena]:checked').val();
  var perawatan_mainvena = $('input:radio[name=perawatan_mainvena]:checked').val();
  var kaji_mainvena = $('input:radio[name=kaji_mainvena]:checked').val();
  var administrasi_mainvena = $('input:radio[name=administrasi_mainvena]:checked').val();
  var edukasi_mainvena = $('input:radio[name=edukasi_mainvena]:checked').val();
  var hand_mainisk = $('input:radio[name=hand_mainisk]:checked').val();
  var kateter_mainisk = $('input:radio[name=kateter_mainisk]:checked').val()
  var baglantai_mainisk = $('input:radio[name=baglantai_mainisk]:checked').val()
  var bagrendah_mainisk = $('input:radio[name=bagrendah_mainisk]:checked').val()
  var posisiselang_mainisk = $('input:radio[name=posisiselang_mainisk]:checked').val()
  var lepas_mainisk = $('input:radio[name=kateter_mainisk]:checked').val()

  console.log({
    no_rawat, kd_kamar,
    tanggal,
    hand_mainvap,
    oral_mainvap,
    manage_mainvap,
    sedasi_mainvap,
    kepala_mainvap,
    hand_mainiadp,
    desinfeksi_mainiadp,
    perawatan_mainiadp,
    dreasing_mainiadp,
    infus_mainiadp,
    infus_mainiadp,
    hand_mainvena,
    perawatan_mainvena,
    kaji_mainvena,
    administrasi_mainvena,
    edukasi_mainvena,
    hand_mainisk,
    kateter_mainisk,
    baglantai_mainisk,
    bagrendah_mainisk,
    posisiselang_mainisk,
    lepas_mainisk
  });
  var url = baseURL + '/bundles_hais/savemaintanance?t=' + mlite.token;
  $.post(url, {
    no_rawat: no_rawat,
    tanggal: tanggal,
    kd_kamar: kd_kamar,
    hand_mainvap: hand_mainvap,
    oral_mainvap: oral_mainvap,
    manage_mainvap: manage_mainvap,
    sedasi_mainvap: sedasi_mainvap,
    kepala_mainvap: kepala_mainvap,
    hand_mainiadp: hand_mainiadp,
    desinfeksi_mainiadp: desinfeksi_mainiadp,
    perawatan_mainiadp: perawatan_mainiadp,
    dreasing_mainiadp: dreasing_mainiadp,
    infus_mainiadp: infus_mainiadp,
    hand_mainvena: hand_mainvena,
    perawatan_mainvena: perawatan_mainvena,
    kaji_mainvena: kaji_mainvena,
    administrasi_mainvena: administrasi_mainvena,
    edukasi_mainvena: edukasi_mainvena,
    hand_mainisk: hand_mainisk,
    kateter_mainisk: kateter_mainisk,
    baglantai_mainisk: baglantai_mainisk,
    bagrendah_mainisk: bagrendah_mainisk,
    posisiselang_mainisk: posisiselang_mainisk,
    lepas_mainisk: lepas_mainisk

  }, function (data) {
    console.log(data);
    // tampilkan data
    var url = baseURL + '/bundles_hais/bundles_maitanance/' + data + '?t=' + mlite.token;
    // var url = baseURL + '/bundles_hais/bundles_maintanance?t=' + mlite.token;
    // $.post(url, {
    //   no_rawat: no_rawat,
    // }, function (data) {
    //   // tampilkan data
    window.location = url;
    //   console.log(data);
    //   $("#maintanance").html(data).show();
    // });
    // $('input:text[name=no_rawat]').val("");
    // $('input:text[name=kd_kamar]').val("");
    $('input:radio[name=hand_mainvap]:checked').val("");
    $('input:radio[name=oral_mainvap]:checked').val("");
    $('input:radio[name=apd_vap]:checked').val("");
    $('input:radio[name=sedasi_mainvap]:checked').val("");
    $('input:radio[name=kepala_mainvap]:checked').val("");
    $('input:radio[name=hand_mainiadp]:checked').val("");
    $('input:radio[name=desinfeksi_mainiadp]:checked').val("");
    $('input:radio[name=perawatan_mainiadp]:checked').val("");
    $('input:radio[name=dreasing_mainiadp]:checked').val("");
    $('input:radio[name=infus_mainiadp]:checked').val("");
    $('input:radio[name=hand_mainvena]:checked').val("");
    $('input:radio[name=perawatan_mainvena]:checked').val("");
    $('input:radio[name=kaji_mainvena]:checked').val("");
    $('input:radio[name=administrasi_mainvena]:checked').val("");
    $('input:radio[name=edukasi_mainvena]:checked').val("");
    $('input:radio[name=hand_mainisk]:checked').val("");
    $('input:radio[name=kateter_mainisk]:checked').val("");
    $('input:radio[name=baglantai_mainisk]:checked').val("");
    $('input:radio[name=bagrendah_mainisk]:checked').val("");
    $('input:radio[name=posisiselang_mainisk]:checked').val("");
    $('input:radio[name=kateter_mainisk]:checked').val("");
    $('input:text[name=tanggalbundles]').val("{?=date('Y-m-d')?}");

    $('#notif').html("<div class=\"alert alert-success alert-dismissible fade in\" role=\"alert\" style=\"border-radius:0px;margin-top:-15px;\">" +
      "Data Bundles Maintanance telah disimpan!" +
      "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">&times;</button>" +
      "</div>").show();
  });
  // alert("coba lagi");
});

$("#bundles_maintanance").on("click", "#selesai_maintanance", function (event) {
  bersih();
  // $("#bundles_insersi").hide();
  // $("#bundles_maintanance").hide();
  // $("#bundles_ido").hide();

  // alert("coba lagi");
});

//bundles_ido
// // ketika tombol simpan diklik
$("#bundles_ido").on("click", "#simpan_ido", function (event) {
  var baseURL = mlite.url + '/' + mlite.admin;
  event.preventDefault();

  var no_rawat = $('input:text[name=no_rawat]').val();
  var tanggal = $('input:text[name=tanggalbundles]').val();
  var kd_kamar = $('input:text[name=kd_kamar]').val();
  var mandi_idopre = $('input:radio[name=mandi_idopre]:checked').val();
  var cukur_idopre = $('input:radio[name=cukur_idopre]:checked').val();
  var guladarah_idopre = $('input:radio[name=guladarah_idopre]:checked').val();
  var antibiotik_idopre = $('input:radio[name=antibiotik_idopre]:checked').val();
  var hand_idointra = $('input:radio[name=hand_idointra]:checked').val();
  var steril_idointra = $('input:radio[name=steril_idointra]:checked').val();
  var antiseptic_idointra = $('input:radio[name=antiseptic_idointra]:checked').val();
  var tehnik_idointra = $('input:radio[name=tehnik_idointra]:checked').val();
  var mobile_idointra = $('input:radio[name=mobile_idointra]:checked').val();
  var suhu_idointra = $('input:radio[name=suhu_idointra]:checked').val();
  var luka_idopost = $('input:radio[name=luka_idopost]:checked').val();
  var rawat_idopost = $('input:radio[name=rawat_idopost]:checked').val();
  var apd_idopost = $('input:radio[name=apd_idopost]:checked').val();
  var kaji_idopost = $('input:radio[name=kaji_idopost]:checked').val();

  console.log({
    no_rawat, 
    tanggal,
    kd_kamar,
    mandi_idopre,       
    cukur_idopre,         
    guladarah_idopre,
    antibiotik_idopre,
    hand_idointra,
    steril_idointra ,
    antiseptic_idointra ,
    tehnik_idointra,
    mobile_idointra,
    suhu_idointra,        
    luka_idopost,
    rawat_idopost, 
    apd_idopost,
    kaji_idopost
  });
  var url = baseURL + '/bundles_hais/savebundles_ido?t=' + mlite.token;
  $.post(url, {
    no_rawat: no_rawat,
    tanggal: tanggal,
    kd_kamar: kd_kamar,
    mandi_idopre: mandi_idopre,
    cukur_idopre: cukur_idopre,
    guladarah_idopre: guladarah_idopre,
    antibiotik_idopre: antibiotik_idopre,
    hand_idointra: hand_idointra,
    steril_idointra: steril_idointra,
    antiseptic_idointra: antiseptic_idointra,
    tehnik_idointra: tehnik_idointra,
    mobile_idointra: mobile_idointra,
    suhu_idointra: suhu_idointra,
    luka_idopost: luka_idopost,
    rawat_idopost: rawat_idopost,
    apd_idopost: apd_idopost,
    kaji_idopost: kaji_idopost

  }, function (data) {
    // tampilkan data
    var url = baseURL + '/bundles_hais/bundles_ido/' + data + '?t=' + mlite.token;
    // var url = baseURL + '/bundles_hais/ido?t=' + mlite.token;
    // $.post(url, {
      // no_rawat : no_rawat,
      // }, function (e) {
        // tampilkan data
        // console.log(data);
        window.location = url;
        // console.log(e);
      // $("#bundles_ido").html(data).show();
      //$("#bundles_ido").show().load(baseURL + '/bundles_hais/bundles_ido'+no_rawat+'?t=' + mlite.token);
      // $("#ido").load(location.href + " #ido");
    // });
    // $('input:text[name=no_rawat]').val("");
    // $('input:text[name=kd_kamar]').val("");
    $('input:radio[name=mandi_idopre]:checked').val("");
    $('input:radio[name=cukur_idopre]:checked').val("");
    $('input:radio[name=guladarah_idopre]:checked').val("");
    $('input:radio[name=antibiotik_idopre]:checked').val("");
    $('input:radio[name=hand_idointra]:checked').val("");
    $('input:radio[name=steril_idointra]:checked').val("");
    $('input:radio[name=antiseptic_idointra]:checked').val("");
    $('input:radio[name=tehnik_idointra]:checked').val("");
    $('input:radio[name=mobile_idointra]:checked').val("");
    $('input:radio[name=suhu_idointra]:checked').val("");
    $('input:radio[name=luka_idopost]:checked').val("");
    $('input:radio[name=rawat_idopost]:checked').val("");
    $('input:radio[name=apd_idopost]:checked').val("");
    $('input:radio[name=kaji_idopost]:checked').val("");
    $('input:text[name=tanggalbundles]').val("{?=date('Y-m-d')?}");

    $('#notif').html("<div class=\"alert alert-success alert-dismissible fade in\" role=\"alert\" style=\"border-radius:0px;margin-top:-15px;\">" +
      "Data Bundles IDO telah disimpan!" +
      "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">&times;</button>" +
      "</div>").show();
  });
  // alert("coba lagi");
});

$("#bundles_ido").on("click", ".hapus_ido_post", function (event) {
  var baseURL = mlite.url + '/' + mlite.admin;
  event.preventDefault();
  var url = baseURL + '/rawat_inap/hapusido?t=' + mlite.token;
  var no_rawat = $(this).attr("data-no_rawat");
  var tanggal = $(this).attr("data-tanggal");
  var luka_idopost = $(this).attr("data-luka_idopost");

  // tampilkan dialog konfirmasi
  bootbox.confirm("Apakah Anda yakin ingin menghapus data ini?", function (result) {
    // ketika ditekan tombol ok
    if (result) {
      // mengirimkan perintah penghapusan
      $.post(url, {
        no_rawat: no_rawat,
        tanggal: tanggal,
        luka_idopost: luka_idopost,
      }, function (data) {
        var url = baseURL + '/rawat_inap/bundles_ido?t=' + mlite.token;
        $.post(url, {
          no_rawat: no_rawat,
        }, function (data) {
          // tampilkan data
          $("#ido").html(data).show();
        });
        $('input:radio[name=luka_idopost]:checked').val("");
        $('input:radio[name=rawat_idopost]:checked').val("");
        $('input:radio[name=apd_idopost]:checked').val("");
        $('input:radio[name=kaji_idopost]:checked').val("");
        $('input:text[name=tanggalbundles]').val("{?=date('Y-m-d')?}");
        $('#notif').html("<div class=\"alert alert-danger alert-dismissible fade in\" role=\"alert\" style=\"border-radius:0px;margin-top:-15px;\">" +
          "Data Bundles IDO POST telah dihapus!" +
          "<button type=\"button\" class=\"close\" data-dismiss=\"alert\" aria-label=\"Close\">&times;</button>" +
          "</div>").show();
      });
    }
  });
});


$("#bundles_ido").on("click", "#selesai_ido", function (event) {
  bersih();
  // $("#bundles_insersi").hide();
  // $("#bundles_maintanance").hide();
  // $("#bundles_ido").hide();

  // alert("coba lagi");
});

function bersih() {
  window.location.href = "http://localhost/litegithub/admin/bundles_hais/manage";
}

// function load_list(){
//   $.ajax({
//       url:"bundles.ido.html",
//       success: function(html)
//       {
//           $("#ido").html(html);
//       }
//   });
// }

