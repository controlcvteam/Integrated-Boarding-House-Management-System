<?php

namespace Tests\Unit;

use App\Models\Tenant;
use Carbon\Carbon;
use Tests\TestCase;

class RentCalculationTest extends TestCase
{
    public function test_standard_due_date_calculation(): void
    {
        $tenant = new Tenant();
        $tenant->move_in_date = Carbon::parse('2026-08-15');

        // For October (31 days)
        $dueDateOct = $tenant->getDueDateForMonthYear(10, 2026);
        $this->assertEquals('2026-10-15', $dueDateOct->format('Y-m-d'));

        // For November (30 days)
        $dueDateNov = $tenant->getDueDateForMonthYear(11, 2026);
        $this->assertEquals('2026-11-15', $dueDateNov->format('Y-m-d'));
    }

    public function test_end_of_month_handling_for_31st(): void
    {
        $tenant = new Tenant();
        $tenant->move_in_date = Carbon::parse('2026-01-31');

        // February 2026 has 28 days
        $dueDateFeb = $tenant->getDueDateForMonthYear(2, 2026);
        $this->assertEquals('2026-02-28', $dueDateFeb->format('Y-m-d'));

        // April has 30 days
        $dueDateApr = $tenant->getDueDateForMonthYear(4, 2026);
        $this->assertEquals('2026-04-30', $dueDateApr->format('Y-m-d'));

        // March has 31 days
        $dueDateMar = $tenant->getDueDateForMonthYear(3, 2026);
        $this->assertEquals('2026-03-31', $dueDateMar->format('Y-m-d'));
    }

    public function test_leap_year_february_handling(): void
    {
        $tenant = new Tenant();
        $tenant->move_in_date = Carbon::parse('2024-05-30');

        // February 2024 (Leap year) has 29 days
        $dueDateFeb2024 = $tenant->getDueDateForMonthYear(2, 2024);
        $this->assertEquals('2024-02-29', $dueDateFeb2024->format('Y-m-d'));
    }
}
