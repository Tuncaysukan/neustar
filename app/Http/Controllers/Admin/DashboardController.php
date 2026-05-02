<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Blog;
use App\Models\CommitmentReminder;
use App\Models\InfrastructureLead;
use App\Models\InternetPackage;
use App\Models\Operator;
use App\Models\PackageReview;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // ── Temel sayaçlar ──────────────────────────────────────────
        $stats = Cache::remember('admin.dashboard.stats', 60, function () {
            return [
                'total_packages'      => InternetPackage::count(),
                'active_packages'     => InternetPackage::where('is_active', true)->count(),
                'sponsored_packages'  => InternetPackage::where('is_sponsored', true)->count(),
                'total_clicks'        => DB::table('package_clicks')->count(),
                'clicks_today'        => DB::table('package_clicks')->whereDate('created_at', today())->count(),
                'clicks_this_week'    => DB::table('package_clicks')->where('created_at', '>=', now()->startOfWeek())->count(),
                'total_operators'     => Operator::count(),
                'active_operators'    => Operator::where('is_active', true)->count(),
                'total_leads'         => InfrastructureLead::count(),
                'new_leads'           => InfrastructureLead::where('status', 'new')->count(),
                'leads_today'         => InfrastructureLead::whereDate('created_at', today())->count(),
                'leads_this_week'     => InfrastructureLead::where('created_at', '>=', now()->startOfWeek())->count(),
                'total_blogs'         => Blog::count(),
                'published_blogs'     => Blog::where('is_active', true)->whereNotNull('published_at')->count(),
                'pending_reviews'     => PackageReview::where('is_approved', false)->count(),
                'total_reviews'       => PackageReview::count(),
                'commitment_reminders'=> CommitmentReminder::count(),
                'commitment_expiring' => CommitmentReminder::where('end_date', '<=', now()->addDays(30))
                                            ->where('end_date', '>=', now())->count(),
                // Ziyaretçi istatistikleri
                'visitors_today'      => DB::table('page_views')->whereDate('viewed_date', today())->count(),
                'visitors_week'       => DB::table('page_views')->where('viewed_date', '>=', now()->startOfWeek()->toDateString())->count(),
                'visitors_month'      => DB::table('page_views')->where('viewed_date', '>=', now()->startOfMonth()->toDateString())->count(),
            ];
        });

        // ── Son 7 günlük başvuru grafiği (günlük) ───────────────────
        $leadChart = Cache::remember('admin.dashboard.lead_chart', 300, function () {
            $days = collect(range(6, 0))->map(fn($i) => now()->subDays($i)->format('Y-m-d'));
            $counts = InfrastructureLead::select(
                DB::raw('DATE(created_at) as date'),
                DB::raw('COUNT(*) as count')
            )
            ->where('created_at', '>=', now()->subDays(6)->startOfDay())
            ->groupBy('date')
            ->pluck('count', 'date');
            return $days->map(fn($d) => [
                'date'  => $d,
                'label' => \Carbon\Carbon::parse($d)->locale('tr')->isoFormat('D MMM'),
                'count' => $counts->get($d, 0),
            ])->values();
        });

        // ── Son 30 günlük ziyaretçi grafiği ─────────────────────────
        $visitorChart = Cache::remember('admin.dashboard.visitor_chart', 300, function () {
            $days = collect(range(29, 0))->map(fn($i) => now()->subDays($i)->format('Y-m-d'));
            $counts = DB::table('page_views')
                ->select(DB::raw('viewed_date as date'), DB::raw('COUNT(*) as count'))
                ->where('viewed_date', '>=', now()->subDays(29)->toDateString())
                ->groupBy('viewed_date')
                ->pluck('count', 'date');
            return $days->map(fn($d) => [
                'date'  => $d,
                'label' => \Carbon\Carbon::parse($d)->locale('tr')->isoFormat('D MMM'),
                'count' => $counts->get($d, 0),
            ])->values();
        });

        // ── Son başvurular ───────────────────────────────────────────
        $recentLeads = InfrastructureLead::latest()->limit(8)->get();

        // ── Onay bekleyen yorumlar ───────────────────────────────────
        $pendingReviews = PackageReview::with('internetPackage.operator')
            ->where('is_approved', false)->latest()->limit(5)->get();

        // ── En çok başvuru alan iller ────────────────────────────────
        $topCities = Cache::remember('admin.dashboard.top_cities', 300, function () {
            return InfrastructureLead::select('city_name', DB::raw('COUNT(*) as count'))
                ->whereNotNull('city_name')->groupBy('city_name')
                ->orderByDesc('count')->limit(5)->get();
        });

        return view('admin.dashboard', compact(
            'stats', 'leadChart', 'visitorChart', 'recentLeads', 'pendingReviews', 'topCities'
        ));
    }
}
