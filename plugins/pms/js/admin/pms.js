$("#form").on("click","#nomor_kegiatan", function(event){
  var baseURL = mlite.url + '/' + mlite.admin;
  event.preventDefault();
  var url = baseURL + '/pms/nomorkegiatan?t=' + mlite.token;
  $.post(url, {
  } ,function(data) {
    $("#nomor_kegiatan").val(data);
  });
});


// tombol batal diklik
$("#nomor_kegiatan").on("click", "#batal", function(event){
  bersih();
});
