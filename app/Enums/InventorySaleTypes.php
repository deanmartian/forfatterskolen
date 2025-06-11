<?php

namespace App\Enums;

use App\Traits\EnumsWithOptions;

class InventorySaleTypes
{
    use EnumsWithOptions;

    const inventory_delivered = 'Delivered';

    const inventory_physical_items = 'Physical Items';

    const inventory_returns = 'Returns';
}
