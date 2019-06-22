{{-- @extends('managebranch::layouts.master') --}}

@extends('layouts.app')

@section('title', 'Manage Branch')

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
                            <a href="javascript:void(0);" onclick="newBranch()" role="button">
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
                                    <th>Branch Name</th>
                                    <th>Branch Location</th>
                                    <th width="40px">Percentage Price</th>
                                    <th width="20px">Is Main Branch?</th>
                                    @if(checkModule('Menu'))
                                    <th width="50px">Total Menu Registered</th>
                                    @endif
                                    <th width="30px">Action</th>
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

<div class="modal fade" id="branchModal" tabindex="-1" role="dialog">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h4 class="modal-title" id="branchModalLabel"></h4>
            </div>
            <div class="modal-body">
                <form id="frmBranch">
                    @csrf
                    <input type="hidden" name="id">
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" class="form-control" name="branchName" placeholder="Branch Name, ex. Singaparna">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="form-line">
                            <input type="text" class="form-control" name="location" placeholder="Branch Location, ex. Jln.Perikanan Darat No 102 (Optional)">
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="input-group">
                            <div class="form-line">
                                <input type="number" min="0" max="100" step="0.1" name="percentagePrice" class="form-control number" placeholder="Multiplied by menu prices, leave for default price menu (Optional)">
                            </div>
                            <span class="input-group-addon">%</span>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="manager">Select Manager (Optional)</label>
                        <select class="form-control show-tick" name="manager">
                            @forelse($staffs as $staff)
                            <option value="{{ $staff->id }}">{{ $staff->name }}</option>
                            @empty
                            <option disabled selected>Doesn't have any staff</option>
                            @endforelse
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Is Main Branch?</label>
                        <div class="switch">
                            <label>False<input type="checkbox" name="isMainBranch"><span class="lever"></span>TRUE</label>
                        </div>
                    </div>
                    <div class="form-group branch-status">
                        <label for="status">Branch Status</label>
                        <select class="form-control show-tick" name="status">
                            <option value="1">Active</option>
                            <option value="2">Not Active</option>
                        </select>
                    </div>
                    <div class="form-group branch-annotation">
                        <label for="annotation">Annotation</label>
                        <textarea class="form-control" name="annotation">
                        </textarea>
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
var modal = $('#branchModal');
var modalLabel = $('#branchModalLabel');
var form = $('#frmBranch');
var url, method;
var urlDataTable = '{{ route('branch.dataTable') }}';

function newBranch() {
    form.get(0).reset();
    url = '{{ route('branch.create') }}';
    method = 'POST';
    modalLabel.html('Add new Branch');
    $('.branch-status').hide();
    $('.branch-annotation').hide();
    modal.modal('show');
}

function updateBranch(id) {
    form.get(0).reset();
    url = '{{ route('branch.update') }}';
    method = 'PATCH';
    modalLabel.html('Update Branch');
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
        url: '{{ route('branch.get') }}',
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
                if(key == 'isMainBranch') {
                    if(val == 1) {
                        $('[name="'+key+'"]').attr('checked', 'checked');
                    } else {
                        $('[name="'+key+'"]').removeAttr('checked');
                    }
                }
                $('[name="'+key+'"]').val(val);
            });
            $('.branch-status').show();
            $('.branch-annotation').show();
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

function loading() {
    $('.btn-save').attr('disabled', 'disabled');
}

function unloading() {
    $('.btn-save').removeAttr('disabled');
}

$(function() {
    var styles = {
        location: function(data) {
            let text = '-';
            if(data != null) {
                text = data.substr(0, 150);
            }
            return text;
        },
        percentage: function(data) {
            let text = '<div class="badge bg-grey">Disabled</div>';
            if(data != null && data != 0) {
                text = '<div class="badge bg-green">'+data+'%</div>';
            }
            return text;
        },
        mainBranch: function(data) {
            let text = '';
            if(data == 1) {
                text = '<div class="badge bg-lime">Main Branch</div>';
            }
            return text;
        },
        @if(checkModule('Menu'))
        totalMenu: function(data) {
            return '<span class="badge bg-teal">'+data+'</span>';
        },
        @endif
        action: function(data) {
            let button = '';
            button += '<a href="#" class="btn bg-cyan btn-xs waves-effect" onclick="updateBranch('+data+')"><i class="material-icons">mode_edit</i></a>';
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
            { data: 'branchName' },
            { data: 'location', render: styles.location },
            { data: 'percentagePrice', sClass: 'text-center', render: styles.percentage },
            { data: 'isMainBranch', sClass: 'text-center', render: styles.mainBranch },
            @if(checkModule('Menu'))
            { data: 'total_menu', sClass: 'text-center', render: styles.totalMenu },
            @endif
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
