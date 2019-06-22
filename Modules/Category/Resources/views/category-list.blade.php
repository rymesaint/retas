@extends('layouts.app')

@section('title', 'Manage Category')

@push('styles')
<style>
.card .header .header-dropdown i {
    font-size: 32px !important;
}
</style>
@endpush

@section('content')
<div class="container-fluid">
    <div class="block-header">
        <h2>@yield('title')</h2>
    </div>
    <div class="row clearfix">
        <div class="col-lg-12 col-md-12 col-sm-12 col-xs-12">
            <div class="card">
                <div class="header">
                    <h2>
                        @yield('title')
                    </h2>
                    <ul class="header-dropdown m-r--5">
                        <li>
                            <a href="javascript:void(0);" onclick="newCategory()" role="button">
                                <i class="material-icons col-green">add_box</i>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover dataTable" id="categoryList">
                            <thead>
                                <tr>
                                    <th width="20px">No</th>
                                    <th>Category Name</th>
                                    <th>Slug</th>
                                    <th>Total Menu (x)</th>
                                    <th width="50px">Action</th>
                                </tr>
                            </thead>
                            <tbody></tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="categoryModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="categoryModalLabel"></h4>
            </div>
            <div class="modal-body">
                <form id="frmCategory">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" class="form-control" name="name" placeholder="Category Name, ex. Ice Drinks">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">{{ __('CLOSE') }}</button>
                <button type="button" class="btn btn-link waves-effect btn-save">{{ __('SAVE') }}</button>
            </div>
        </div>
    </div>
</div>
@stop

@push('scripts')
<script>
var table;
var modal = $('#categoryModal');
var modalLabel = $('#categoryModalLabel');
var form = $('#frmCategory');
var url, method;
var urlDataTable = '{{ route('category.dataTable') }}';

function newCategory() {
    form.get(0).reset();
    url = '{{ route('category.create') }}';
    method = 'POST';
    modalLabel.html('Add new Category');
    modal.modal('show');
}

function updateCategory(id) {
    form.get(0).reset();
    url = '{{ route('category.update') }}';
    method = 'PATCH';
    modalLabel.html('Update Category');
    getData(id);
}

function reloadDT(query, backToOne){
    query = (query) ? query : ''

    if (backToOne) {
        table.ajax.url(urlDataTable + query).draw()
    } else {
        table.ajax.url(urlDataTable + query).draw(false)
    }
}

function getData(id) {
    $.ajax({
        url: '{{ route('category.get') }}',
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        data: {
            id: id
        },
        success: function(resp) {
            var data = resp.data;
            _.map(data, function(val, key) {
                $('[name="'+key+'"]').val(val);
            });
            modal.modal('show');
        },
        error: function(err) {
            if (_.has(err.responseJSON, 'errors')) {
                _.map(err.responseJSON.errors, function(val, key){
                    showNotification('bg-red', val[0], 'top', 'right', 'animated bounceIn', 'animated bounceOut');
                })
            } else {
                showNotification('bg-red', err.responseJSON.message, 'top', 'right', 'animated bounceIn', 'animated bounceOut');
            }
            modal.modal('hide');
        }
    });
}

function deleteCategory(id) {
    swal({
        title: "Delete this category with the related data?",
        text: "You will not be able to recover this data!",
        type: "warning",
        showCancelButton: true,
        confirmButtonColor: "#DD6B55",
        confirmButtonText: "Sure",
        cancelButtonText: "Cancel",
        closeOnConfirm: true,
        closeOnCancel: true
    }, function (isConfirm) {
        if (isConfirm) {
            deleteData(id);
        }
    });
}

function deleteData(id) {
    $.ajax({
        url: '{{ route('category.delete') }}',
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        data: {
            id: id
        },
        success: function(resp) {
            reloadDT();
        },
        error: function(err) {
            if (_.has(err.responseJSON, 'errors')) {
                _.map(err.responseJSON.errors, function(val, key){
                    showNotification('bg-red', val[0], 'top', 'right', 'animated bounceIn', 'animated bounceOut');
                })
            } else {
                showNotification('bg-red', err.responseJSON.message, 'top', 'right', 'animated bounceIn', 'animated bounceOut');
            }
        }
    });
}

function loading() {
    $('.btn-save').attr('disabled', 'disabled');
}

function unloading() {
    $('.btn-save').removeAttr('disabled');
}

$(function() {
    var styles = {
        menuCount: function(data) {
            let text = '<span class="badge bg-grey">'+data+'</span>';
            if(data >= 1 ) {
                text = '<span class="badge bg-green">'+data+'</span>';
            }
            return text;
        },
        action: function(data) {
            let button = '';
            button += '<a href="#" class="btn bg-cyan btn-xs waves-effect" onclick="updateCategory('+data+')"><i class="material-icons">mode_edit</i></a>';
            button += '&nbsp;<a href="#" class="btn bg-red btn-xs waves-effect" onclick="deleteCategory('+data+')"><i class="material-icons">delete</i></a>';
            return button;
        }
    }

    table = $('#categoryList').DataTable({
        processing: true,
        serverSide: true,
        responsive: true,
        ajax: {
            headers: {
              'X-CSRF-TOKEN': "{{ csrf_token() }}"
            },
            url: urlDataTable,
            method: 'POST',
        },
        columns: [
            { data: 'DT_RowIndex' },
            { data: 'name' },
            { data: 'slug' },
            { data: 'menu_count', sClass: 'text-center', render: styles.menuCount },
            { data: 'id', sClass: 'text-center', render: styles.action }
        ]
    });

    $('.btn-save').click(function() {
        var data = form.serialize();

        $.ajax({
            url: url,
            data: data,
            method: method,
            beforeSend: loading(),
            success: function(resp) {
                showNotification('bg-green', resp.message, 'top', 'right', 'animated flipInX', 'animated flipOutX');
                modal.modal('hide');
                form.get(0).reset();
                reloadDT();
                unloading();
            },
            error: function(err) {
                if (_.has(err.responseJSON, 'errors')) {
                    _.map(err.responseJSON.errors, function(val, key){
                        showNotification('bg-red', val[0], 'top', 'right', 'animated bounceIn', 'animated bounceOut');
                    })
                } else {
                    showNotification('bg-red', err.responseJSON.message, 'top', 'right', 'animated bounceIn', 'animated bounceOut');
                }
                unloading();
            }
        })
    });
})
</script>
@endpush
