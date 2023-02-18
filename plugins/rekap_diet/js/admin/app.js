$(document).ready(function(){

  $('#manage').on('click', '#submit_periode_rawat_jalan', function(event){
    var baseURL = mlite.url + '/' + mlite.admin;
    event.preventDefault();
    var url    = baseURL + '/presensi/rekap_presensi?t=' + mlite.token;
    var periode_rawat_jalan  = $('input:text[name=periode_rawat_jalan]').val();
    var periode_rawat_jalan_akhir  = $('input:text[name=periode_rawat_jalan_akhir]').val();
    var s  = $('input:text[name=s]').val();
  
    if(periode_rawat_jalan == '') {
      alert('Tanggal awal masih kosong!')
    }
    if(periode_rawat_jalan_akhir == '') {
      alert('Tanggal akhir masih kosong!')
    }

    var ss = decodeURI(s);

    var optionText = document.getElementById("bangsal").value;
    var option = optionText.toLowerCase();
    var opt = decodeURI(option);

    window.location.href = baseURL+'/rekap_diet/rekap_dietpasien?awal='+periode_rawat_jalan+'&akhir='+periode_rawat_jalan_akhir+'&ruang='+opt+'&s='+ss+'&t=' + mlite.token;
    
    event.stopPropagation();
  
  });
  $('#manage').on('click', '#cari', function(event){
    var baseURL = mlite.url + '/' + mlite.admin;
    event.preventDefault();
    var url    = baseURL + '/rekap_diet/rekap_dietpasien?t=' + mlite.token;
    var s  = $('input:text[name=s]').val();
  
    
    var optionText = document.getElementById("bangsal").value;
    var option = optionText.toLowerCase();
    var opt = decodeURI(option);

    window.location.href = baseURL+'/rekap_diet/rekap_dietpasien?s='+s+'&ruang='+opt+'&t=' + mlite.token;
    
  
    event.stopPropagation();
  
  });
})

   $(function () {
       $('.periode_rawat_jalan').datetimepicker({
         defaultDate: new Date(),
         format: 'YYYY-MM-DD',
         locale: 'id'
       });
   });
