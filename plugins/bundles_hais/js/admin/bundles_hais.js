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

//bundles_insersi
// ketika tombol simpan diklik
$("#bundles_insersi").on("click", "#simpan_insersi", function (event) {
  var baseURL = mlite.url + '/' + mlite.admin;
  event.preventDefault();

  var no_rawat          = $('input:text[name=no_rawat]').val();
  var tanggal           = $('input:text[name=tanggalbundles]').val();
  var kd_kamar          = $('input:text[name=kd_kamar]').val();
  var hand_vap          = $('input:radio[name=hand_vap]:checked').val();
  var tehniksteril_vap  = $('input:radio[name=tehniksteril_vap]:checked').val();
  var apd_vap           = $('input:radio[name=apd_vap]:checked').val();
  var sedasi_vap        = $('input:radio[name=sedasi_vap]:checked').val();
  var hand_iadp         = $('input:radio[name=hand_iadp]:checked').val();
  var area_iadp         = $('input:radio[name=area_iadp]:checked').val();
  var tehniksteril_iadp = $('input:radio[name=tehniksteril_iadp]:checked').val();
  var alcohol_iadp      = $('input:radio[name=alcohol_iadp]:checked').val();
  var apd_iadp          = $('input:radio[name=apd_iadp]:checked').val();
  var hand_vena         = $('input:radio[name=hand_vena]:checked').val();
  var kaji_vena         = $('input:radio[name=kaji_vena]:checked').val();
  var tehnik_vena       = $('input:radio[name=tehnik_vena]:checked').val();
  var petugas_vena      = $('input:radio[name=petugas_vena]:checked').val();
  var desinfeksi_vena   = $('input:radio[name=desinfeksi_vena]:checked').val();
  var kaji_isk          = $('input:radio[name=kaji_isk]:checked').val();
  var petugas_isk       = $('input:radio[name=petugas_isk]:checked').val();
  var tangan_isk        = $('input:radio[name=tangan_isk]:checked').val();
  var tehniksteril_isk  = $('input:radio[name=tehniksteril_isk]:checked').val();

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
    var url = baseURL + '/bundles_hais/bundlesinsersi/' + data + '?t=' + mlite.token;
      window.location = url;
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
  });
});

 // ketika tombol edit INSERSI VAP ditekan
$("#bundles_insersi").on("click",".edit_insersi_vap", function(event){
  var baseURL = mlite.url + '/' + mlite.admin;
  event.preventDefault();
  var no_rawat          = $(this).attr("data-no_rawat");
  var tanggal           = $(this).attr("data-tanggal");
  var kd_kamar          = $(this).attr("data-kd_kamar");
  var no_rkm_medis      = $(this).attr("data-no_rkm_medis");
  var nm_pasien         = $(this).attr("data-nm_pasien");
  var hand_vap          = $(this).attr("data-hand_vap");
  var tehniksteril_vap  = $(this).attr("data-tehniksteril_vap");
  var apd_vap           = $(this).attr("data-apd_vap");
  var sedasi_vap        = $(this).attr("data-sedasi_vap");
  
  $('input:text[name=tanggalbundles]').val(tanggal);
  $('input:text[name=no_rawat]').val(no_rawat);
  $('input:text[name=no_rkm_medis]').val(no_rkm_medis);
  $('input:text[name=nm_pasien]').val(nm_pasien);
  $('input:text[name=kd_kamar]').val(kd_kamar);

 //hand_vap
  if (hand_vap === '1') {
    if ($('#hand_vap1').prop('checked')==false){
      $('#hand_vap1').prop('checked', true).change();
    } else if ($('#hand_vap1').prop('checked')==true){
      $('#hand_vap1').prop('checked', false).change(); 
    }
    
  } else if(hand_vap === '0'){
    if ($('#hand_vap0').prop('checked')==true){
      $('#hand_vap0').prop('checked', false).change();
    } else if ($('#hand_vap0').prop('checked')==false){
      $('#hand_vap0').prop('checked', true).change();
    }
  }
  //tehniksteril_vap
  if (tehniksteril_vap === '1') {
    if ($('#tehniksteril_vap1').prop('checked')==false){
      $('#tehniksteril_vap1').prop('checked', true).change();
    } else if ($('#tehniksteril_vap1').prop('checked')==true){
      $('#tehniksteril_vap1').prop('checked', false).change(); 
    }
  } else if(tehniksteril_vap === '0'){
    if ($('#tehniksteril_vap0').prop('checked')==true){
      $('#tehniksteril_vap0').prop('checked', false).change();
    }else if ($('#tehniksteril_vap0').prop('checked')==false){
      $('#tehniksteril_vap0').prop('checked', true).change();
    }
  }
  //apd_vap
  if (apd_vap === '1') {
    if ($('#apd_vap1').prop('checked')==false){
      $('#apd_vap1').prop('checked', true).change();
    } else if ($('#apd_vap1').prop('checked')==true){
      $('#apd_vap1').prop('checked', false).change(); 
    }
  } else if(apd_vap === '0'){
    if ($('#apd_vap0').prop('checked')==true){
      $('#apd_vap0').prop('checked', false).change();
    }else if ($('#apd_vap0').prop('checked')==false){
      $('#apd_vap0').prop('checked', true).change();
    }
  }
  //sedasi_vap
  if (sedasi_vap === '1') {
    if ($('#sedasi_vap1').prop('checked')==false){
      $('#sedasi_vap1').prop('checked', true).change();
    } else if ($('#sedasi_vap1').prop('checked')==true){
      $('#sedasi_vap1').prop('checked', false).change(); 
    }
    
  } else if(sedasi_vap === '0'){
    if ($('#sedasi_vap0').prop('checked')==true){
      $('#sedasi_vap0').prop('checked', false).change();
    } else if ($('#sedasi_vap0').prop('checked')==false){
      $('#sedasi_vap0').prop('checked', true).change();
    }
  }
});

// ketika tombol edit HAPUS VAP ditekan
$("#bundles_insersi").on("click", ".hapus_insersi_vap", function (event) {
  var baseURL = mlite.url + '/' + mlite.admin;
 event.preventDefault();
  var url = baseURL + '/bundles_hais/hapus_insersi_vap?t=' + mlite.token;
   var id                = $(this).attr("data-id");
   var no_rawat          = $(this).attr("data-no_rawat");
   var tanggal           = $(this).attr("data-tanggal");
   var kd_kamar          = $(this).attr("data-kd_kamar");
   var hand_vap          = $(this).attr("data-hand_vap");
   var tehniksteril_vap  = $(this).attr("data-tehniksteril_vap");
   var apd_vap           = $(this).attr("data-apd_vap");
   var sedasi_vap        = $(this).attr("data-sedasi_vap");

 // tampilkan dialog konfirmasi
 bootbox.confirm("Apakah Anda yakin ingin menghapus data ini?", function(result){
   // ketika ditekan tombol ok
   if (result){
     // mengirimkan perintah penghapusan
     $.post(url, {
     id: id,
     no_rawat: no_rawat,
     tanggal: tanggal,
     kd_kamar: kd_kamar,
     hand_vap: hand_vap,
     tehniksteril_vap: tehniksteril_vap,
     apd_vap: apd_vap,
     sedasi_vap: sedasi_vap

     } ,function(data) {
       var url = baseURL + '/bundles_hais/bundlesinsersi/' + data + '?t=' + mlite.token;
      //  console.log(url)
       $.post(url, {id : id,
       }, function(data) {
         // tampilkan data
         bootbox.alert('Data Berhasil Dihapus');
        window.location = url;
       });
      $('input:radio[name=hand_vap]:checked').val("");
      $('input:radio[name=tehniksteril_vap]:checked').val("");
      $('input:radio[name=apd_vap]:checked').val("");
      $('input:radio[name=sedasi_vap]:checked').val("");
      $('input:text[name=tanggalbundles]').val("{?=date('Y-m-d')?}");
     });
   }
 });
 }); 

 // ketika tombol edit INSERSI IADP ditekan
 $("#bundles_insersi").on("click",".edit_insersi_iadp", function(event){
  var baseURL = mlite.url + '/' + mlite.admin;
  event.preventDefault();
  var no_rawat          = $(this).attr("data-no_rawat");
  var tanggal           = $(this).attr("data-tanggal");
  var kd_kamar          = $(this).attr("data-kd_kamar");
  var no_rkm_medis      = $(this).attr("data-no_rkm_medis");
  var nm_pasien         = $(this).attr("data-nm_pasien");
  var hand_iadp         = $(this).attr("data-hand_iadp");
  var area_iadp         = $(this).attr("data-area_iadp");
  var tehniksteril_iadp = $(this).attr("data-tehniksteril_iadp");
  var alcohol_iadp      = $(this).attr("data-alcohol_iadp");
  var apd_iadp          = $(this).attr("data-apd_iadp");
  
  $('input:text[name=tanggalbundles]').val(tanggal);
  $('input:text[name=no_rawat]').val(no_rawat);
  $('input:text[name=no_rkm_medis]').val(no_rkm_medis);
  $('input:text[name=nm_pasien]').val(nm_pasien);
  $('input:text[name=kd_kamar]').val(kd_kamar);

 //hand_iadp
  if (hand_iadp === '1') {
    if ($('#hand_iadp1').prop('checked')==false){
      $('#hand_iadp1').prop('checked', true).change();
    } else if ($('#hand_iadp1').prop('checked')==true){
      $('#hand_iadp1').prop('checked', false).change(); 
    }
    
  } else if(hand_iadp === '0'){
    if ($('#hand_iadp0').prop('checked')==true){
      $('#hand_iadp0').prop('checked', false).change();
    } else if ($('#hand_iadp0').prop('checked')==false){
      $('#hand_iadp0').prop('checked', true).change();
    }
  }
  //area_iadp
  if (area_iadp === '1') {
    if ($('#area_iadp1').prop('checked')==false){
      $('#area_iadp1').prop('checked', true).change();
    } else if ($('#area_iadp1').prop('checked')==true){
      $('#area_iadp1').prop('checked', false).change(); 
    }
  } else if(area_iadp === '0'){
    if ($('#area_iadp0').prop('checked')==true){
      $('#area_iadp0').prop('checked', false).change();
    }else if ($('#area_iadp0').prop('checked')==false){
      $('#area_iadp0').prop('checked', true).change();
    }
  }
  //tehniksteril_iadp
  if (tehniksteril_iadp === '1') {
    if ($('#tehniksteril_iadp1').prop('checked')==false){
      $('#tehniksteril_iadp1').prop('checked', true).change();
    } else if ($('#tehniksteril_iadp1').prop('checked')==true){
      $('#tehniksteril_iadp1').prop('checked', false).change(); 
    }
  } else if(tehniksteril_iadp === '0'){
    if ($('#tehniksteril_iadp0').prop('checked')==true){
      $('#tehniksteril_iadp0').prop('checked', false).change();
    }else if ($('#tehniksteril_iadp0').prop('checked')==false){
      $('#tehniksteril_iadp0').prop('checked', true).change();
    }
  }
  //alcohol_iadp
  if (alcohol_iadp === '1') {
    if ($('#alcohol_iadp1').prop('checked')==false){
      $('#alcohol_iadp1').prop('checked', true).change();
    } else if ($('#alcohol_iadp1').prop('checked')==true){
      $('#alcohol_iadp1').prop('checked', false).change(); 
    }
    
  } else if(alcohol_iadp === '0'){
    if ($('#alcohol_iadp0').prop('checked')==true){
      $('#alcohol_iadp0').prop('checked', false).change();
    } else if ($('#alcohol_iadp0').prop('checked')==false){
      $('#alcohol_iadp0').prop('checked', true).change();
    }
  }
  //apd_iadp
  if (apd_iadp === '1') {
    if ($('#apd_iadp1').prop('checked')==false){
      $('#apd_iadp1').prop('checked', true).change();
    } else if ($('#apd_iadp1').prop('checked')==true){
      $('#apd_iadp1').prop('checked', false).change(); 
    }
    
  } else if(apd_iadp === '0'){
    if ($('#apd_iadp0').prop('checked')==true){
      $('#apd_iadp0').prop('checked', false).change();
    } else if ($('#apd_iadp0').prop('checked')==false){
      $('#apd_iadp0').prop('checked', true).change();
    }
  }
});

// ketika tombol hapus INSERSI IADP ditekan
$("#bundles_insersi").on("click", ".hapus_insersi_iadp", function (event) {
  var baseURL = mlite.url + '/' + mlite.admin;
 event.preventDefault();
  var url = baseURL + '/bundles_hais/hapus_insersi_iadp?t=' + mlite.token;
   var id                = $(this).attr("data-id");
   var no_rawat          = $(this).attr("data-no_rawat");
   var tanggal           = $(this).attr("data-tanggal");
   var kd_kamar          = $(this).attr("data-kd_kamar");
   var hand_iadp         = $(this).attr("data-hand_iadp");
   var area_iadp         = $(this).attr("data-area_iadp");
   var tehniksteril_iadp = $(this).attr("data-tehniksteril_iadp");
   var alcohol_iadp      = $(this).attr("data-alcohol_iadp");
   var apd_iadp          = $(this).attr("data-apd_iadp");

 // tampilkan dialog konfirmasi
 bootbox.confirm("Apakah Anda yakin ingin menghapus data ini?", function(result){
   // ketika ditekan tombol ok
   if (result){
     // mengirimkan perintah penghapusan
     $.post(url, {
     id: id,
     no_rawat: no_rawat,
     tanggal: tanggal,
     kd_kamar: kd_kamar,
     hand_iadp: hand_iadp,
     area_iadp: area_iadp,
     tehniksteril_iadp: tehniksteril_iadp,
     alcohol_iadp: alcohol_iadp,
     apd_iadp: apd_iadp

     } ,function(data) {
       var url = baseURL + '/bundles_hais/bundlesinsersi/' + data + '?t=' + mlite.token;
       console.log(url)
       $.post(url, {id : id,
       }, function(data) {
         // tampilkan data
         bootbox.alert('Data Berhasil Dihapus');
         window.location = url;
       });
      $('input:radio[name=hand_iadp]:checked').val();
      $('input:radio[name=area_iadp]:checked').val();
      $('input:radio[name=tehniksteril_iadp]:checked').val();
      $('input:radio[name=alcohol_iadp]:checked').val();
      $('input:radio[name=apd_iadp]:checked').val();
      $('input:text[name=tanggalbundles]').val("{?=date('Y-m-d')?}");
     });
   }
 });
 }); 

 // ketika tombol edit INSERSI VENA ditekan
 $("#bundles_insersi").on("click",".edit_insersi_vena", function(event){
  var baseURL = mlite.url + '/' + mlite.admin;
  event.preventDefault();
  var no_rawat          = $(this).attr("data-no_rawat");
  var tanggal           = $(this).attr("data-tanggal");
  var kd_kamar          = $(this).attr("data-kd_kamar");
  var no_rkm_medis      = $(this).attr("data-no_rkm_medis");
  var nm_pasien         = $(this).attr("data-nm_pasien");
  var hand_vena         = $(this).attr("data-hand_vena");
  var kaji_vena         = $(this).attr("data-kaji_vena");
  var tehnik_vena       = $(this).attr("data-tehnik_vena");
  var petugas_vena      = $(this).attr("data-petugas_vena");
  var desinfeksi_vena   = $(this).attr("data-desinfeksi_vena");
  
  $('input:text[name=tanggalbundles]').val(tanggal);
  $('input:text[name=no_rawat]').val(no_rawat);
  $('input:text[name=no_rkm_medis]').val(no_rkm_medis);
  $('input:text[name=nm_pasien]').val(nm_pasien);
  $('input:text[name=kd_kamar]').val(kd_kamar);

 //hand_vena
  if (hand_vena === '1') {
    if ($('#hand_vena1').prop('checked')==false){
      $('#hand_vena1').prop('checked', true).change();
    } else if ($('#hand_vena1').prop('checked')==true){
      $('#hand_vena1').prop('checked', false).change(); 
    }
    
  } else if(hand_vena === '0'){
    if ($('#hand_vena0').prop('checked')==true){
      $('#hand_vena0').prop('checked', false).change();
    } else if ($('#hand_vena0').prop('checked')==false){
      $('#hand_vena0').prop('checked', true).change();
    }
  }
  //kaji_vena
  if (kaji_vena === '1') {
    if ($('#kaji_vena1').prop('checked')==false){
      $('#kaji_vena1').prop('checked', true).change();
    } else if ($('#kaji_vena1').prop('checked')==true){
      $('#kaji_vena1').prop('checked', false).change(); 
    }
  } else if(kaji_vena === '0'){
    if ($('#kaji_vena0').prop('checked')==true){
      $('#kaji_vena0').prop('checked', false).change();
    }else if ($('#kaji_vena0').prop('checked')==false){
      $('#kaji_vena0').prop('checked', true).change();
    }
  }
  //tehnik_vena 
  if (tehnik_vena  === '1') {
    if ($('#tehnik_vena1').prop('checked')==false){
      $('#tehnik_vena1').prop('checked', true).change();
    } else if ($('#tehnik_vena1').prop('checked')==true){
      $('#tehnik_vena1').prop('checked', false).change(); 
    }
  } else if(tehnik_vena  === '0'){
    if ($('#tehnik_vena0').prop('checked')==true){
      $('#tehnik_vena0').prop('checked', false).change();
    }else if ($('#tehnik_vena0').prop('checked')==false){
      $('#tehnik_vena0').prop('checked', true).change();
    }
  }
  //petugas_vena 
  if (petugas_vena  === '1') {
    if ($('#petugas_vena1').prop('checked')==false){
      $('#petugas_vena1').prop('checked', true).change();
    } else if ($('#petugas_vena1').prop('checked')==true){
      $('#petugas_vena1').prop('checked', false).change(); 
    }
    
  } else if(petugas_vena  === '0'){
    if ($('#petugas_vena0').prop('checked')==true){
      $('#petugas_vena0').prop('checked', false).change();
    } else if ($('#petugas_vena0').prop('checked')==false){
      $('#petugas_vena0').prop('checked', true).change();
    }
  }
  //desinfeksi_vena
  if (desinfeksi_vena === '1') {
    if ($('#desinfeksi_vena1').prop('checked')==false){
      $('#desinfeksi_vena1').prop('checked', true).change();
    } else if ($('#desinfeksi_vena1').prop('checked')==true){
      $('#desinfeksi_vena1').prop('checked', false).change(); 
    }
    
  } else if(desinfeksi_vena === '0'){
    if ($('#desinfeksi_vena0').prop('checked')==true){
      $('#desinfeksi_vena0').prop('checked', false).change();
    } else if ($('#desinfeksi_vena0').prop('checked')==false){
      $('#desinfeksi_vena0').prop('checked', true).change();
    }
  }
});

// ketika tombol hapus INSERSI VENA ditekan
$("#bundles_insersi").on("click", ".hapus_insersi_vena", function (event) {
  var baseURL = mlite.url + '/' + mlite.admin;
 event.preventDefault();
  var url = baseURL + '/bundles_hais/hapus_insersi_vena?t=' + mlite.token;
   var id                = $(this).attr("data-id");
   var no_rawat          = $(this).attr("data-no_rawat");
   var tanggal           = $(this).attr("data-tanggal");
   var kd_kamar          = $(this).attr("data-kd_kamar");
   var hand_vena         = $(this).attr("data-hand_vena");
   var kaji_vena         = $(this).attr("data-kaji_vena");
   var tehnik_vena       = $(this).attr("data-tehnik_vena");
   var petugas_vena      = $(this).attr("data-petugas_vena");
   var desinfeksi_vena   = $(this).attr("data-desinfeksi_vena");

 // tampilkan dialog konfirmasi
 bootbox.confirm("Apakah Anda yakin ingin menghapus data ini?", function(result){
   // ketika ditekan tombol ok
   if (result){
     // mengirimkan perintah penghapusan
     $.post(url, {
     id: id,
     no_rawat: no_rawat,
     tanggal: tanggal,
     kd_kamar: kd_kamar,
     hand_vena: hand_vena,
     kaji_vena: kaji_vena,
     tehnik_vena: tehnik_vena,
     petugas_vena: petugas_vena,
     desinfeksi_vena: desinfeksi_vena

     } ,function(data) {
       var url = baseURL + '/bundles_hais/bundlesinsersi/' + data + '?t=' + mlite.token;
       console.log(url)
       $.post(url, {id : id,
       }, function(data) {
         // tampilkan data
         bootbox.alert('Data Berhasil Dihapus');
         window.location = url;
       });
      $('input:radio[name=hand_vena]:checked').val();
      $('input:radio[name=kaji_vena]:checked').val();
      $('input:radio[name=tehnik_vena]:checked').val();
      $('input:radio[name=petugas_vena]:checked').val();
      $('input:radio[name=desinfeksi_vena]:checked').val();
      $('input:text[name=tanggalbundles]').val("{?=date('Y-m-d')?}");
     });
   }
 });
 }); 

 // ketika tombol edit INSERSI ISK ditekan
 $("#bundles_insersi").on("click",".edit_insersi_isk", function(event){
  var baseURL = mlite.url + '/' + mlite.admin;
  event.preventDefault();
  var no_rawat          = $(this).attr("data-no_rawat");
  var tanggal           = $(this).attr("data-tanggal");
  var kd_kamar          = $(this).attr("data-kd_kamar");
  var no_rkm_medis      = $(this).attr("data-no_rkm_medis");
  var nm_pasien         = $(this).attr("data-nm_pasien");
  var kaji_isk          = $(this).attr("data-kaji_isk");
  var petugas_isk       = $(this).attr("data-petugas_isk");
  var tangan_isk        = $(this).attr("data-tangan_isk");
  var tehniksteril_isk  = $(this).attr("data-tehniksteril_isk");
  
  $('input:text[name=tanggalbundles]').val(tanggal);
  $('input:text[name=no_rawat]').val(no_rawat);
  $('input:text[name=no_rkm_medis]').val(no_rkm_medis);
  $('input:text[name=nm_pasien]').val(nm_pasien);
  $('input:text[name=kd_kamar]').val(kd_kamar);

 //kaji_isk
  if (kaji_isk === '1') {
    if ($('#kaji_isk1').prop('checked')==false){
      $('#kaji_isk1').prop('checked', true).change();
    } else if ($('#kaji_isk1').prop('checked')==true){
      $('#kaji_isk1').prop('checked', false).change(); 
    }
    
  } else if(kaji_isk === '0'){
    if ($('#kaji_isk0').prop('checked')==true){
      $('#kaji_isk0').prop('checked', false).change();
    } else if ($('#kaji_isk0').prop('checked')==false){
      $('#kaji_isk0').prop('checked', true).change();
    }
  }
  //petugas_isk
  if (petugas_isk === '1') {
    if ($('#petugas_isk1').prop('checked')==false){
      $('#petugas_isk1').prop('checked', true).change();
    } else if ($('#petugas_isk1').prop('checked')==true){
      $('#petugas_isk1').prop('checked', false).change(); 
    }
  } else if(petugas_isk === '0'){
    if ($('#petugas_isk0').prop('checked')==true){
      $('#petugas_isk0').prop('checked', false).change();
    }else if ($('#petugas_isk0').prop('checked')==false){
      $('#petugas_isk0').prop('checked', true).change();
    }
  }
  //tangan_isk 
  if (tangan_isk  === '1') {
    if ($('#tangan_isk1').prop('checked')==false){
      $('#tangan_isk1').prop('checked', true).change();
    } else if ($('#tangan_isk1').prop('checked')==true){
      $('#tangan_isk1').prop('checked', false).change(); 
    }
  } else if(tangan_isk  === '0'){
    if ($('#tangan_isk0').prop('checked')==true){
      $('#tangan_isk0').prop('checked', false).change();
    }else if ($('#tangan_isk0').prop('checked')==false){
      $('#tangan_isk0').prop('checked', true).change();
    }
  }
  //tehniksteril_isk 
  if (tehniksteril_isk  === '1') {
    if ($('#tehniksteril_isk1').prop('checked')==false){
      $('#tehniksteril_isk1').prop('checked', true).change();
    } else if ($('#tehniksteril_isk1').prop('checked')==true){
      $('#tehniksteril_isk1').prop('checked', false).change(); 
    }
    
  } else if(tehniksteril_isk  === '0'){
    if ($('#tehniksteril_isk0').prop('checked')==true){
      $('#tehniksteril_isk0').prop('checked', false).change();
    } else if ($('#tehniksteril_isk0').prop('checked')==false){
      $('#tehniksteril_isk0').prop('checked', true).change();
    }
  }
  
});

// ketika tombol hapus INSERSI ISK ditekan
$("#bundles_insersi").on("click", ".hapus_insersi_isk", function (event) {
  var baseURL = mlite.url + '/' + mlite.admin;
 event.preventDefault();
  var url = baseURL + '/bundles_hais/hapus_insersi_isk?t=' + mlite.token;
   var id                = $(this).attr("data-id");
   var no_rawat          = $(this).attr("data-no_rawat");
   var tanggal           = $(this).attr("data-tanggal");
   var kd_kamar          = $(this).attr("data-kd_kamar");
   var kaji_isk          = $(this).attr("data-kaji_isk");
   var petugas_isk       = $(this).attr("data-petugas_isk");
   var tangan_isk        = $(this).attr("data-tangan_isk");
   var tehniksteril_isk  = $(this).attr("data-tehniksteril_isk");

 // tampilkan dialog konfirmasi
 bootbox.confirm("Apakah Anda yakin ingin menghapus data ini?", function(result){
   // ketika ditekan tombol ok
   if (result){
     // mengirimkan perintah penghapusan
     $.post(url, {
     id: id,
     no_rawat: no_rawat,
     tanggal: tanggal,
     kd_kamar: kd_kamar,
     kaji_isk: kaji_isk,
     petugas_isk: petugas_isk,
     tangan_isk: tangan_isk,
     tehniksteril_isk: tehniksteril_isk

     } ,function(data) {
       var url = baseURL + '/bundles_hais/bundlesinsersi/' + data + '?t=' + mlite.token;
       console.log(url)
       $.post(url, {id : id,
       }, function(data) {
         // tampilkan data
         bootbox.alert('Data Berhasil Dihapus');
         window.location = url;
       });
       $('input:radio[name=kaji_isk]:checked').val();
       $('input:radio[name=petugas_isk]:checked').val();
       $('input:radio[name=tangan_isk]:checked').val();
       $('input:radio[name=tehniksteril_isk]:checked').val();
      $('input:text[name=tanggalbundles]').val("{?=date('Y-m-d')?}");
     });
   }
 });
 }); 

$("#bundles_insersi").on("click", "#selesai_insersi", function (event) {
  bersih();
});

//bundles_maintanance
// // ketika tombol simpan diklik
$("#bundles_maintanance").on("click", "#simpan_maintanance", function (event) {
  var baseURL = mlite.url + '/' + mlite.admin;
  event.preventDefault();

  var no_rawat              = $('input:text[name=no_rawat]').val();
  var tanggal               = $('input:text[name=tanggalbundles]').val();
  var kd_kamar              = $('input:text[name=kd_kamar]').val();
  var hand_mainvap          = $('input:radio[name=hand_mainvap]:checked').val();
  var oral_mainvap          = $('input:radio[name=oral_mainvap]:checked').val();
  var manage_mainvap        = $('input:radio[name=manage_mainvap]:checked').val();
  var sedasi_mainvap        = $('input:radio[name=sedasi_mainvap]:checked').val();
  var kepala_mainvap        = $('input:radio[name=kepala_mainvap]:checked').val();
  var hand_mainiadp         = $('input:radio[name=hand_mainiadp]:checked').val();
  var desinfeksi_mainiadp   = $('input:radio[name=desinfeksi_mainiadp]:checked').val();
  var perawatan_mainiadp    = $('input:radio[name=perawatan_mainiadp]:checked').val();
  var dreasing_mainiadp     = $('input:radio[name=dreasing_mainiadp]:checked').val();
  var infus_mainiadp        = $('input:radio[name=infus_mainiadp]:checked').val();
  var hand_mainvena         = $('input:radio[name=hand_mainvena]:checked').val();
  var perawatan_mainvena    = $('input:radio[name=perawatan_mainvena]:checked').val();
  var kaji_mainvena         = $('input:radio[name=kaji_mainvena]:checked').val();
  var administrasi_mainvena = $('input:radio[name=administrasi_mainvena]:checked').val();
  var edukasi_mainvena      = $('input:radio[name=edukasi_mainvena]:checked').val();
  var hand_mainisk          = $('input:radio[name=hand_mainisk]:checked').val();
  var kateter_mainisk       = $('input:radio[name=kateter_mainisk]:checked').val();
  var baglantai_mainisk     = $('input:radio[name=baglantai_mainisk]:checked').val();
  var bagrendah_mainisk     = $('input:radio[name=bagrendah_mainisk]:checked').val();
  var posisiselang_mainisk  = $('input:radio[name=posisiselang_mainisk]:checked').val();
  var lepas_mainisk         = $('input:radio[name=kateter_mainisk]:checked').val();

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
    lepas_mainisk,

  });
  var url = baseURL + '/bundles_hais/savebundles_maintanance?t=' + mlite.token;
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
    var url = baseURL + '/bundles_hais/bundles_maintanance/' + data + '?t=' + mlite.token;
    window.location = url;
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
  });
});

 // ketika tombol edit MAINTANANCE VAP ditekan
 $("#bundles_maintanance").on("click",".edit_main_vap", function(event){
  var baseURL = mlite.url + '/' + mlite.admin;
  event.preventDefault();
  var no_rawat          = $(this).attr("data-no_rawat");
  var tanggal           = $(this).attr("data-tanggal");
  var kd_kamar          = $(this).attr("data-kd_kamar");
  var no_rkm_medis      = $(this).attr("data-no_rkm_medis");
  var nm_pasien         = $(this).attr("data-nm_pasien");
  var hand_mainvap      = $(this).attr("data-hand_mainvap");
  var oral_mainvap      = $(this).attr("data-oral_mainvap");
  var manage_mainvap    = $(this).attr("data-manage_mainvap");
  var sedasi_mainvap    = $(this).attr("data-sedasi_mainvap");
  var kepala_mainvap    = $(this).attr("data-kepala_mainvap");
  
  $('input:text[name=tanggalbundles]').val(tanggal);
  $('input:text[name=no_rawat]').val(no_rawat);
  $('input:text[name=no_rkm_medis]').val(no_rkm_medis);
  $('input:text[name=nm_pasien]').val(nm_pasien);
  $('input:text[name=kd_kamar]').val(kd_kamar);

 //hand_mainvap
  if (hand_mainvap === '1') {
    if ($('#hand_mainvap1').prop('checked')==false){
      $('#hand_mainvap1').prop('checked', true).change();
    } else if ($('#hand_mainvap1').prop('checked')==true){
      $('#hand_mainvap1').prop('checked', false).change(); 
    }
    
  } else if(hand_mainvap === '0'){
    if ($('#hand_mainvap0').prop('checked')==true){
      $('#hand_mainvap0').prop('checked', false).change();
    } else if ($('#hand_mainvap0').prop('checked')==false){
      $('#hand_mainvap0').prop('checked', true).change();
    }
  }
  //oral_mainvap
  if (oral_mainvap === '1') {
    if ($('#oral_mainvap1').prop('checked')==false){
      $('#oral_mainvap1').prop('checked', true).change();
    } else if ($('#oral_mainvap1').prop('checked')==true){
      $('#oral_mainvap1').prop('checked', false).change(); 
    }
  } else if(oral_mainvap === '0'){
    if ($('#oral_mainvap0').prop('checked')==true){
      $('#oral_mainvap0').prop('checked', false).change();
    }else if ($('#oral_mainvap0').prop('checked')==false){
      $('#oral_mainvap0').prop('checked', true).change();
    }
  }
  //manage_mainvap 
  if (manage_mainvap  === '1') {
    if ($('#manage_mainvap1').prop('checked')==false){
      $('#manage_mainvap1').prop('checked', true).change();
    } else if ($('#manage_mainvap1').prop('checked')==true){
      $('#manage_mainvap1').prop('checked', false).change(); 
    }
  } else if(manage_mainvap  === '0'){
    if ($('#manage_mainvap0').prop('checked')==true){
      $('#manage_mainvap0').prop('checked', false).change();
    }else if ($('#manage_mainvap0').prop('checked')==false){
      $('#manage_mainvap0').prop('checked', true).change();
    }
  }
  //sedasi_mainvap 
  if (sedasi_mainvap  === '1') {
    if ($('#sedasi_mainvap1').prop('checked')==false){
      $('#sedasi_mainvap1').prop('checked', true).change();
    } else if ($('#sedasi_mainvap1').prop('checked')==true){
      $('#sedasi_mainvap1').prop('checked', false).change(); 
    }
    
  } else if(sedasi_mainvap  === '0'){
    if ($('#sedasi_mainvap0').prop('checked')==true){
      $('#sedasi_mainvap0').prop('checked', false).change();
    } else if ($('#sedasi_mainvap0').prop('checked')==false){
      $('#sedasi_mainvap0').prop('checked', true).change();
    }
  }
   //kepala_mainvap 
   if (kepala_mainvap  === '1') {
    if ($('#kepala_mainvap1').prop('checked')==false){
      $('#kepala_mainvap1').prop('checked', true).change();
    } else if ($('#kepala_mainvap1').prop('checked')==true){
      $('#kepala_mainvap1').prop('checked', false).change(); 
    }
    
  } else if(kepala_mainvap  === '0'){
    if ($('#kepala_mainvap0').prop('checked')==true){
      $('#kepala_mainvap0').prop('checked', false).change();
    } else if ($('#kepala_mainvap0').prop('checked')==false){
      $('#kepala_mainvap0').prop('checked', true).change();
    }
  }
});

// ketika tombol hapus MAINTANANCE VAP ditekan
$("#bundles_maintanance").on("click", ".hapus_main_vap", function (event) {
  var baseURL = mlite.url + '/' + mlite.admin;
 event.preventDefault();
  var url = baseURL + '/bundles_hais/hapus_main_vap?t=' + mlite.token;
   var id              = $(this).attr("data-id");
   var no_rawat        = $(this).attr("data-no_rawat");
   var tanggal         = $(this).attr("data-tanggal");
   var kd_kamar        = $(this).attr("data-kd_kamar");
   var hand_mainvap    = $(this).attr("data-hand_mainvap");
   var oral_mainvap    = $(this).attr("data-oral_mainvap");
   var manage_mainvap  = $(this).attr("data-manage_mainvap");
   var sedasi_mainvap  = $(this).attr("data-sedasi_mainvap");
   var kepala_mainvap  = $(this).attr("data-kepala_mainvap");

 // tampilkan dialog konfirmasi
 bootbox.confirm("Apakah Anda yakin ingin menghapus data ini?", function(result){
   // ketika ditekan tombol ok
   if (result){
     // mengirimkan perintah penghapusan
     $.post(url, {
     id: id,
     no_rawat: no_rawat,
     tanggal: tanggal,
     kd_kamar: kd_kamar,
     hand_mainvap: hand_mainvap,
     oral_mainvap: oral_mainvap,
     manage_mainvap: manage_mainvap,
     sedasi_mainvap: sedasi_mainvap,
     kepala_mainvap: kepala_mainvap

     } ,function(data) {
       var url = baseURL + '/bundles_hais/bundles_maintanance/' + data + '?t=' + mlite.token;
       console.log(url)
       $.post(url, {id : id,
       }, function(data) {
         // tampilkan data
         bootbox.alert('Data Berhasil Dihapus');
         window.location = url;
       });
       $('input:radio[name=hand_mainvap]:checked').val();
       $('input:radio[name=oral_mainvap]:checked').val();
       $('input:radio[name=manage_mainvap]:checked').val();
       $('input:radio[name=sedasi_mainvap]:checked').val();
       $('input:radio[name=kepala_mainvap]:checked').val();
      $('input:text[name=tanggalbundles]').val("{?=date('Y-m-d')?}");
     });
   }
 });
 });
 
 // ketika tombol edit MAINTANANCE IADP ditekan
 $("#bundles_maintanance").on("click",".edit_main_iadp", function(event){
  var baseURL = mlite.url + '/' + mlite.admin;
  event.preventDefault();
  var no_rawat            = $(this).attr("data-no_rawat");
  var tanggal             = $(this).attr("data-tanggal");
  var kd_kamar            = $(this).attr("data-kd_kamar");
  var no_rkm_medis        = $(this).attr("data-no_rkm_medis");
  var nm_pasien           = $(this).attr("data-nm_pasien");
  var hand_mainiadp       = $(this).attr("data-hand_mainiadp");
  var desinfeksi_mainiadp = $(this).attr("data-desinfeksi_mainiadp");
  var perawatan_mainiadp  = $(this).attr("data-perawatan_mainiadp");
  var dreasing_mainiadp   = $(this).attr("data-dreasing_mainiadp");
  var infus_mainiadp      = $(this).attr("data-infus_mainiadp");
  
  $('input:text[name=tanggalbundles]').val(tanggal);
  $('input:text[name=no_rawat]').val(no_rawat);
  $('input:text[name=no_rkm_medis]').val(no_rkm_medis);
  $('input:text[name=nm_pasien]').val(nm_pasien);
  $('input:text[name=kd_kamar]').val(kd_kamar);

 //hand_mainiadp
  if (hand_mainiadp === '1') {
    if ($('#hand_mainiadp1').prop('checked')==false){
      $('#hand_mainiadp1').prop('checked', true).change();
    } else if ($('#hand_mainiadp1').prop('checked')==true){
      $('#hand_mainiadp1').prop('checked', false).change(); 
    }
    
  } else if(hand_mainiadp === '0'){
    if ($('#hand_mainiadp0').prop('checked')==true){
      $('#hand_mainiadp0').prop('checked', false).change();
    } else if ($('#hand_mainiadp0').prop('checked')==false){
      $('#hand_mainiadp0').prop('checked', true).change();
    }
  }
  //desinfeksi_mainiadp
  if (desinfeksi_mainiadp === '1') {
    if ($('#desinfeksi_mainiadp1').prop('checked')==false){
      $('#desinfeksi_mainiadp1').prop('checked', true).change();
    } else if ($('#desinfeksi_mainiadp1').prop('checked')==true){
      $('#desinfeksi_mainiadp1').prop('checked', false).change(); 
    }
  } else if(desinfeksi_mainiadp === '0'){
    if ($('#desinfeksi_mainiadp0').prop('checked')==true){
      $('#desinfeksi_mainiadp0').prop('checked', false).change();
    }else if ($('#desinfeksi_mainiadp0').prop('checked')==false){
      $('#desinfeksi_mainiadp0').prop('checked', true).change();
    }
  }
  //perawatan_mainiadp 
  if (perawatan_mainiadp  === '1') {
    if ($('#perawatan_mainiadp1').prop('checked')==false){
      $('#perawatan_mainiadp1').prop('checked', true).change();
    } else if ($('#perawatan_mainiadp1').prop('checked')==true){
      $('#perawatan_mainiadp1').prop('checked', false).change(); 
    }
  } else if(perawatan_mainiadp  === '0'){
    if ($('#perawatan_mainiadp0').prop('checked')==true){
      $('#perawatan_mainiadp0').prop('checked', false).change();
    }else if ($('#perawatan_mainiadp0').prop('checked')==false){
      $('#perawatan_mainiadp0').prop('checked', true).change();
    }
  }
  //dreasing_mainiadp 
  if (dreasing_mainiadp  === '1') {
    if ($('#dreasing_mainiadp1').prop('checked')==false){
      $('#dreasing_mainiadp1').prop('checked', true).change();
    } else if ($('#dreasing_mainiadp1').prop('checked')==true){
      $('#dreasing_mainiadp1').prop('checked', false).change(); 
    }
    
  } else if(dreasing_mainiadp  === '0'){
    if ($('#dreasing_mainiadp0').prop('checked')==true){
      $('#dreasing_mainiadp0').prop('checked', false).change();
    } else if ($('#dreasing_mainiadp0').prop('checked')==false){
      $('#dreasing_mainiadp0').prop('checked', true).change();
    }
  }
   //infus_mainiadp 
   if (infus_mainiadp  === '1') {
    if ($('#infus_mainiadp1').prop('checked')==false){
      $('#infus_mainiadp1').prop('checked', true).change();
    } else if ($('#infus_mainiadp1').prop('checked')==true){
      $('#infus_mainiadp1').prop('checked', false).change(); 
    }
    
  } else if(infus_mainiadp  === '0'){
    if ($('#infus_mainiadp0').prop('checked')==true){
      $('#infus_mainiadp0').prop('checked', false).change();
    } else if ($('#infus_mainiadp0').prop('checked')==false){
      $('#infus_mainiadp0').prop('checked', true).change();
    }
  }
  
});

// ketika tombol hapus MAINTANANCE IADP ditekan
$("#bundles_maintanance").on("click", ".hapus_main_iadp", function (event) {
  var baseURL = mlite.url + '/' + mlite.admin;
 event.preventDefault();
  var url = baseURL + '/bundles_hais/hapus_main_iadp?t=' + mlite.token;
  var id                  = $(this).attr("data-id");
  var no_rawat            = $(this).attr("data-no_rawat");
  var tanggal             = $(this).attr("data-tanggal");
  var kd_kamar            = $(this).attr("data-kd_kamar");
  var hand_mainiadp       = $(this).attr("data-hand_mainiadp");
  var desinfeksi_mainiadp = $(this).attr("data-desinfeksi_mainiadp");
  var perawatan_mainiadp  = $(this).attr("data-perawatan_mainiadp");
  var dreasing_mainiadp   = $(this).attr("data-dreasing_mainiadp");
  var infus_mainiadp      = $(this).attr("data-infus_mainiadp");

 // tampilkan dialog konfirmasi
 bootbox.confirm("Apakah Anda yakin ingin menghapus data ini?", function(result){
   // ketika ditekan tombol ok
   if (result){
     // mengirimkan perintah penghapusan
     $.post(url, {
     id: id,
     no_rawat: no_rawat,
     tanggal: tanggal,
     kd_kamar: kd_kamar,
     hand_mainiadp: hand_mainiadp,
     desinfeksi_mainiadp: desinfeksi_mainiadp,
     perawatan_mainiadp: perawatan_mainiadp,
     dreasing_mainiadp: dreasing_mainiadp,
     infus_mainiadp: infus_mainiadp

     } ,function(data) {
       var url = baseURL + '/bundles_hais/bundles_maintanance/' + data + '?t=' + mlite.token;
       console.log(url)
       $.post(url, {id : id,
       }, function(data) {
         // tampilkan data
         bootbox.alert('Data Berhasil Dihapus');
         window.location = url;
       });
       $('input:radio[name=hand_mainiadp]:checked').val();
       $('input:radio[name=desinfeksi_mainiadp]:checked').val();
       $('input:radio[name=perawatan_mainiadp]:checked').val();
       $('input:radio[name=dreasing_mainiadp]:checked').val();
       $('input:radio[name=infus_mainiadp]:checked').val();
      $('input:text[name=tanggalbundles]').val("{?=date('Y-m-d')?}");
     });
   }
 });
 });

  // ketika tombol edit MAINTANANCE VENA ditekan
  $("#bundles_maintanance").on("click",".edit_main_vena", function(event){
    var baseURL = mlite.url + '/' + mlite.admin;
    event.preventDefault();
    var no_rawat              = $(this).attr("data-no_rawat");
    var tanggal               = $(this).attr("data-tanggal");
    var kd_kamar              = $(this).attr("data-kd_kamar");
    var no_rkm_medis          = $(this).attr("data-no_rkm_medis");
    var nm_pasien             = $(this).attr("data-nm_pasien");
    var hand_mainvena         = $(this).attr("data-hand_mainvena");
    var perawatan_mainvena    = $(this).attr("data-perawatan_mainvena");
    var kaji_mainvena         = $(this).attr("data-kaji_mainvena");
    var administrasi_mainvena = $(this).attr("data-administrasi_mainvena");
    var edukasi_mainvena      = $(this).attr("data-edukasi_mainvena");
    
    $('input:text[name=tanggalbundles]').val(tanggal);
    $('input:text[name=no_rawat]').val(no_rawat);
    $('input:text[name=no_rkm_medis]').val(no_rkm_medis);
    $('input:text[name=nm_pasien]').val(nm_pasien);
    $('input:text[name=kd_kamar]').val(kd_kamar);
  
   //hand_mainvena
    if (hand_mainvena === '1') {
      if ($('#hand_mainvena1').prop('checked')==false){
        $('#hand_mainvena1').prop('checked', true).change();
      } else if ($('#hand_mainvena1').prop('checked')==true){
        $('#hand_mainvena1').prop('checked', false).change(); 
      }
      
    } else if(hand_mainvena === '0'){
      if ($('#hand_mainvena0').prop('checked')==true){
        $('#hand_mainvena0').prop('checked', false).change();
      } else if ($('#hand_mainvena0').prop('checked')==false){
        $('#hand_mainvena0').prop('checked', true).change();
      }
    }
    //perawatan_mainvena
    if (perawatan_mainvena === '1') {
      if ($('#perawatan_mainvena1').prop('checked')==false){
        $('#perawatan_mainvena1').prop('checked', true).change();
      } else if ($('#perawatan_mainvena1').prop('checked')==true){
        $('#perawatan_mainvena1').prop('checked', false).change(); 
      }
    } else if(perawatan_mainvena === '0'){
      if ($('#perawatan_mainvena0').prop('checked')==true){
        $('#perawatan_mainvena0').prop('checked', false).change();
      }else if ($('#perawatan_mainvena0').prop('checked')==false){
        $('#perawatan_mainvena0').prop('checked', true).change();
      }
    }
    //kaji_mainvena 
    if (kaji_mainvena  === '1') {
      if ($('#kaji_mainvena1').prop('checked')==false){
        $('#kaji_mainvena1').prop('checked', true).change();
      } else if ($('#kaji_mainvena1').prop('checked')==true){
        $('#kaji_mainvena1').prop('checked', false).change(); 
      }
    } else if(kaji_mainvena  === '0'){
      if ($('#kaji_mainvena0').prop('checked')==true){
        $('#kaji_mainvena0').prop('checked', false).change();
      }else if ($('#kaji_mainvena0').prop('checked')==false){
        $('#kaji_mainvena0').prop('checked', true).change();
      }
    }
    //administrasi_mainvena 
    if (administrasi_mainvena  === '1') {
      if ($('#administrasi_mainvena1').prop('checked')==false){
        $('#administrasi_mainvena1').prop('checked', true).change();
      } else if ($('#administrasi_mainvena1').prop('checked')==true){
        $('#administrasi_mainvena1').prop('checked', false).change(); 
      }
      
    } else if(administrasi_mainvena  === '0'){
      if ($('#administrasi_mainvena0').prop('checked')==true){
        $('#administrasi_mainvena0').prop('checked', false).change();
      } else if ($('#administrasi_mainvena0').prop('checked')==false){
        $('#administrasi_mainvena0').prop('checked', true).change();
      }
    }
     //edukasi_mainvena 
     if (edukasi_mainvena  === '1') {
      if ($('#edukasi_mainvena1').prop('checked')==false){
        $('#edukasi_mainvena1').prop('checked', true).change();
      } else if ($('#edukasi_mainvena1').prop('checked')==true){
        $('#edukasi_mainvena1').prop('checked', false).change(); 
      }
      
    } else if(edukasi_mainvena  === '0'){
      if ($('#edukasi_mainvena0').prop('checked')==true){
        $('#edukasi_mainvena0').prop('checked', false).change();
      } else if ($('#edukasi_mainvena0').prop('checked')==false){
        $('#edukasi_mainvena0').prop('checked', true).change();
      }
    }
    
  });
  
  // ketika tombol hapus MAINTANANCE VENA ditekan
  $("#bundles_maintanance").on("click", ".hapus_main_vena", function (event) {
    var baseURL = mlite.url + '/' + mlite.admin;
   event.preventDefault();
    var url = baseURL + '/bundles_hais/hapus_main_vena?t=' + mlite.token;
    var id                    = $(this).attr("data-id");
    var no_rawat              = $(this).attr("data-no_rawat");
    var tanggal               = $(this).attr("data-tanggal");
    var kd_kamar              = $(this).attr("data-kd_kamar");
    var hand_mainvena         = $(this).attr("data-hand_mainvena ");
    var perawatan_mainvena    = $(this).attr("data-perawatan_mainvena");
    var kaji_mainvena         = $(this).attr("data-kaji_mainvena");
    var administrasi_mainvena = $(this).attr("data-administrasi_mainvena");
    var edukasi_mainvena      = $(this).attr("data-edukasi_mainvena");
  
   // tampilkan dialog konfirmasi
   bootbox.confirm("Apakah Anda yakin ingin menghapus data ini?", function(result){
     // ketika ditekan tombol ok
     if (result){
       // mengirimkan perintah penghapusan
       $.post(url, {
       id: id,
       no_rawat: no_rawat,
       tanggal: tanggal,
       kd_kamar: kd_kamar,
       hand_mainvena : hand_mainvena ,
       perawatan_mainvena: perawatan_mainvena,
       kaji_mainvena: kaji_mainvena,
       administrasi_mainvena: administrasi_mainvena,
       edukasi_mainvena: edukasi_mainvena
  
       } ,function(data) {
         var url = baseURL + '/bundles_hais/bundles_maintanance/' + data + '?t=' + mlite.token;
         console.log(url)
         $.post(url, {id : id,
         }, function(data) {
           // tampilkan data
          bootbox.alert('Data Berhasil Dihapus');
          window.location = url;
         });
         $('input:radio[name=hand_mainVENA]:checked').val();
         $('input:radio[name=perawatan_mainvena]:checked').val();
         $('input:radio[name=kaji_mainvena]:checked').val();
         $('input:radio[name=administrasi_mainvena]:checked').val();
         $('input:radio[name=edukasi_mainvena]:checked').val();
        $('input:text[name=tanggalbundles]').val("{?=date('Y-m-d')?}");
       });
     }
   });
   });

    // ketika tombol edit MAINTANANCE ISK ditekan
  $("#bundles_maintanance").on("click",".edit_main_isk", function(event){
    var baseURL = mlite.url + '/' + mlite.admin;
    event.preventDefault();
    var no_rawat              = $(this).attr("data-no_rawat");
    var tanggal               = $(this).attr("data-tanggal");
    var kd_kamar              = $(this).attr("data-kd_kamar");
    var no_rkm_medis          = $(this).attr("data-no_rkm_medis");
    var nm_pasien             = $(this).attr("data-nm_pasien");
    var hand_mainisk          = $(this).attr("data-hand_mainisk ");
    var kateter_mainisk       = $(this).attr("data-kateter_mainisk");
    var baglantai_mainisk     = $(this).attr("data-baglantai_mainisk");
    var bagrendah_mainisk     = $(this).attr("data-bagrendah_mainisk");
    var posisiselang_mainisk  = $(this).attr("data-posisiselang_mainisk");
    var lepas_mainisk         = $(this).attr("data-lepas_mainisk ");
    
    $('input:text[name=tanggalbundles]').val(tanggal);
    $('input:text[name=no_rawat]').val(no_rawat);
    $('input:text[name=no_rkm_medis]').val(no_rkm_medis);
    $('input:text[name=nm_pasien]').val(nm_pasien);
    $('input:text[name=kd_kamar]').val(kd_kamar);
  
   //hand_mainisk 
    if (hand_mainisk  === '1') {
      if ($('#hand_mainisk1').prop('checked')==false){
        $('#hand_mainisk1').prop('checked', true).change();
      } else if ($('#hand_mainisk1').prop('checked')==true){
        $('#hand_mainisk1').prop('checked', false).change(); 
      }
      
    } else if(hand_mainisk  === '0'){
      if ($('#hand_mainisk0').prop('checked')==true){
        $('#hand_mainisk0').prop('checked', false).change();
      } else if ($('#hand_mainisk0').prop('checked')==false){
        $('#hand_mainisk0').prop('checked', true).change();
      }
    }
    //kateter_mainisk
    if (kateter_mainisk === '1') {
      if ($('#kateter_mainisk1').prop('checked')==false){
        $('#kateter_mainisk1').prop('checked', true).change();
      } else if ($('#kateter_mainisk1').prop('checked')==true){
        $('#kateter_mainisk1').prop('checked', false).change(); 
      }
    } else if(kateter_mainisk === '0'){
      if ($('#kateter_mainisk0').prop('checked')==true){
        $('#kateter_mainisk0').prop('checked', false).change();
      }else if ($('#kateter_mainisk0').prop('checked')==false){
        $('#kateter_mainisk0').prop('checked', true).change();
      }
    }
    //baglantai_mainisk 
    if (baglantai_mainisk  === '1') {
      if ($('#baglantai_mainisk1').prop('checked')==false){
        $('#baglantai_mainisk1').prop('checked', true).change();
      } else if ($('#baglantai_mainisk1').prop('checked')==true){
        $('#baglantai_mainisk1').prop('checked', false).change(); 
      }
    } else if(baglantai_mainisk  === '0'){
      if ($('#baglantai_mainisk0').prop('checked')==true){
        $('#baglantai_mainisk0').prop('checked', false).change();
      }else if ($('#baglantai_mainisk0').prop('checked')==false){
        $('#baglantai_mainisk0').prop('checked', true).change();
      }
    }
    //bagrendah_mainisk 
    if (bagrendah_mainisk  === '1') {
      if ($('#bagrendah_mainisk1').prop('checked')==false){
        $('#bagrendah_mainisk1').prop('checked', true).change();
      } else if ($('#bagrendah_mainisk1').prop('checked')==true){
        $('#bagrendah_mainisk1').prop('checked', false).change(); 
      }
      
    } else if(bagrendah_mainisk  === '0'){
      if ($('#bagrendah_mainisk0').prop('checked')==true){
        $('#bagrendah_mainisk0').prop('checked', false).change();
      } else if ($('#bagrendah_mainisk0').prop('checked')==false){
        $('#bagrendah_mainisk0').prop('checked', true).change();
      }
    }
    //posisiselang_mainisk
    if (posisiselang_mainisk === '1') {
      if ($('#posisiselang_mainisk1').prop('checked')==false){
        $('#posisiselang_mainisk1').prop('checked', true).change();
      } else if ($('#posisiselang_mainisk1').prop('checked')==true){
        $('#posisiselang_mainisk1').prop('checked', false).change(); 
      }
      
    } else if(posisiselang_mainisk === '0'){
      if ($('#posisiselang_mainisk0').prop('checked')==true){
        $('#posisiselang_mainisk0').prop('checked', false).change();
      } else if ($('#posisiselang_mainisk0').prop('checked')==false){
        $('#posisiselang_mainisk0').prop('checked', true).change();
      }
    }

     //lepas_mainisk 
     if (lepas_mainisk  === '1') {
      if ($('#lepas_mainisk1').prop('checked')==false){
        $('#lepas_mainisk1').prop('checked', true).change();
      } else if ($('#lepas_mainisk1').prop('checked')==true){
        $('#lepas_mainisk1').prop('checked', false).change(); 
      }
      
    } else if(lepas_mainisk  === '0'){
      if ($('#lepas_mainisk0').prop('checked')==true){
        $('#lepas_mainisk0').prop('checked', false).change();
      } else if ($('#lepas_mainisk0').prop('checked')==false){
        $('#lepas_mainisk0').prop('checked', true).change();
      }
    }
    
  });
  
  // ketika tombol hapus MAINTANANCE ISK ditekan
  $("#bundles_maintanance").on("click", ".hapus_main_isk", function (event) {
    var baseURL = mlite.url + '/' + mlite.admin;
   event.preventDefault();
    var url = baseURL + '/bundles_hais/hapus_main_isk?t=' + mlite.token;
    var id                    = $(this).attr("data-id");
    var no_rawat              = $(this).attr("data-no_rawat");
    var tanggal               = $(this).attr("data-tanggal");
    var kd_kamar              = $(this).attr("data-kd_kamar");
    var hand_mainisk          = $(this).attr("data-hand_mainisk ");
    var kateter_mainisk       = $(this).attr("data-kateter_mainisk");
    var baglantai_mainisk     = $(this).attr("data-baglantai_mainisk");
    var bagrendah_mainisk     = $(this).attr("data-bagrendah_mainisk");
    var posisiselang_mainisk  = $(this).attr("data-posisiselang_mainisk");
    var lepas_mainisk         = $(this).attr("data-lepas_mainisk ");
  
   // tampilkan dialog konfirmasi
   bootbox.confirm("Apakah Anda yakin ingin menghapus data ini?", function(result){
     // ketika ditekan tombol ok
     if (result){
       // mengirimkan perintah penghapusan
       $.post(url, {
       id: id,
       no_rawat: no_rawat,
       tanggal: tanggal,
       kd_kamar: kd_kamar,
       hand_mainisk : hand_mainisk,
       kateter_mainisk: kateter_mainisk,
       baglantai_mainisk: baglantai_mainisk,
       bagrendah_mainisk: bagrendah_mainisk,
       posisiselang_mainisk: posisiselang_mainisk,
       lepas_mainisk: lepas_mainisk
  
       } ,function(data) {
         var url = baseURL + '/bundles_hais/bundles_maintanance/' + data + '?t=' + mlite.token;
         console.log(url)
         $.post(url, {id : id,
         }, function(data) {
           // tampilkan data
           bootbox.alert('Data Berhasil Dihapus');
          window.location = url;
         });
         $('input:radio[name=hand_mainisk]:checked').val();
         $('input:radio[name=kateter_mainisk]:checked').val();
         $('input:radio[name=baglantai_mainisk]:checked').val();
         $('input:radio[name=bagrendah_mainisk]:checked').val();
         $('input:radio[name=posisiselang_mainisk]:checked').val();
         $('input:radio[name=lepas_mainisk]:checked').val();
        $('input:text[name=tanggalbundles]').val("{?=date('Y-m-d')?}");
       });
     }
   });
   });


$("#bundles_maintanance").on("click", "#selesai_maintanance", function (event) {
  bersih();
});

//bundles_ido
// // ketika tombol simpan diklik
$("#bundles_ido").on("click", "#simpan_ido", function (event) {
  var baseURL = mlite.url + '/' + mlite.admin;
  event.preventDefault();

  var no_rawat            = $('input:text[name=no_rawat]').val();
  var tanggal             = $('input:text[name=tanggalbundles]').val();
  var kd_kamar            = $('input:text[name=kd_kamar]').val();
  var mandi_idopre        = $('input:radio[name=mandi_idopre]:checked').val();
  var cukur_idopre        = $('input:radio[name=cukur_idopre]:checked').val();
  var guladarah_idopre    = $('input:radio[name=guladarah_idopre]:checked').val();
  var antibiotik_idopre   = $('input:radio[name=antibiotik_idopre]:checked').val();
  var hand_idointra       = $('input:radio[name=hand_idointra]:checked').val();
  var steril_idointra     = $('input:radio[name=steril_idointra]:checked').val();
  var antiseptic_idointra = $('input:radio[name=antiseptic_idointra]:checked').val();
  var tehnik_idointra     = $('input:radio[name=tehnik_idointra]:checked').val();
  var mobile_idointra     = $('input:radio[name=mobile_idointra]:checked').val();
  var suhu_idointra       = $('input:radio[name=suhu_idointra]:checked').val();
  var luka_idopost        = $('input:radio[name=luka_idopost]:checked').val();
  var rawat_idopost       = $('input:radio[name=rawat_idopost]:checked').val();
  var apd_idopost         = $('input:radio[name=apd_idopost]:checked').val();
  var kaji_idopost        = $('input:radio[name=kaji_idopost]:checked').val();

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
    kaji_idopost: kaji_idopost,

  }, function (data) {
    // tampilkan data
    var url = baseURL + '/bundles_hais/bundles_ido/' + data + '?t=' + mlite.token;
    window.location = url;
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
  });
});

// ketika tombol EDIT ditekan
$("#bundles_ido").on("click",".edit_ido_pre", function(event){
  var baseURL = mlite.url + '/' + mlite.admin;
  event.preventDefault();
  var no_rawat          = $(this).attr("data-no_rawat");
  var tanggal           = $(this).attr("data-tanggal");
  var kd_kamar          = $(this).attr("data-kd_kamar");
  var no_rkm_medis      = $(this).attr("data-no_rkm_medis");
  var nm_pasien         = $(this).attr("data-nm_pasien");
  var mandi_idopre      = $(this).attr("data-mandi_idopre");
  var cukur_idopre      = $(this).attr("data-cukur_idopre");
  var guladarah_idopre  = $(this).attr("data-guladarah_idopre");
  var antibiotik_idopre = $(this).attr("data-antibiotik_idopre");
  //alert(mandi_idopre);

  $('input:text[name=tanggalbundles]').val(tanggal);
  $('input:text[name=no_rawat]').val(no_rawat);
  $('input:text[name=no_rkm_medis]').val(no_rkm_medis);
  $('input:text[name=nm_pasien]').val(nm_pasien);
  $('input:text[name=kd_kamar]').val(kd_kamar);

 //mandi_idopre
  if (mandi_idopre === '1') {
    if ($('#mandi_idopre1').prop('checked')==false){
      $('#mandi_idopre1').prop('checked', true).change();
    } else if ($('#mandi_idopre1').prop('checked')==true){
      $('#mandi_idopre1').prop('checked', false).change(); 
    }
    
  } else if(mandi_idopre === '0'){
    if ($('#mandi_idopre0').prop('checked')==true){
      $('#mandi_idopre0').prop('checked', false).change();
    } else if ($('#mandi_idopre0').prop('checked')==false){
      $('#mandi_idopre0').prop('checked', true).change();
    }
  }
  //cukur_idopre
  if (cukur_idopre === '1') {
    if ($('#cukur_idopre1').prop('checked')==false){
      $('#cukur_idopre1').prop('checked', true).change();
    } else if ($('#cukur_idopre1').prop('checked')==true){
      $('#cukur_idopre1').prop('checked', false).change(); 
    }
  } else if(cukur_idopre === '0'){
    if ($('#cukur_idopre0').prop('checked')==true){
      $('#cukur_idopre0').prop('checked', false).change();
    }else if ($('#cukur_idopre0').prop('checked')==false){
      $('#cukur_idopre0').prop('checked', true).change();
    }
  }
  //guladarah_idopre
  if (guladarah_idopre === '1') {
    if ($('#guladarah_idopre1').prop('checked')==false){
      $('#guladarah_idopre1').prop('checked', true).change();
    } else if ($('#guladarah_idopre1').prop('checked')==true){
      $('#guladarah_idopre1').prop('checked', false).change(); 
    }
  } else if(guladarah_idopre === '0'){
    if ($('#guladarah_idopre0').prop('checked')==true){
      $('#guladarah_idopre0').prop('checked', false).change();
    }else if ($('#guladarah_idopre0').prop('checked')==false){
      $('#guladarah_idopre0').prop('checked', true).change();
    }
  }
  //antibiotik_idopre
  if (antibiotik_idopre === '1') {
    if ($('#antibiotik_idopre1').prop('checked')==false){
      $('#antibiotik_idopre1').prop('checked', true).change();
    } else if ($('#antibiotik_idopre1').prop('checked')==true){
      $('#antibiotik_idopre1').prop('checked', false).change(); 
    }
    
  } else if(antibiotik_idopre === '0'){
    if ($('#antibiotik_idopre0').prop('checked')==true){
      $('#antibiotik_idopre0').prop('checked', false).change();
    } else if ($('#antibiotik_idopre0').prop('checked')==false){
      $('#antibiotik_idopre0').prop('checked', true).change();
    }
  }
  
});

$("#bundles_ido").on("click", ".hapus_idopre", function (event) {
 var baseURL = mlite.url + '/' + mlite.admin;
event.preventDefault();
 var url = baseURL + '/bundles_hais/hapus_idopre?t=' + mlite.token;
  var id                = $(this).attr("data-id");
  var no_rawat          = $(this).attr("data-no_rawat");
  var tanggal           = $(this).attr("data-tanggal");
  var kd_kamar          = $(this).attr("data-kd_kamar");
  var mandi_idopre      = $(this).attr("data-mandi_idopre");
  var cukur_idopre      = $(this).attr("data-cukur_idopre");
  var guladarah_idopre  = $(this).attr("data-guladarah_idopre");
  var antibiotik_idopre = $(this).attr("data-antibiotik_idopre");

// tampilkan dialog konfirmasi
bootbox.confirm("Apakah Anda yakin ingin menghapus data ini?", function(result){
  // ketika ditekan tombol ok
  if (result){
    // mengirimkan perintah penghapusan
    $.post(url, {
    id: id,
    no_rawat: no_rawat,
    tanggal: tanggal,
    kd_kamar: kd_kamar,
    mandi_idopre: mandi_idopre,
    cukur_idopre: cukur_idopre,
    guladarah_idopre: guladarah_idopre,
    antibiotik_idopre: antibiotik_idopre
    } ,function(data) {
      var url = baseURL + '/bundles_hais/bundles_ido/' + data + '?t=' + mlite.token;
      console.log(url)
      $.post(url, {id : id,
      }, function(data) {
        // tampilkan data
        bootbox.alert('Data Berhasil Dihapus');
        window.location = url;
      });
      $('input:radio[name=mandi_idopre]:checked').val("");
      $('input:radio[name=cukur_idopre]:checked').val("");
      $('input:radio[name=guladarah_idopre]:checked').val("");
      $('input:radio[name=antibiotik_idopre]:checked').val("");
      $('input:text[name=tanggalbundles]').val("{?=date('Y-m-d')?}");
    });
  }
});
}); 

// ketika tombol EDIT ditekan
$("#bundles_ido").on("click",".edit_ido_intra", function(event){
  var baseURL = mlite.url + '/' + mlite.admin;
  event.preventDefault();
  var no_rawat            = $(this).attr("data-no_rawat");
  var tanggal             = $(this).attr("data-tanggal");
  var kd_kamar            = $(this).attr("data-kd_kamar");
  var no_rkm_medis        = $(this).attr("data-no_rkm_medis");
  var nm_pasien           = $(this).attr("data-nm_pasien");
  var hand_idointra       =  $(this).attr("data-hand_idointra");
  var steril_idointra     =  $(this).attr("data-steril_idointra");
  var antiseptic_idointra =  $(this).attr("data-antiseptic_idointra");
  var tehnik_idointra     =  $(this).attr("data-tehnik_idointra");
  var mobile_idointra     =  $(this).attr("data-mobile_idointra");
  var suhu_idointra       =  $(this).attr("data-suhu_idointra");

  $('input:text[name=tanggalbundles]').val(tanggal);
  $('input:text[name=no_rawat]').val(no_rawat);
  $('input:text[name=no_rkm_medis]').val(no_rkm_medis);
  $('input:text[name=nm_pasien]').val(nm_pasien);
  $('input:text[name=kd_kamar]').val(kd_kamar);
 
  if (hand_idointra  === '1') {
    if ($('#hand_idointra1').prop('checked')==false){
      $('#hand_idointra1').prop('checked', true).change();
    } else if ($('#hand_idointra1').prop('checked')==true){
      $('#hand_idointra1').prop('checked', false).change(); 
    }
    
  } else if(hand_idointra  === '0'){
    if ($('#hand_idointra0').prop('checked')==true){
      $('#hand_idointra0').prop('checked', false).change();
    } else if ($('#hand_idointra0').prop('checked')==false){
      $('#hand_idointra0').prop('checked', true).change();
    }
  }

  if (steril_idointra  === '1') {
    if ($('#steril_idointra1').prop('checked')==false){
      $('#steril_idointra1').prop('checked', true).change();
    } else if ($('#steril_idointra1').prop('checked')==true){
      $('#steril_idointra1').prop('checked', false).change(); 
    }
    
  } else if(steril_idointra  === '0'){
    if ($('#steril_idointra0').prop('checked')==true){
      $('#steril_idointra0').prop('checked', false).change();
    } else if ($('#steril_idointra0').prop('checked')==false){
      $('#steril_idointra0').prop('checked', true).change();
    }
  }

  if (antiseptic_idointra  === '1') {
    if ($('#antiseptic_idointra1').prop('checked')==false){
      $('#antiseptic_idointra1').prop('checked', true).change();
    } else if ($('#antiseptic_idointra1').prop('checked')==true){
      $('#antiseptic_idointra1').prop('checked', false).change(); 
    }
    
  } else if(antiseptic_idointra  === '0'){
    if ($('#antiseptic_idointra0').prop('checked')==true){
      $('#antiseptic_idointra0').prop('checked', false).change();
    } else if ($('#antiseptic_idointra0').prop('checked')==false){
      $('#antiseptic_idointra0').prop('checked', true).change();
    }
  }

 if (tehnik_idointra  === '1') {
    if ($('#tehnik_idointra1').prop('checked')==false){
      $('#tehnik_idointra1').prop('checked', true).change();
    } else if ($('#tehnik_idointra1').prop('checked')==true){
      $('#tehnik_idointra1').prop('checked', false).change(); 
    }
    
  } else if(tehnik_idointra  === '0'){
    if ($('#tehnik_idointra0').prop('checked')==true){
      $('#tehnik_idointra0').prop('checked', false).change();
    } else if ($('#tehnik_idointra0').prop('checked')==false){
      $('#tehnik_idointra0').prop('checked', true).change();
    }
  }

  if (mobile_idointra  === '1') {
    if ($('#mobile_idointra1').prop('checked')==false){
      $('#mobile_idointra1').prop('checked', true).change();
    } else if ($('#mobile_idointra1').prop('checked')==true){
      $('#mobile_idointra1').prop('checked', false).change(); 
    }
    
  } else if(mobile_idointra  === '0'){
    if ($('#mobile_idointra0').prop('checked')==true){
      $('#mobile_idointra0').prop('checked', false).change();
    } else if ($('#mobile_idointra0').prop('checked')==false){
      $('#mobile_idointra0').prop('checked', true).change();
    }
  }

 if (suhu_idointra  === '1') {
    if ($('#suhu_idointra1').prop('checked')==false){
      $('#suhu_idointra1').prop('checked', true).change();
    } else if ($('#suhu_idointra1').prop('checked')==true){
      $('#suhu_idointra1').prop('checked', false).change(); 
    }
    
  } else if(suhu_idointra  === '0'){
    if ($('#suhu_idointra0').prop('checked')==true){
      $('#suhu_idointra0').prop('checked', false).change();
    } else if ($('#suhu_idointra0').prop('checked')==false){
      $('#suhu_idointra0').prop('checked', true).change();
    }
  }
});

$("#bundles_ido").on("click", ".hapus_idointra", function (event) {
  var baseURL = mlite.url + '/' + mlite.admin;
 event.preventDefault();
  var url = baseURL + '/bundles_hais/hapus_idointra?t=' + mlite.token;
   var id                  = $(this).attr("data-id");
   var no_rawat            = $(this).attr("data-no_rawat");
   var tanggal             = $(this).attr("data-tanggal");
   var kd_kamar            = $(this).attr("data-kd_kamar");
   var hand_idointra       = $(this).attr("data-hand_idointra");
   var steril_idointra     = $(this).attr("data-steril_idointra");
   var antiseptic_idointra = $(this).attr("data-antiseptic_idointra");
   var tehnik_idointra     = $(this).attr("data-tehnik_idointra");
   var mobile_idointra     = $(this).attr("data-mobile_idointra");
   var suhu_idointra       = $(this).attr("data-suhu_idointra");
 
 // tampilkan dialog konfirmasi
 bootbox.confirm("Apakah Anda yakin ingin menghapus data ini?", function(result){
   // ketika ditekan tombol ok
   if (result){
     // mengirimkan perintah penghapusan
     $.post(url, {
     id: id,
     no_rawat: no_rawat,
     tanggal: tanggal,
     kd_kamar: kd_kamar,
     hand_idointra: hand_idointra,
     steril_idointra: steril_idointra,
     antiseptic_idointra: antiseptic_idointra,
     tehnik_idointra: tehnik_idointra,
     mobile_idointra : mobile_idointra ,
    suhu_idointra:suhu_idointra,

     } ,function(data) {
       var url = baseURL + '/bundles_hais/bundles_ido/' + data + '?t=' + mlite.token;
       console.log(url)
       $.post(url, {id : id,
       }, function(data) {
         // tampilkan data
        bootbox.alert('Data Berhasil Dihapus');
        window.location = url;
       });
       $('input:radio[name=hand_idointra]:checked').val("");
       $('input:radio[name=steril_idointra]:checked').val("");
       $('input:radio[name=antiseptic_idointra]:checked').val("");
       $('input:radio[name=tehnik_idointra]:checked').val("");
       $('input:radio[name=mobile_idointra]:checked').val("");
       $('input:radio[name=suhu_idointra]:checked').val("");
       $('input:text[name=tanggalbundles]').val("{?=date('Y-m-d')?}");
      
     });
   }
 });
 });


// ketika tombol EDIT ditekan
$("#bundles_ido").on("click",".edit_ido_post", function(event){
  var baseURL = mlite.url + '/' + mlite.admin;
  event.preventDefault();
  var no_rawat      = $(this).attr("data-no_rawat");
  var tanggal       = $(this).attr("data-tanggal");
  var kd_kamar      = $(this).attr("data-kd_kamar");
  var no_rkm_medis  = $(this).attr("data-no_rkm_medis");
  var nm_pasien     = $(this).attr("data-nm_pasien");
  var luka_idopost  =  $(this).attr("data-luka_idopost");
  var rawat_idopost =  $(this).attr("data-rawat_idopost");
  var apd_idopost   =  $(this).attr("data-apd_idopost");
  var kaji_idopost  =  $(this).attr("data-kaji_idopost");

  $('input:text[name=tanggalbundles]').val(tanggal);
  $('input:text[name=no_rawat]').val(no_rawat);
  $('input:text[name=no_rkm_medis]').val(no_rkm_medis);
  $('input:text[name=nm_pasien]').val(nm_pasien);
  $('input:text[name=kd_kamar]').val(kd_kamar);

  if (luka_idopost  === '1') {
    if ($('#luka_idopost1').prop('checked')==false){
      $('#luka_idopost1').prop('checked', true).change();
    } else if ($('#luka_idopost1').prop('checked')==true){
      $('#luka_idopost1').prop('checked', false).change(); 
    }
    
  } else if(luka_idopost  === '0'){
    if ($('#luka_idopost0').prop('checked')==true){
      $('#luka_idopost0').prop('checked', false).change();
    } else if ($('#luka_idopost0').prop('checked')==false){
      $('#luka_idopost0').prop('checked', true).change();
    }
  }

  if (rawat_idopost  === '1') {
    if ($('#rawat_idopost1').prop('checked')==false){
      $('#rawat_idopost1').prop('checked', true).change();
    } else if ($('#rawat_idopost1').prop('checked')==true){
      $('#rawat_idopost1').prop('checked', false).change(); 
    }
    
  } else if(rawat_idopost  === '0'){
    if ($('#rawat_idopost0').prop('checked')==true){
      $('#rawat_idopost0').prop('checked', false).change();
    } else if ($('#rawat_idopost0').prop('checked')==false){
      $('#rawat_idopost0').prop('checked', true).change();
    }
  }

 if (apd_idopost  === '1') {
    if ($('#apd_idopost1').prop('checked')==false){
      $('#apd_idopost1').prop('checked', true).change();
    } else if ($('#apd_idopost1').prop('checked')==true){
      $('#apd_idopost1').prop('checked', false).change(); 
    }
    
  } else if(apd_idopost  === '0'){
    if ($('#apd_idopost0').prop('checked')==true){
      $('#apd_idopost0').prop('checked', false).change();
    } else if ($('#apd_idopost0').prop('checked')==false){
      $('#apd_idopost0').prop('checked', true).change();
    }
  }

  if (kaji_idopost  === '1') {
    if ($('#kaji_idopost1').prop('checked')==false){
      $('#kaji_idopost1').prop('checked', true).change();
    } else if ($('#kaji_idopost1').prop('checked')==true){
      $('#kaji_idopost1').prop('checked', false).change(); 
    }
    
  } else if(kaji_idopost  === '0'){
    if ($('#kaji_idopost0').prop('checked')==true){
      $('#kaji_idopost0').prop('checked', false).change();
    } else if ($('#kaji_idopost0').prop('checked')==false){
      $('#kaji_idopost0').prop('checked', true).change();
    }
  }
});

$("#bundles_ido").on("click", ".hapus_idopost", function (event) {
  var baseURL = mlite.url + '/' + mlite.admin;
 event.preventDefault();
  var url = baseURL + '/bundles_hais/hapus_idopost?t=' + mlite.token;
   var id                  = $(this).attr("data-id");
   var no_rawat            = $(this).attr("data-no_rawat");
   var tanggal             = $(this).attr("data-tanggal");
   var kd_kamar            = $(this).attr("data-kd_kamar");
   var luka_idopost        = $(this).attr("data-luka_idopost");
   var rawat_idopost       = $(this).attr("data-rawat_idopost");
   var apd_idopost         = $(this).attr("data-apd_idopost");
   var kaji_idopost        = $(this).attr("data-kaji_idopost");
 
 // tampilkan dialog konfirmasi
 bootbox.confirm("Apakah Anda yakin ingin menghapus data ini?", function(result){
   // ketika ditekan tombol ok
   if (result){
     // mengirimkan perintah penghapusan
     $.post(url, {
     id: id,
     no_rawat: no_rawat,
     tanggal: tanggal,
     kd_kamar: kd_kamar,
     luka_idopost : luka_idopost ,
     rawat_idopost: rawat_idopost,
     apd_idopost:  apd_idopost,
     kaji_idopost: kaji_idopost,

     } ,function(data) {
       var url = baseURL + '/bundles_hais/bundles_ido/' + data + '?t=' + mlite.token;
       console.log(url)
       $.post(url, {id : id,
       }, function(data) {
         // tampilkan data
         bootbox.alert('Data Berhasil Dihapus');
         window.location = url;
       });
       $('input:radio[name=luka_idopost]:checked').val("");
       $('input:radio[name=rawat_idopost]:checked').val("");
       $('input:radio[name=apd_idopost]:checked').val("");
       $('input:radio[name=kaji_idopost]:checked').val("");
       $('input:text[name=tanggalbundles]').val("{?=date('Y-m-d')?}");
     });
   }
 });
 });


$("#bundles_ido").on("click", "#selesai_ido", function (event) {
  bersih();
});

function bersih() {
  window.history.back();
}


