@extends('newmaster')
@section('content')
<style>
    td.details-control {
        background: url("{{ asset('assets/img/details_open.png')}}") no-repeat center center;
        cursor: pointer;
    }
    tr.shown td.details-control {
        background: url("{{ asset('assets/img/details_close.png')}}") no-repeat center center;
    }
</style>
<div class="row">
  @if(Auth::user()->role == "super admin")
  <button class="btn btn-success btn-sm mb-4 ml-2 add">
    <i class="fa fa-plus" aria-hidden="true"></i>  Tambah User
  </button>
  @endif
</div>
<div class="table-responsive">          
<table class="table" id="example">
    <thead>
        <tr>
            <th></th>
            <th>Nama User</th>
            <th>Email</th>
            <th>User Sebagai</th>
            <th>Aksi</th>
        </tr>
    </thead>
    <tbody></tbody>
</table>
</div>
  <!-- Demo content -->
  <div class="modal fade addsa" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <form method="POST" action="{{ route('user.store') }}" enctype="multipart/form-data" id="import_participan">
        @csrf
        <div class="modal-dialog modal-lg" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">
                        <strong>Tambah Data User</strong>
                    </h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>Mohon Input Data User</p>
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <label><strong>Nama User</strong></label>
                        </div>
                        <div class="col-md-6">
                            <input type="text" name="nama" id="nama" class="form-control" required="true">
                        </div>
                    </div>
                    <div id="email-password">
                        <div class="row mb-3">
                            <div class="col-md-2">
                                <label><strong>Email</strong></label>
                            </div>
                            <div class="col-md-6">
                                <input type="email" name="email" id="email" class="form-control" required="true">
                            </div>
                        </div>
                        <div class="row mb-3">
                            <div class="col-md-2">
                                <label><strong>Password</strong></label>
                            </div>
                            <div class="col-md-6">
                                <input type="password" name="password" id="password" class="form-control" required="true">
                            </div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-2">
                            <label><strong>User Sebagai</strong></label>
                        </div>
                        <div class="col-md-6">
                            <select name="role" class="form-control" id="user_sebagai" required="true">
                              <option value="">Pilih</option>
                              <option value="Admin">Admin</option>
                              <option value="Staff">Staff</option>
                            </select>
                            
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary modal-close btn-sm" data-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-success btn-sm">Simpan</button>
                </div>
                <input type="hidden" name="id" id="id" value=""/>
                <input type="hidden" name="id_user" id="id_user" value=""/>
          </div>
        </div>
    </form>
 </div>
@endsection
@push('scripts')
<script type="text/javascript">
    function format ( d ) {
        console.log(d.data())
        var url = '{{ route("user_parent.datatable") }}';
        var id = d.data().id;
        var table = $('<table class="display" width="100%"/>');
            // Display it the child row
            d.child(table).show();

            // Initialise as a DataTable
            var usersTable = table.DataTable({
                dom: 'Bfrtip',
                pageLength: 5,
                ajax: {
                    url: url + '?id=' + id,
                    type: 'get',
                    data: function ( d ) {
                        // d.site = rowData.id;
                    }
                },
                columns: [
                    { title: 'Nama User', data: 'name' },
                    { title: 'Email', data: 'email' },
                    { title: 'User Sebagai', data: 'role' },
                    { title: 'Aksi', data: 'aksi' },
                ],
                select: false,
                searching: false,
                paging: false,
                buttons: []
            });
    }
    $(document).ready(function(){
  // Sidebar toggle behavior
        $('body').on('click','.modal-close', function(){
            $("#id").val(' ')
            $("#nama").val(' ')
            $("#kode_pasyankes").val(' ')
            $("#user_sebagai").val(' ')
            $("#email").val(' ')
            $("#password").val(' ')
            $("#id_user").val(' ')
            $('.modal').modal('hide')
        })
        $('body').on('click','.close', function(){
            $("#id").val(' ')
            $("#nama").val(' ')
            $("#kode_pasyankes").val(' ')
            $("#user_sebagai").val(' ')
            $("#email").val('')
            $("#password").val(' ')
            $("#id_user").val(' ')
            $('.modal').modal('hide')
        })
        $('#sidebarCollapse').on('click', function() {
            $('#sidebar, #content').toggleClass('active');
        });

        $('body').on('click','.add', function(){
            $(".modal-title").html('Add data user')
            $("#email-password").show()
            $("#email").prop('disabled', false)
            $("#password").prop('disabled', false)
            $('.modal').modal('show')
        })

        $('body').on('click', '.delete', function(){
            if(confirm('Apakah anda yakin menghapus data ini ?')){
                var id = $(this).attr('data-bind');
                var token = $("meta[name='csrf-token']").attr("content");
                $.ajax({
                    dataType: 'json',
                    method: 'DELETE',
                    url: '{{ route("user.delete") }}',
                    data: {
                        "id": id,
                        "_token" : token,
                    }
                }).done(function(){
                    location.reload()
                })
            }
        })
        
        $('body').on('click','.edit', function(){
            var data = JSON.parse($(this).attr("data-bind"));
            console.log(data)
            $(".modal-title").html('Edit data user')
            $("#id").val(data.id)
            $("#nama").val(data.name)
            $("#kode_pasyankes").val(data.kode_pasyankes)
            $("#user_sebagai").val(data.role)
            $("#email-password").hide()
            $("#email").prop('disabled', true)
            $("#password").prop('disabled', true)
            $("#id_user").val(data.parent_admin)
            $('.modal').modal('show')
        })

        $('body').on('click','.add-staff', function(){
            var id_user = $(this).attr("data-bind");
            $(".modal-title").html('Tambah staff')
            $("#id_user").val(id_user)
            $("#user_sebagai").val("Staff")
            $('.modal').modal('show')
        })

        var url = '{{ route("user.datatable") }}';
        var table = $('#example').DataTable({
            processing: true,
            serverSide: true,
            ajax: url,
            columns: [
                {
                    "className": 'details-control',
                    "orderable":  false,
                    "data": null,
                    "defaultContent": ''
                },
                { data: "name"},
                { data: "email"},
                { data: "role"},
                { data: "aksi" },
            ],
            "order": [[2, 'desc']],
             createdRow: function (row, data, index) {
                console.log(data.total_user)
                var td = $(row).find("td:first");
                // td.removeClass('details-control');
             }
        });
        $('#example tbody').on('click', 'td.details-control', function () {
            var tr = $(this).closest('tr');
            var row = table.row( tr );
            if ( row.child.isShown() ) {
                // This row is already open - close it
                row.child.hide();
                tr.removeClass('shown');
            }
            else {
                // Open this row
                format(row, 'child-table');
                tr.addClass('shown');
            }
        });
    })
</script>
@endpush