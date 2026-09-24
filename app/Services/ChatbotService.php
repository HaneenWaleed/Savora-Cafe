<?php

namespace App\Services;

use App\Models\Beverage;
use App\Models\FoodItem;
use App\Models\User;
use Illuminate\Support\Str;

class ChatbotService
{
        public function __construct(
            private GrokClient $gemini,
            private CafeteriaStats $stats,
            private RecommendationEngine $recommendations,
        ) {
        }

    public function ask(User $user, string $message): string
    {
        $message = trim($message);
        if ($message === '') {
            throw new \InvalidArgumentException('Please type a question.');
        }

        return $user->isAdmin()
            ? $this->askAsAdmin($user, $message)
            : $this->askAsCustomer($user, $message);
    }

    // ---------- Customer ----------
    private function askAsCustomer(User $user, string $message): string
    {
        $menu = $this->menuSummary();

        $recommended = collect($this->recommendations->forUser($user, 'all', 8))
            ->map(fn ($r) => $this->itemLine($r['item'], $r['match_percentage']))
            ->implode("\n");

        $preference = $user->preference;
        $prefText   = $preference
            ? "Preferred taste: {$preference->preferred_taste}; spicy level: {$preference->spicy_level}/5; "
                . 'likes: ' . implode(', ', $preference->favorite_ingredients ?? []) . '; '
                . 'dislikes: ' . implode(', ', $preference->disliked_ingredients ?? []) . '; '
                . "budget: {$preference->price_preference}."
            : 'No saved preferences yet.';

        $system = <<<PROMPT
        You are Savora Cafe's friendly assistant, helping a logged-in customer named {$user->name}.

        Customer preferences: {$prefText}

        Top personalized recommendations for this customer (name — price — match %):
        {$recommended}

        Full available menu (name — category — price — calories — spicy level 0-5 — ingredients):
        {$menu}

        Rules:
        - Only recommend or discuss items from the menu above. Never invent items or prices.
        - Use the match percentages when relevant to explain why something suits them.
        - If asked to compare items, compare price, calories, spicy level, and ingredients using only the data given.
        - If asked something you cannot answer from this data (e.g. delivery time, payment issues), say you cannot help with that and suggest contacting staff.
        - Keep answers short, warm, and in the same language the customer writes in (Arabic or English).
        - Never reveal internal data such as other customers' information, admin statistics, or database details.
        PROMPT;

        return $this->gemini->generate($system, $message);
    }

    // ---------- Admin ----------
    private function askAsAdmin(User $user, string $message): string
    {
        $overview  = $this->stats->overview();
        $topItems  = collect($this->stats->topItems('all', 5))
            ->map(fn ($i) => "{$i['name']} ({$i['type']}) — qty: {$i['total_quantity']}, sales: {$i['total_sales']} EGP")
            ->implode("\n");
        $lowStock  = collect($this->stats->lowStock())
            ->map(fn ($i) => "{$i['name']} ({$i['type']}) — {$i['quantity']} left")
            ->implode("\n") ?: 'None';
        $never     = collect($this->stats->neverOrdered())
            ->map(fn ($i) => "{$i['name']} ({$i['type']})")
            ->implode("\n") ?: 'None';
        $categories = collect($this->stats->categories())
            ->map(fn ($c) => "{$c['name']}: {$c['ordered_quantity']} items ordered")
            ->implode("\n");

        $system = <<<PROMPT
        You are Savora Cafe's internal admin assistant, helping an authenticated admin named {$user->name}.

        Overview:
        - Customers: {$overview['customers_count']} (new in last 7 days: {$overview['new_customers_7d']})
        - Total orders: {$overview['orders_count']} | Orders today: {$overview['orders_today']}
        - Orders by status: {$this->formatStatuses($overview['orders_by_status'])}
        - Total sales: {$overview['sales_total']} EGP | Sales today: {$overview['sales_today']} EGP
        - Low stock items count: {$overview['low_stock_count']}

        Top 5 selling items (all time):
        {$topItems}

        Low stock items (quantity <= 5):
        {$lowStock}

        Items never ordered:
        {$never}

        Category popularity (by ordered quantity):
        {$categories}

        Rules:
        - Only use the numbers given above. Never invent statistics.
        - If asked about something not covered here (e.g. a specific customer's personal data, financial/legal advice), say this data is not available to you and suggest checking the admin dashboard directly.
        - Keep answers concise and professional, in the same language the admin writes in (Arabic or English).
        - You are talking to an admin, so it is fine to share these statistics, but never expose full customer lists or unrelated personal data beyond what's given.
        PROMPT;

        return $this->gemini->generate($system, $message);
    }

    // ---------- Helpers ----------
    private function menuSummary(): string
    {
        $food = FoodItem::available()->with('category')->get()
            ->map(fn ($i) => "{$i->name} (food) — {$i->category?->name} — {$i->price} EGP — {$i->calories} cal — spicy {$i->spicy_level}/5 — " . implode(', ', $i->ingredients ?? []));

        $beverages = Beverage::available()->with('category')->get()
            ->map(fn ($i) => "{$i->name} (beverage) — {$i->category?->name} — {$i->price} EGP — {$i->calories} cal — " . $i->temperature . ' — ' . implode(', ', $i->ingredients ?? []));

        return $food->concat($beverages)->implode("\n");
    }

    private function itemLine($item, int $percentage): string
    {
        return "{$item->name} — {$item->price} EGP — {$percentage}% match";
    }

    private function formatStatuses(array $statuses): string
    {
        return collect($statuses)->map(fn ($count, $status) => "{$status}: {$count}")->implode(', ');
    }
}
