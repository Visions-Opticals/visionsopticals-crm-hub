<?php

namespace App\Http\Controllers;

use App\Dorcas\Hub\Utilities\UiResponse\UiResponse;
use Carbon\Carbon;
use GuzzleHttp\Exception\ServerException;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Cookie;

class HomeController extends Controller
{
    const SUMMARY_ICONS = [
        'cash' => 'account_balance_wallet',
        'custom_fields' => 'import_contacts',
        'customers' => 'face',
        'departments' => 'account_balance',
        'employees' => 'people_outline',
        'groups' => 'group',
        'locations' => 'room',
        'orders' => 'add_shopping_cart',
        'products' => 'whatshot',
        'services' => 'domain',
        'teams' => 'group'
    ];
    
    /** @var array  */
    /*const SETUP_UI_COMPONENTS = [
        ['name' => 'App Store', 'id' => 'appstore', 'enabled' => true, 'is_readonly' => false, 'path' => 'app-store'],
        ['name' => 'Customers', 'id' => 'customers', 'enabled' => true, 'is_readonly' => false, 'path' => 'apps/crm'],
        ['name' => 'eCommerce', 'id' => 'ecommerce', 'enabled' => true, 'is_readonly' => false, 'path' => 'apps/ecommerce'],
        ['name' => 'Finance', 'id' => 'finance', 'enabled' => true, 'is_readonly' => false, 'path' => 'apps/finance'],
        ['name' => 'Integrations', 'id' => 'integrations', 'enabled' => true, 'is_readonly' => false, 'path' => 'integrations'],
        ['name' => 'People', 'id' => 'organisation', 'enabled' => true, 'is_readonly' => false, 'path' => 'apps/people'],
        ['name' => 'Sales', 'id' => 'sales', 'enabled' => true, 'is_readonly' => false, 'path' => ['apps/inventory', 'apps/invoicing']],
        ['name' => 'Settings', 'id' => 'settings', 'enabled' => true, 'is_readonly' => false, 'path' => 'settings'],
        ['name' => 'Services', 'id' => 'services', 'enabled' => true, 'is_readonly' => true],
        ['name' => 'Vendors', 'id' => 'vendors', 'enabled' => true, 'is_readonly' => true],
    ];*/

    const SETUP_UI_COMPONENTS = [
        ['name' => 'Dashboard', 'base' => true, 'id' => 'dashboard', 'enabled' => true, 'is_readonly' => false, 'path' => 'dashboard', 'children' => []],
        ['name' => 'Customers', 'base' => true, 'id' => 'customers', 'enabled' => true, 'is_readonly' => false, 'path' => 'mcu', 'children' => []],
        ['name' => 'eCommerce', 'base' => false, 'id' => 'ecommerce', 'enabled' => true, 'is_readonly' => false, 'path' => 'mec', 'children' => []],
        ['name' => 'People', 'base' => false, 'id' => 'people', 'enabled' => true, 'is_readonly' => false, 'path' => 'mpe', 'children' => []],
        ['name' => 'Finance', 'base' => false, 'id' => 'finance', 'enabled' => true, 'is_readonly' => false, 'path' => 'mfn', 'children' => []],
        ['name' => 'Sales', 'base' => false, 'id' => 'sales', 'enabled' => true, 'is_readonly' => false, 'path' => 'msl', 'children' => []],
        ['name' => 'Operations', 'base' => true, 'id' => 'operations', 'enabled' => true, 'is_readonly' => false, 'path' => 'mop', 'children' => []],
        ['name' => 'Addons', 'base' => true, 'id' => 'addons', 'enabled' => true, 'is_readonly' => false, 'path' => ['mda', 'mmp', 'map', 'mit'], 'children' => []],
        //['name' => 'Addons', 'base' => true, 'id' => 'addons', 'enabled' => true, 'is_readonly' => false, 'path' => ['mda', 'mit'], 'children' => []],
        ['name' => 'Settings', 'base' => true, 'id' => 'settings', 'enabled' => true, 'is_readonly' => false, 'path' => 'mse', 'children' => []],
        ['name' => 'Services', 'base' => true, 'id' => 'services', 'enabled' => true, 'is_readonly' => true, 'path' => ['mps', 'mpp', 'map'], 'children' => []],
        ['name' => 'Vendors', 'base' => true, 'id' => 'vendors', 'enabled' => true, 'is_readonly' => true, 'path' => 'mvd', 'children' => []],
    ];

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
        parent::__construct();
        $this->data['page']['title'] = 'Dashboard';
    }

    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function index(Request $request, Sdk $sdk)
    {
        $this->setViewUiResponse($request);
        $company = $request->user()->company(true, true);
        # get the company
        $this->data['message'] = $request->query('message');
        # a message in the URL
        $viewMode = $request->session()->get('viewMode', null);
        # get the current view mode
        $userConfigurations = (array) $request->user()->extra_configurations;
        $userUiSetup = $userConfigurations['ui_setup'] ?? [];
        $configurations = (array) $company->extra_data;
        $this->data['isConfigured'] = true;
        if (empty($userUiSetup)) {
            # user's UI is not configured
            $this->data['isFirstConfiguration'] = empty($configurations['ui_setup']);
            if ($request->has('show_ui_wizard')) {
                $this->data['isConfigured'] = false;
            } else {
                $this->data['isConfigured'] = !$this->data['isFirstConfiguration'];
            }
            # check if the UI has been configured
            $currentUiSetup = $configurations['ui_setup'] ?? [];
            $this->data['setupUiFields'] = collect(self::SETUP_UI_COMPONENTS)->map(function ($field) use ($currentUiSetup) {
                if (!empty($field['is_readonly'])) {
                    return $field;
                }
                if (empty($currentUiSetup)) {
                    return $field;
                }
                $field['enabled'] = in_array($field['id'], $currentUiSetup);
                return $field;
            });
            # add the UI components
        }
        $this->data['countries'] = $countries = $this->getCountries($sdk);
        # get the countries listing
        $nigeria = !empty($countries) && $countries->count() > 0 ? $countries->where('iso_code', 'NG')->first() : null;
        # get the nigeria country model
        if (!empty($nigeria)) {
            $this->data['states'] = $this->getDorcasStates($sdk, $nigeria->id);
            # get the states
        }
        $daysAgo = Carbon::now()->subDays(config('hub.dashboard.graph.days_ago'));
        if (!empty($viewMode) && ($viewMode === 'professional' || $viewMode === 'vendor')) {
            $template = 'home-professional';
            $this->data['professionalProfile'] = $profile = $this->getProfessionalProfile($sdk);
            $this->data['summary'] = [
                'credentials' => [
                    'icon' => 'school',
                    'number' => !empty($profile->professional_credentials) ? count($profile->professional_credentials['data']) : 0
                ],
                'experience' => [
                    'icon' => 'business',
                    'number' => !empty($profile->professional_experiences) ? count($profile->professional_experiences['data']) : 0
                ],
                'services' => [
                    'icon' => 'business_center',
                    'number' => !empty($profile->professional_services) ? count($profile->professional_services['data']) : 0
                ],
            ];
            
            $metricsData = Cache::remember('company.metrics.directory.'.$viewMode.'.'.$request->user()->id, 30, function () use ($sdk, $daysAgo, $viewMode) {
                $metrics = $sdk->createMetricsService();
                $metricQuery = [
                    'resource' => 'professional',
                    'metrics' => ['requests_count', 'requests_pending', 'requests_accepted', 'requests_rejected']
                ];
                $metricsData = $metrics->addBodyParam('metrics', [$metricQuery])
                                        ->addBodyParam('from_date', $daysAgo->format('Y-m-d'))
                                        ->send('POST');
                # send a post request to get the data
                if (!$metricsData->isSuccessful()) {
                    return null;
                }
                return $metricsData->getData(true);
            });
            $this->data['daysAgo'] = 30;
            $this->data['requestGraph'] = $graph = $this->processRequestsGraphData($metricsData->professional);
            # we get the graph data
            
        } else {
            # default view mode
            $metricsData = Cache::remember('company.metrics.'.$company->id, 30, function () use ($sdk, $daysAgo) {
                $metrics = $sdk->createMetricsService();
                $metricsData = $metrics->addBodyParam('metrics', [
                        ['resource' => 'products', 'metrics' => ['sales_total']]
                    ])
                    ->addBodyParam('from_date', $daysAgo->format('Y-m-d'))
                    ->send('POST');
                # send a post request to get the data
                if (!$metricsData->isSuccessful()) {
                    return null;
                }
                return $metricsData->getData(true);
            });
            $this->data['daysAgo'] = 30;
            $salesData = !empty($metricsData->products) ? ($metricsData->products['sales_total'] ?? []) : [];
            $this->data['salesGraph'] = $graph = $this->processGraphData($salesData);
            # we get the graph data
            $response = $sdk->createCompanyService()->send('GET', ['status']);
            # get the company status
            $this->data['summary'] = self::prepareSummary(
                $response->getData()['counts'] ?? [],
                ['employees', 'customers', 'orders', 'cash']
            );
            $template = 'home';
        }
        $expiry = Carbon::parse($company->access_expires_at);
        # get the expiry
        if ($expiry->lessThanOrEqualTo(Carbon::now()) && empty($company->extra_data['paystack_auth_code'])) {
            # subscription expiry in effect, and there is no automatic authorization code for charging; we need one now
            $plan = $company->plan['data'];
            # get the plan
            if (empty($plan)) {
                throw new \RuntimeException('Something went wrong, and we could not determine your pricing plan.');
            }
            $this->data['plan']['price'] = $plan['price_' . $company->plan_type]['raw'];
        }
        return view($template, $this->data);
    }
    
    /**
     * @param Request $request
     * @param Sdk     $sdk
     *
     * @return \Illuminate\Http\RedirectResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function post(Request $request, Sdk $sdk)
    {
        $this->validate($request, [
            'business_name' => 'required|string|max:80',
            'business_type' => 'required|string|max:80',
            'business_sector' => 'required|string|max:80',
            'business_size' => 'required|string|max:80',
            'business_country' => 'required|string|max:80',
            'business_state' => 'nullable|string|max:80',
            'currency' => 'nullable|string|size:3',
            'selected_apps' => 'required|array',
            'selected_apps.*' => 'string'
        ]);
        # validate the request
        $company = $this->getCompany();
        # get the company
        $configurations = (array) $company->extra_data;
        $this->data['isConfigured'] = !empty($configurations['ui_setup']);
        
        
        $readonlyExtend = collect(self::SETUP_UI_COMPONENTS)->filter(function ($field) {
            return !empty($field['is_readonly']) && !empty($field['enabled']);
        })->pluck('id');
        # get the enabled-readonly values
        
        $readonlyRemovals = collect(self::SETUP_UI_COMPONENTS)->filter(function ($field) {
            return !empty($field['is_readonly']) && empty($field['enabled']);
        })->pluck('id');
        # get the disabled-readonly values
        
        $selectedApps = collect($request->input('selected_apps', []))->merge($readonlyExtend);
        # set the selected apps
        
        $selectedApps = $selectedApps->filter(function ($id) use ($readonlyRemovals) {
            return !$readonlyRemovals->contains($id);
        });
        # remove them
        
        try {
            $configurations['business_type'] = $request->input('business_type');
            $configurations['business_size'] = $request->input('business_size');
            $configurations['business_sector'] = $request->input('business_sector');
            
            $configurations['country_id'] = $request->input('business_country');
            $configurations['state_id'] = $request->input('business_state');
            $configurations['currency'] = strtoupper($request->input('currency', 'NGN'));
            $configurations['ui_setup'] = $selectedApps->unique()->all();
            
            $query = $sdk->createCompanyService()->addBodyParam('name', $request->business_name, true)
                                                ->addBodyParam('extra_data', $configurations)
                                                ->send('PUT');
            # send the request
            if (!$query->isSuccessful()) {
                throw new \RuntimeException('Failed while updating your business information. Please try again.');
            }
            $message = ['Successfully updated business information for '.$request->name];
            $response = (material_ui_html_response([$message]))->setType(UiResponse::TYPE_SUCCESS);
        } catch (ServerException $e) {
            $message = json_decode((string) $e->getResponse()->getBody(), true);
            $response = (material_ui_html_response([$message['message']]))->setType(UiResponse::TYPE_ERROR);
        } catch (\Exception $e) {
            $response = (material_ui_html_response([$e->getMessage()]))->setType(UiResponse::TYPE_ERROR);
        }
        return redirect(url()->current())->with('UiResponse', $response);
    }

    /**
     * @param array $metrics
     *
     * @return array
     */
    protected function processGraphData(array $metrics): array
    {
        $graph = [];
        foreach ($metrics as $dateKey => $value) {
            $date = Carbon::parse($dateKey);
            $graph[] = [
                'date' => $date->format('d M'),
                'count' => $value['NGN']['count'] ?? 0,
                'total' => $value['NGN']['total'] ?? 0
            ];
        }
        return $graph;
    }
    
    /**
     * @param array $metrics
     *
     * @return array
     */
    protected function processRequestsGraphData(array $metrics): array
    {
        $sections = [];
        $temp = [];
        foreach ($metrics as $section => $data) {
            $sections[] = $section;
            $temp[$section] = [];
            foreach ($data as $dateKey => $value) {
                $date = Carbon::parse($dateKey);
                $temp[$section][] = [
                    'date' => $date->format('d M'),
                    'count' => is_numeric($value) ? $value : count($value)
                ];
            }
        }
        $graphs = [];
        foreach ($sections as $section) {
            $entryKey = substr($section, 9);
            # get the substring
            foreach ($temp[$section] as $entry) {
                if (!isset($graphs[$entry['date']])) {
                    $graphs[$entry['date']] = ['date' => $entry['date']];
                }
                $graphs[$entry['date']][$entryKey] = $entry['count'];
            }
        }
        return array_values($graphs);
    }

    /**
     * @param array $rawStatuses
     * @param array $only
     *
     * @return array
     */
    public static function prepareSummary(array $rawStatuses, array $only = []): array
    {
        $summary = [];
        $only = empty($only) ? array_keys(static::SUMMARY_ICONS) : $only;
        # set the keys to pull up
        foreach ($only as $key) {
            $number = $rawStatuses[$key] ?? 0;
            $key = $key === 'contact_fields' ? 'custom_fields' : $key;
            $prefix = $key === 'cash' ? 'NGN ' : '';
            $summary[] = [
                'name' => str_replace('_', ' ', $key),
                'count' => $number,
                'count_formatted' => $prefix . number_format($number),
                'icon' => self::SUMMARY_ICONS[$key] ?? 'poll'
            ];
        }
        return $summary;
    }
}
