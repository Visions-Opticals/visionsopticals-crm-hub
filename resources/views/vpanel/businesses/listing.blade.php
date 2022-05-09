@extends('vpanel.layouts.app')
@section('body_class') class="js" @endsection
@section('body_content_main_header')
    <header id="header-bar">
        <div class="title-wrap pull-left">
            <div class="wrap">
                <div class="title">{{ $header['title'] }}</div>
            </div>
            <div class="reset"></div>
        </div>
        <div class="opts-wrap pull-right">
            <a href="#" v-if="companies.length > 0" v-on:click.prevent="openInviteModal"
               class="btn btn-default btn-default-type">Invite a Business</a>
        </div>
    </header>
@endsection
@section('body_content_main_container')
    <div class="scrollable" id="listing">
        <div class="table-type1" v-if="companies.length > 0 || !is_fetched">
            <table
                   class="bootstrap-table"
                   data-pagination="true"
                   data-search="true"
                   data-side-pagination="server"
                   data-show-refresh="true"
                   data-id-field="id"
                   data-unique-id="id"
                   data-row-attributes="vpanel.formatters.companies"
                   data-response-handler="processCompanies"
                   data-url="{{ route('xhr.vpanel.companies') }}?filters={{ $filters ?? '' }}"
                   id="tbl-companies-listing"
                   v-on:click="clicked($event)">
                <thead>
                <tr>
                    <th data-field="name">Name</th>
                    <th data-field="registration">Reg. No.</th>
                    <th data-field="phone">Phone</th>
                    <th data-field="email">Email</th>
                    <th data-field="plan.data.name">Plan</th>
                    <th data-field="status">In Trash</th>
                    <th data-field="menu">More Actions</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
        <div v-if="companies.length === 0 && is_fetched">
            @component('vpanel.layouts.components.empty-state-box')
                @slot('header')
                    No Companies
                @endslot
                <p>
                    There are no companies to be displayed. You can proceed to invite them one at a time using the button below.
                </p>
                @slot('buttons')
                    <a href="#" v-on:click.prevent="openInviteModal" class="btn btn-default btn-default-type">Invite a @{{ mode === 'partner' ? 'Partner' : 'Company' }}</a>
                @endslot
            @endcomponent
        </div>

        <div class="extras hidden">
            <div class="invite-form-spec">
                <header>
                    <span class="text" id="modal-title">Invite a Business</span>
                </header>
                <form action="" method="post">
                    {{ csrf_field() }}
                    <div class="spec-content">
                        <div class="content">
                            <div class="current">
                                <div class="form">
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Business Name</label>
                                                <input type="text" class="form-control" placeholder="The business' name" required
                                                       name="business" id="invite_business" maxlength="80">
                                                @if ($errors->has('business'))
                                                    <span class="text-danger">
                                                        <strong>{{ $errors->first('business') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Email</label>
                                                <input type="email" class="form-control" placeholder="Email address of the contact" required
                                                       name="email" id="invite_email" maxlength="80">
                                                @if ($errors->has('email'))
                                                    <span class="text-danger">
                                                        <strong>{{ $errors->first('email') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>First Name</label>
                                                <input type="text" class="form-control" placeholder="First name of the user" required
                                                       name="firstname" id="invite_firstname" maxlength="30">
                                                @if ($errors->has('firstname'))
                                                    <span class="text-danger">
                                                        <strong>{{ $errors->first('firstname') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-sm-6">
                                            <div class="form-group">
                                                <label>Last Name</label>
                                                <input type="text" class="form-control" placeholder="Last name of the contact" required
                                                       name="lastname" id="invite_lastname" maxlength="30">
                                                @if ($errors->has('lastname'))
                                                    <span class="text-danger">
                                                        <strong>{{ $errors->first('lastname') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="spec-actions text-right">
                        <button type="submit" class="btn btn-default-type btn-success --place-booking">Send Invite</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('body_js')
    <script>
        let vm = new Vue({
            el: '#listing',
            data: {
                companies: [],
                company: {},
                is_fetched: false,
                mode: "{{ $listingMode ?? 'company' }}"
            },
            methods: {
                openInviteModal: function (mode) {
                    let context = this;
                    swal({
                        animation: false,
                        html: $("<div />").append($(".invite-form-spec").clone()).html(),
                        customClass: 'service-spec-wrap swal-type11',
                        showCloseButton: true,
                        showCancelButton: false,
                        showConfirmButton: false,
                        onOpen: () => {

                        }
                    }).then((res) => {
                            console.log(res)
                    });
                },
                clicked: function ($event) {
                    let target = $event.target;
                    if (target.hasAttribute('data-action')) {
                        target = target.parentNode.hasAttribute('data-action') ? target.parentNode : target;
                    }
                    let action = target.getAttribute('data-action').toLowerCase();
                    if (action === 'view') {
                        return true;
                    } else if (action === 'delete') {
                        let index = parseInt(target.getAttribute('data-index'), 10);
                        if (!isNaN(index)) {
                            this.deleteCompany(index);
                        }
                    } else {
                        return true;
                    }
                },
                deleteCompany: function (index) {
                    let context = this;
                    this.company = typeof this.companies[index] !== 'undefined' ? this.companies[index] : {};
                    if (typeof this.company.id === 'undefined') {
                        return;
                    }
                    swal({
                        animation: true,
                        title: "Are you sure?",
                        text: "You are about to " + (context.company.is_trashed ? 'permanently ' : '') + "delete this " + context.mode + ": " + context.company.name,
                        customClass: 'swal2-btns-left',
                        showCancelButton: true,
                        confirmButtonClass: 'swal2-btn swal2-btn-confirm',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonClass: 'swal2-btn swal2-btn-cancel',
                        cancelButtonText: 'Cancel',
                        closeOnConfirm: false,
                    }).then((b) => {
                        console.log(b);
                    if (typeof b.dismiss !== 'undefined' && b.dismiss === 'cancel') {
                        return '';
                    }
                    let params = {};
                    if (context.company.is_trashed) {
                        params.purge = true;
                    }
                    axios.delete("/xhr/vpanel/companies/" + context.company.id, {params: params})
                        .then(function (response) {
                            console.log(response);
                            $('#tbl-companies-listing').bootstrapTable('removeByUniqueId', context.company.id);
                            context.company = {};
                            return vpanel_simple_swal("Deleted!", "The company was successfully deleted.", "success");
                        })
                        .catch(function (error) {
                            let message = '';
                            if (error.response) {
                                // The request was made and the server responded with a status code
                                // that falls out of the range of 2xx
                                let e = error.response.data.errors[0];
                                message = e.title;
                            } else if (error.request) {
                                // The request was made but no response was received
                                // `error.request` is an instance of XMLHttpRequest in the browser and an instance of
                                // http.ClientRequest in node.js
                                message = 'The request was made but no response was received';
                            } else {
                                // Something happened in setting up the request that triggered an Error
                                message = error.message;
                            }
                            return vpanel_simple_swal("Delete Failed", message, "warning");
                        });
                });
                }
            }
        });

        let hvm = new Vue({
            el: '#header-bar',
            data: {
                companies: vm.companies,
                mode: vm.mode
            },
            methods: {
                openInviteModal: function () {
                    vm.openInviteModal();
                }
            }
        });

        function processCompanies(response) {
            vm.companies = response.rows;
            hvm.companies = vm.companies;
            vm.is_fetched = true;
            return response;
        }
    </script>
@endsection
