<?php

namespace App\Http\Middleware;

use Closure;
use GuzzleHttp\Psr7\Uri;
use Hostville\Dorcas\Sdk;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use App\Dorcas\Hub\Utilities\DomainManager\DorcasSubdomain;

class ResolveCustomSubdomain
{
    /**
     * Array containing paths that should be redirected to a specified custom sub-domain along.
     * service => path
     *
     * @var array
     */
    protected $redirectPathsToSubdomain = [
        'store' => 'store',
        'blog' => 'blog',
        'market' => 'market'
    ];
    
    /** @var array  */
    protected $standardHosts = ['dorcashub.test'];
    //protected $standardHosts = [];
    # empty it

    /**
     * String containing the Dorcas Edition
     *
     * @var string
     */
    protected $dorcasEdition = 'business';

    /**
     * ResolveCustomSubdomain constructor.
     */
    public function __construct()
    {
        $this->dorcasEdition = config('dorcas.edition','business');   
        $uri = new Uri(config('app.url'));
        # the base URI

        $this->standardHosts[] = $uri->getHost();
        # append the configured host

        //$this->standardHosts =  config('dorcas.standard_hosts');
        # get this from ENV as opposed to hard-coded $this->standardHosts

    }
    
    /**
     * @param         $request
     * @param Closure $next
     *
     * @return mixed
     */
    public function handle(Request $request, Closure $next)
    {
        $host = $request->header('host');
        # get the host header value

        try {

            $domainInfo = $this->splitHost($host, $request->path());
            # get the host information

            $slug =  $this->dorcasEdition === 'business' ? $domainInfo->getHost() : $domainInfo->getSubdomain();
            // we  need  to later  modify DorcasSubdomain to accept a  full "domain" field later
            # get the slug

            //dd($slug);
            
            if (empty($slug) && $this->dorcasEdition === 'business') {
                # no matches
                throw new \RuntimeException('Could not reliably determine the URL domain for this host.');
            } elseif (empty($slug) && $this->dorcasEdition !== 'business') {
                # no matches
                throw new \RuntimeException('Could not reliably determine the URL slug for this host.');
            }

            //dd(array($domainInfo, $slug, $this->dorcasEdition));

            $serviceRedirectUrl = $this->getServiceRedirectUrl($domainInfo, $request->path(), $request->getQueryString());
            //dd($serviceRedirectUrl);
            
            if ($serviceRedirectUrl !== null) {
                return redirect($serviceRedirectUrl);
            }

            $sdk = app(Sdk::class);
            # get the Sdk

            $domain = Cache::remember('domain_' . $slug, 86400, function () use ($slug, $sdk) {
                $query = $sdk->createDomainResource()->addQueryArgument('id', $slug)
                ->addQueryArgument('include', 'owner,owner.users')
                ->send('get', ['resolver']);
                # send the query
                if (!$query->isSuccessful()) {
                    return null;
                }
                return (object) $query->getData();
            });
            
            if (empty($domain)) {
                throw new \RuntimeException('Could not resolve the custom subdomain');
            }
            $owner = !empty($domain->owner['data']['slug']) ? (object) $domain->owner['data'] : null;
            # set the partner, if it was resolved as such
            $partner = $this->isPartner($owner) ? $owner : null;
            # if the object is a partner, we set it - else, just ignore it
            $isPartnerAdmin = false;
            $request->session()->put('domainInfo', $domainInfo);
            $request->session()->put('domain', $domain);
            if (!empty($partner)) {
                $request->session()->put('partner', $partner);
                $userRoles = Auth::check() ? $request->user()->roles['data'] : [];
                if (!empty($userRoles)) {
                    $partnerRole = collect($userRoles)->filter(function ($r) {
                        return $r['name'] === 'partner';
                    });
                    $isPartnerAdmin = $partnerRole->count() > 0;
                    if ($isPartnerAdmin) {
                        $request->session()->put('isPartnerAdministrator', true);
                    }
                }
            }
            View::composer('*', function ($view) use ($domain, $partner, $isPartnerAdmin, $sdk) {
                $view->with('domain', $domain);
                $defaultProductName = !empty($partner) ? $partner->name : 'Hub';
                if (!empty($partner)) {
                    $partnerConfig = (array) $partner->extra_data;
                    $hubConfig = $partnerConfig['hubConfig'] ?? [];
                    $hubConfig['product_logo'] = !empty($partner->logo) ? $partner->logo : null;
                    $view->with('partner', $partner);
                    $view->with('partnerHubConfig', $hubConfig);
                    $view->with('appUiSettings', $hubConfig);
                    $view->with('partnerHubProductName', $hubConfig['product_name'] ?? $defaultProductName);
                    $view->with('isPartnerAdministrator', $isPartnerAdmin);
                    $scheme = app()->environment() === 'production' ? 'https' : 'http';
                    //$vPanelUrl = $scheme . '://' . $domain->prefix . '.' . $domain->domain['data']['domain'] . '/vpanel';
                    $vPanelUrl = generate_partner_url($partner, 'vpanel', ['token' => $sdk->getAuthorizationToken()]);
                    $view->with('vPanelUrl', $vPanelUrl);
                }
            });
            # set the domain to the session
        } catch (\RuntimeException $e) {
            $request->session()->remove('domainInfo');
            $request->session()->remove('domain');
            # remove the domain from the session, if previously set
            $request->session()->remove('partner');
            # same for the partner
        }
        return $next($request);
    }
    
    /**
     * @param \stdClass|null $object
     *
     * @return bool
     */
    protected function isPartner(\stdClass $object = null): bool
    {
        if (empty($object)) {
            return false;
        }
        return !empty($object->name) && !empty($object->slug);
    }
    
    /**
     * @param \App\Dorcas\Hub\Utilities\DomainManager\DorcasSubdomain $domainInfo
     * @param string                                                  $path
     * @param string|null                                             $query
     *
     * @return string|null
     */
    public function getServiceRedirectUrl(DorcasSubdomain $domainInfo, string $path, string $query = null): ? string
    {
        if ($domainInfo->getService() === null || !starts_with($path, array_values($this->redirectPathsToSubdomain))) {
            # no resolved service OR it's not a service configured for compulsory redirect
            return null;
        }
        $servicePath = $this->redirectPathsToSubdomain[$domainInfo->getService()];
        $resultingPath = substr($path, strlen($servicePath));
        $redirectUrl = $domainInfo->getDomain() . '/' .
            (strpos($resultingPath, '/') === 0 ? substr($resultingPath, 1) : $resultingPath) . '?' . ($query ?? '');
        return $redirectUrl;
    }
    
    /**
     * @param string      $host
     * @param string|null $path
     *
     * @return \App\Dorcas\Hub\Utilities\DomainManager\DorcasSubdomain
     */
    public function splitHost(string $host, string $path = null): DorcasSubdomain
    {
        //$this->standardHosts[] = env('STANDARD_HOST');

        $finalParts = [];

        $multiTenant = $this->dorcasEdition === 'business' ? false : true;

        // Get Services
        $services = array_keys($this->redirectPathsToSubdomain);
        $serviceName = null;
        $serviceExists = false;
       
        if (substr_count($host,"localhost") >= 1) {
            throw new \RuntimeException('Accessing via localhost.');
        }

        if (in_array($host, $this->standardHosts, true)) {
          throw new \RuntimeException('Accessing via the regular domain.');
        }
        # ignore & exist  as  Resolver  is only concerened with subdomain/slug/service type scenarios



        $parts = $multiTenant ? explode('.', $host, 4)  : explode('.', $host, 3);
        $partsCount = count($parts);
        # split it up to at most 3 parts -- we're trying to match things like: xyz.dorcas.io (business), xyz.store.dorcas.io (cloud, community and enterprise)

        $partsExtension = $parts[($partsCount - 1)]; // last bit
        $partsDomain = $parts[($partsCount - 2)]; //2nd to last bit
        # the above 2 are usually always constant
        $partsHost = $partsDomain . "." . $partsExtension;

        $partsService = "";
        $partsSubdomain = "";

        $slug = $parts[0];
        /*
            the first part next to base domain.
            In Community & enterprise scenarios (it is the subdomain OR the service)
            In Business, its the service
        */
        if ($partsCount == 4 && $multiTenant) {
            //we have a tenant on a service e.g. xyz.store.dorcas.io
            if ( in_array($parts[1], $redirectPathsToSubdomain)) {
                //3rd to last is a service
                $serviceExists = true;
                $partsService = $serviceName = $parts[1];
            }
            $partsSubdomain = $parts[0];

        } elseif ($partsCount == 3 && $multiTenant) {
            //then probably a subdomain (e.g. xyz.dorcas.io)
            // OR 
            // we have a service like marketplace (e.g market.dorcas.io) 
            if ( $parts[0] == "market") {
                $serviceExists = true;
                $partsService = $serviceName =  $parts[0];
            } else {
                $partsSubdomain = $parts[0];
            }

        } elseif ($partsCount == 3 && !$multiTenant) {
            //then probably a service in business (e.g. store.dorcas.io)
            if ( in_array($parts[0], $redirectPathsToSubdomain)) {
                //3rd to last is a service
                $serviceExists = true;
                $partsService = $serviceName = $parts[0];
            }
        } else {
            // a default scenario of dorcas.io
        }

        // Let's recompose for DorcasSubDomain
        $finalParts[0] = !empty($partsSubdomain) ? $partsSubdomain : '';
        $finalParts[1] = !empty($partsService) ? $partsService : null;
        $finalParts[2] = !empty($partsHost) ? $partsHost : 'dorcas.default';

        # get the services - we want to identify the service to be loaded
        # the below even if it looks like duplicate, helps properly identify path service like /store
        foreach ($services as $service) {
            $pathRefersToService = $path !== null && starts_with($path, $service);
            $slugContainsService  = str_contains($slug, $service);
            if (!$pathRefersToService && !$slugContainsService) {
                continue;
            }
            $serviceName = $service;
            break;
        }

        // if ($parts[count($parts) - 2] === 'dorcas') { // meaning the cloud version
        //     # since the 2nd to the last index is just dorcas - we merge it with the TLD
        //     $parts[1] .= '.' . $parts[2];
        //     if ($serviceName !== null) {
        //         $parts[2] = $parts[1];
        //         # switch the domain to the last index since we have discovered a service
        //         $parts[1] = $serviceName;
        //     } else {
        //         unset($parts[2]);
        //     }
        // }
        # NOT NEEDED AGAIN for Open Source

        // if (count($parts) === 3 && !starts_with($parts[2], 'dorcas')) {
        //   // 3 parts - business. Later needAdd a replica for comminity  & enterprise
        //     # we still have 3 indexes in the array, but the host domain is a not a Dorcas domain
        //     $host = $parts[1] . '.' . $parts[2];
        //     # recreate the parts
        //     $parts[0] = $parts[1];
        //     # set the slug to be the domain name
        //     if ($serviceName !== null) {
        //         $parts[1] = $serviceName;
        //         $parts[2] = $host;
        //     } else {
        //         $parts[1] = $host;
        //         unset($parts[2]);
        //     }
        // }
        # NOT NEEDED AGAIN for Open Source
        
        $resp = new DorcasSubdomain($finalParts, true, $this->dorcasEdition);
        
        return $resp;
    }
}
