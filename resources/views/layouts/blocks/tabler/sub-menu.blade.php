

    <div class="col-md-3">
        <!-- <h3 class="page-title mb-5">Options</h3> -->
        <div>
          @component('layouts.blocks.tabler.sub-menu-action')
            @slot('submenu_action')
              {!! $submenuAction !!}
            @endslot
          @endcomponent
            <div style="margin-top: 10px !important;" class="list-group list-group-transparent mb-0" id="sub-menu-menu">

            @if (count(config($submenuConfig))>0)
{{--               @php  dd(config($submenuConfig)); @endphp--}}
                @foreach(config($submenuConfig) as $key => $value)
                    @if ( empty($value['visibility']) || ( isset($value['visibility']) && $value['visibility']==='show' ) )
                        <a href="{{ safe_href_route($value['route']) ? route($value['route']) : 'javascript:void(0)' }}"
                           class="list-group-item list-group-item-action d-flex align-items-center"
                           v-bind:class="{'active': selectedSubMenu === '{{ $key }}'}">
                            <span class="icon mr-3">
                                <i class="{{ isset($value['icon']) ? $value['icon'] : 'fe fe-file' }}"></i>
                            </span>
                            {!! $value['title'] !!}
                        </a>
                    @endif
                @endforeach
            @endif
                <!-- <a href="#" class="list-group-item list-group-item-action d-flex align-items-center active">
                  <span class="icon mr-3"><i class="fe fe-inbox"></i></span>Inbox <span class="ml-auto badge badge-primary">14</span>
              </a>
              <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                  <span class="icon mr-3"><i class="fe fe-send"></i></span>Sent Mail
              </a>
              <a href="#" class="list-group-item list-group-item-action d-flex align-items-center">
                  <span class="icon mr-3"><i class="fe fe-alert-circle"></i></span>Important <span class="ml-auto badge badge-secondary">3</span>
              </a> -->
          </div>
        </div>
    </div>