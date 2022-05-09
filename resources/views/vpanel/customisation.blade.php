@extends('vpanel.layouts.app')
@section('body_content_main_header')
    <header>
        <div class="title-wrap pull-left">
            <div class="wrap">
                <div class="title">{{ $header['title'] }}</div>
            </div>
            <div class="reset"></div>
        </div>
        <div class="opts-wrap pull-right">

        </div>
    </header>
@endsection
@section('body_content_main_container')
    <div class="scrollable" id="settings-box">
        <div class="v-form_wrap">
            <div class="row">
                <div class="col-sm-12">
                    <div class="title">Adjust your Settings</div>
                    <div class="vgap-2x"></div>
                    <form method="post" action="">
                        {{ csrf_field() }}
                        <div class="form">
                            <div class="progress" v-if="loading">
                                <div class="progress-bar progress-bar-animated progress-bar-striped" style="width: 100%"></div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 col-md-4">
                                    <div class="form-group">
                                        <label>Display Name</label>
                                        <input type="text" class="form-control" id="name" name="name" required
                                               placeholder="Name to show" v-model="partner.name" maxlength="80">
                                        @if ($errors->has('name'))
                                            <span class="text-danger">
                                                <strong>{{ $errors->first('name') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-4">
                                    <div class="form-group">
                                        <label>Welcome Video (YouTube Only)</label>
                                        <input type="text" class="form-control" id="video_url" name="video_url"
                                               placeholder="Welcome video to show users"
                                               v-model="video_url" maxlength="80">
                                        @if ($errors->has('video_url'))
                                            <span class="text-danger">
                                                <strong>{{ $errors->first('video_url') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-4">
                                    <div class="form-group">
                                        <label>Product Name</label>
                                        <input type="text" class="form-control" id="product_name" name="product_name"
                                               placeholder="What do you want to name this product?"
                                               v-model="hubConfig.product_name" maxlength="80">
                                        @if ($errors->has('product_name'))
                                            <span class="text-danger">
                                                <strong>{{ $errors->first('product_name') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-sm-12 col-md-4">
                                    <div class="form-group">
                                        <label>Support Email</label>
                                        <input type="text" class="form-control" id="support_email" name="support_email"
                                               placeholder="Support Email Address" v-model="partner.extra_data.support_email" maxlength="80">
                                        @if ($errors->has('support_email'))
                                            <span class="text-danger">
                                                <strong>{{ $errors->first('support_email') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <hr style="border-top: 2px solid #0e2949;" />
                            </div>
                            <div class="row">
                                <div class="col-sm-12 col-md-4">
                                    <div class="form-group">
                                        <label>Invite Email Subject</label>
                                        <input type="text" class="form-control" id="email_subject" name="email_subject"
                                               placeholder="Invite Email Subject" v-model="inviteConfig.email_subject" maxlength="100">
                                        @if ($errors->has('email_subject'))
                                            <span class="text-danger">
                                                <strong>{{ $errors->first('email_subject') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-4">
                                    <div class="form-group">
                                        <label>Invite Email Body</label>
                                        <textarea type="text" class="form-control" id="email_body" name="email_body" v-model="inviteConfigBody" rows="5">
                                        </textarea>
                                        @if ($errors->has('email_body'))
                                            <span class="text-danger">
                                                <strong>{{ $errors->first('email_body') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-4">
                                    <div class="form-group">
                                        <label>Invite Email Footer</label>
                                        <textarea type="text" class="form-control" id="email_footer" name="email_footer" v-model="inviteConfigFooter" rows="5">
                                        </textarea>
                                        @if ($errors->has('email_footer'))
                                            <span class="text-danger">
                                                <strong>{{ $errors->first('email_footer') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>


                            <div class="row">
                                <hr style="border-top: 2px solid #0e2949;" />
                            </div>
                            <div class="row">
                                <div class="col-sm-12 col-md-4">
                                    <div class="form-group">
                                        <label>Enable Global Marketplace</label>
                                        <select class="form-control" name="marketplace_global_enable" id="marketplace_global_enable" v-model="marketplaceConfig.global_enable" required>
                                            <option value="">Select</option>
                                            <option value="1">Yes</option>
                                            <option value="0">No</option>
                                        </select>
                                        @if ($errors->has('marketplace_global_enable'))
                                            <span class="text-danger">
                                                <strong>{{ $errors->first('marketplace_global_enable') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-4">
                                    <div class="form-group">
                                        <label>Manage Product Categories</label>
                                        <select class="form-control" name="marketplace_sales_categories[]" id="marketplace_sales_categories" multiple>
                                            <option disabled="">Leave Unselected. Select / ReSelect to UPDATE</option>
                                            <option v-for="category in marketplaceConfig.sales_categories" v-bind:value="category">@{{ category }}</option>
                                        </select>
                                        <p><b><a href="#" v-on:click.prevent="addCategory">Click to ADD PRODUCT CATEGORIES</a></b> (remember to Save Customisations)</p>
                                        @if ($errors->has('marketplace_sales_categories'))
                                            <span class="text-danger">
                                                <strong>{{ $errors->first('marketplace_sales_categories') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                                <div class="col-sm-12 col-md-4">
                                    <div class="form-group">
                                        <label>Manage Service Categories</label>
                                        <select class="form-control" name="marketplace_service_categories[]" id="marketplace_service_categories" multiple>
                                            <option disabled="">Leave Unselected. Select / ReSelect to UPDATE</option>
                                            <option v-for="category in marketplaceConfig.service_categories" v-bind:value="category">@{{ category }}</option>
                                        </select>
                                        <p><b><a href="#" v-on:click.prevent="addServiceCategory">Click to ADD SERVICE CATEGORIES</a></b> (remember to Save Customisations)</p>
                                        @if ($errors->has('marketplace_service_categories'))
                                            <span class="text-danger">
                                                <strong>{{ $errors->first('marketplace_service_categories') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>


                            <div class="row" style="text-align:right">
                                <hr style="border-top: 2px solid #0e2949;" />
                                <button type="submit" class="btn btn-default btn-default-type">Save Customisations</button>
                            </div>
                            
                        </div>
                    </form>
                </div>
            </div>
            <div class="vgap-3x"></div>
            <div class="row">
                <div class="col-sm-12 col-md-4" v-if="video_url.length > 0">
                    <div class="embed-responsive embed-responsive-16by9">
                        <iframe class="embed-responsive-item" :src="'https://www.youtube.com/embed/' + partner.extra_data.welcome_video_id + '?rel=0'" allowfullscreen></iframe>
                    </div>
                </div>
            </div>

            <div class="vgap-3x"></div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="title">Brand Settings</div>
                    <div class="vgap-2x"></div>
                    <form method="post" action="" enctype="multipart/form-data">
                        {{ csrf_field() }}
                        <div class="form">
                            <div class="row">
                                <div class="col-sm-12 col-md-4">
                                    <div class="form-group">
                                        <label>Business Logo</label>
                                        <input type="file" class="form-control" id="logo" name="logo" accept="image/*">
                                        @if ($errors->has('logo'))
                                            <span class="text-danger">
                                                <strong>{{ $errors->first('logo') }}</strong>
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>
                            <button type="submit" class="btn btn-default btn-default-type">Save Settings</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
@section('body_js')
    <script>

function get_html_translation_table (table, quoteStyle) { // eslint-disable-line camelcase
  //  discuss at: http://locutus.io/php/get_html_translation_table/
  // original by: Philip Peterson
  //  revised by: Kevin van Zonneveld (http://kvz.io)
  // bugfixed by: noname
  // bugfixed by: Alex
  // bugfixed by: Marco
  // bugfixed by: madipta
  // bugfixed by: Brett Zamir (http://brett-zamir.me)
  // bugfixed by: T.Wild
  // improved by: KELAN
  // improved by: Brett Zamir (http://brett-zamir.me)
  //    input by: Frank Forte
  //    input by: Ratheous
  //      note 1: It has been decided that we're not going to add global
  //      note 1: dependencies to Locutus, meaning the constants are not
  //      note 1: real constants, but strings instead. Integers are also supported if someone
  //      note 1: chooses to create the constants themselves.
  //   example 1: get_html_translation_table('HTML_SPECIALCHARS')
  //   returns 1: {'"': '&quot;', '&': '&amp;', '<': '&lt;', '>': '&gt;'}

  var entities = {}
  var hashMap = {}
  var decimal
  var constMappingTable = {}
  var constMappingQuoteStyle = {}
  var useTable = {}
  var useQuoteStyle = {}

  // Translate arguments
  constMappingTable[0] = 'HTML_SPECIALCHARS'
  constMappingTable[1] = 'HTML_ENTITIES'
  constMappingQuoteStyle[0] = 'ENT_NOQUOTES'
  constMappingQuoteStyle[2] = 'ENT_COMPAT'
  constMappingQuoteStyle[3] = 'ENT_QUOTES'

  useTable = !isNaN(table)
    ? constMappingTable[table]
    : table
      ? table.toUpperCase()
      : 'HTML_SPECIALCHARS'

  useQuoteStyle = !isNaN(quoteStyle)
    ? constMappingQuoteStyle[quoteStyle]
    : quoteStyle
      ? quoteStyle.toUpperCase()
      : 'ENT_COMPAT'

  if (useTable !== 'HTML_SPECIALCHARS' && useTable !== 'HTML_ENTITIES') {
    throw new Error('Table: ' + useTable + ' not supported')
  }

  entities['38'] = '&amp;'
  if (useTable === 'HTML_ENTITIES') {
    entities['160'] = '&nbsp;'
    entities['161'] = '&iexcl;'
    entities['162'] = '&cent;'
    entities['163'] = '&pound;'
    entities['164'] = '&curren;'
    entities['165'] = '&yen;'
    entities['166'] = '&brvbar;'
    entities['167'] = '&sect;'
    entities['168'] = '&uml;'
    entities['169'] = '&copy;'
    entities['170'] = '&ordf;'
    entities['171'] = '&laquo;'
    entities['172'] = '&not;'
    entities['173'] = '&shy;'
    entities['174'] = '&reg;'
    entities['175'] = '&macr;'
    entities['176'] = '&deg;'
    entities['177'] = '&plusmn;'
    entities['178'] = '&sup2;'
    entities['179'] = '&sup3;'
    entities['180'] = '&acute;'
    entities['181'] = '&micro;'
    entities['182'] = '&para;'
    entities['183'] = '&middot;'
    entities['184'] = '&cedil;'
    entities['185'] = '&sup1;'
    entities['186'] = '&ordm;'
    entities['187'] = '&raquo;'
    entities['188'] = '&frac14;'
    entities['189'] = '&frac12;'
    entities['190'] = '&frac34;'
    entities['191'] = '&iquest;'
    entities['192'] = '&Agrave;'
    entities['193'] = '&Aacute;'
    entities['194'] = '&Acirc;'
    entities['195'] = '&Atilde;'
    entities['196'] = '&Auml;'
    entities['197'] = '&Aring;'
    entities['198'] = '&AElig;'
    entities['199'] = '&Ccedil;'
    entities['200'] = '&Egrave;'
    entities['201'] = '&Eacute;'
    entities['202'] = '&Ecirc;'
    entities['203'] = '&Euml;'
    entities['204'] = '&Igrave;'
    entities['205'] = '&Iacute;'
    entities['206'] = '&Icirc;'
    entities['207'] = '&Iuml;'
    entities['208'] = '&ETH;'
    entities['209'] = '&Ntilde;'
    entities['210'] = '&Ograve;'
    entities['211'] = '&Oacute;'
    entities['212'] = '&Ocirc;'
    entities['213'] = '&Otilde;'
    entities['214'] = '&Ouml;'
    entities['215'] = '&times;'
    entities['216'] = '&Oslash;'
    entities['217'] = '&Ugrave;'
    entities['218'] = '&Uacute;'
    entities['219'] = '&Ucirc;'
    entities['220'] = '&Uuml;'
    entities['221'] = '&Yacute;'
    entities['222'] = '&THORN;'
    entities['223'] = '&szlig;'
    entities['224'] = '&agrave;'
    entities['225'] = '&aacute;'
    entities['226'] = '&acirc;'
    entities['227'] = '&atilde;'
    entities['228'] = '&auml;'
    entities['229'] = '&aring;'
    entities['230'] = '&aelig;'
    entities['231'] = '&ccedil;'
    entities['232'] = '&egrave;'
    entities['233'] = '&eacute;'
    entities['234'] = '&ecirc;'
    entities['235'] = '&euml;'
    entities['236'] = '&igrave;'
    entities['237'] = '&iacute;'
    entities['238'] = '&icirc;'
    entities['239'] = '&iuml;'
    entities['240'] = '&eth;'
    entities['241'] = '&ntilde;'
    entities['242'] = '&ograve;'
    entities['243'] = '&oacute;'
    entities['244'] = '&ocirc;'
    entities['245'] = '&otilde;'
    entities['246'] = '&ouml;'
    entities['247'] = '&divide;'
    entities['248'] = '&oslash;'
    entities['249'] = '&ugrave;'
    entities['250'] = '&uacute;'
    entities['251'] = '&ucirc;'
    entities['252'] = '&uuml;'
    entities['253'] = '&yacute;'
    entities['254'] = '&thorn;'
    entities['255'] = '&yuml;'
  }

  if (useQuoteStyle !== 'ENT_NOQUOTES') {
    entities['34'] = '&quot;'
  }
  if (useQuoteStyle === 'ENT_QUOTES') {
    entities['39'] = '&#39;'
  }
  entities['60'] = '&lt;'
  entities['62'] = '&gt;'

  // ascii decimals to real symbols
  for (decimal in entities) {
    if (entities.hasOwnProperty(decimal)) {
      hashMap[String.fromCharCode(decimal)] = entities[decimal]
    }
  }

  return hashMap
}

function htmlspecialchars_decode(string, quote_style) {  
    // Convert special HTML entities back to characters    
    //   
    // version: 901.714  
    // discuss at: http://phpjs.org/functions/htmlspecialchars_decode  
    // +   original by: Mirek Slugen  
    // +   improved by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)  
    // +   bugfixed by: Mateusz "loonquawl" Zalega  
    // +      input by: ReverseSyntax  
    // +      input by: Slawomir Kaniecki  
    // +      input by: Scott Cariss  
    // +      input by: Francois  
    // +   bugfixed by: Onno Marsman  
    // +    revised by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)  
    // -    depends on: get_html_translation_table  
    // *     example 1: htmlspecialchars_decode("<p>this -&gt; &quot;</p>", 'ENT_NOQUOTES');  
    // *     returns 1: '<p>this -> &quot;</p>'  
    var histogram = {}, symbol = '', tmp_str = '', entity = '';  
    tmp_str = string.toString();  
      
    if (false === (histogram = get_html_translation_table('HTML_SPECIALCHARS', quote_style))) {  
        return false;  
    }  
  
    // &amp; must be the last character when decoding!  
    delete(histogram['&']);  
    histogram['&'] = '&amp;';  
  
    for (symbol in histogram) {  
        entity = histogram[symbol];  
        tmp_str = tmp_str.split(entity).join(symbol);  
    }  
      
    return tmp_str;  
} 

        new Vue({
            el: '#settings-box',
            data: {
                address: {},
                partner: {!! json_encode($partner) !!},
                hubConfig: {},
                inviteConfig: {},
                inviteConfigBody: '',
                inviteConfigFooter: '',
                loading: false,
                marketplaceConfig: {
                    global_enable: 1,
                    sales_categories: [],
                    service_categories: []
                }
            },
            methods: {
                addCategory: function () {
                    Swal.fire({
                        title: 'New Product Category',
                        text: "Enter the name for the category:",
                        input: "text",
                        inputAttributes: {
                            autocapitalize: 'off'
                        },
                        showCancelButton: true,
                        confirmButtonText: 'Save',
                        animation: "slide-from-top",
                        showLoaderOnConfirm: true,
                        inputPlaceholder: "e.g. Clothing",
                        inputValidator: (value) => {
                            if (!value) {
                                return 'You need to write something!'
                            }
                        },
                        preConfirm: (value) => {
                            this.marketplaceConfig.sales_categories.push(value);
                            return true;
                            //return swal("Success", "The item <b>" + value + "</b> was successfully added to Global eCommerce Product Categories<br><br><em>Remember to Save Customisations</em>", "success");
                        }
                    });
                },
                addServiceCategory: function () {
                    Swal.fire({
                        title: 'New Service Category',
                        text: "Enter the name for the category:",
                        input: "text",
                        inputAttributes: {
                            autocapitalize: 'off'
                        },
                        showCancelButton: true,
                        confirmButtonText: 'Save',
                        animation: "slide-from-top",
                        showLoaderOnConfirm: true,
                        inputPlaceholder: "e.g. HR",
                        inputValidator: (value) => {
                            if (!value) {
                                return 'You need to write something!'
                            }
                        },
                        preConfirm: (value) => {
                            this.marketplaceConfig.service_categories.push(value);
                            return true;
                            //return swal("Success", "The item <b>" + value + "</b> was successfully added to Global eCommerce Product Categories<br><br><em>Remember to Save Customisations</em>", "success");
                        }
                    });
                }
            },
            mounted: function () {
                if (typeof this.partner.extra_data.hubConfig !== 'undefined') {
                    this.hubConfig = this.partner.extra_data.hubConfig;
                }
                if (typeof this.partner.extra_data.inviteConfig !== 'undefined') {
                    this.inviteConfig = this.partner.extra_data.inviteConfig;
                    this.inviteConfigBody = htmlspecialchars_decode(this.inviteConfig.email_body, 'ENT_QUOTES')
                    this.inviteConfigFooter = htmlspecialchars_decode(this.inviteConfig.email_footer, 'ENT_QUOTES')
                }
                if (typeof this.partner.extra_data.marketplaceConfig !== 'undefined') {
                    this.marketplaceConfig = this.partner.extra_data.marketplaceConfig;
                }
            },
            computed: {
                video_url: function () {
                    let video_id = typeof this.partner.extra_data.welcome_video_id !== 'undefined' ? this.partner.extra_data.welcome_video_id : '';
                    if (video_id.length === 0) {
                        return '';
                    }
                    return 'https://youtu.be/' + video_id;
                }
            }
        });
    </script>
@endsection

