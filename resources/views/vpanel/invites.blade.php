@extends('vpanel.layouts.app')
@section('body_class') class="js" @endsection
@section('body_content_main_header')
    <header id="header-bar">
        <div class="title-wrap pull-left">
            <div class="wrap">
                <div class="title">Invites</div>
            </div>
            <div class="reset"></div>
        </div>
        <div class="opts-wrap pull-right">
            <a href="#" v-on:click.prevent="openInviteModal" class="btn btn-default btn-default-type">Invite a Business</a>
        </div>
    </header>
@endsection
@section('body_content_main_container')
    <div class="scrollable" id="listing">
        <div class="table-type1" v-if="invites.length > 0 || !is_fetched">
            <table
                   class="bootstrap-table"
                   data-pagination="true"
                   data-search="true"
                   data-side-pagination="server"
                   data-show-refresh="true"
                   data-id-field="id"
                   data-unique-id="id"
                   data-row-attributes="vpanel.formatters.invites"
                   data-response-handler="processInvites"
                   data-url="{{ route('xhr.vpanel.invites') }}?filters={{ $filters ?? '' }}"
                   id="tbl-invites-listing"
                   v-on:click="clicked($event)">
                <thead>
                <tr>
                    <th data-field="firstname">First Name</th>
                    <th data-field="lastname">Last Name</th>
                    <th data-field="email">Email</th>
                    <th data-field="action">Action</th>
                    <th data-field="invited_by">Invited By</th>
                    <th data-field="status">Status</th>
                    <th data-field="sent_at">Sent</th>
                    <th data-field="menu">Actions</th>
                </tr>
                </thead>
                <tbody>

                </tbody>
            </table>
        </div>
        <div v-if="invites.length === 0 && is_fetched">
            @component('vpanel.layouts.components.empty-state-box')
                @slot('header')
                    No Invites Sent
                @endslot
                <p>
                    There are no invites to be displayed. You can proceed to invite them one at a time using the button below.
                </p>
                @slot('buttons')
                    <a href="#" v-on:click.prevent="openInviteModal" class="btn btn-default btn-default-type">Invite a Business</a>
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
                invites: [],
                invite: {},
                is_fetched: false,
            },
            methods: {
                openInviteModal: function () {
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
                            this.deleteInvite(index);
                        }
                    } else {
                        return true;
                    }
                },
                deleteInvite: function (index) {
                    let context = this;
                    this.invite = typeof this.invites[index] !== 'undefined' ? this.invites[index] : {};
                    if (typeof this.invite.id === 'undefined') {
                        return;
                    }
                    swal({
                        animation: true,
                        title: "Are you sure?",
                        text: "You are about to rescind the invite to " + context.invite.email + "?",
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
                        axios.delete("/xhr/vpanel/invites/" + context.invite.id)
                            .then(function (response) {
                                console.log(response);
                                $('#tbl-invites-listing').bootstrapTable('removeByUniqueId', context.invite.id);
                                context.invites.splice(index, 1);
                                context.invite = {};
                                return vpanel_simple_swal("Deleted!", "The invite was successfully deleted.", "success");
                            })
                            .catch(function (error) {
                                let message = '';
                                console.log(error);
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
                                context.invite = {};
                                return vpanel_simple_swal("Delete Failed", message, "warning");
                            });
                    });
                }
            }
        });

        let hvm = new Vue({
            el: '#header-bar',
            data: {
                invites: vm.invites,
            },
            methods: {
                openInviteModal: function () {
                    vm.openInviteModal();
                }
            }
        });

        function processInvites(response) {
            vm.is_fetched = true;
            vm.invites = response.rows;
            hvm.invites = vm.invites;
            return response;
        }
    </script>
@endsection
