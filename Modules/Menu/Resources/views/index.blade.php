@extends('layouts.app')

@section('title', 'Manage Menu')

@push('styles')
<link href="{{ asset('vendor/multi-select/css/multi-select.css') }}" rel="stylesheet" />
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
                            <a href="javascript:void(0);" onclick="newMenu()" role="button">
                                <i class="material-icons col-green">add_box</i>
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover dataTable" id="branchList">
                            <thead>
                                <tr>
                                    <th width="20px">No</th>
                                    <th>Menu Name</th>
                                    <th>Price</th>
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

<div class="modal fade" id="menuModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="menuModalLabel"></h4>
            </div>
            <div class="modal-body">
                <form id="frmMenu">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" class="form-control" name="name" placeholder="Menu Name, ex. Fried Rice">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" name="price" class="form-control price" placeholder="Default Price for menu, ex. 12.000">
                        </div>
                    </div>
                    @if(checkModule('Category'))
                    <div class="form-group">
                        <label for="category_id">Select Categories (Optional)</label>
                        <select class="form-control category_id" name="menu_categories[]" multiple>
                            @forelse($categories as $category)
                            <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @empty
                            <option disabled selected>Doesn't have any category</option>
                            @endforelse
                        </select>
                    </div>
                    @endif
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
<script src="{{ asset('vendor/multi-select/js/jquery.multi-select.js') }}"></script>
<script src="{{ asset('vendor/jquery-inputmask/jquery.inputmask.bundle.js') }}"></script>
<script src="{{ asset('vendor/jquery-mask/jquery.mask.min.js') }}"></script>
<script>
var table;
var modal = $('#menuModal');
var modalLabel = $('#menuModalLabel');
var form = $('#frmMenu');
var url, method;
var urlDataTable = '{{ route('menu.dataTable') }}';

function newMenu() {
    form.get(0).reset();
    @if(checkModule('Category'))
    $('.category_id').multiSelect('deselect_all');
    $('.category_id').multiSelect('refresh');
    @endif
    url = '{{ route('menu.create') }}';
    method = 'POST';
    modalLabel.html('Add new Menu');
    modal.modal('show');
}

function updateMenu(id) {
    @if(checkModule('Category'))
    $('.category_id').multiSelect('deselect_all');
    @endif
    form.get(0).reset();
    url = '{{ route('menu.update') }}';
    method = 'PATCH';
    modalLabel.html('Update Menu');
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
        url: '{{ route('menu.get') }}',
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
                @if(checkModule('Category'))
                if(key == 'menu_categories') {
                    if(key.length >= 1) {
                        var values = [];
                        _.map(val, function(val, key) {
                            $('.category_id option[value="'+val.category_id+'"').attr('selected', true);
                            $('.category_id').multiSelect('refresh');
                        });
                    }
                }
                @endif
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

function deleteMenu(id) {
    swal({
        title: "Delete this menu with the related data?",
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
        url: '{{ route('menu.delete') }}',
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
    @if(checkModule('Category'))
    $('.category_id').multiSelect({ selectableOptgroup: false });
    @endif
    $(".price").inputmask({
        "alias":"numeric",
        "prefix":"Rp",
        "digits":0,
        "digitsOptional":false,
        "decimalProtect":true,
        "groupSeparator":",",
        "radixPoint":".",
        "radixFocus":true,
        "autoGroup":true,
        "autoUnmask":true,
        "clearMaskOnLostFocus": true,
        "allowMinus": false,
        "repeat": 6
    });

    $(".priceDisplay").mask('000.000.000', {reverse: true});

    var styles = {
        price: function(data) {
            let text = '<span>No Price</div>';
            if(data != null && data != 0) {
                text = 'Rp. <span class="priceDisplay">'+data+'</span>';
            }
            return text;
        },
        action: function(data) {
            let button = '';
            button += '<a href="#" class="btn bg-cyan btn-xs waves-effect" onclick="updateMenu('+data+')"><i class="material-icons">mode_edit</i></a>';
            button += '&nbsp;<a href="#" class="btn bg-red btn-xs waves-effect" onclick="deleteMenu('+data+')"><i class="material-icons">delete</i></a>';
            return button;
        }
    }

    table = $('#branchList').DataTable({
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
            { data: 'price', render: styles.price },
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
