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
    "order": [[ 0, 'desc' ]],
   // "buttons": ['excel', 'pdf'],
    //"ordering": true,
    "buttons" : [
      {
          extend: 'excel',
          exportOptions: {
              columns: [ 0, 1, 2, 3, 4, 5, 6, 7, 9 ]
          }
      }
    ],
    columnDefs: [
      {   "targets": [0],
          "visible": false,
          "searchable": false
      }],
  });
  $('#laporan').DataTable({
    "dom": 'Bfrtip',
    "buttons": ['excel', 'pdf']
  });
});

$(document).ready(function() {
  var t = $('#lapstr').DataTable({
    "dom": 'Bfrtip',
    "buttons": ['excel', 'pdf'],
    order: [[0, 'asc']],
  });
  t.on('order.dt search.dt', function () {
    let i = 1;

    t.cells(null, 0, { search: 'applied', order: 'applied' }).every(function (cell) {
        this.data(i++);
    });
}).draw();
});

document.write("\n");
// document.write(new Date().getMonth());
//bulan = date.getMonth();
// document.write(arrbulan[bulan]);
// arrbulan = ["Januari","Februari","Maret","April","Mei","Juni","Juli","Agustus","September","Oktober","November","Desember"];
 // bulan = date.getMonth();
$(document).ready(function() {
  var date = new Date();
  var tahun = date.getFullYear();
  var bulan = date.getMonth();
  switch(bulan) {
    case 0: bulan = "JANUARI"; break;
    case 1: bulan = "FEBRUARI"; break;
    case 2: bulan = "MARET"; break;
    case 3: bulan = "APRIL"; break;
    case 4: bulan = "MEI"; break;
    case 5: bulan = "JUNI"; break;
    case 6: bulan = "JULI"; break;
    case 7: bulan = "AGUSTUS"; break;
    case 8: bulan = "SEPTEMBER"; break;
    case 9: bulan = "OKTOBER"; break;
    case 10: bulan = "NOVEMBER"; break;
    case 11: bulan = "DESEMBER"; break;
   }

  var t = $('#dukpns').DataTable({
    "dom": 'Bfrtip',
    // "buttons":['print', 'excel',  'pdf'],
    "buttons" : [
      {
          extend:'pdf',
          footer: true,
          header: true,
          title: ['DAFTAR URUT KEPANGKATAN PEGAWAI NEGERI SIPIL \n DI LINGKUNGAN PEMERINTAH KABUPATEN HULU SUNGAI TENGAH \n UNIT KERJA: RSUD H.DAMANHURI BARABAI \n KEADAAN : '+bulan + " " + tahun],
          filename: 'DUK PNS RSUD H.DAMANHURI BARABAI', 
          orientation: 'landscape',
          pageSize: 'TABLOID', 
          exportOptions: {
            columns: ':visible'
        }  ,
        customize : function(doc) {
          doc.styles['td:nth-child(2)'] = { 
            width: '100px',
            'max-width': '100px'
          }
       }
      },
      {
       extend:'excel',
       title:  ['DAFTAR URUT KEPANGKATAN PEGAWAI NEGERI SIPIL \n DI LINGKUNGAN PEMERINTAH KABUPATEN HULU SUNGAI TENGAH \n UNIT KERJA: RSUD H.DAMANHURI BARABAI \n KEADAAN : '+bulan + " " + tahun],
        filename: 'DUK PNS RSUD H.DAMANHURI BARABAI', 
        exportOptions: {
            columns: ':visible',
        },
    },
    ],
    order: [[3, 'desc']],
  });
  t.on('order.dt search.dt', function () {
    let i = 1;

    t.cells(null, 0, { search: 'applied', order: 'applied' }).every(function (cell) {
        this.data(i++);
    });
}).draw();
});

$(document).ready(function() {
  var date = new Date();
  var tahun = date.getFullYear();
  var bulan = date.getMonth();
  switch(bulan) {
    case 0: bulan = "JANUARI"; break;
    case 1: bulan = "FEBRUARI"; break;
    case 2: bulan = "MARET"; break;
    case 3: bulan = "APRIL"; break;
    case 4: bulan = "MEI"; break;
    case 5: bulan = "JUNI"; break;
    case 6: bulan = "JULI"; break;
    case 7: bulan = "AGUSTUS"; break;
    case 8: bulan = "SEPTEMBER"; break;
    case 9: bulan = "OKTOBER"; break;
    case 10: bulan = "NOVEMBER"; break;
    case 11: bulan = "DESEMBER"; break;
   }

  var t = $('#pensiunpns').DataTable({
    "dom": 'Bfrtip',
    "buttons" : [
      {
          extend:'pdf',
          footer: true,
          header: true,
          title: ['DAFTAR PERKIRAAN PENSIUN PEGAWAI NEGERI SIPIL \n DI LINGKUNGAN PEMERINTAH KABUPATEN HULU SUNGAI TENGAH \n UNIT KERJA: RSUD H.DAMANHURI BARABAI \n KEADAAN : '+bulan + " " + tahun],
          filename: 'PERKIRAAN PENSIUN PNS RSUD H.DAMANHURI BARABAI', 
          orientation: 'landscape',
          pageSize: 'A4', 
          exportOptions: {
            columns: ':visible'
        },
      },
      {
       extend:'excel',
       title:  ['DAFTAR PERKIRAAN PENSIUN PNS \n UNIT KERJA: RSUD H.DAMANHURI BARABAI \n KEADAAN : '+bulan + " " + tahun],
        filename: 'PERKIRAAN PENSIUN PNS RSUD H.DAMANHURI BARABAI', 
        exportOptions: {
            columns: ':visible',
        },
    },
    ],
    order: [[4, 'desc']],
  });
  t.on('order.dt search.dt', function () {
    let i = 1;

    t.cells(null, 0, { search: 'applied', order: 'applied' }).every(function (cell) {
        this.data(i++);
    });
}).draw();
});



