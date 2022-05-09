@extends('vpanel.layouts.app')
@section('body_class') class="js" @endsection
@section('body_content_main_header')
    <header>
        <div class="title-wrap pull-left">
            <div class="wrap">
                <div class="sub-title">
                    <a href="{{ route('vpanel.businesses') }}"><i class="ion-ios-arrow-back"></i> Back to Businesses</a>
                </div>
                <div class="title">{{ $header['title'] ?? 'Business Information' }}</div>
            </div>
            <div class="reset"></div>
        </div>
        <div class="opts-wrap pull-right">
            <a href="#" onclick="profileVue.editItem()" class="btn btn-default btn-default-type">Edit</a>
            <div class="hgap-1x"></div>
            <a class="btn btn-danger btn-default-type --btn-del-vehicle" onclick="profileVue.deleteItem()">Delete</a>
        </div>
    </header>
@endsection
@section('body_content_main_container')
    <div class="scrollable" id="profile">
        <div class="details_wrap-type1">
            <div class="set" style="width: 50%;">
                <h6>Business details</h6>
                <div class="vgap-1x"></div>
                <table>
                    <thead>
                    <tr>
                        <th>Reg. No</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th>Website</th>
                        <th>&nbsp;</th>
                    </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>@{{ business.registration !== null ? business.registration : 'N/A' }}</td>
                            <td>@{{ business.phone !== null ? business.phone : 'N/A' }}</td>
                            <td>@{{ business.email !== null ? business.email : 'N/A' }}</td>
                            <td>@{{ business.website !== null ? business.website : 'N/A' }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="set" style="width: 40%;">
                <h6>Plan Information</h6>
                <div class="vgap-1x"></div>
                <table>
                    <thead>
                    <tr>
                        <th>Plan</th>
                        <th>Type</th>
                        <th>Expiry</th>
                    </tr>
                    </thead>
                    <tbody>
                    <tr>
                        <td>@{{ business.plan.data.name.title_case() }}</td>
                        <td>@{{ business.plan_type.title_case() }}</td>
                        <td>@{{ moment(business.access_expires_at).format('DD MMM, YYYY HH:mm') }}</td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="vgap-5x"></div>

        <div class="table-type1" v-if="users.length > 0 || !is_fetched">
            <table class="bootstrap-table"
                    data-pagination="true"
                    data-search="true"
                    data-side-pagination="server"
                    data-show-refresh="true"
                    data-id-field="id"
                    data-unique-field="id"
                    data-row-attributes="vpanel.formatters.users"
                    data-response-handler="processUsers"
                    data-url="{{ route('xhr.vpanel.users') }}?company_id={{ $businessProfile->id }}"
                    id="tbl-users-listing"
                    v-on:click="clicked($event)">
                <thead>
                <tr>
                    <th data-field="firstname">First Name</th>
                    <th data-field="lastname">Last Name</th>
                    <th data-field="phone">Phone</th>
                    <th data-field="email">Email</th>
                    <th data-field="is_vendor">Is Vendor</th>
                    <th data-field="status">In Trash</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>

        <div v-if="users.length === 0 && is_fetched">
            @component('vpanel.layouts.components.empty-state-box')
                @slot('header')
                    No Users
                @endslot
                <p>
                    There are no user accounts under this business.
                </p>
                @slot('buttons')

                @endslot
            @endcomponent
        </div>

        <div class="extras hidden">
            <div class="edit-form-spec">
                <header>
                    <span class="text" id="modal-title">Edit Information</span>
                </header>
                <form action="" method="post">
                    {{ csrf_field() }}
                    <div class="spec-content">
                        <div class="content">
                            <div class="current">
                                <div class="form">
                                    <div class="row">
                                        <div class="col-sm-12 col-md-4">
                                            <div class="form-group">
                                                <label>Business Name</label>
                                                <input type="text" class="form-control" placeholder="The business' name" required
                                                       name="name" id="name" maxlength="80" v-model="business.name">
                                                @if ($errors->has('business'))
                                                    <span class="text-danger">
                                                        <strong>{{ $errors->first('business') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-4">
                                            <div class="form-group">
                                                <label>Registration No.</label>
                                                <input type="text" class="form-control" placeholder="RC No."
                                                       name="registration" id="registration" maxlength="30" v-model="business.registration">
                                                @if ($errors->has('registration'))
                                                    <span class="text-danger">
                                                        <strong>{{ $errors->first('registration') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-4">
                                            <div class="form-group">
                                                <label>Contact Email</label>
                                                <input type="email" class="form-control" placeholder="Contact email address"
                                                       name="email" id="email" maxlength="80">
                                                @if ($errors->has('email'))
                                                    <span class="text-danger">
                                                        <strong>{{ $errors->first('email') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-sm-12 col-md-4">
                                            <div class="form-group">
                                                <label>Contact Phone</label>
                                                <input type="text" class="form-control" placeholder="Contact phone number"
                                                       name="phone" id="phone" maxlength="30" v-model="business.phone">
                                                @if ($errors->has('phone'))
                                                    <span class="text-danger">
                                                        <strong>{{ $errors->first('phone') }}</strong>
                                                    </span>
                                                @endif
                                            </div>
                                        </div>
                                        <div class="col-sm-12 col-md-4">
                                            <div class="form-group">
                                                <label>Business Website</label>
                                                <input type="text" class="form-control" placeholder="Business website"
                                                       name="website" id="website" maxlength="30" v-model="business.website">
                                                @if ($errors->has('website'))
                                                    <span class="text-danger">
                                                        <strong>{{ $errors->first('website') }}</strong>
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
                        <button type="submit" class="btn btn-default-type btn-success --place-booking">Save Changes</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
@endsection
@section('body_js')
    <script>
        let profileVue = new Vue({
            el: '#profile',
            data: {
                business: {!! json_encode($businessProfile ?: []) !!},
                plan: {},
                users: [],
                loggedInUser: {!! json_encode($dorcasUser) !!},
                is_fetched: false,
            },
            mounted: function () {
                if (typeof this.business.plan !== 'undefined') {
                    this.plan = this.business.plan.data;
                }
            },
            methods: {
                moment: function (dateStr) {
                    return moment(dateStr);
                },
                editItem: function () {
                    let context = this;
                    swal({
                        animation: false,
                        html: $("<div />").append($(".edit-form-spec").clone()).html(),
                        customClass: 'service-spec-wrap swal-type11',
                        showCloseButton: true,
                        showCancelButton: false,
                        showConfirmButton: false,
                        onOpen: () => {
                            let $el = $(swal.getContent());
                            $el.find('#name').val(context.business.name);
                            $el.find('#registration').val(context.business.registration);
                            $el.find('#email').val(context.business.email);
                            $el.find('#phone').val(context.business.phone);
                            $el.find('#website').val(context.business.website);
                        }
                    }).then((res) => {
                        console.log(res)
                    });
                },
                deleteItem: function () {
                    let context = this;
                    swal({
                        animation: true,
                        title: "Are you sure?",
                        text: "You are about to delete this business: " + context.business.name,
                        customClass: 'swal2-btns-left',
                        showCancelButton: true,
                        confirmButtonClass: 'swal2-btn swal2-btn-confirm',
                        confirmButtonText: 'Yes, delete it!',
                        cancelButtonClass: 'swal2-btn swal2-btn-cancel',
                        cancelButtonText: 'Cancel',
                        closeOnConfirm: false,
                    }).then((b) => {
                        if (typeof b.dismiss !== 'undefined' && b.dismiss === 'cancel') {
                            return '';
                        }
                        axios.delete("/xhr/vpanel/companies/" + context.business.id)
                            .then(function (response) {
                                console.log(response);
                                window.location = '{{ route('vpanel.businesses') }}';
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

        function processUsers(response) {
            profileVue.users = response.rows;
            profileVue.is_fetched = true;
            return response;
        }
    </script>
@endsection
