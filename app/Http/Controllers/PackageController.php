<?php

namespace App\Http\Controllers;

use App\Models\InternetPackage;
use App\Models\Operator;
use App\Models\Faq;
use Illuminate\Http\Request;

class PackageController extends Controller
{
    public function index(Request $request)
    {
        $query = InternetPackage::with('operator')->where('is_active', true);

        // --- Operator(s) -------------------------------------------------
        $operatorIds = $this->asIntArray($request->query('operator'));
        if (! empty($operatorIds)) {
            $query->whereIn('operator_id', $operatorIds);
        }

        // --- Infrastructure(s) ------------------------------------------
        $allowedInfras = ['fiber', 'vdsl', 'adsl', 'fixed_wireless'];
        $infras = array_values(array_intersect(
            $this->asStringArray($request->query('infrastructure')),
            $allowedInfras
        ));
        if (! empty($infras)) {
            $query->whereIn('infrastructure_type', $infras);
        }

        // --- Speed tiers (OR'ed ranges) ---------------------------------
        $speeds = $this->asStringArray($request->query('speed'));
        if (! empty($speeds)) {
            $query->where(function ($q) use ($speeds) {
                foreach ($speeds as $range) {
                    if (preg_match('/^(\d+)-(\d+)$/', $range, $m)) {
                        $q->orWhereBetween('speed', [(int) $m[1], (int) $m[2]]);
                    }
                }
            });
        }

        // --- Commitment --------------------------------------------------
        $commitment = $request->query('commitment');
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

        // --- Sponsor only ------------------------------------------------
        if ($request->query('sponsor_only') === '1') {
            $query->where('is_sponsored', true);
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
            'commitment'     => $commitment,
            'price_min'      => is_numeric($priceMin) ? (float) $priceMin : null,
            'price_max'      => is_numeric($priceMax) ? (float) $priceMax : null,
            'sponsor_only'   => $request->query('sponsor_only') === '1',
        ];

        $hasActiveFilter = ! empty($operatorIds)
            || ! empty($infras)
            || ! empty($speeds)
            || in_array($commitment, ['0', '1'], true)
            || $filters['price_min'] !== null
            || $filters['price_max'] !== null
            || $filters['sponsor_only'];

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
