<?php

namespace App\Http\Controllers;

use App\Models\InternetPackage;
use App\Models\Operator;
use App\Models\Faq;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index(Request $request, $operatorSlug = null, $infraSlug = null)
    {
        $query = InternetPackage::with('operator')->where('is_active', true);

        $operatorIds = $this->asIntArray($request->query('operator'));
        if ($operatorSlug) {
            $operator = Operator::where('slug', $operatorSlug)->first();
            if ($operator) {
                $query->where('operator_id', $operator->id);
                // URL slug'dan geleni filtreye dahil et
                if (!in_array($operator->id, $operatorIds)) {
                    $operatorIds[] = $operator->id;
                }
            }
        }
        
        if (! empty($operatorIds)) {
            $query->whereIn('operator_id', $operatorIds);
        }

        // --- Infrastructure(s) ------------------------------------------
        $allowedInfras = ['fiber', 'vdsl', 'adsl', 'fixed_wireless'];
        $infras = array_values(array_intersect(
            $this->asStringArray($request->query('infrastructure')),
            $allowedInfras
        ));
        
        if ($infraSlug) {
            $dbInfra = str_replace('-', '_', $infraSlug);
            if (in_array($dbInfra, $allowedInfras)) {
                $query->where('infrastructure_type', $dbInfra);
                if (!in_array($dbInfra, $infras)) {
                    $infras[] = $dbInfra;
                }
            }
        }

        if (! empty($infras)) {
            $query->whereIn('infrastructure_type', $infras);
        }

        // --- Speed (tek tek değerler) ----------------------------------
        $speeds = $this->asIntArray($request->query('speed'));
        if (! empty($speeds)) {
            $query->whereIn('speed', $speeds);
        }

        // --- Modem -------------------------------------------------------
        $allowedModems = ['free', 'paid'];
        $modems = array_values(array_intersect(
            $this->asStringArray($request->query('modem')),
            $allowedModems
        ));
        if (! empty($modems)) {
            $query->whereIn('modem_included', $modems);
        }

        // --- Commitment --------------------------------------------------
        // URL'de okunabilir değerler: taahhütsüz=0, taahhutlu=1
        $commitmentRaw = $request->query('commitment');
        $commitmentMap = ['taahhütsüz' => '0', 'taahhütsuz' => '0', 'taahhütsüz' => '0', 'taahhutlu' => '1', 'taahhütlü' => '1'];
        $commitment = $commitmentMap[$commitmentRaw] ?? $commitmentRaw; // eski 0/1 de çalışsın

        if ($commitment === '0') {
            $query->where('commitment_period', 0);
        } elseif ($commitment === '1') {
            $query->where('commitment_period', '>', 0);
        }

        // --- Price range -------------------------------------------------
        $priceMin = $request->query('price_min');
        $priceMax = $request->query('price_max');
        if (is_numeric($priceMin)) {
            $query->where('price', '>=', (float) $priceMin);
        }
        if (is_numeric($priceMax)) {
            $query->where('price', '<=', (float) $priceMax);
        }

        // --- Sort --------------------------------------------------------
        $sort = (string) $request->query('sort', 'featured');
        $allowedSort = ['featured', 'price-asc', 'price-desc', 'speed-desc', 'speed-asc'];
        if (! in_array($sort, $allowedSort, true)) {
            $sort = 'featured';
        }

        switch ($sort) {
            case 'price-asc':
                $query->orderByDesc('is_sponsored')->orderBy('price');
                break;
            case 'price-desc':
                $query->orderByDesc('is_sponsored')->orderByDesc('price');
                break;
            case 'speed-desc':
                $query->orderByDesc('is_sponsored')->orderByDesc('speed');
                break;
            case 'speed-asc':
                $query->orderByDesc('is_sponsored')->orderBy('speed');
                break;
            case 'featured':
            default:
                $query->orderByDesc('is_sponsored')->orderBy('price');
                break;
        }

        $packages = $query->paginate(12)->withQueryString();

        // --- Data for sidebar -------------------------------------------
        $operators = Operator::where('is_active', true)
            ->orderBy('name')
            ->get(['id', 'name', 'slug']);

        $priceBounds = InternetPackage::where('is_active', true)
            ->selectRaw('MIN(price) as min_price, MAX(price) as max_price')
            ->first();

        $filters = [
            'operator'       => $operatorIds,
            'infrastructure' => $infras,
            'speed'          => $speeds,
            'modem'          => $modems,
            'commitment'     => $commitment, // normalize edilmiş 0/1
            'price_min'      => is_numeric($priceMin) ? (float) $priceMin : null,
            'price_max'      => is_numeric($priceMax) ? (float) $priceMax : null,
        ];

        $hasActiveFilter = ! empty($operatorIds)
            || ! empty($infras)
            || ! empty($speeds)
            || ! empty($modems)
            || in_array($commitment, ['0', '1', 'taahhütsüz', 'taahhutlu'], true)
            || $filters['price_min'] !== null
            || $filters['price_max'] !== null
            ;

        return view('frontend.packages.index', compact(
            'packages', 'sort', 'operators', 'priceBounds', 'filters', 'hasActiveFilter'
        ));
    }

    public function show($slug)
    {
        $package = InternetPackage::with(['operator', 'reviews' => function ($q) {
            $q->where('is_approved', true)->latest();
        }])->where('slug', $slug)->firstOrFail();

        $faqs = Faq::where('is_active', true)
            ->where(function ($query) use ($package) {
                $query->where('page_type', 'package')
                      ->where('relation_id', $package->id)
                      ->orWhere('page_type', 'general');
            })
            ->orderBy('order')
            ->get();

        return view('frontend.packages.show', compact('package', 'faqs'));
    }

    /**
     * GET /internet-paketleri/{slug}/basvur
     * Güzel loading/yönlendirme sayfası — operatörün sitesine gönderir.
     */
    public function apply($slug)
    {
        $package = InternetPackage::with('operator')
            ->where('slug', $slug)
            ->where('is_active', true)
            ->firstOrFail();

        if ($package->apply_type === 'site') {
            $target = $package->external_url ?: $package->operator->website_url;
            if ($target) {
                return view('frontend.packages.redirect', [
                    'package' => $package,
                    'target'  => $target,
                ]);
            }
        }

        if ($package->apply_type === 'call' && $package->call_number) {
            return redirect()->to('tel:' . $package->call_number);
        }

        // apply_type = 'form' veya null → operatör sitesi varsa yönlendirme sayfası, yoksa form
        $target = $package->affiliate_url ?: $package->operator->website_url;
        if ($target) {
            return view('frontend.packages.redirect', [
                'package' => $package,
                'target'  => $target,
            ]);
        }

        return view('frontend.packages.apply_form', compact('package'));
    }

    public function submitApplication(Request $request, $slug)
    {
        $package = InternetPackage::where('slug', $slug)->where('is_active', true)->firstOrFail();

        $data = $request->validate([
            'full_name' => 'required|string|max:255',
            'email'     => 'required|email|max:255',
            'phone'     => 'required|string|max:32',
            'kvkk'      => 'required|accepted',
        ]);

        // We can reuse InfrastructureLead or create a new Applications table.
        // For now, let's use InfrastructureLead or a simpler log.
        // Actually, creating a new table might be better, but InfrastructureLead is already there and has similar fields.
        // Let's create a simpler log or use InfrastructureLead but mark it as 'package_apply'.
        
        \App\Models\InfrastructureLead::create([
            'full_name'         => $data['full_name'],
            'email'             => $data['email'],
            'phone'             => $data['phone'],
            'lookup_snapshot'   => ['package_id' => $package->id, 'package_name' => $package->name, 'type' => 'direct_apply'],
            'status'            => 'new',
            'ip'                => $request->ip(),
            'user_agent'        => substr((string) $request->userAgent(), 0, 255),
            'kvkk_approved_at'  => now(),
        ]);

        return redirect()->back()->with('status', 'Başvurunuz başarıyla alındı. Uzmanlarımız sizi en kısa sürede arayacaktır.');
    }

    // ------------------------------------------------------------------
    // Helpers
    // ------------------------------------------------------------------

    /**
     * Normalise a query value to an array of ints (accepts scalar or array).
     */
    private function asIntArray($value): array
    {
        if ($value === null || $value === '') {
            return [];
        }
        $arr = is_array($value) ? $value : [$value];
        return array_values(array_filter(array_map('intval', $arr)));
    }

    /**
     * Normalise a query value to an array of non-empty strings.
     */
    private function asStringArray($value): array
    {
        if ($value === null || $value === '') {
            return [];
        }
        $arr = is_array($value) ? $value : [$value];
        return array_values(array_filter(array_map(function ($v) {
            return is_scalar($v) ? trim((string) $v) : '';
        }, $arr), fn ($s) => $s !== ''));
    }
}
