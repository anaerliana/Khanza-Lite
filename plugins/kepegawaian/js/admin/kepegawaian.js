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

$(document).ready(function() {
  var t = $('#dukpns').DataTable({
    "dom": 'Bfrtip',
    // "buttons":['print', 'excel',  'pdf'],
    "buttons" : [
      {
          extend:'pdf',
          // footer: true,
          title: ['DAFTAR URUT KEPANGKATAN PEGAWAI NEGERI SIPIL DI LINGKUNGAN PEMERINTAH KABUPATEN HULU SUNGAI TENGAH','%0Ag'],
          //title: ['UNIT KERJA : RSUD H.DAMANHURI BARABAI'],
          //KEADAAN : DESEMBER 2022
          filename: 'DUK PNS', 
          //messageTop: 'DAFTAR URUT KEPANGKATAN PNS',
          // alignment: 'center',
          orientation: 'landscape',
          pageSize: 'TABLOID',
          exportOptions: {
              columns: ':visible'
          },
          
      },
      {
        extend:'excel',
        // footer: true,
        title: ['DAFTAR URUT KEPANGKATAN PEGAWAI NEGERI SIPIL DI LINGKUNGAN PEMERINTAH KABUPATEN HULU SUNGAI TENGAH'],
        filename: 'DUK PNS RSUD H.DAMANHURI BARABAI', 
        exportOptions: {
            columns: ':visible'
        }
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



