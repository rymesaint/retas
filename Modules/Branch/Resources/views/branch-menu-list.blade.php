@extends('layouts.app')

@section('title', 'Branch Menu')

@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/chosen/chosen.min.css') }}">
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
                                    <th>Branch</th>
                                    <th>Menu Name</th>
                                    <th>Price</th>
                                    <th>Use Master Price</th>
                                    <th>Availability</th>
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

<div class="modal fade" id="menuBranchModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="menuBranchModalLabel"></h4>
            </div>
            <div class="modal-body">
                <form id="frmMenuBranch">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="form-group">
                        <div class="form-line">
                            <select name="branch_id" class="form-control" required>
                                <option value="" selected>Please select the branch</option>
                                @forelse($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->branchName }}</option>
                                @empty
                                <option disabled selected>Doesn't have any branch</option>
                                @endforelse
                            </select>
                        </div>
                    </div>
                    <div class="form-group menuName" style="display:none">
                        <div class="form-line">
                            <input type="text" name="name" class="form-control" disabled>
                        </div>
                    </div>
                    <div class="form-group priceEdit" style="display:none">
                        <div class="form-line">
                            <input type="text" name="price" class="form-control price" placeholder="Default Price for menu, ex. 12.000">
                        </div>
                    </div>
                    <div class="form-group useMasterPrice" style="display:none">
                        <label>Use Master Price?</label>
                        <div class="switch">
                            <label>False<input type="checkbox" name="useMasterPrice"><span class="lever"></span>TRUE</label>
                        </div>
                    </div>
                    <div class="form-group selectMenus">
                        <label for="menus">Select Menus</label>
                        <select class="form-control menus" name="menu_id[]" data-placeholder="Select menus for branch" multiple>
                            @forelse($menus as $menu)
                            <option value="{{ $menu->id }}">{{ $menu->name }}</option>
                            @empty
                            <option disabled selected>Doesn't have any menu</option>
                            @endforelse
                        </select>
                    </div>
                    <div class="form-group usePercentagePrice">
                        <label>Use Percentage Price Based On Branch?</label>
                        <label class="percentageLabel"></label>
                        <div class="switch">
                            <label>False<input type="checkbox" name="usePercentagePrice"><span class="lever"></span>TRUE</label>
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
<script src="{{ asset('vendor/chosen/chosen.jquery.min.js') }}"></script>
<script src="{{ asset('vendor/jquery-inputmask/jquery.inputmask.bundle.js') }}"></script>
<script src="{{ asset('vendor/jquery-mask/jquery.mask.min.js') }}"></script>
<script>
var table;
var modal = $('#menuBranchModal');
var modalLabel = $('#menuBranchModalLabel');
var form = $('#frmMenuBranch');
var url, method;
var marketPrice;
var branchPrice;
var urlDataTable = '{{ route('menu.branch.dataTable') }}';

function newMenu() {
    form.get(0).reset();
    $('[name="usePercentagePrice').removeAttr('checked');
    $('[name="usePercentagePrice"]').attr('disabled', 'disabled');
    $('.percentageLabel').html('');
    $('.menus').prop('disabled', true).trigger('chosen:updated');
    $('[name="branch_id"]').removeAttr('disabled');
    $('.selectMenus').show();
    $('.useMasterPrice').hide();
    $('.menuName').hide();
    $('.priceEdit').hide();
    url = '{{ route('menu.branch.create') }}';
    method = 'POST';
    modalLabel.html('Add Menu To Branch');
    modal.modal('show');
}

function updateMenu(id) {
    form.get(0).reset();
    $('.selectMenus').hide();
    $('.useMasterPrice').show();
    $('.priceEdit').show();
    $('.menuName').show();
    $('[name="branch_id"]').attr('disabled', 'disabled');
    url = '{{ route('menu.branch.update') }}';
    method = 'PATCH';
    modalLabel.html('Update Menu At Branch');
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
        url: '{{ route('menu.branch.get') }}',
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        data: {
            id: id
        },
        success: function(resp) {
            var data = resp.data;
            marketPrice = data.menu.price;

            $('[name="id"]').val(data.id);
            $('[name="branch_id"]').val(data.branch.id);
            $('[name="name"]').val(data.menu.name);
            if(data.useMasterPrice == 1) {
                $('[name="useMasterPrice"]').prop('checked', 'checked');
                $('[name="price"]').val(data.menu.price);
            } else {
                $('[name="useMasterPrice"]').prop('checked', '');
                $('[name="price"]').val(data.price);
            }
            $('[name="useMasterPrice"]').trigger('change');

            var price = 0;
            if(data.menu.price >= 1) {
                var formula = (data.menu.price * data.branch.percentagePrice) / 100;
                price = data.menu.price + formula;
            }

            branchPrice = price;

            if(price == data.price) {
                $('[name="usePercentagePrice"]').prop('checked', 'checked');
            } else {
                $('[name="usePercentagePrice"]').prop('checked', '');
            }
            $('[name="usePercentagePrice"]').trigger('change');

            if(data.branch.percentagePrice != 0 && data.branch.percentagePrice != null) {
                $('.percentageLabel').html('<span class="badge bg-green">'+data.branch.percentagePrice+'%</span>');
            }

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
        title: "Delete this menu from branch?",
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
        url: '{{ route('menu.branch.delete') }}',
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

function setAvailability(isAvailable, id) {
    $.ajax({
        url: '{{ route('menu.branch.availability') }}',
        method: 'PATCH',
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        data: {
            id: id,
            availability: isAvailable
        },
        success: function(resp) {
            showNotification('bg-green', resp.message, 'top', 'right', 'animated flipInX', 'animated flipOutX');
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
    $('.menus').chosen({width: "100%"});
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

    $('[name="useMasterPrice"]').on('change', function() {
        if($(this).is(':checked')) {
            $('[name="price"]').attr('readonly', 'readonly');
            $('[name="usePercentagePrice"]').prop('checked', '');
            $('[name="usePercentagePrice"]').prop('disabled', true);
            $('[name="price"]').val(marketPrice);
        } else {
            $('[name="price"]').removeAttr('readonly');
            $('[name="usePercentagePrice"]').prop('disabled', false);
        }
    });

    $('[name="usePercentagePrice"]').on('change', function() {
        if($(this).is(':checked')) {
            $('[name="useMasterPrice"]').prop('checked', '');
            $('[name="useMasterPrice"]').prop('disabled', true);
            $('[name="usePercentagePrice"]').prop('checked', true);
            $('[name="price"]').val(branchPrice);
        } else {
            $('[name="useMasterPrice"]').prop('disabled', false);
        }
    });

    $('[name="branch_id"]').on('change', function() {
        var id = $(this).val();
        if(id != '') {
            $('.menus').prop('disabled', false).trigger('chosen:updated');
            $('[name="usePercentagePrice"]').removeAttr('disabled');
            $.ajax({
                url: '{{ route('menu.branch.get') }}',
                method: 'GET',
                data: {
                    'branch_id': id
                },
                beforeSend: loading(),
                success: function(resp) {
                    var data = resp.data;
                    var values = [];
                    _.map(data.branch_menus, function(val, key) {
                        values.push(val.menu_id);
                    });
                    $('.menus').val(values);
                    if(data.branch.percentagePrice != 0 && data.branch.percentagePrice != null) {
                        $('[name="usePercentagePrice"]').prop('checked', 'checked');
                        $('.percentageLabel').html('<span class="badge bg-green">'+data.branch.percentagePrice+'%</span>');
                    }

                    $(".menus").trigger("chosen:updated");
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
            });
        } else {
            form.get(0).reset();
            $('[name="usePercentagePrice"]').attr('disabled', 'disabled');
            $('[name="usePercentagePrice').removeAttr('checked');
            $('.percentageLabel').html('');
            $('.menus').prop('disabled', true).trigger('chosen:updated');
        }
    });

    var styles = {
        price: function(data, row, field) {
            let text = '<span>No Price</div>';
            if(field.useMasterPrice == 1) {
                text = 'Rp. <span class="priceDisplay">'+field.menu.price+'</span>';
            } else {
                if(data != null && data != 0) {
                    text = 'Rp. <span class="priceDisplay">'+data+'</span>';
                }
            }
            return text;
        },
        useMasterPrice: function(data) {
            let text = '<span class="badge bg-red"><i class="material-icons">clear</span>';
            if(data == true) {
                text = '<span class="badge bg-green"><i class="material-icons">done</i></span>';
            }
            return text;
        },
        availability: function(data, row, field) {
            let value = '';
            if(data == 1) {
                value = 'checked';
            }
            return '<div class="switch">'
                +'<label>Unavailable<input type="checkbox" class="availability" data-id="'+field.id+'" name="availability" '+value+'><span class="lever"></span>Available</label>'
                +'</div>';
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
            { data: 'branch.branchName' },
            { data: 'menu.name' },
            { data: 'price', render: styles.price },
            { data: 'useMasterPrice', sClass: 'text-center', render: styles.useMasterPrice },
            { data: 'availability', sClass: 'text-center', render: styles.availability },
            { data: 'id', sClass: 'text-center', render: styles.action }
        ],
        drawCallback: function() {
            $('.availability').on('change', function() {
                if($(this).is(':checked')) {
                    setAvailability(1, $(this).data('id'))
                } else {
                    setAvailability(0, $(this).data('id'))
                }
            });
        }
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
