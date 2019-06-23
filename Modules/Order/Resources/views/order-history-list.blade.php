@extends('layouts.app')

@section('title', 'Order History')

@push('styles')
<style>
.card .header .header-dropdown i {
    font-size: 32px !important;
}
.quantityPrice, .subTotalPrice, .price, .quantity {
    text-align: right;
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
                </div>
                <div class="body">
                    <div class="table-responsive">
                        <table class="table table-bordered table-striped table-hover dataTable" id="orderList">
                            <thead>
                                <tr>
                                    <th width="20px">No</th>
                                    <th>Order Code</th>
                                    <th>Branch</th>
                                    <th>Request Order At</th>
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

<div class="modal fade" id="orderModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="orderModalLabel"></h4>
            </div>
            <div class="modal-body">
                <form id="frmOrder">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="order_code">Order Code</label>
                            <div class="form-line">
                                <input type="text" class="form-control" name="order_code" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="order_code">Customer Name</label>
                            <div class="form-line">
                                <input type="text" class="form-control" name="customerName" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-6">
                        <div class="form-group">
                            <label for="order_code">Branch Name</label>
                            <div class="form-line">
                                <input type="text" class="form-control" name="branchName" readonly>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="order_code">Request Order Date</label>
                            <div class="form-line">
                                <input type="text" class="form-control" name="created_at" readonly>
                            </div>
                        </div>
                    </div>
                    <div class="col-lg-12">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="menuList">
                                <thead>
                                    <tr>
                                        <th>Menu Name</th>
                                        <th>Price</th>
                                        <th>Quantity</th>
                                        <th>Total Price</th>
                                    </tr>
                                </thead>
                                <tbody id="listMenu"></tbody>
                                <tfoot>
                                    <tr>
                                        <th colspan="3" style="text-align:right">Sub Total</th>
                                        <th class="subTotalPrice quantityPrice">0</th>
                                    </tr>
                                </tfoot>
                            </table>
                        </div>
                    </div>
                </form>
                <div class="logError alert alert-danger" style="display:none;">There's has been some changed in price menu, so the actual total price is different from sub total.</div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-link waves-effect" data-dismiss="modal">{{ __('CLOSE') }}</button>
            </div>
        </div>
    </div>
</div>
@stop

@push('scripts')
<script src="{{ asset('vendor/jquery-mask/jquery.mask.min.js') }}"></script>
<script>
var table;
var modal = $('#orderModal');
var modalLabel = $('#orderModalLabel');
var form = $('#frmOrder');
var url, method;
var urlDataTable = '{{ route('order.history.dataTable') }}';

function viewOrder(id) {
    form.get(0).reset();
    modalLabel.html('View History Order');
    $(".quantityPrice, .price").mask('000.000.000', {reverse: true});
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
    $('#listMenu').empty();
    $.ajax({
        url: '{{ route('order.history.get') }}',
        method: 'GET',
        headers: {
            'X-CSRF-TOKEN': "{{ csrf_token() }}"
        },
        data: {
            id: id
        },
        success: function(resp) {
            var data = resp.data;
            var total = 0;
            $('[name="order_code"]').val(data.order_code);
            $('[name="branchName"]').val(data.branch.branchName);
            $('[name="customerName"]').val(data.customerName);
            $('[name="created_at"]').val(data.created_at);
            if(data.menus.length >= 1) {
                _.map(data.menus, function(v, i) {
                    var quantityMenuPrice = v.branch_menu.price * v.quantity;
                    total += quantityMenuPrice;
                    $('#listMenu').append(`<tr><td>${v.branch_menu.menu.name}</td><td class="price">${v.branch_menu.price}</td><td class="quantity">${v.quantity}</td><td class="quantityPrice">${quantityMenuPrice}</td></tr>`);
                });
            }

            if(total != data.total) {
                $('.logError').show();
            } else {
                $('.logError').hide();
            }

            $('.subTotalPrice').html(data.total);

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

$(function() {
    var styles = {
        action: function(data) {
            let button = '';
            button += '<a href="#" class="btn bg-cyan btn-xs waves-effect" onclick="viewOrder('+data+')"><i class="material-icons">info</i></a>';
            return button;
        }
    }

    table = $('#orderList').DataTable({
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
            { data: 'order_code' },
            { data: 'branch.branchName' },
            { data: 'created_at', sClass: 'text-center', render: styles.menuCount },
            { data: 'id', sClass: 'text-center', render: styles.action }
        ]
    });
})
</script>
@endpush
